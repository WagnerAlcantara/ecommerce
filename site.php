<?php

use \Hcode\Page;

/** Rota  default */
$app->get('/', function () {
  $page = new Page();
  $page->setTpl("index");
});
