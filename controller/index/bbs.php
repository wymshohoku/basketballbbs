<?php

$comment_json = array(
    'art_id' => 'art-1',
    'comment_pages' => 4,
    'comment_count' => 5,
    'comments' => array(
        array(
            'commentid' => 1,
            'userimg' => '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp',
            'username' => '篮筐里的球1',
            'time' => '2019.08.09 23:49',
            'msg' => '波士顿的小托马斯也很励志',
        )
        , array(
            'commentid' => 2,
            'userimg' => 'webp',
            'username' => '篮筐里的球2',
            'time' => '2019-08-03 15:49:03',
            'msg' => '波士顿的小托马斯也很励志',
        )
        , array(
            'commentid' => 3,
            'userimg' => '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp',
            'username' => '篮筐里的球3',
            'time' => '2019.08.09 23:49',
            'msg' => '波士顿的小托马斯也很励志',
        )
        , array(
            'commentid' => 4,
            'userimg' => '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp',
            'username' => '篮筐里的球4',
            'time' => '2019.08.09 23:49',
            'msg' => '波士顿的小托马斯也很励志',
        )
        , array(
            'commentid' => 5,
            'userimg' => '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp',
            'username' => '篮筐里的球5',
            'time' => '2019.08.09 23:49',
            'msg' => '波士顿的小托马斯也很励志',
        ),
    ),
);

function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST) && isset($_POST['art_id'])) {
    if (test_input($_POST['art_id']) === $comment_json['art_id']) {
        $comment_json['comment_count'] += 1;
        $comment_json['comments'][] = array(
            'commentid' => $comment_json['comment_count'],
            'userimg' => '//upload.jianshu.io/users/upload_avatars/2571348/ca2d531d-05db-4172-817d-582436cf6ea1.jpg?imageMogr2/auto-orient/strip|imageView2/1/w/80/h/80/format/webp',
            'username' => test_input($_POST['username']),
            'time' => test_input($_POST['time']),
            'msg' => test_input($_POST['msg']),
        );
    }
}

header('Content-Type:application/json; charset=utf-8');
$json = json_encode($comment_json);
exit($json);
