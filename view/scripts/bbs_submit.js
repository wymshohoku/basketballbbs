// 评论内容提交
var comment_pages = document.querySelector("#comment-pages");
// 提交表单数据，并验证数据的有效性，过滤字符串
var form = document.querySelector("#commentForm");
form.onsubmit = function (e) {
  var name = form.querySelector("#username");
  var msg = form.querySelector("#msg");

  if (name.value === "") {
    e.preventDefault();
    alert("请输入昵称！");
  } else if (msg.value === "") {
    e.preventDefault();
    alert("请输入留言内容！")
  }
}