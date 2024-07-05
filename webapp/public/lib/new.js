$(document).ready(function () {
  getAllUsers();
  $(".js-example-basic-multiple").select2();
});
function getAllUsers() {
  var auth =
    "Basic " + btoa("myapp:obyJJpDtIxcZgd7Kd0YBFRDpAMBgi9Q1bsCZkwK7Mn6AyRJsg2");

  $.ajax({
    url: "http://localhost/fdc_messageboard/api/v1/users", // Replace with your server URL
    type: "GET",
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    beforeSend: function (xhr) {
      xhr.setRequestHeader("Authorization", auth);
    },
    success: function (response) {
      //   console.log(response);
      var json = JSON.parse(JSON.stringify(response));
      var datas = json.message;
      let users = "";
      for (let i = 0; i < datas.length; i++) {
        users +=
          '<option value="' +
          datas[i].User.id +
          '">' +
          datas[i].User.name +
          "</option>";
      }
      $("#users-name").append(users);
    },
    error: function (error) {
      // Handle any errors
      console.log(JSON.stringify(error));
    },
  });
}

$("#send-message").click(function () {
  //   alert($("#msg").val());

  var data = {
    msg: $("#msg").val(),
    ids: $("#users-name").val(),
  };
  var auth =
    "Basic " + btoa("myapp:obyJJpDtIxcZgd7Kd0YBFRDpAMBgi9Q1bsCZkwK7Mn6AyRJsg2");

  $.ajax({
    url: "http://localhost/fdc_messageboard/api/v1/message", // Replace with your server URL
    type: "POST",
    data: JSON.stringify(data),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    beforeSend: function (xhr) {
      xhr.setRequestHeader("Authorization", auth);
    },
    success: function (response) {
      console.log(response);
      var json = JSON.parse(JSON.stringify(response));
      if (json.status == 201) {
        window.location.href = "#!msg";
      }
    },
    error: function (error) {
      // Handle any errors
      console.log(JSON.stringify(error));
    },
  });
});
