$("#register").click(function () {
  if (
    $("#fname").val() != "" &&
    $("#email").val() != "" &&
    $("#password").val() != "" &&
    $("#conf").val() != ""
  ) {
    var data = {
      name: $("#fname").val(),
      username: $("#email").val(),
      password: $("#password").val(),
      confirm: $("#conf").val(),
    };

    var auth =
      "Basic " +
      btoa("myapp:obyJJpDtIxcZgd7Kd0YBFRDpAMBgi9Q1bsCZkwK7Mn6AyRJsg2");

    $.ajax({
      url: "http://localhost/fdc_messageboard/api/v1/register", // Replace with your server URL
      type: "POST",
      data: JSON.stringify(data),
      contentType: "application/json; charset=utf-8",
      dataType: "json",
      beforeSend: function (xhr) {
        xhr.setRequestHeader("Authorization", auth);
      },
      success: function (response) {
        var json = JSON.parse(JSON.stringify(response));
        if (json.status == 201) {
          window.location.href = "success.html";
        }
      },
      error: function (error) {
        // Handle any errors
        alert(JSON.stringify(error));
      },
    });
  }
});
