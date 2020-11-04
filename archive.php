<?php

$context = Timber::context();

if (is_category()) {
  $term = get_queried_object();
  $args = array(
    'post_type' => 'project',
    'category_name' => $term->slug,
    'meta_query' => array(
      array(
        'key' => '_thumbnail_id'
      )
    )
  );

  $context['projects'] = Timber::get_posts($args);
  Timber::render('projects.twig', $context);
}
