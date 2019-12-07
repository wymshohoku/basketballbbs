
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

// 添加评论
function addComment(comment_current_page_count, comment_pre_paga_count, comment_current_paga_index, jsonComments) {
	var user_comment = document.querySelector(".user_comment");
	var comment_1 = document.querySelector(".user_info");
	for (var comment_id = 1; comment_id <= comment_current_page_count; comment_id++) {
		var commentNew = null;
		if (comment_id === 1) {
			commentNew = comment_1;
		} else {
			commentNew = comment_1.cloneNode(true);
			user_comment.appendChild(commentNew);
		}
		var id = jsonComments[comment_id - 1].commentid;
		commentNew.setAttribute("id", "comment-" + id);
		// 设置头像
		commentNew.querySelector("#img-user").src =
			jsonComments[comment_id - 1].userimg;

		commentNew.querySelector("#username").innerHTML =
			jsonComments[comment_id - 1].username;

		var comment_floor = commentNew.querySelector("span");
		comment_floor.innerHTML = comment_current_paga_index * comment_pre_paga_count + comment_id + 1 + " 楼";

		var comment_time = commentNew.querySelector("time");
		comment_time.innerHTML = jsonComments[comment_id - 1].time;

		commentNew.querySelector("#msg").innerHTML =
			jsonComments[comment_id - 1].msg;
		commentNew.style.display = "";
	}
}

function callbackArticle(jsonArticle) {
	if (jsonArticle === null) {
		showOrHideComments(0);
	}
	else {
		setArticleTitleAndText(jsonArticle.art_id, jsonArticle.art_title, jsonArticle.art_text);

		if (showOrHideComments(jsonArticle.comment_count)) {
			var comment_count = document.querySelector(".comment_count");
			comment_count.innerHTML = jsonArticle.comment_count;
			addComment(jsonArticle.comment_current_page_count, jsonArticle.comment_pre_paga_count, jsonArticle.comment_page_index, jsonArticle.comments);
		}
		showOrHidePage(jsonArticle.comment_count, jsonArticle.comment_pre_paga_count);

		setPageCount(jsonArticle);
	}
}
function setPageCount(jsonArticle) {
	if (showOrHidePage(jsonArticle.comment_count, jsonArticle.comment_pre_paga_count)) {
		var home = document.querySelector("#home");
		var prev = document.querySelector("#prev");
		var next = document.querySelector("#next");
		var last = document.querySelector("#last");
		var page_number = document.querySelector("#page_number");
		if (jsonArticle.comment_page_index == 1) {// 首页
			home.style.cursor = "default";
			prev.style.cursor = "default";
			next.style.cursor = "pointer";
			last.style.cursor = "pointer";

			home.onclick = null;
			prev.onclick = null;
			next.onclick = function () {
				pageClick("next");
			};
			last.onclick = function () {
				pageClick(jsonArticle.comment_pages);
			};
		} else if (jsonArticle.comment_page_index == jsonArticle.comment_pages) {// 尾页
			home.style.cursor = "pointer";
			prev.style.cursor = "pointer";
			next.style.cursor = "default";
			last.style.cursor = "default";

			home.onclick = function () {
				pageClick(1);
			};
			prev.onclick = function () {
				pageClick("prev");
			};
			next.onclick = null;
			last.onclick = null;
		} else {// 其他页
			home.style.cursor = "pointer";
			prev.style.cursor = "pointer";
			next.style.cursor = "pointer";
			last.style.cursor = "pointer";
			home.onclick = function () {
				pageClick(1);
			};
			prev.onclick = function () {
				pageClick("prev");
			};
			next.onclick = function () {
				pageClick("next");
			};
			last.onclick = function () {
				pageClick(jsonArticle.comment_pages);
			};
		}
	}
}
function showOrHidePage(comment_count, comment_pre_paga_count) {
	if (comment_count <= comment_pre_paga_count) {
		var comment = document.querySelector("#comment-pages");
		comment.style.display = "none";
		return false;
	} else {
		var comment = document.querySelector("#comment-pages");
		comment.style.display = "";
	}

	return true;
}
function setArticleTitleAndText(art_id, art_title, art_text) {
	if (art_title != null) {
		var title = document.querySelector(".art_title");
		title.id = art_id;
		title.innerHTML = art_title;
	}
	if (art_text != null) {
		var text = document.querySelector(".art_text");
		text.querySelector("p").innerHTML = art_text;
	}
}

function showOrHideComments(comment_count) {
	if (comment_count === 0) {
		var comment = document.querySelector(".comment");
		comment.style.display = "none";
		return false;
	} else if (comment_count > 0) {
		var comment = document.querySelector(".comment");
		comment.style.display = "";
	}
	return true;
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

window.onload = function () {
	//loadArticle("../../controller/index/bbs.php", "POST", callbackArticle);
} 
