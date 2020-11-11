<?php

$context         = Timber::context();
$timber_post     = Timber::get_post();

$context['project'] = $timber_post;
$context['subtitle'] = apply_filters('plugins/wp_subtitle/get_subtitle', '', array(
  'before' => '<div class="subtitle font-weight-bold mb-3">',
  'after' => '</div>',
));
Timber::render('project.twig', $context);
