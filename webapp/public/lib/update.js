$(document).ready(function () {
  $("#image-upload").change(function (event) {
    // Get the selected file
    var file = event.target.files[0];
    // Create a FileReader object
    var reader = new FileReader();
    // Set the onload event to display the image
    reader.onload = function (e) {
      // Set the src attribute of the img tag to the file content
      $("#image-view").attr("src", e.target.result).show();
    };
    // Read the image file as a data URL
    reader.readAsDataURL(file);
  });
});

$("#btn-update").click(function () {
  // alert("../img/profile/" + $("#image-upload").val().split("\\")[2]);
  var data = {
    img_url: "../img/profile/" + $("#image-upload").val().split("\\")[2],
    name: $("#name").val(),
    birthdate: $("#datepicker").val(),
    gender: $("#customRadioInline1").is(":checked") ? "Male" : "Female",
    hubby: $("#hubby").val(),
  };
  var auth =
    "Basic " + btoa("myapp:obyJJpDtIxcZgd7Kd0YBFRDpAMBgi9Q1bsCZkwK7Mn6AyRJsg2");
  $.ajax({
    url: "http://localhost/fdc_messageboard/api/v1/update", // Replace with your server URL
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
        moveImage();
        window.location.href = "#!profile";
      } else {
        $("#error-message").append("Check your infiormation");
      }
    },
    error: function (error) {
      // Handle any errors
      console.log(JSON.stringify(error));
    },
  });
});

function moveImage() {
  var fileInput = document.getElementById("image-upload");
  var file = fileInput.files[0];
  var formData = new FormData();
  formData.append("image", file);
  $.ajax({
    url: "../lib/move.php",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,
    success: function (response) {
      console.log(response);
    },
    error: function (error) {
      // Handle any errors
      console.log(JSON.stringify(error));
    },
  });
}
