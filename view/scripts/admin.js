
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

window.addEventListener("load", function () {
    onChangeArticle(1);
});

function onChangeArticle(index) {
    if (index == 1) {
        loadTable("", "POST", "user", callbackShowUsers);
    } else if (index == 2) {
        loadTable("", "POST", "article", callbackShowArticles);
    } else if (index == 3) {
        loadTable("", "POST", "comment", callbackShowComments);
    } else if (index == 4) {
        loadTable("", "POST", "my", callbackShowMy);
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

    var data = "view=" + func + "&func=select_all";
    xhr.send(data);
}

function callbackShowUsers(jsonUsers) {
    if (jsonUsers != null) {
        var table = document.querySelector("#user").querySelector("table");
        deleteTableElement(table);
        for (var index = 0; index < jsonUsers.count; index++) {
            var rownum = table.rows.length;
            var newRow = table.insertRow(rownum);
            newRow.setAttribute("id", "id_" + Number(index + 1));
            var cellindex = newRow.insertCell(0);
            cellindex.innerHTML = index + 1;
            var cellid = newRow.insertCell(1);
            cellid.innerHTML = jsonUsers[index].id;
            var cellname = newRow.insertCell(2);
            cellname.innerHTML = jsonUsers[index].name;
            var cellimg = newRow.insertCell(3);
            cellimg.innerHTML = jsonUsers[index].img;
            var cellfunc = newRow.insertCell(4);
            cellfunc.innerHTML = "<a href='#' onclick=\"deleteRecord('user#" + index + "&id=" + jsonUsers[index].id + "');\">删除</a>";
        }
        showUsers();
    }
}
function callbackShowArticles(jsonArticle) {
    if (jsonArticle != null) {
        var table = document.querySelector("#article").querySelector("table");
        deleteTableElement(table);
        for (var index = 0; index < jsonArticle.count; index++) {
            var rownum = table.rows.length;
            var newRow = table.insertRow(rownum);
            newRow.setAttribute("id", "id_" + Number(index + 1));
            var cellindex = newRow.insertCell(0);
            cellindex.innerHTML = index + 1;
            var cellid = newRow.insertCell(1);
            cellid.innerHTML = jsonArticle.articles[index].id;
            var cellname = newRow.insertCell(2);
            cellname.innerHTML = jsonArticle.articles[index].title;
            var cellimg = newRow.insertCell(3);
            cellimg.innerHTML = jsonArticle.articles[index].text;
            var cellfunc = newRow.insertCell(4);
            cellfunc.innerHTML = "<a href='#' onclick=\"deleteRecord('article#" + index + "&id=" + jsonArticle.articles[index].id + "');\">删除</a>";
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
            newRow.setAttribute("id", "id_" + Number(index + 1));
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
            var func = "<a href='#' onclick=\"deleteRecord('comment#" + index + "&id=" + jsonComments[index].id + "');\">删除</a>";
            if (jsonComments[index].approval == 1) {
                func += "&nbsp<a href='#' onclick=\"approvalRecord('comment#" + index + "&id=" + jsonComments[index].id + "');\">审核</a>";
            }
            cellfunc.innerHTML = func;
        }
        showComments();
    }
}

function callbackShowMy(jsonUsers) {

}
function deleteTableElement(table) {
    if (table !== "undefined") {
        for (var index = table.rows.length; 1 < index; index--) {
            table.deleteRow(index - 1);
        }
    }
}

function deleteRecord(recordIndex) {
    recordIndex = recordIndex.replace("#", "&index=");
    recordIndex += "&func=delete";
    recordFunc("", "POST", recordIndex, callbackDeleteRecord);
}

function approvalRecord(recordIndex) {
    recordIndex = recordIndex.replace("#", "&index=");
    recordIndex += "&func=approval";
    recordFunc("", "POST", recordIndex, callbackApprovalRecord);
}

function callbackDeleteRecord(jsonResult) {
    if (jsonResult !== null && jsonResult.result) {
        var table = document.querySelector("#" + jsonResult.name).querySelector("table");
        var i = Number(jsonResult.index) + 1;
        var row = table.querySelector("#id_" + i);
        table.deleteRow(row.rowIndex);
    }
}

function callbackApprovalRecord(jsonResult) {
    if (jsonResult !== null && jsonResult.result) {
        var table = document.querySelector("#" + jsonResult.name).querySelector("table");
        var i = Number(jsonResult.index) + 1;
        var row = table.querySelector("#id_" + i);
        var a = row.querySelectorAll("a")[1];
        a.innerHTML = "已审核";
        a.onclick = "";
        //table.deleteRow(row.rowIndex);
    }
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

    var data = "view=" + func;
    xhr.send(data);
}

function onLoginOut() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "");
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.responseType = "json";
    var data = "view=logout&Logout=true";

    xhr.onload = function () {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 304)) {
            json = xhr.response;
            if (json != null) {
                window.location.href = json.location;
            }
        }
    };
    xhr.send(data);
}