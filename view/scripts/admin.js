window.addEventListener("load", function () {
	onChangeArticle(1);
});

function onChangeArticle(index){
    if(index == 1){
        loadTable("../../controller/admin/admin.php", "POST", "user", callbackShowUsers);
    }else if(index == 2){
        loadTable("../../controller/admin/admin.php", "POST", "article", callbackShowUsers);
    }else if(index == 3){
        loadTable("../../controller/admin/admin.php", "POST", "comment", callbackShowUsers);
    }else if(index == 4){
        loadTable("../../controller/admin/admin.php", "POST", "my", callbackShowUsers);
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

function callbackShowUsers(jsonUsers){
    if(jsonUsers != null){
        var table = document.querySelector("#user").querySelector("table");
        for(var index = 0; index < jsonUsers.count; index++){
            var rownum = table.rows.length;
            var newRow = table.insertRow(rownum);
            var cellindex = newRow.insertCell(0);
            cellindex.innerHTML = index + 1;
            var cellname = newRow.insertCell(1);
            cellname.innerHTML = jsonUsers[index].name;
            var cellimg = newRow.insertCell(2);
            cellimg.innerHTML = jsonUsers[index].img;
            var cellfunc = newRow.insertCell(3);
            cellfunc.innerHTML = "<a href='#'" + index + 1 + ">删除</a>";
        }
        showUsers();
    }
}
function showUsers(){
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

function callbackShowArticle(jsonUsers){

}
function showArticle(){
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

function callbackShowComment(jsonUsers){

}
function showComment(){
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

function callbackShowMy(jsonUsers){

}
function showMy(){
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