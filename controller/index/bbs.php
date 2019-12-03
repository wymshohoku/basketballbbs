<?php

$comment_json = array(
    'title_id' => 1,
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
            'time' => '2019-08-03T15:49:03.000Z',
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

header('Content-Type:application/json; charset=utf-8');
$json = json_encode($comment_json);
exit($json);
