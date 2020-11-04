<?php

$context = Timber::context();
$context['page'] = new Timber\Post();
Timber::render('page.twig', $context);
