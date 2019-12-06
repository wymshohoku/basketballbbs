
window.addEventListener("load", function () {
	loadArticle("../../controller/index/bbs.php", "POST", callbackArticle);
});

function loadArticle(url, method, callback) {
	let xhr = new XMLHttpRequest();
	xhr.open(method, url);
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
function addComment(comment_count, comment_paga_count, jsonComments) {
	var user_comment = document.querySelector(".user_comment");
	var comment_1 = document.querySelector(".user_info");
	for (var comment_index = 1, comment_page_index = 0; comment_index <= comment_count && comment_page_index < comment_paga_count; comment_index++ , comment_page_index++) {
		var commentNew = null;
		if (comment_index === 1) {
			commentNew = comment_1;
		} else {
			commentNew = comment_1.cloneNode(true);
			user_comment.appendChild(commentNew);
		}
		var id = jsonComments[comment_index - 1].commentid;
		commentNew.setAttribute("id", "comment-" + id);
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
		commentNew.style.display = "";
	}
}
function pageClick(page_index) {
	var user_comment = document.querySelector(".user_comment");
	if (user_comment.childNodes.length > 3) {// 第一个评论节点不删除
		do {
			user_comment.removeChild(user_comment.lastChild);
		} while (user_comment.childNodes.length > 3);
	}
	var comment_1 = document.querySelector(".user_info");// 评论模板节点
	comment_1.style.display = "none";

	loadArticle("../../controller/index/bbs.php?page_index=" + page_index, "GET", callbackArticle);
}
function callbackArticle(jsonArticle) {
	if (jsonArticle === null) {
		var comment = document.querySelector(".comment");
		comment.style.display = "none";
	}
	else {
		if (jsonArticle.art_title != null) {
			var art_title = document.querySelector(".art_title");
			art_title.id = jsonArticle.art_id;
			art_title.innerHTML = jsonArticle.art_title;
		}
		if (jsonArticle.art_text != null) {
			var art_text = document.querySelector(".art_text");
			art_text.querySelector("p").innerHTML = jsonArticle.art_text;
		}
		if (jsonArticle.comment_count === 0) {
			var comment = document.querySelector(".comment");
			comment.style.display = "none";
		} else {
			var comment_count = document.querySelector(".comment_count");
			comment_count.innerHTML = jsonArticle.comment_count;
			addComment(jsonArticle.comment_count, jsonArticle.comment_page_count, jsonArticle.comments);
		}
		if (jsonArticle.comment_count <= jsonArticle.comment_page_count) {
			var comment = document.querySelector("#comment-pages");
			comment.style.display = "none";
		}
		if (jsonArticle.comment_count > 0) {
			var comment = document.querySelector(".comment");
			comment.style.display = "inline";
		}
		if (jsonArticle.comment_count > jsonArticle.comment_page_count) {
			var comment = document.querySelector("#comment-pages");
			comment.style.display = "inline";

			var home = document.querySelector("#home");
			var prev = document.querySelector("#prev");
			var next = document.querySelector("#next");
			var last = document.querySelector("#last");
			var page_number = document.querySelector("#page_number");
			if (jsonArticle.comment_page_index == 0) {// 首页
				home.style.cursor = "";
				prev.style.cursor = "";
				next.style.cursor = "pointer";
				last.style.cursor = "pointer";

				home.onclick = null;
				prev.onclick = null;
				next.onclick = function () {
					pageClick("next");
				};
				last.onclick = function () {
					pageClick(jsonArticle.comment_page_count);
				};
			} else if (jsonArticle.comment_page_index == jsonArticle.comment_page_count - 1) {// 尾页
				home.style.cursor = "pointer";
				prev.style.cursor = "pointer";
				next.style.cursor = "";
				last.style.cursor = "";

				home.onclick = function () {
					pageClick(0);
				};
				prev.onclick = function () {
					pageClick("prev");
				};
				next.onclick = null;
				last.onclick = null;
			} else {// 其他页
				home.style.cursor = "pointer";
				home.onclick = function () {
					pageClick(0);
				};
				prev.onclick = function () {
					pageClick("prev");
				};
				next.onclick = function () {
					pageClick("next");
				};
				last.onclick = function () {
					pageClick(jsonArticle.comment_page_count);
				};
			}
		}
	}
}
