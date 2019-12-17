window.addEventListener("load", function () {
    //loadArticle("../../controller/admin/login.php", "POST");
    var form = document.querySelector("#login");
	form.onsubmit = function (e) {
		var passwd = form.querySelector("#password").value;
		form.querySelector("#password").value = md5(passwd);
    }
});

function loadArticle(url, method) {
    let xhr = new XMLHttpRequest();
    xhr.open(method, url);
    xhr.responseType = "json";

    xhr.onload = function () {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 304)) {
            json = xhr.response;
            if (json != null) {
                window.location.href = json.location;
            }
        }
    };

    xhr.send();
}