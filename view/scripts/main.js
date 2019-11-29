let myImage = document.querySelector('*[name="site"]');

myImage.onclick = function () {
    let mySrc = myImage.getAttribute('src');
    if (mySrc === 'view/images/site.jpg') {
        myImage.setAttribute('src', 'view/images/site1.jpg');
    } else {
        myImage.setAttribute('src', 'view/images/site.jpg');
    }
}
/*
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
