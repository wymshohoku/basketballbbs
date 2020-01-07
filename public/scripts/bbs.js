
window.addEventListener("load", function () {
	pageClick(1);
	// 提交表单数据，并验证数据的有效性，过滤字符串
	var form = document.querySelector("#commentForm");
	form.onsubmit = function (e) {
		var art_id = document.querySelector(".art_title").id;
		var user_id = form.querySelector("#userid").value;
		var name = form.querySelector("#username").value;
		var passwd = form.querySelector("#password").value;
		var msg = form.querySelector("#msg").value;
		var code = form.querySelector("#authcode").value;
		var token = form.querySelector("#token").value;

		if (name === "") {
			alert("请输入昵称！");
		} else if (msg === "") {
			alert("请输入留言内容！");
		} else if (code === "") {
			alert("请输入验证码！");
		} else if (token === "" && passwd === "") {
			alert("未登陆或已过期，请输入登陆密码！");
			form.querySelector("#wd").style.display = "inline";
			form.querySelector("#password").type = "password";
		} else {
			var data = "&art_id=" + art_id +
				"&id=" + user_id +
				"&username=" + name +
				"&pwd=" + passwd +
				"&msg=" + msg +
				"&authcode=" + code +
				"&token=" + token +
				"&view=submit";

			let xhr = new XMLHttpRequest();
			xhr.open("POST", "#");
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.responseType = "json";

			xhr.onload = function () {
				if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 304)) {
					//callbackComment(xhr.response);
					jsonComment = xhr.response;
					if (jsonComment != null) {
						var comment_count = document.querySelector(".comment_count");
						comment_count.innerHTML = jsonComment.comment_count;

						if (jsonComment.comment_count > 0) {
							var comment = document.querySelector(".comment");
							comment.style.display = "inline";
						}
						if (jsonComment.comment_count > jsonComment.comment_page_count) {
							var comment = document.querySelector("#comment-pages");
							comment.style.display = "inline";
						} else {
							if (jsonComment.haserror == true) {
								alert(jsonComment.error[0]);
								form.querySelector("#wd").style.display = "inline";
								form.querySelector("#password").type = "password";
							}
							else {
								//var userimg = jsonComment.comments[0].userimg;
								//appendComment(jsonComment.comment_count, userimg, name, comment_time, msg);
								pageClick(1);
								alert("评论已提交！");
								form.querySelector("#msg").value = "";
								form.querySelector("#wd").style.display = "none";
								form.querySelector("#password").type = "hidden";
							}
							form.querySelector("#authcode").value = "";
							form.querySelector("#password").value = "";
							form.querySelector("#userid").value = jsonComment.id;
							form.querySelector("#token").value = jsonComment.token;
							document.getElementById('captcha_img').src = 'r/' + Math.floor((Math.random() * 10000) + 1);
						}
					}
				}
			};

			xhr.send(data);
		}

		e.preventDefault();
	}
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
		comment_floor.innerHTML = (comment_current_paga_index - 1) * comment_pre_paga_count + comment_id + 1 + " 楼";

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
		//setArticleTitleAndText(jsonArticle.art_id, jsonArticle.art_title, jsonArticle.art_text);

		if (showOrHideComments(jsonArticle.comment_count)) {
			var comment_count = document.querySelector(".comment_count");
			comment_count.innerHTML = jsonArticle.comment_count;
			addComment(jsonArticle.comment_current_page_count, jsonArticle.comment_pre_paga_count, jsonArticle.comment_page_index, jsonArticle.comments);
		}

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
	var art_id = document.querySelector(".art_title").id;
	var user_comment = document.querySelector(".user_comment");
	if (user_comment.childNodes.length > 3) {// 第一个评论节点不删除
		do {
			user_comment.removeChild(user_comment.lastChild);
		} while (user_comment.childNodes.length > 3);
	}
	//var comment_1 = document.querySelector(".user_info");// 评论模板节点
	//comment_1.style.display = "none";

	loadArticle("page/" + page_index + "/", "GET", callbackArticle);
}

Date.prototype.Format = function (fmt) {
	var o = {
		"M+": this.getMonth() + 1, // 月份
		"d+": this.getDate(), // 日
		"h+": this.getHours(), // 小时
		"m+": this.getMinutes(), // 分
		"s+": this.getSeconds(), // 秒
		"q+": Math.floor((this.getMonth() + 3) / 3), // 季度
		"S": this.getMilliseconds() // 毫秒
	};
	if (/(y+)/.test(fmt))
		fmt = fmt.replace(RegExp.$1, (this.getFullYear() + ""));
	for (var k in o)
		if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
	return fmt;
}

function appendComment(comment_count, userimg, username, time, msg) {
	var user_comment = document.querySelector(".user_comment");
	var comment_1 = document.querySelector("#comment-1");
	var commentNew = comment_1;
	if (comment_count > 1) {
		commentNew = comment_1.cloneNode(true);
	}
	commentNew.setAttribute("id", "comment-" + comment_count);
	// 设置头像
	commentNew.querySelector("#img-user").src = userimg;

	commentNew.querySelector("#username").innerHTML = username;

	var comment_floor = commentNew.querySelector("span");
	comment_floor.innerHTML = comment_count + " 楼";

	var comment_time = commentNew.querySelector("time");
	comment_time.innerHTML = new Date(time).Format('yy-MM-dd hh:mm:ss'); //"2018-11-15 17:40:00"

	commentNew.querySelector("#msg").innerHTML = msg;

	if (comment_count > 1) {
		user_comment.appendChild(commentNew);
	}
}
