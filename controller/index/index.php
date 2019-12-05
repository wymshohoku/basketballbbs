<?php

$articles_json = array(
    'art_count' => 2,
    'art_pages' => 4,
    'articles' => array(
        array(
            'art_id' => 1,
            'art_title' => '一句“打篮球能当饭吃吗”伤害了多少孩子的心！',
        )
        , array(
            'art_id' => 2,
            'art_title' => '小篮球（花式，实战比赛）特色学校，特色课程设计方案',
        ),
    ),
);

header('Content-Type:application/json; charset=utf-8');
$json = json_encode($articles_json);
exit($json);
