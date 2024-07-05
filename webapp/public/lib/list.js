$(document).ready(function () {
  loadList();
});

function loadList() {
  var data = {
    limit: 5,
    page: 1,
  };

  var auth =
    "Basic " + btoa("myapp:obyJJpDtIxcZgd7Kd0YBFRDpAMBgi9Q1bsCZkwK7Mn6AyRJsg2");

  $.ajax({
    url: "http://localhost/fdc_messageboard/api/v1/lists", // Replace with your server URL
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
      var datas = json.message;
      let components = "";
      let tmp_comp = "";
      for (let i = 0; i < datas.length; i++) {
        const img_url = datas[i].User.img_url;
        const name = datas[i].User.name;
        const created = datas[i].Message.created;
        const message = datas[i].Message.message;
        const msgid = datas[i].List.msg_id;
        const userid = datas[i].List.userid;

        tmp_comp += `
            <div class="alert alert-secondary" role="alert">
            <div class="row">
              <div class="col-sm-2">
                <img
                  src="${img_url}"
                  alt="profile"
                  height="100"
                  width="100"
                  class=""
                />
                <p>${name}</p>
              </div>
              <div class="col-sm-10">
                <p>
                  ${message}
                </p>
                <hr />
                <div class="row">
                  <div class="col-sm-10">
                    <button class="btn btn-link" class="mx-2" onclick="test(${msgid},${userid},'${img_url}')">View</button>
                    <a href="" class="mx-2">Delete</a>
                  </div>
                  <div class="col-sm-2">${created}</div>
                </div>
              </div>
            </div>
          </div>
        `;

        // console.log(tmp_comp);
        // console.log(datas[i].List.userid + "-" + datas[i].List.msg_id);
        // components +=
        //   '<div class="alert alert-success w-100" role="alert">' +
        //   '<div class="row">' +
        //   '<div class="col-sm-2">' +
        //   '<img src="' +
        //   datas[i].User.img_url +
        //   '" alt="profile" height="100" width="100" class="" />' +
        //   "</div>" +
        //   '<div class="col-sm-10">' +
        //   "<p>" +
        //   datas[i].Message.message +
        //   "</p>" +
        //   "<hr />" +
        //   '<div class="row">' +
        //   '<div class="col-sm-10">' +
        //   '<button class="btn btn-link mx-2" onclick="test(' +
        //   datas[i].List.msg_id +
        //   "," +
        //   datas[i].List.userid +
        //   ",'" +
        //   datas[i].User.img_url +
        //   '")">View</button>' +
        //   '<a href="" class="mx-2">Delete</a>' +
        //   "</div>" +
        //   '<div class="col-sm-2">' +
        //   datas[i].Message.created +
        //   "</div>" +
        //   "</div>" +
        //   "</div>" +
        //   "</div>" +
        //   "</div>";
      }
      $("#message-list-data").append(tmp_comp);
    },
    error: function (error) {
      // Handle any errors
      console.log(JSON.stringify(error));
    },
  });
}

function test(pid, uid, pp) {
  console.log(pid + "-" + uid + "-" + pp);
  localStorage.setItem("user-id", uid);
  localStorage.setItem("partner-id", pid);
  localStorage.setItem("profile-pic", pp);
  window.location.href = "#!details";
}
