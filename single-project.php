<?php

$context         = Timber::context();
$timber_post     = Timber::get_post();
$context['project'] = $timber_post;

Timber::render('single-project.twig', $context);
