function loginAccount() {
  if ($("#login-username").val() != "" && $("#login-password").val() != "") {
    var data = {
      username: $("#login-username").val(),
      password: $("#login-password").val(),
    };

    var auth =
      "Basic " +
      btoa("myapp:obyJJpDtIxcZgd7Kd0YBFRDpAMBgi9Q1bsCZkwK7Mn6AyRJsg2");

    $.ajax({
      url: "http://localhost/fdc_messageboard/api/v1/login", // Replace with your server URL
      type: "POST",
      data: JSON.stringify(data),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("Authorization", auth);
      },
      success: function (response) {
        var json = JSON.parse(JSON.stringify(response));
        if (json.status == 200) {
          localStorage.setItem("user", $("#login-username").val());
          window.location.href = "./public/pages/";
        }
      },
      error: function (error) {
        // Handle any errors
        alert(JSON.stringify(error));
      },
    });
  }
}
