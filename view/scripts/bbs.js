
window.addEventListener("load", function () {
	loadArticle("../../controller/index/bbs.php", callbackArticle);
});

function loadArticle(url, callback) {
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url);
	xhr.responseType = "json";

	xhr.onload = function () {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 304))
			callback(xhr.response);
	};

	xhr.send();
}

//

var comment = document.querySelector(".comment");
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

		var comment_floor = commentNew.querySelector("span");
		comment_floor.innerHTML = comment_index + 1 + " 楼";

		var comment_time = commentNew.querySelector("time");
		comment_time.innerHTML = jsonComments[comment_index - 1].time;

		commentNew.querySelector("#msg").innerHTML =
			jsonComments[comment_index - 1].msg;
	}
}

function callbackArticle(jsonArticle) {
	if (jsonArticle === null) {
		var comment = document.querySelector(".comment");
		comment.setAttribute("display", "none");
	}
	else {
		var art_title = document.querySelector(".art_title");
		art_title.id = jsonArticle.art_id;
		art_title.innerHTML = jsonArticle.art_title;
		var art_text = document.querySelector(".art_text");
		art_text.querySelector("p").innerHTML = jsonArticle.art_text;
		if (jsonArticle.comment_count === 0) {
			var comment = document.querySelector(".comment");
			comment.style.display = "none";
		} else {
			var comment_count = document.querySelector(".comment_count");
			comment_count.innerHTML = jsonArticle.comment_count;
			addComment(jsonArticle.comment_count, jsonArticle.comments);
		}
		if(jsonArticle.comment_count <= jsonArticle.comment_page_count){
			var comment = document.querySelector("#comment-pages");
			comment.style.display = "none";
		}
	}
}
