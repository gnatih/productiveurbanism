<?php

if (!class_exists('Timber')) {
  add_action('admin_notices', function () {
    echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
  });
  return;
}

Timber::$dirname = array('templates');

class PuSite extends Timber\Site
{
  public function __construct()
  {
    add_action('after_setup_theme', array($this, 'theme_supports'));
    add_action('wp_enqueue_scripts', array($this, 'theme_assets'));
    add_action('init', array($this, 'register_post_types'));
    add_action('timber/context', array($this, 'add_to_context'));
    add_action('add_meta_boxes', array($this, 'theme_metaboxes'));
    add_action("category_edit_form_fields", array($this, 'add_form_fields_example'), 10, 2);

    remove_filter('pre_term_description', 'wp_filter_kses');
    remove_filter('term_description', 'wp_kses_data');
    parent::__construct();
  }

  public function add_form_fields_example($term, $taxonomy)
  {
?>
    <tr valign="top">
      <th scope="row">Description</th>
      <td>
        <?php wp_editor(html_entity_decode($term->description), 'description', array('media_buttons' => false)); ?>
        <script>
          jQuery(window).ready(function() {
            jQuery('label[for=description]').parent().parent().remove();
          });
        </script>
      </td>
    </tr>
<?php
  }

  public function add_to_context($context)
  {
    $context['menu'] = new Timber\Menu('primary');
    $context['custom_logo_url'] = wp_get_attachment_image_url(get_theme_mod('custom_logo'));

    return $context;
  }

  public function register_post_types()
  {
    register_nav_menus(array(
      'primary' => 'Primary',
    ));

    register_post_type('project', array(
      'labels' => array(
        'name' => __('Projects', 'ai'),
        'singular_name' => __('Project', 'ai')
      ),
      'public' => true,
      'taxonomies' => array('category'),
      'has_archive' => 'projects',
      'show_ui' => true,
      'rewrite' => array('slug' => 'project'),
      'supports' => array('title', 'thumbnail', 'post-thumbnails', 'editor'),
      'show_in_rest' => true,
      'menu_icon' => 'dashicons-portfolio'
    ));
  }

  public function theme_assets()
  {
    if (!is_admin()) {
      wp_deregister_script('jquery');
      wp_enqueue_script('jquery', 'https://code.jquery.com/jquery-3.5.1.min.js', array(), '3.5.1', true);
      wp_enqueue_script('bootstrap', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/js/bootstrap.min.js', array('jquery', 'popper'), '4.5.0', true);
      wp_enqueue_script('popper', 'https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js', array(), '1.14.7', true);
      wp_enqueue_style('lineawesome', 'https://cdnjs.cloudflare.com/ajax/libs/line-awesome/1.3.0/line-awesome/css/line-awesome.min.css', array(), '1.3.0');
      wp_enqueue_style('ibm-plex-sans', 'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;600&display=swap');
      wp_enqueue_style('pu', mix('/style.css'), array(), wp_get_theme()->get('Version'));
      wp_enqueue_script('pu', get_template_directory_uri() . '/script.js', array('bootstrap'), '1.0', true);
      wp_register_script('pu-inline-scripts', '', array('jquery'), '1.0', true);
      wp_enqueue_script('pu-inline-scripts');
    }
  }

  public function theme_supports()
  {
    add_theme_support('custom-logo');
    add_theme_support('editor-styles');
    add_theme_support('post-thumbnails');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('title-tag');
    add_theme_support('menus');
    add_theme_support('responsive-embeds');

    add_post_type_support('project', 'wps_subtitle');
    add_editor_style('style-editor.css');
    add_editor_style('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;600&display=swap');
  }
}

new PuSite();

if (!function_exists('mix')) {
  function mix($path)
  {
    $pathWithOutSlash = ltrim($path, '/');
    $pathWithSlash    = '/' . ltrim($path, '/');
    $manifestFile     = get_theme_file_path('mix-manifest.json');

    if (!$manifestFile) {
      return get_template_directory_uri() . '/' . $pathWithOutSlash;
    }

    $manifestArray = json_decode(file_get_contents($manifestFile), true);

    if (array_key_exists($pathWithSlash, $manifestArray)) {
      return get_template_directory_uri() . '/' . ltrim($manifestArray[$pathWithSlash], '/');
    }

    return get_template_directory_uri() . '/' . $pathWithOutSlash;
  }
}
