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
	comment_floor.innerHTML = comment_count + 1 + " 楼";

	var comment_time = commentNew.querySelector("time");
	comment_time.innerHTML = new Date(time).Format('yy-MM-dd hh:mm:ss'); //"2018-11-15 17:40:00"

	commentNew.querySelector("#msg").innerHTML = msg;

	if (comment_count > 1) {
		user_comment.appendChild(commentNew);
	}
}

// 提交表单数据，并验证数据的有效性，过滤字符串
var form = document.querySelector("#commentForm");
form.onsubmit = function (e) {
	var comment_time = new Date().Format("yyyy-MM-dd hh:mm:ss");
	var art_id = document.querySelector(".art_title").id;
	var name = form.querySelector("#username").value;
	var msg = form.querySelector("#msg").value;

	if (name === "") {
		alert("请输入昵称！");
	} else if (msg === "") {
		alert("请输入留言内容！")
	}

	var data = "art_id=" + art_id + "&username=" + name + "&msg=" + msg + "&time=" + comment_time;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", "../../controller/index/bbs.php");
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.responseType = "json";

	xhr.onload = function () {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 304))
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
				var userimg = jsonComment.comments[0].userimg;
				appendComment(jsonComment.comment_count, userimg, name, comment_time, msg);
			}
		}
	};

	xhr.send(data);

	e.preventDefault();
}