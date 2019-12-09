window.addEventListener("load", function () {
    onChangeArticle(1);
});

function onChangeArticle(index) {
    if (index == 1) {
        loadTable("../../controller/admin/admin.php", "POST", "user", callbackShowUsers);
    } else if (index == 2) {
        loadTable("../../controller/admin/admin.php", "POST", "article", callbackShowArticles);
    } else if (index == 3) {
        loadTable("../../controller/admin/admin.php", "POST", "comment", callbackShowComments);
    } else if (index == 4) {
        loadTable("../../controller/admin/admin.php", "POST", "my", callbackShowMy);
    }
}
function loadTable(url, method, func, callback) {
    let xhr = new XMLHttpRequest();
    xhr.open(method, url);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.responseType = "json";

    xhr.onload = function () {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 304))
            callback(xhr.response);
    };

    var data = "func_name=" + func;
    xhr.send(data);
}

function callbackShowUsers(jsonUsers) {
    if (jsonUsers != null) {
        var table = document.querySelector("#user").querySelector("table");
        deleteTableElement(table);
        for (var index = 0; index < jsonUsers.count; index++) {
            var rownum = table.rows.length;
            var newRow = table.insertRow(rownum);
            var cellindex = newRow.insertCell(0);
            cellindex.innerHTML = index + 1;
            var cellid = newRow.insertCell(1);
            cellid.innerHTML = jsonUsers[index].id;
            var cellname = newRow.insertCell(2);
            cellname.innerHTML = jsonUsers[index].name;
            var cellimg = newRow.insertCell(3);
            cellimg.innerHTML = jsonUsers[index].img;
            var cellfunc = newRow.insertCell(4);
            cellfunc.innerHTML = "<a href='#' onclick=\"deleteRecord('User#" + index + "');\">删除</a>";
        }
        showUsers();
    } else {
        window.location.href = "../../view/admin/login.html";
    }
}
function callbackShowArticles(jsonArticle) {
    if (jsonArticle != null) {
        var table = document.querySelector("#article").querySelector("table");
        deleteTableElement(table);
        for (var index = 0; index < jsonArticle.count; index++) {
            var rownum = table.rows.length;
            var newRow = table.insertRow(rownum);
            var cellindex = newRow.insertCell(0);
            cellindex.innerHTML = index + 1;
            var cellid = newRow.insertCell(1);
            cellid.innerHTML = jsonArticle.articles[index].id;
            var cellname = newRow.insertCell(2);
            cellname.innerHTML = jsonArticle.articles[index].title;
            var cellimg = newRow.insertCell(3);
            cellimg.innerHTML = jsonArticle.articles[index].text;
            var cellfunc = newRow.insertCell(4);
            cellfunc.innerHTML = "<a href='#' onclick=\"deleteRecord('Article#" + index + "');\">删除</a>";
        }
        showArticles();
    }
}
function callbackShowComments(jsonComments) {
    if (jsonComments != null) {
        var table = document.querySelector("#comment").querySelector("table");
        deleteTableElement(table);
        for (var index = 0; index < jsonComments.count; index++) {
            var rownum = table.rows.length;
            var newRow = table.insertRow(rownum);
            var cellindex = newRow.insertCell(0);
            cellindex.innerHTML = index + 1;
            var cellid = newRow.insertCell(1);
            cellid.innerHTML = jsonComments[index].id;
            var cellarticleid = newRow.insertCell(2);
            cellarticleid.innerHTML = jsonComments[index].articleid;
            var celluserid = newRow.insertCell(3);
            celluserid.innerHTML = jsonComments[index].userid;
            var cellcomment = newRow.insertCell(4);
            cellcomment.innerHTML = jsonComments[index].comment;
            var celldate = newRow.insertCell(5);
            celldate.innerHTML = jsonComments[index].date;
            var cellfunc = newRow.insertCell(6);
            var func = "<a href='#' onclick=\"deleteRecord('Comment#" + index + "');\">删除</a>";
            if (jsonComments[index].approval == 1) {
                func += "<a href='#' onclick=\"approvalRecord('ApprovalComment#" + index + "');\">审核</a>";
            }
            cellfunc.innerHTML = func;
        }
        showComments();
    }
}

function callbackShowMy(jsonUsers) {

}
function showUsers() {
    document.querySelector("#user_menu_item").style.backgroundColor = "#dddddd";
    document.querySelector("#user_menu_item").style.color = "#000000";
    document.querySelector("#article_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#article_menu_item").style.color = "floralwhite";
    document.querySelector("#comment_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#comment_menu_item").style.color = "floralwhite";
    document.querySelector("#my_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#my_menu_item").style.color = "floralwhite";

    document.querySelector("#user").style.display = "inline-block";
    document.querySelector("#article").style.display = "none";
    document.querySelector("#comment").style.display = "none";
    document.querySelector("#my").style.display = "none";
}

function showArticles() {
    document.querySelector("#user_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#user_menu_item").style.color = "floralwhite";
    document.querySelector("#article_menu_item").style.backgroundColor = "#dddddd";
    document.querySelector("#article_menu_item").style.color = "#000000";
    document.querySelector("#comment_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#comment_menu_item").style.color = "floralwhite";
    document.querySelector("#my_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#my_menu_item").style.color = "floralwhite";

    document.querySelector("#user").style.display = "none";
    document.querySelector("#article").style.display = "inline-block";
    document.querySelector("#comment").style.display = "none";
    document.querySelector("#my").style.display = "none";
}

function showComments() {
    document.querySelector("#user_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#user_menu_item").style.color = "floralwhite";
    document.querySelector("#article_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#article_menu_item").style.color = "floralwhite";
    document.querySelector("#comment_menu_item").style.backgroundColor = "#dddddd";
    document.querySelector("#comment_menu_item").style.color = "#000000";
    document.querySelector("#my_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#my_menu_item").style.color = "floralwhite";

    document.querySelector("#user").style.display = "none";
    document.querySelector("#article").style.display = "none";
    document.querySelector("#comment").style.display = "inline-block";
    document.querySelector("#my").style.display = "none";
}

function showMy() {
    document.querySelector("#user_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#user_menu_item").style.color = "floralwhite";
    document.querySelector("#article_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#article_menu_item").style.color = "floralwhite";
    document.querySelector("#comment_menu_item").style.backgroundColor = "#2b3643";
    document.querySelector("#comment_menu_item").style.color = "floralwhite";
    document.querySelector("#my_menu_item").style.backgroundColor = "#dddddd";
    document.querySelector("#my_menu_item").style.color = "#000000";

    document.querySelector("#user").style.display = "none";
    document.querySelector("#article").style.display = "none";
    document.querySelector("#comment").style.display = "none";
    document.querySelector("#my").style.display = "inline-block";
}

function deleteTableElement(table) {
    if (table !== "undefined") {
        for (var index = table.rows.length; 1 < index; index--) {
            table.deleteRow(index - 1);
        }
    }
}

function deleteRecord(recordIndex) {
    alert(recordIndex);
    recordFunc("../../controller/admin/admin.php", "POST", recordIndex, callbackDeleteRecord);
}

function approvalRecord(recordIndex) {
    alert(recordIndex);
    recordFunc("../../controller/admin/admin.php", "POST", recordIndex, callbackApprovalRecord);
}

function callbackDeleteRecord(record) {
    alert(record);
}

function callbackApprovalRecord(record) {
    alert(record);
}

function recordFunc(url, method, func, callback) {
    let xhr = new XMLHttpRequest();
    xhr.open(method, url);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.responseType = "json";

    xhr.onload = function () {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 304))
            callback(xhr.response);
    };

    var data = "record_func=" + func;
    xhr.send(data);
}
