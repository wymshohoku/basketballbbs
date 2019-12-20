<?php

session_start();

require_once '../../model/index/bbs.php';

use model\comment\comment;
use model\index\bbs as CBbs;

if (CBbs::isAuthCode()) {
    CBbs::getAuthCodeImg();
    exit();
} else if (CBbs::isStoreArticleId() && isset($_GET['token'])) {
    if (CBbs::checkToken($_GET['art_id'], $_GET['token'])) {
    }
    exit();
} else if (CBbs::isUserSubmitComment()) {
    $json = null;
    if (CBbs::checkToken($_POST['art_id'], $_POST['token'])) {
        $bbs = new CBbs();
        // 验证提交评论的验证码
        if ($bbs->checkAuthCode($_POST['authcode'])) {
            // 提交评论
            $bbs->submitComment(
                $_POST['art_id'],
                $_POST['username'],
                $_POST['msg'],
                $_POST['time'],
                $_POST['id'],
                $_POST['pwd'],
                $_POST['token']
            );
        }
    }
    header('Content-Type:application/json; charset=utf-8');
    $json = json_encode($bbs->serialize());
    exit($json);
} else if (CBbs::isUserClickArticle()) { // 用户点击文章链接
    $bbs = new CBbs();
    if (CBbs::isUserClickPage()) { // 用户点击翻页
        $bbs->getArticle($_GET['page_index']);
    } else {
        CBbs::setArticleId($_GET['art_id']);
        // 用户点击了文章链接跳转，获取该文章和所有评论
        $bbs->getArticle(1);
    }

    $json = $bbs->serialize();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8" />
    <link href="../styles/normalize.css" rel="stylesheet" type="text/css" />
    <link href="../styles/bbs.css" rel="stylesheet" type="text/css" />
    <link href="https://fonts.font.im/css?family=Open+Sans" rel="stylesheet" type="text/css" />
    <title>篮球世界</title>
</head>

<body>
    <script src="../scripts/bbs.js"></script>
    <header>
        <!-- 本站所有网页的统一主标题 -->
        <img src="../images/beachball.png" />
        <h1 class="siteWorld">欢迎来到<strong>篮球世界</strong></h1>
        <img src="../images/beachball.png" />
    </header>

    <nav>
        <!-- 本站所有网页的统一主标题 -->
        <ul class="menu-list">
            <li><a href="../">主页</a></li>
            <!-- <li><a href="#">留言板</a></li> -->
            <!-- 共n个导航栏项目，省略…… -->
        </ul>
    </nav>

    <main>
        <section>
            <div>
                <h1 class="art_title" id="<?php echo $json['art_id']; ?>"><?php echo $json['art_title']; ?></h1>
                <article class="art_text">
                    <p><?php echo $json['art_text']; ?></p>
                </article>
            </div>
        </section>
        <section class="comment" style="display: '<?php echo $json['comment_count'] > 0 ? '' : 'none;'; ?>' ">
            <h3 class="comment_count_info">
                <div class="comment_title">
                    <span>全部评论</span>
                    <span class="comment_count"><?php echo $json['comment_count']; ?></span>
                </div>
            </h3>
            <div class="user_comment">
                <?php
                $page_pre_cnt = $json['comment_pre_paga_count'];
                $page_index = $json['comment_page_index'];
                $i = $page_pre_cnt * ($page_index - 1) + 1;
                foreach ($json['comments'] as $comment) {
                    $i++;
                ?>
                    <div class="user_info" id="comment-<?php echo $comment['commentid']; ?>">
                        <img class="img_user" id="img-user" src="<?php echo $comment['userimg']; ?>" alt="" />
                        <div class="user">
                            <div class="name" id="username">
                                <?php echo $comment['username']; ?>
                            </div>
                            <div class="comment_info">
                                <span><?php echo $i; ?>楼 </span>
                                <time><?php echo $comment['time']; ?></time>
                            </div>
                            <div class="comment_text" id="msg"><?php echo $comment['msg']; ?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div id="comment-pages">
                <div class="first" id="home">首页</div>
                <div class="first" id="prev">上一页</div>
                <div id="page_number">
                    <h2><?php echo $json['comment_page_index']; ?></ h2>
                </div>
                <div class="first" id="next">下一页</div>
                <div class="first" id="last">尾页</div>
                <div class="current"></div>
            </div>
        </section>
        <section id="bbsForm">
            <!-- 此处包含一个 article（一篇文章），内容略…… -->
            <form id="commentForm">
                <div>
                    <input type="hidden" id="userid" value="" />
                    <input type="hidden" id="token" value="<?php echo $json['token'] ?>" />
                    <label for="name">昵称：</label>
                    <input type="text" id="username" name="username" required />
                </div>
                <div id="wd" style="display: none;">
                    <label for="passwd">密码：</label>
                    <input type="hidden" id="password" name="password" required />
                </div>
                <div>
                    <label for="msg">留言内容：</label>
                    <textarea id="msg" name="msg" onfocus="document.getElementById('captcha_img').src='<?php echo $json['art_id'] ?>/r='+Math.floor((Math.random()*10000)+1); " required></textarea>
                </div>
                <div>
                    <label for="code">验证码图片：</label>
                    <img id="captcha_img" border="1" src="<?php echo $json['art_id'] ?>/r=<?php echo rand(); ?>" alt="" width="100" height="30" />
                    <a href="javascript:void(0)" onclick="document.getElementById('captcha_img').src='<?php echo $json['art_id'] ?>/r='+Math.floor((Math.random()*10000)+1); ">换一个?
                    </a>
                </div>
                <div>
                    <label for="inputcode">请输入图片中的内容：</label>
                    <input type="text" id="authcode" name="authcode" value="" required />
                </div>
                <div>
                    <input id="submit" type="submit" />
                    <!-- <button id="submit" onclick="sendData()">提交</button> -->
                </div>
            </form>
        </section>
    </main>

    <footer>
        <!-- 本站所有网页的统一页脚 -->
        <p>© 2050 某某保留所有权利</p>
    </footer>
</body>

</html>