var app = angular.module('pvpk', ['ngRoute', 'ngResource']);

app.constant('config', {
    APP_NAME: 'PVPK',
    APP_VERSION: 1.0,
    API_URL: API_URL,
    API_TOKEN: API_TOKEN
});


// app.config(function($stateProvider, $locationProvider) {
//     // $stateProvider
//     // .state('report',{
//     //     views: {
//     //         'search': {
//     //             templateUrl: 'report-filters.html',
//     //             controller: searchController
//     //         },
//     //         'graph': {
//     //             templateUrl: 'report-table.html',
//     //             controller: graphController
//     //         },
//     //         'map': {
//     //             templateUrl: 'report-graph.html',
//     //             controller: mapController
//     //         }
//     //     }
//     // });
//    $locationProvider.html5Mode(true);
// });

app.config(['$routeProvider', '$locationProvider', function ($routeProvider, $locationProvider) {

}]);


app.controller('mainController', function ($rootScope, $scope, $http, $window) {

    this.$onInit = function () {
        $scope.showLoadingScreen = false;
    };

    $rootScope.handleErrorResponse = function (response) {
        switch (response.status) {
            case 400:
                console.log('API ERROR 400');
                break;
            case 401:
                jQuery('#modalExpiredToken').modal('show');
                break;
            case 404:
                console.log('API ERROR 404');
                break;
            case 500:
                console.log('API ERROR 500');
                break;
            default:
        }
    };

    $scope.reloadApp = function () {
        $window.location.reload();
    }
});


app.controller('searchController', function ($rootScope, $scope, $location, config, Device) {

    this.$onInit = function () {
        var fromTime = new Date();
        fromTime.setHours(7, 0, 0, 0);

        var toTime = new Date();
        toTime.setHours(16, 0, 0, 0);

        var toDate = new Date();
        var fromDate = new Date(toDate.getTime() - (30 * 24 * 60 * 60 * 1000));

        var params = $location.search();

        $scope.search = {
            location: params.location,
            fromDate: params.fromDate == null ? fromDate : new Date(parseInt(params.fromDate)),
            toDate: params.toDate == null ? toDate : new Date(parseInt(params.toDate)),
            fromTime: params.fromTime == null ? fromTime : new Date(parseInt(params.fromTime)),
            toTime: params.toTime == null ? toTime : new Date(parseInt(params.toTime)),
            direction: params.direction == null ? true : !!+params.direction
        };

        $scope.locations = [];
        $scope.showLocationsLoading = false;

        if (params.location!=null && params.location.length > 2) {
            $scope.searchLocations(false);
        }
    };


    $scope.searchLocations = function (saveToUrl) {
        $scope.showLocationsLoading = true;

        if (saveToUrl)
            $location.search({
                location: $scope.search.location,
                fromDate: $scope.search.fromDate.getTime(),
                toDate: $scope.search.toDate.getTime(),
                fromTime: $scope.search.fromTime.getTime(),
                toTime: $scope.search.toTime.getTime(),
                direction: $scope.search.direction ? 1 : 0
            });

        Device({jwt: config.API_TOKEN}).query({
            address: $scope.search.location,
            showDirection: $scope.search.direction ? 1 : 0
        }, function (data) {
            $scope.locations = data;
            $scope.showLocationsLoading = false;

            var params = $location.search();
            if(!saveToUrl && jQuery.grep($scope.locations, function(e){ return e.id === params.deviceId; }).length>0){
                $scope.selectDevice(params.deviceId);
            }

        }, function (response) {
            $scope.showLocationsLoading = false;
            console.log('Error api all Devices');
            $rootScope.handleErrorResponse(response);
        });
    };

    $scope.selectDevice = function (id) {
        $scope.deviceId = id;

        var searchObject = $location.search();
        searchObject.deviceId = id;
        $location.search(searchObject);

        Device({jwt: config.API_TOKEN}).get({
            id: id
            // dateFrom: $scope.search.fromDate.getTime(),
            // dateTo: $scope.search.toDate.getTime(),
            // timeFrom: $scope.search.fromTime.getTime(),
            // timeTo: $scope.search.toTime.getTime(),
            // direction: $scope.search.direction ? 1 : 0
        }, function (data) {
            $rootScope.$emit('renderGraph', data);
        }, function (response) {
            console.log('Error api get Devices');
            $rootScope.handleErrorResponse(response);
        });
    };

});


app.controller('graphController', function ($rootScope, $scope, config, Vehicle) {

    this.$onInit = function () {
        $rootScope.graphShow = false;
        $scope.vehicles = [];
    };

    $rootScope.$on("renderGraph", function (event, args) {
        $rootScope.graphShow = true;

        Vehicle({jwt: config.API_TOKEN}).query(null, function (data) {
            $scope.vehicles = data;

        }, function (response) {
            $rootScope.graphShow = false;
            console.log('Error api all Vehicles');
            $rootScope.handleErrorResponse(response);
        });
    });

});

app.controller('mapController', function ($scope) {

    this.$onInit = function () {

    };

});


app.factory("Device", function ($resource, config) {
    return function (headers) {
        return $resource(config.API_URL + "/devices/:id", {id: '@id'}, {
            'get': {
                url: config.API_URL + '/devices/:id',
                method: 'GET',
                headers: headers || {}
            },
            'query': {
                url: config.API_URL + '/devices',
                method: 'GET',
                isArray: true,
                headers: headers || {}
            }
        });
    };
});

app.factory("Vehicle", function ($resource, config) {
    return function (headers) {
        return $resource(config.API_URL + "/vehicles", null, {
            'query': {
                url: config.API_URL + '/vehicles',
                method: 'GET',
                isArray: true,
                headers: headers || {}
            }
        });
    };
});