$(document).ready(function () {
  loadProfile();
});

function loadProfile() {
  let profile_components = "";

  var auth =
    "Basic " + btoa("myapp:obyJJpDtIxcZgd7Kd0YBFRDpAMBgi9Q1bsCZkwK7Mn6AyRJsg2");

  $.ajax({
    url: "http://localhost/fdc_messageboard/api/v1/user", // Replace with your server URL
    type: "POST",
    // data: JSON.stringify(data),
    contentType: "application/json; charset=utf-8",
    dataType: "json",
    beforeSend: function (xhr) {
      xhr.setRequestHeader("Authorization", auth);
    },
    success: function (response) {
      console.log(response);
      var json = JSON.parse(JSON.stringify(response));
      var datas = json.message;

      profile_components +=
        '<div class="d-flex justify-content-center profile-profile">' +
        '<img src="' +
        datas[0].User.img_url +
        '" alt="Profile" width="150" height="150" />' +
        "</div>" +
        '<div class="d-flex flex-column text-center content-profile">' +
        "<p>" +
        datas[0].User.name +
        ' <span class="color2 f-small">(' +
        datas[0].User.gender +
        ")</span></p>" +
        '<p class="color2 f-small-profile m-0 p-0">' +
        '<i class="fa-solid fa-cake-candles"></i> ' +
        datas[0].User.birthdate +
        "" +
        "</p>" +
        '<p class="m-0 p-0">--------------------</p>' +
        '<p class="color2 f-small-profile m-0 p-0">Joined : ' +
        datas[0].User.created +
        "</p>" +
        '<p class="color2 f-small-profile m-0 p-0">Last login : ' +
        datas[0].User.last_login_time +
        "</p>" +
        '<p class="hubby-profile mt-3 bg-secondary mx-5 text-light py-1">Hubby</p>' +
        '<p class="f-small-profile">' +
        datas[0].User.hubby +
        "</p>" +
        "</div>";

      $("#main-profile").append(profile_components);
    },
    error: function (error) {
      // Handle any errors
      console.log(JSON.stringify(error));
    },
  });
}
