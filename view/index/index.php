<?php

require_once '../../model/article/article.php';

use model\article\article;

/**
 * 返回文章列表
 */
$art = new article();
$art->getTable();
$articles_json = $art->serialize();

?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="utf-8">
    <!-- <script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU="
    crossorigin="anonymous"></script> -->
    <!-- <script src="scripts/main.js"></script> -->
    <link href="styles/normalize.css" rel="stylesheet" type="text/css">
    <link href="styles/style.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.font.im/css?family=Open+Sans" rel="stylesheet" type="text/css">
    <title>篮球世界</title>
</head>

<body>
    <header>
        <!-- 本站所有网页的统一主标题 -->
        <img src="images/beachball.png">
        <h1>欢迎来到<strong>篮球世界</strong></h1>
        <img src="images/beachball.png">
    </header>

    <nav>
        <!-- 本站所有网页的统一主标题 -->
        <ul>
            <li><a href="#">主页</a></li>
            <!-- <li><a href="view/index/bbs.html">留言板</a></li> -->
            <!-- 共n个导航栏项目，省略…… -->
        </ul>
        <form>
            <!-- 搜索栏是站点内导航的一个非线性的方式。 -->
            <input type="search" name="q" placeholder="要搜索的内容">
            <input type="submit" value="搜索">
        </form>
    </nav>

    <main>
        <!-- 网页主体内容 -->
        <article>
            <img name="site" src="images/site.jpg" alt="My test image">
            <p>篮球世界是一个全球社区，这里聚集着来自五湖四海的</p>
            <ul id="art_list">
                <?php foreach ($articles_json['articles'] as $article) { ?>
                    <li><a href="<?php echo 'articles/' . $article['id']; ?>/"><?php echo $article['title']; ?></a></li>
                <?php } ?>
            </ul>
            <p>我们致力于……</p>
            <button>切换用户</button>

            <!-- 此处包含一个 article（一篇文章），内容略…… -->

        </article>

        <aside>
            <!-- 侧边栏在主内容右侧 -->
            <h2><a href="login/">登录后台</a></h2>
            <ul>
                <!-- 侧边栏有n个超链接，略略略…… -->
            </ul>
        </aside>
    </main>

    <footer>
        <!-- 本站所有网页的统一页脚 -->
        <p>© 2050 某某保留所有权利</p>
    </footer>

</body>

</html>