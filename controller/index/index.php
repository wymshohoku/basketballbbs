<?php

require_once '../../model/article/article.php';

use model\article\article;

$art = new article();
$art->getTable();
$articles_json = $art->serialize();

header('Content-Type:application/json; charset=utf-8');
$json = json_encode($articles_json);
exit($json);
