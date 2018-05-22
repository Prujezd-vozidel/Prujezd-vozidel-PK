let app = angular.module('pvpk', ['ngRoute', 'ngResource', 'ngSanitize']);

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


app.controller('mainController', function ($rootScope, $scope, $location, $window) {

    this.$onInit = function () {

    };

    $window.onload = function () {
        let params = $location.search();
        if (params.deviceId) {
            $rootScope.$emit('infoLocation', {id: params.deviceId, direction: params.direction});
            $rootScope.$emit('activeMarker', {id: params.deviceId});
        }

        $scope.showLoadingScreen = false;
    };

    $rootScope.$on('$locationChangeSuccess', function (event, newUrl, oldUrl) {

        if (newUrl !== oldUrl && $scope.historyUrl) {
            let params = $location.search();

            if ($scope.historyUrl.q !== $scope.historyUrl.q || $scope.historyUrl.isDirection != params.isDirection) {
                $rootScope.$emit('setSearchFromUrl', null);
            }

            if ($scope.historyUrl.fromDate !== params.fromDate || $scope.historyUrl.toDate !== params.toDate ||
                $scope.historyUrl.fromTime !== params.fromTime || $scope.historyUrl.toTime !== params.toTime) {
                $rootScope.$emit('setRangeFromUrl', null);
                if (params.deviceId) {
                    $rootScope.$emit('infoLocation', {id: params.deviceId, direction: params.direction});
                }
            } else if (params.deviceId && ($scope.historyUrl.deviceId !== params.deviceId || $scope.historyUrl.direction !== params.direction)) {
                $rootScope.$emit('infoLocation', {id: params.deviceId, direction: params.direction});
                $rootScope.$emit('activeMarker', {id: params.deviceId});
            }else if(!params.deviceId && $scope.historyUrl.deviceId){
                $rootScope.selectDevice = null;
                $rootScope.$emit('setDefaultMap', null);
            }
        }

        $scope.historyUrl = $location.search();
    });

    $rootScope.handleErrorResponse = function (response) {

        let modalError = jQuery('#modalError');
        switch (response.status) {
            case 400:
                console.log('API ERROR 400');
                $scope.modalError = {
                    title: 'Neplatný požadavek',
                    body: 'Požadavek nemůže být vyřízen, poněvadž byl syntakticky nesprávně zapsán.',
                    button: 'OK'
                };
                modalError.modal('show');
                break;
            case 401:
                $scope.modalError = {
                    title: 'Platnost webové aplikace vypršela',
                    body: 'Pro obnovení platnosti stačí stisknout tlačítko <strong>Obnovit</strong>.',
                    button: 'Obnovit',
                    clickButton: $scope.reloadApp
                };
                modalError.modal({backdrop: 'static', keyboard: false});
                break;
            case 404:
                console.log('API ERROR 404');
                $scope.modalError = {title: 'Nenalezen', body: 'Záznam nebyl nalezen.', button: 'OK'};
                modalError.modal('show');
                break;
            case 500:
                console.log('API ERROR 500');
                $scope.modalError = {title: 'Chyba', body: 'Chyba serveru. Zopakujte akci později.', button: 'OK'};
                modalError.modal('show');
                break;
            case -1:
                console.log('API NOT CONNECTED');
                $scope.modalError = {
                    title: 'Připojení k internetu',
                    body: 'Nejste připojeni k internetu. Zkontrolujte připojení.',
                    button: 'OK'
                };
                modalError.modal('show');
                break;
            default:
                console.log('API UNKNOWN ERROR');
                $scope.modalError = {title: 'Neočekávaná chyba', body: 'Nastala neočekávaná chyba.', button: 'OK'};
                modalError.modal('show');
                break;
        }
    };

    $scope.reloadApp = function () {
        $window.location.reload();
    }
});


app.controller('searchController', function ($rootScope, $scope, $location, config, Device) {

    this.$onInit = function () {
        $scope.locations = [];
        $scope.showSearchLoading = false;

        $rootScope.$emit('setSearchFromUrl', null);
    };

    $scope.searchLocations = function () {
        if (!$scope.search.q || $scope.search.q.length <= 1) {
            $scope.locations = [];
            return;
        }

        $scope.showSearchLoading = true;

        let params = $location.search();
        params.q = $scope.search.q;
        params.isDirection = $scope.search.isDirection ? 1 : 0;
        $location.search(params);

        Device.query({
            address: $scope.search.q,
            showDirection: $scope.search.isDirection ? 1 : 0
        }, function (data) {
            $scope.locations = data;
            $scope.showSearchLoading = false;
        }, function (response) {
            $scope.showSearchLoading = false;
            console.log('Error api all Devices');
            $rootScope.handleErrorResponse(response);
        });
    };

    $rootScope.$on('setSearchFromUrl', function (event, args) {
        let params = $location.search();

        $scope.search = {
            q: params.q,
            isDirection: params.isDirection ? !!+params.isDirection : false
        };
        $scope.searchLocations();
    });

    $scope.selectDevice = function (id, direction) {
        $rootScope.$emit('activeMarker', {id: id});
        $rootScope.$emit('infoLocation', {id: id, direction: direction});
    };

});


app.controller('infoController', function ($rootScope, $scope, $location, config, Device, Vehicle) {

    this.$onInit = function () {
        $rootScope.selectDevice = null;
        $scope.showInfoLoading = false;
        $scope.vehicles = [];
        $scope.typeVehicle = null;
        $scope.filterVehicles = [];

        Vehicle.query(null, function (data) {
            $scope.vehicles = data;
        }, function (response) {
            $rootScope.graphShow = false;
            console.log('Error api all Vehicles');
            $rootScope.handleErrorResponse(response);
        });

        $rootScope.$emit('setRangeFromUrl', null);
    };

    $rootScope.$on('setRangeFromUrl', function (event, args) {
        let params = $location.search();
        let defaultRange = $scope.defaultRange();

        $scope.range = {
            fromDate: moment(params.fromDate, 'YYYY-MM-DD').isValid() ? moment(params.fromDate).toDate() : defaultRange.fromDate.toDate(),
            toDate: moment(params.toDate, 'YYYY-MM-DD').isValid() ? moment(params.toDate).toDate() : defaultRange.toDate.toDate(),
            fromTime: moment(params.fromTime, 'HH:mm').isValid() ? moment(params.fromTime, 'HH:mm').toDate() : defaultRange.fromTime.toDate(),
            toTime: moment(params.toTime, 'HH:mm').isValid() ? moment(params.toTime, 'HH:mm').toDate() : defaultRange.toTime.toDate()
        };

    });

    $rootScope.$on('infoLocation', function (event, args) {
        $scope.showInfoLoading = true;

        let params = $location.search();
        params.deviceId = args.id;
        params.direction = args.direction;
        $location.search(params);

        let range = $scope.getRange();

        Device.get({
            id: args.id,
            direction: args.direction,
            dateFrom: range.fromDate.format('YYYY-MM-DD'),
            dateTo: range.toDate.format('YYYY-MM-DD'),
            timeFrom: range.fromTime.format('HH:mm'),
            timeTo: range.toTime.format('HH:mm'),
        }, function (data) {
            $rootScope.selectDevice = data;

            $scope.typeVehicle = null;
            $scope.renderGraphAverageSpeed();
            $scope.renderGraphNumberVehicles();

            $scope.showInfoLoading = false;
        }, function (response) {
            $rootScope.selectDevice = null;
            $scope.showInfoLoading = false;
            console.log('Error api get Devices');
            $rootScope.handleErrorResponse(response);
        });

    });

    $scope.changeRange = function () {
        if ($scope.range.fromDate >= $scope.range.toDate || $scope.range.fromTime >= $scope.range.toTime) {
            $rootScope.selectDevice.traffics = [];
            return;
        }

        let range = $scope.getRange();

        let params = $location.search();
        params.fromDate = range.fromDate.format('YYYY-MM-DD');
        params.toDate = range.toDate.format('YYYY-MM-DD');
        params.fromTime = range.fromTime.format('HH:mm');
        params.toTime = range.toTime.format('HH:mm');
        $location.search(params);

        if ($rootScope.selectDevice)
            $rootScope.$emit('infoLocation', {
                id: $rootScope.selectDevice.id,
                direction: $rootScope.selectDevice.direction
            });
    };

    $scope.getRange = function () {
        let defaultRange = $scope.defaultRange();

        return {
            fromDate: moment($scope.range.fromDate).isValid() ? moment($scope.range.fromDate) : defaultRange.fromDate,
            toDate: moment($scope.range.toDate).isValid() ? moment($scope.range.toDate) : defaultRange.toDate,
            fromTime: moment($scope.range.fromTime).isValid() ? moment($scope.range.fromTime) : defaultRange.fromTime,
            toTime: moment($scope.range.toTime).isValid() ? moment($scope.range.toTime) : defaultRange.toTime
        };
    };

    $scope.defaultRange = function () {
        return {
            fromDate: moment().day(-30),
            toDate: moment().day(-1),
            fromTime: moment({hour: 7}),
            toTime: moment({hour: 16})
        };
    };


    $scope.renderGraphAverageSpeed = function () {

        let t = $rootScope.selectDevice.traffics.reduce(function (l, r) {
            let key = r.timeFrom;
            if (typeof l[key] === 'undefined') {
                l[key] = {
                    numberVehicle: 0,
                    speedSum: 0
                };
            }

            if (r.speedAverage > 0) {
                l[key].numberVehicle += r.numberVehicle;
                l[key].speedSum += r.speedAverage * r.numberVehicle;
            }
            return l;
        }, {});

        let labels = jQuery.unique($rootScope.selectDevice.traffics.map(function (d) {
            return d.timeFrom;
        }));
        let data = Object.values(t).map(function (d) {
            return Math.round(d.speedSum / d.numberVehicle);
        });


        let canvasGraphAverageSpeed = document.getElementById('graphAverageSpeed').getContext('2d');

        if ($scope.graphAverageSpeed)
            $scope.graphAverageSpeed.destroy();

        $scope.graphAverageSpeed = new Chart(canvasGraphAverageSpeed, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    borderWidth: 2,
                    label: "Rychlost",
                    fill: 'start',
                    backgroundColor: 'rgba(0, 123, 255, 0.3)',
                    borderColor: 'rgba(0, 123, 255,1)',
                    cubicInterpolationMode: 'monotone',
                    radius: 0
                }]
            },
            options: {
                responsive: true,
                pointDot: false,
                scales: {
                    xAxes: [{
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 15
                        }
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'km/h'
                        },
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                tooltips: {
                    enabled: true,
                    mode: 'single',
                    callbacks: {
                        label: function (tooltipItems) {
                            return tooltipItems.yLabel + ' km/h';
                        }
                    }
                }
            }
        });
    };


    $scope.renderGraphNumberVehicles = function () {
        let color = ['rgba(158, 158, 158, #alpha)', 'rgba(213, 0, 0, #alpha)', 'rgba(0, 123, 255, #alpha)', 'rgba(170, 0, 255, #alpha)',
            'rgba(0, 200, 83, #alpha)', 'rgba(255, 214, 0, #alpha)', 'rgba(255, 109, 0, #alpha)',
            'rgba(174, 234, 0, #alpha)', 'rgba(98, 0, 234, #alpha)', 'rgba(255, 171, 0, #alpha)', 'rgba(100, 221, 23, #alpha)', 'rgba(0, 184, 212, #alpha)'];


        let labels = jQuery.unique($rootScope.selectDevice.traffics.map(function (d) {
            return d.timeFrom;
        }));

        let useVehiclesIds = jQuery.unique($rootScope.selectDevice.traffics.map(function (d) {
            return d.typeVehicleId;
        }));

        $scope.filterVehicles = jQuery.grep($scope.vehicles, function (n) {
            return useVehiclesIds.indexOf(n.id) >= 0;
        });

        let datasets = [];
        for (let i = 0, vehicle; vehicle = $scope.filterVehicles[i]; i++) {
            if ($scope.typeVehicle == null || $scope.typeVehicle === vehicle.id) {
                let dataset = {
                    label: vehicle.name,
                    backgroundColor: color[vehicle.id].replace("#alpha", "0.3"),
                    borderColor: color[vehicle.id].replace("#alpha", "1"),
                    borderWidth: 2,
                    data: []
                };

                let l = 0;
                for (let j = 0, traffic; traffic = $rootScope.selectDevice.traffics[j]; j++) {
                    if (labels[l] !== traffic.timeFrom) {
                        l++;
                        if (dataset.data.length < l) {
                            dataset.data.push(0);
                        }
                    }
                    if (traffic.typeVehicleId === vehicle.id) {
                        dataset.data.push(traffic.numberVehicleAverage);
                    }
                }
                datasets.push(dataset);
            }
        }

        let canvasGraphNumberVehicles = document.getElementById('graphNumberVehicles').getContext('2d');

        if ($scope.graphNumberVehicles)
            $scope.graphNumberVehicles.destroy();

        $scope.graphNumberVehicles = new Chart(canvasGraphNumberVehicles, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: datasets
            },
            options: {
                tooltips: {
                    mode: 'index',
                    intersect: false
                },
                responsive: true,
                scales: {
                    xAxes: [{
                        stacked: true,
                        ticks: {
                            autoSkip: true,
                            maxTicksLimit: 15
                        }
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: "počet vozidel"
                        },
                        stacked: true
                    }]
                }
            }
        });
    };


    $scope.infoClose = function () {
        $rootScope.selectDevice = null;

        let params = $location.search();
        params.deviceId = null;
        params.direction = null;
        $location.search(params);

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
            lng: config.DEFAULT_POSITION.LNG,
            // styles: [
            //     {
            //         featureType: "poi",
            //         elementType: "labels",
            //         stylers: [{ visibility: "off" }]
            //     }
            // ]
        });

        Device.query({showDirection: 0}, function (data) {
            for (let i = 0, lctn; lctn = data[i]; i++) {
                $scope.createMarker(lctn);
            }
        }, function (response) {
            console.log('Error api all Devices');
            $rootScope.handleErrorResponse(response);
        });
    };


    $scope.createMarker = function (lctn) {
        if (lctn.lat && lctn.lng) {
            $scope.map.addMarker({
                lat: lctn.lat,
                lng: lctn.lng,
                title: lctn.name,
                click: function () {
                    $rootScope.$emit('infoLocation', {id: lctn.id});
                },
                infoWindow: {
                    content: '<h6 class="mb-1">' + lctn.name + '</h6>'
                    + '<address>' + lctn.street + ', ' + lctn.town + '</address>'
                },
                id: lctn.id
            });
        }
    };

    $rootScope.$on('activeMarker', function (event, args) {
        let id = args.id;
        for (let i = 0, marker; marker = $scope.map.markers[i]; i++) {
            if (marker.id && marker.id === id && marker.infoWindow) {
                $scope.map.setCenter(marker.position.lat(), marker.position.lng());
                $scope.map.setZoom(12);
                $scope.map.hideInfoWindows();
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


app.factory('Device', function ($resource, config) {
    return $resource(config.API_URL + '/devices/:id', {id: '@id'}, {
        'get': {
            url: config.API_URL + '/devices/:id/time-period',
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'jwt': config.API_TOKEN
            }
        },
        'query': {
            url: config.API_URL + '/devices',
            method: 'GET',
            isArray: true,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'jwt': config.API_TOKEN
            }
        }
    });
});

app.factory('Vehicle', function ($resource, config) {
    return $resource(config.API_URL + '/vehicles', null, {
        'query': {
            url: config.API_URL + '/vehicles',
            method: 'GET',
            isArray: true,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'jwt': config.API_TOKEN
            }
        }
    });
});