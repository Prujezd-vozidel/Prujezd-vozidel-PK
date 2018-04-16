
//var app = angular.module('PVPK', []);


// app.controller('myController', function($scope, $http, $window) {
//     $scope.render = function() {
//         var url = 'http://www.recipepuppy.com/api/';
//
//         var c = $window.angular.callbacks.counter.toString(36);
//         $window['angularcallbacks' + c] = function (data) {
//             $window.angular.callbacks['_' + c](data);
//             delete $window['angularcallbacks' + c];
//         };
//
//         $http.jsonp('http://www.recipepuppy.com/api/?i=onions,garlic&q=omelet&p=1&callback=JSON_CALLBACK').then(function(response) {
//             $scope.records = response.data.results;
//         });
//     };
//
//     $scope.render();
// });