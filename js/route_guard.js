const token = localStorage.getItem("token");

if (!!token) {
  var xhr = new XMLHttpRequest();

  xhr.open("POST", "./APIs/auth/check_token.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");

  xhr.onreadystatechange = function () {
    if (xhr.readyState != 4 && xhr.status != 200) {
      window.location.href = "index.html";
    }
  };

  xhr.send(JSON.stringify({ token: token }));
} else {
  window.location.href = "index.html";
}
