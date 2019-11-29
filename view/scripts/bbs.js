
// 首先创建一个用来发送数据的iframe.
var iframe = document.createElement("iframe");
iframe.name = "myTarget";

// 然后，将iframe附加到主文档
window.addEventListener("load", function () {
  iframe.style.display = "none";
  document.body.appendChild(iframe);
});

// 下面这个函数是真正用来发送数据的.
// 它只有一个参数,一个由键值对填充的对象.
function sendData(data) {
  var name,
      form = document.createElement("form"),
      node = document.createElement("input");

  // 定义响应时发生的事件
  iframe.addEventListener("load", function () {
    alert("Yeah! Data sent.");
  });
    
  form.action = "../../controller/index/bbs.php";
  form.target = iframe.name;

  for(name in data) {
    node.name  = name;
    node.value = data[name].toString();
    form.appendChild(node.cloneNode());
  }

  // 表单元素需要附加到主文档中，才可以被发送。
  form.style.display = "none";
  document.body.appendChild(form);

  form.submit();

  // 表单提交后，就可以删除这个表单，不影响下次的数据发送。
  document.body.removeChild(form);
}