
// 首先创建一个用来发送数据的iframe.
/* var iframe = document.createElement("iframe");
iframe.name = "myTarget";

// 然后，将iframe附加到主文档
window.addEventListener("load", function () {
  iframe.style.display = "none";
  document.body.appendChild(iframe);
}); */
function loadComment(url, callback) {
  let xhr = new XMLHttpRequest();
  xhr.open('POST', url);
  xhr.responseType = "json";

  xhr.onload = function () {
    if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 304))
      callback(xhr.response);
  };

  xhr.send();
}

//

var comment = document.querySelector(".comment");
var comment_count = document.getElementsByClassName("comment_count");
//comment.style.display="none";
//comment_count.style.text = "3";

// 添加评论
function addComment(comment_count, jsonComments) {
  var user_comment = document.querySelector(".user_comment");
  var comment_1 = document.querySelector("#comment-1");
  for (var comment_index = 1; comment_index <= comment_count; comment_index++) {
    var commentNew = null;
    if (comment_index === 1) {
      commentNew = comment_1;
    } else {
      commentNew = comment_1.cloneNode(true);
      var id = jsonComments[comment_index - 1].commentid;
      commentNew.setAttribute("id", "comment-" + id);
      user_comment.appendChild(commentNew);
    }
    // 设置头像
    commentNew.querySelector("#img-user").src = 
      jsonComments[comment_index - 1].userimg;
      
    commentNew.querySelector("#username").innerHTML =  
      jsonComments[comment_index - 1].username;
      
    var t = commentNew.querySelector("time");
    t.datatime = 
      jsonComments[comment_index - 1].time;
    
    commentNew.querySelector("#msg").innerHTML = 
      jsonComments[comment_index - 1].msg;
  }
}

function callbackComment(jsonComment) {
  addComment(jsonComment.comment_count, jsonComment.comments);
}

loadComment('../../controller/index/bbs.php', callbackComment);
