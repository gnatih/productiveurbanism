<?php

$context = Timber::context();
$args = array(
  'post_type' => 'project',
  'tax_query' => array(
    array(
      'taxonomy' => 'category',
      'field' => 'slug',
      'terms' => array('featured')
    )
  ),
  'meta_query' => array(
    array(
      'key' => '_thumbnail_id'
    )
  )
);

$context['projects'] = Timber::get_posts($args);

Timber::render('front-page.twig', $context);
