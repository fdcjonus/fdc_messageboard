var app = angular.module("myApp", ["ngRoute"]);
app.config(function ($routeProvider) {
  $routeProvider
    .when("/", {
      templateUrl: "home.html",
    })
    .when("/msg", {
      templateUrl: "list.html",
    })
    .when("/profile", {
      templateUrl: "profile.html",
    })
    .when("/update", {
      templateUrl: "update.html",
    })
    .when("/new", {
      templateUrl: "new.html",
    })
    .when("/details", {
      templateUrl: "message.html",
    });
});
