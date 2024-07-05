$(document).ready(function () {
  getMessages();
});

function getMessages() {
  var data = {
    limit: 5,
    page: 1,
    message_id: localStorage.getItem("partner-id"),
    userid: localStorage.getItem("user-id"),
  };
  var auth =
    "Basic " + btoa("myapp:obyJJpDtIxcZgd7Kd0YBFRDpAMBgi9Q1bsCZkwK7Mn6AyRJsg2");

  $.ajax({
    url: "http://localhost/fdc_messageboard/api/v1/messages", // Replace with your server URL
    type: "POST",
    data: JSON.stringify(data),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    beforeSend: function (xhr) {
      xhr.setRequestHeader("Authorization", auth);
    },
    success: function (response) {
      let message_components = "";
      var json = JSON.parse(JSON.stringify(response));
      var datas = json.message;
      for (let i = 0; i < datas.length; i++) {
        message_components +=
          '<div class="alert alert-secondary" role="alert">' +
          '<div class="row">' +
          '<div class="col-sm-2">' +
          "<img " +
          'src="' +
          datas[i].User.img_url +
          '"' +
          'alt="profile"' +
          'height="100"' +
          'width="100"' +
          'class=""' +
          "/>" +
          "<p>" +
          datas[i].User.name +
          "</p>" +
          "</div>" +
          '<div class="col-sm-10">' +
          "<p>" +
          datas[i].Message.message +
          "</p>" +
          "<hr />" +
          '<div class="row">' +
          '<div class="col-sm-10">' +
          '<a href="" class="mx-2">Delete</a>' +
          "</div>" +
          '<div class="col-sm-2">' +
          datas[i].Message.created +
          "</div>" +
          "</div>" +
          "</div>" +
          "</div>" +
          "</div>";
      }
      $("#partner-messages").append(message_components);
    },
    error: function (error) {
      // Handle any errors
      console.log(JSON.stringify(error));
    },
  });
}
$("#reply-btn").click(function () {
  var data = {
    msg: $("#reply-message").val(),
    userid: localStorage.getItem("user-id"),
    ids: JSON.parse("[" + localStorage.getItem("partner-id") + "]"),
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
      location.reload(true);
    },
    error: function (error) {
      // Handle any errors
      console.log(JSON.stringify(error));
    },
  });
});

function loadUserInfo(id) {
  var data = {
    userid: id,
  };
  var auth =
    "Basic " + btoa("myapp:obyJJpDtIxcZgd7Kd0YBFRDpAMBgi9Q1bsCZkwK7Mn6AyRJsg2");

  $.ajax({
    url: "http://localhost/fdc_messageboard/api/v1/userdet", // Replace with your server URL
    type: "POST",
    data: JSON.stringify(data),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    beforeSend: function (xhr) {
      xhr.setRequestHeader("Authorization", auth);
    },
    success: function (response) {
      console.log(response);
      location.reload(true);
    },
    error: function (error) {
      // Handle any errors
      console.log(JSON.stringify(error));
    },
  });
}
