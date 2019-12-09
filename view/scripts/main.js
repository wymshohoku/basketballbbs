/*let myImage = document.querySelector('*[name="site"]');

myImage.onclick = function () {
    let mySrc = myImage.getAttribute('src');
    if (mySrc === 'view/images/site.jpg') {
        myImage.setAttribute('src', 'view/images/site1.jpg');
    } else {
        myImage.setAttribute('src', 'view/images/site.jpg');
    }
}

let myButton = document.querySelector('button');
let myHeading = document.querySelector('h1');
function setUserName() {
    let myName = prompt('请输入你的名字。');
    if (!myName || myName === null) {
        setUserName();
    } else {
        localStorage.setItem('name', myName);
        myHeading.textContent = '篮球世界 酷毙了，' + myName;
    }
}

if (!localStorage.getItem('name')) {
    setUserName();
} else {
    let storedName = localStorage.getItem('name');
    myHeading.textContent = '篮球世界 酷毙了，' + storedName;
}

myButton.onclick = function () {
    setUserName();
}
 */

window.addEventListener("load", function () {
    loadArticle("../../controller/index/index.php", callbackArticle);
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

// 添加评论
function addArticle(articles_count, jsonArticles) {
    var art_list = document.querySelector("#art_list");
    for (var article_index = 1; article_index <= articles_count; article_index++) {
        var li_article = document.createElement("li");
        var a_article = document.createElement("a");
        li_article.appendChild(a_article);

        // 设置链接
        a_article.href = "../../controller/index/bbs.php?art_id=" + article_index;

        a_article.innerHTML =
        jsonArticles[article_index - 1].title;

        art_list.appendChild(li_article);
    }
}

function callbackArticle(jsonArticles) {
    if(jsonArticles !== null){
        addArticle(jsonArticles.count, jsonArticles.articles);
    }
}
