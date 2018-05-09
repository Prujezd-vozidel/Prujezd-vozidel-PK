var app = angular.module('pvpk', ['ngRoute', 'ngResource']);

app.constant('config', {
    APP_NAME: 'PVPK',
    APP_VERSION: 1.0,
    API_URL: API_URL,
    API_TOKEN: API_TOKEN,
    DEFAULT_POSITION: {LAT: 49.53, LNG: 13.3},
    DEFAULT_ZOOM: 10
});

//PRIPRAVA PRO REFAKTORING
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


        var toDate = new Date(new Date().getTime() - (1 * 24 * 60 * 60 * 1000));

        //DODELAT OMEZENI
        $scope.maxDate = toDate;
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
        $scope.showSearchLoading = false;

        if (params.location != null && params.location.length > 2) {
            $scope.searchLocations(false);
        }
    };


    $scope.searchLocations = function (saveToUrl) {
        if (!$scope.search.location || $scope.search.location.length <= 1) {
            $scope.locations = [];
            return;
        }

        $scope.showSearchLoading = true;

        if (saveToUrl)
            $location.search({
                location: $scope.search.location,
                // fromDate: $scope.search.fromDate.getTime(),
                // toDate: $scope.search.toDate.getTime(),
                // fromTime: $scope.search.fromTime.getTime(),
                // toTime: $scope.search.toTime.getTime(),
                direction: $scope.search.direction ? 1 : 0
            });

        Device.query({
            address: $scope.search.location,
            showDirection: $scope.search.direction ? 1 : 0
        }, function (data) {
            $scope.locations = data;
            $scope.showSearchLoading = false;

            var params = $location.search();
            if (!saveToUrl && jQuery.grep($scope.locations, function (e) {
                return e.id === params.deviceId;
            }).length > 0) {
                $scope.selectDevice(params.deviceId);
            }

        }, function (response) {
            $scope.showSearchLoading = false;
            console.log('Error api all Devices');
            $rootScope.handleErrorResponse(response);
        });
    };

    $scope.selectDevice = function (id) {
        var searchObject = $location.search();
        searchObject.deviceId = id;
        $location.search(searchObject);

        $rootScope.$emit('activeMarker', {id: id});
        $rootScope.$emit('infoLocation', {id: id});
    };

});


app.controller('infoController', function ($rootScope, $scope, config, Device, Vehicle) {

    this.$onInit = function () {
        $rootScope.selectDevice = null;
        $scope.vehicles = [];
        $scope.showInfoLoading = false;

        Vehicle.query(null, function (data) {
            $scope.vehicles = data;
        }, function (response) {
            $rootScope.graphShow = false;
            console.log('Error api all Vehicles');
            $rootScope.handleErrorResponse(response);
        });
    };

    $rootScope.$on('infoLocation', function (event, args) {
        $scope.showInfoLoading = true;

        Device.get({
            id: args.id
            // dateFrom: $scope.search.fromDate.getTime(),
            // dateTo: $scope.search.toDate.getTime(),
            // timeFrom: $scope.search.fromTime.getTime(),
            // timeTo: $scope.search.toTime.getTime(),
            // direction: $scope.search.direction ? 1 : 0
        }, function (data) {
            $rootScope.selectDevice = data;
            $scope.showInfoLoading = false;
        }, function (response) {
            $rootScope.selectDevice = null;
            $scope.showInfoLoading = false;
            console.log('Error api get Devices');
            $rootScope.handleErrorResponse(response);
        });

    });

    $scope.infoClose = function () {
        $rootScope.selectDevice = null;

        $rootScope.$emit('setDefaultMap', null);
    };
});


app.controller('mapController', function ($rootScope, $scope, config, Device) {

    this.$onInit = function () {

        $scope.map = new GMaps({
            div: '#map',
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false,
            mapTypeId: 'roadmap',
            zoom: config.DEFAULT_ZOOM,
            lat: config.DEFAULT_POSITION.LAT,
            lng: config.DEFAULT_POSITION.LNG
        });

        Device.query({showDirection: 0}, function (data) {
            $scope.createMarkerNext(data, 0);
        }, function (response) {
            console.log('Error api all Devices');
            $rootScope.handleErrorResponse(response);
        });
    };

    $scope.createMarkerNext = function (data, i) {
        var lctn = data[i];

        GMaps.geocode({
            address: lctn.street + ', ' + lctn.town + ', Plzeňský kraj',
            callback: function (results, status) {
                if (status === 'OK') {
                    latlng = results[0].geometry.location;

                    var marker = $scope.map.addMarker({
                        lat: latlng.lat(),
                        lng: latlng.lng(),
                        title: lctn.name,
                        label: 'U',
                        click: function (e) {
                            $rootScope.$emit('infoLocation', {id: lctn.id});
                            //alert("asdfas");
                        },
                        infoWindow: {
                            content: '<h6 class="mb-1">' + lctn.name + '</h6>'
                            + '<address>' + lctn.street + ', ' + lctn.town + '</address>'
                        },
                        id: lctn.id
                    });

                } else if (status === 'ZERO_RESULTS') {
                    console.log('No results found address');
                }

                i++;
                if (i < data.length) {
                    setTimeout(function () {
                        $scope.createMarkerNext(data, i);
                    }, 900);
                }
            }
        });

    };

    $rootScope.$on('activeMarker', function (event, args) {
        var id = args.id;
        for (var i = 0, marker; marker = $scope.map.markers[i]; i++) {
            if (marker.id && marker.id === id && marker.infoWindow) {
                $scope.map.setCenter(marker.position.lat(), marker.position.lng());
                $scope.map.setZoom(12);
                marker.infoWindow.open($scope.map, marker);
                return;
            }
        }
    });

    $rootScope.$on('setDefaultMap', function (event, args) {
        $scope.map.setCenter(config.DEFAULT_POSITION.LAT, config.DEFAULT_POSITION.LNG);
        $scope.map.setZoom(config.DEFAULT_ZOOM);
        $scope.map.hideInfoWindows();
    });

});


app.factory("Device", function ($resource, config) {
    return $resource(config.API_URL + "/devices/:id", {id: '@id'}, {
        'get': {
            url: config.API_URL + '/devices/:id',
            method: 'GET',
            headers: {jwt: config.API_TOKEN}
        },
        'query': {
            url: config.API_URL + '/devices',
            method: 'GET',
            isArray: true,
            headers: {jwt: config.API_TOKEN}
        }
    });
});

app.factory("Vehicle", function ($resource, config) {
    return $resource(config.API_URL + "/vehicles", null, {
        'query': {
            url: config.API_URL + '/vehicles',
            method: 'GET',
            isArray: true,
            headers: {jwt: config.API_TOKEN}
        }
    });
});