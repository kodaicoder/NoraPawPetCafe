async function hideMenu(isRouteGuard) {
  const token = localStorage.getItem("token");

  const loginItem = document.getElementsByClassName("login-item")[0];
  const adminItem = document.getElementsByClassName("admin-item")[0];
  const userItem = document.getElementsByClassName("user-item")[0];

  if (!!token) {
    const res = await postAsync("./APIs/auth/check_token.php", {
      token: token,
    });
    if (res.code == 200) {
      if (res.role == 1) {
        loginItem.classList.add("d-none");
        adminItem.classList.remove("d-none");
      } else {
        loginItem.classList.add("d-none");
        userItem.classList.remove("d-none");
      }
    }
  }
}

async function adoptModal() {
  const res = await getUserId();
  if (!!res) {
    if (res.code == 200) {
      $("#customerId").val(res.userId);
    } else {
      $(".adopt-btn").each((idx, btn) => {
        $(btn).attr("data-bs-target", "#loginAlertModal");
      });
    }
  } else {
    $(".adopt-btn").each((idx, btn) => {
      $(btn).attr("data-bs-target", "#loginAlertModal");
    });
  }
}

async function getUserId() {
  let token = localStorage.getItem("token");
  const res = await postAsync("./APIs/auth/check_token.php", {
    token: token,
  });
  if (!!token) {
    return res;
  } else {
    return false;
  }
}

async function logout() {
  localStorage.clear("token");
  const res = await getAsync("APIs/auth/logout.php");
  if (res.code == 200) {
    location.reload();
  }
}
