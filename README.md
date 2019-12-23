article：
    GET     /---->列出所有文章的列表
    GET     /articles---->列出所有文章的列表
    rewrite /view/index/index.php?view=all

bbs：
    GET     /articles/[0-9]*---->点击了某篇文章列表
    rewrite /view/index/bbs.php?view=single&id=[0-9]*

    GET     /articles/[0-9]*/bbs/page/[0-9]*---->返回当前分页的评论
    rewrite /view/index/bbs.php?view=single&id=[0-9]*&page=[0-9]*

    GET     /articles/[0-9]*/bbs/token---->返回当前的token
    rewrite /view/index/bbs.php?view=single&id=[0-9]*&func=token

    GET     /articles/[0-9]*/bbs/code---->返回当前的验证码
    rewrite /view/index/bbs.php?view=single&id=[0-9]*&func=code

    POST    /articles/[0-9]*/bbs/loginin---->提交评论
        para：  userid---->用户ID
                password---->用户密码
                username---->用户名
                comment---->评论内容
                token---->评论token
                code---->评论验证码
    rewrite /view/index/bbs.php?view=single&id=[0-9]*&func=loginin

login：
    GET     /login---->展示登录界面
    rewrite /view/admin/admin.php?view=login

    GET     /login/token---->返回当前的token
    rewrite /view/admin/admin.php?view=login&func=token

    GET     /articles/[0-9]*/bbs/code---->返回当前的验证码
    rewrite /view/admin/admin.php?view=login&func=code

    POST    /login/submit---->提交评论
        para：  password---->用户密码
                username---->用户名
                token---->评论token
                code---->评论验证码
    rewrite /view/admin/admin.php?view=login&func=submit

admin：
    GET     /admin---->展示后台界面
    rewrite /view/admin/admin.php?view=admin

    GET     /admin/articles---->显示文章界面
    rewrite /view/admin/admin.php?view=articles

    GET     /admin/articles/[0-9]*/delete---->删除文章操作
    rewrite /view/admin/admin.php?view=articles&id=[0-9]*&fun=delete

    GET     /admin/users---->显示用户界面
    rewrite /view/admin/admin.php?view=users

    GET     /admin/users/[0-9]*/delete---->删除用户操作
    rewrite /view/admin/admin.php?view=users&id=[0-9]*&fun=delete

    GET     /admin/comments---->显示评论界面
    rewrite /view/admin/admin.php?view=comment

    GET     /admin/comments/[0-9]*/delete---->删除评论操作
    rewrite /view/admin/admin.php?view=users&id=[0-9]*&fun=delete

    GET     /admin/me---->显示我的信息界面
    rewrite /view/admin/admin.php?view=me

    GET     /admin/loginout---->显示我的信息界面
    rewrite /view/admin/admin.php?view=me&fun=loginout

css:
    GET     /styles/(.*)---->获取样式表
    rewrite /view/styles/(.*)

js:
    GET     /scripts/(.*)---->获取js
    rewrite /view/scripts/(.*)

image:
    GET     /images/(.*)---->获取图片
    rewrite /view/images/(.*)

正常返回参数格式json：
{
    "status": "",
    "msg": "",
    "data": {
        [],
        [],
    }
}
错误返回参数格式json：
{
    "error": "",
    "detail": {
        "": ""
    }
}