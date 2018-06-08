angular.module('pvpk', ['ngResource', 'ngSanitize']);
angular.module('pvpk')
    .constant('config', {
        APP_NAME: 'PVPK',
        APP_VERSION: '1.3.0',
        API_URL: API_URL,
        API_TOKEN: API_TOKEN,
        DEFAULT_POSITION: {lat: 49.53, lng: 13.3},
        DEFAULT_ZOOM: 10,
        DEFAULT_ZOOM_MIN: 7,
        DEFAULT_RANGE_DATE_DAY: {from: -30, to: -1},
        DEFAULT_RANGE_TIME_HOUR: {from: 7, to: 16}
    });
angular.module('pvpk')
    .controller('infoController', ['$rootScope', '$scope', '$location', 'config', 'Device', 'Vehicle', function ($rootScope, $scope, $location, config, Device, Vehicle) {

        this.$onInit = function () {
            $rootScope.selectDevice = null;
            $scope.showInfoLoading = false;
            $scope.vehicles = [];
            $scope.urlExportCsv = null;

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
            var params = $location.search();
            $scope.range = {
                fromDate: moment(params.fromDate, 'YYYY-MM-DD').isValid() ? moment(params.fromDate).toDate() : moment().add(config.DEFAULT_RANGE_DATE_DAY.from, 'd').toDate(),
                toDate: moment(params.toDate, 'YYYY-MM-DD').isValid() ? moment(params.toDate).toDate() : moment().add(config.DEFAULT_RANGE_DATE_DAY.to, 'd').toDate(),
                fromTime: moment(params.fromTime, 'HH:mm').isValid() ? moment(params.fromTime, 'HH:mm').toDate() : moment({hour: config.DEFAULT_RANGE_TIME_HOUR.from}).toDate(),
                toTime: moment(params.toTime, 'HH:mm').isValid() ? moment(params.toTime, 'HH:mm').toDate() : moment({hour: config.DEFAULT_RANGE_TIME_HOUR.to}).toDate(),
                isTime: params.isTime == 0 ? false : true
            };
        });

        $rootScope.$on('infoLocation', function (event, args) {
            $scope.showInfoLoading = true;

            var params = $location.search();
            params.deviceId = args.id;
            params.direction = args.direction;
            $location.search(params);

            var range = $scope.getRange();

            var query = {
                period: range.isTime ? 'time-period' : 'day-period',
                id: args.id,
                direction: args.direction,
                dateFrom: range.fromDate.format('YYYY-MM-DD'),
                dateTo: range.toDate.format('YYYY-MM-DD'),
                timeFrom: range.isTime ? range.fromTime.format('HH:mm') : null,
                timeTo: range.isTime ? range.toTime.format('HH:mm') : null
            };

            Device.get(query, function (data) {
                $rootScope.selectDevice = data;
                $scope.renderGraph();
                $scope.urlExportCsv = $scope.generateUrlExportCsv(query);

                $scope.showInfoLoading = false;
            }, function (response) {
                $rootScope.selectDevice = null;
                $scope.showInfoLoading = false;
                console.log('Error api get Devices');
                $rootScope.handleErrorResponse(response);
            });

        });

        $scope.generateUrlExportCsv = function (query) {
            var relativeUrl = '/devices/:id/:period/csv?'.replace(':id', query.id).replace(':period', query.period);
            delete query.id;
            delete query.period;

            var paramsUrl = jQuery.param(query);
            return config.API_URL + relativeUrl + paramsUrl;
        };

        $scope.changeRange = function () {
            if ($scope.range.fromDate >= $scope.range.toDate || ($scope.range.isTime && $scope.range.fromTime >= $scope.range.toTime)) {
                $rootScope.selectDevice.traffics = [];
                return;
            }

            var range = $scope.getRange();

            var params = $location.search();
            params.fromDate = range.fromDate.format('YYYY-MM-DD');
            params.toDate = range.toDate.format('YYYY-MM-DD');
            params.fromTime = range.isTime ? range.fromTime.format('HH:mm') : null;
            params.toTime = range.isTime ? range.toTime.format('HH:mm') : null;
            params.isTime = range.isTime ? null : 0;
            $location.search(params);

            if ($rootScope.selectDevice)
                $rootScope.$emit('infoLocation', {
                    id: $rootScope.selectDevice.id,
                    direction: $rootScope.selectDevice.direction
                });
        };

        $scope.getRange = function () {
            return {
                fromDate: moment($scope.range.fromDate).isValid() ? moment($scope.range.fromDate) : moment().add(config.DEFAULT_RANGE_DATE_DAY.from, 'd'),
                toDate: moment($scope.range.toDate).isValid() ? moment($scope.range.toDate) : moment().add(config.DEFAULT_RANGE_DATE_DAY.to, 'd'),
                fromTime: moment($scope.range.fromTime).isValid() ? moment($scope.range.fromTime) : moment({hour: config.DEFAULT_RANGE_TIME_HOUR.from}),
                toTime: moment($scope.range.toTime).isValid() ? moment($scope.range.toTime) : moment({hour: config.DEFAULT_RANGE_TIME_HOUR.to}),
                isTime: $scope.range.isTime ? true : false
            };
        };

        $scope.renderGraph = function () {
            var color = ['rgba(158, 158, 158, #alpha)', 'rgba(213, 0, 0, #alpha)', 'rgba(0, 123, 255, #alpha)', 'rgba(170, 0, 255, #alpha)',
                'rgba(0, 200, 83, #alpha)', 'rgba(255, 214, 0, #alpha)', 'rgba(255, 109, 0, #alpha)',
                'rgba(174, 234, 0, #alpha)', 'rgba(98, 0, 234, #alpha)', 'rgba(255, 171, 0, #alpha)', 'rgba(100, 221, 23, #alpha)', 'rgba(0, 184, 212, #alpha)'];

            var labels = jQuery.unique($rootScope.selectDevice.traffics.map(function (d) {
                return $scope.range.isTime ? d.timeFrom : moment(d.date, 'YYYY-MM-DD').format('D.M.YYYY');
            }));

            var useVehiclesIds = jQuery.unique($rootScope.selectDevice.traffics.map(function (d) {
                return d.typeVehicleId;
            }));

            var filterVehicles = jQuery.grep($scope.vehicles, function (n) {
                return useVehiclesIds.indexOf(n.id) >= 0;
            });

            var datasetsNumberVehicles = [];
            var datasetsAverageSpeed = [];

            for (var i = 0, vehicle; vehicle = filterVehicles[i]; i++) {
                var datasetNumberVehicles = {
                    label: vehicle.name,
                    backgroundColor: color[vehicle.id].replace("#alpha", "0.3"),
                    borderColor: color[vehicle.id].replace("#alpha", "1"),
                    borderWidth: 2,
                    data: []
                };

                var datasetAverageSpeed = {
                    data: [],
                    borderWidth: 2,
                    label: vehicle.name,
                    fill: false,
                    //fill: 'start',
                    backgroundColor: color[vehicle.id].replace("#alpha", "0.3"),
                    borderColor: color[vehicle.id].replace("#alpha", "1"),
                    cubicInterpolationMode: 'monotone',
                    pointRadius: 0
                };

                var l = 0;
                for (var j = 0, traffic; traffic = $rootScope.selectDevice.traffics[j]; j++) {
                    if (($scope.range.isTime && labels[l] !== traffic.timeFrom) || (!$scope.range.isTime && labels[l] !== moment(traffic.date, 'YYYY-MM-DD').format('D.M.YYYY'))) {
                        l++;
                        if (datasetNumberVehicles.data.length < l) {
                            datasetNumberVehicles.data.push(0);
                            datasetAverageSpeed.data.push(null);
                        }
                    }
                    if (traffic.typeVehicleId === vehicle.id) {
                        datasetNumberVehicles.data.push($scope.range.isTime ? traffic.numberVehicleAverage : traffic.numberVehicle);
                        datasetAverageSpeed.data.push(traffic.speedAverage <= 0 ? null : traffic.speedAverage);
                    }
                }
                datasetsNumberVehicles.push(datasetNumberVehicles);
                datasetsAverageSpeed.push(datasetAverageSpeed);
            }

            $rootScope.$emit('renderGraphNumberVehicles', {
                data: {
                    labels: labels,
                    datasets: datasetsNumberVehicles
                }
            });

            $rootScope.$emit('renderGraphAverageSpeed', {
                data: {
                    labels: labels,
                    datasets: datasetsAverageSpeed
                }
            });
        };

        $scope.infoClose = function () {
            $rootScope.selectDevice = null;

            var params = $location.search();
            params.deviceId = null;
            params.direction = null;
            $location.search(params);

            $rootScope.$emit('setDefaultMap', null);
        };
    }]);

angular.module('pvpk')
    .controller('mainController', ['$rootScope', '$scope', '$location', '$window', function ($rootScope, $scope, $location, $window) {

        this.$onInit = function () {
            $scope.showLoadingScreen = true;
        };

        $window.onload = function () {
            var params = $location.search();
            if (params.deviceId) {
                $rootScope.$emit('activeMarker', {id: params.deviceId});
            }

            $scope.$apply(function () {
                $scope.showLoadingScreen = false;
            });
        };

        $rootScope.$on('$locationChangeSuccess', function (event, newUrl, oldUrl) {
            var params = $location.search();

            if (newUrl !== oldUrl && $scope.historyUrl) {
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
                } else if (!params.deviceId && $scope.historyUrl.deviceId) {
                    $rootScope.selectDevice = null;
                    $rootScope.$emit('setDefaultMap', null);
                }
            } else if (params.deviceId) {
                $rootScope.$emit('infoLocation', {id: params.deviceId, direction: params.direction});
            }

            $scope.historyUrl = $location.search();
        });

        $rootScope.handleErrorResponse = function (response) {

            var modalError = jQuery('#modalError');
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
    }]);
angular.module('pvpk')
    .controller('mapController', ['$rootScope', '$scope', 'config', 'Device', function ($rootScope, $scope, config, Device) {

        this.$onInit = function () {
            $scope.markers = [];

            $scope.map = new google.maps.Map(document.getElementById('map'), {
                center: config.DEFAULT_POSITION,
                zoom: config.DEFAULT_ZOOM,
                minZoom: config.DEFAULT_ZOOM_MIN,
                zoomControl: true,
                mapTypeControl: false,
                scaleControl: false,
                streetViewControl: false,
                rotateControl: false,
                fullscreenControl: false,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            Device.query({showDirection: 0}, function (data) {
                for (var i = 0, lctn; lctn = data[i]; i++) {
                    $scope.createMarker(lctn);
                }
            }, function (response) {
                console.log('Error api all Devices');
                $rootScope.handleErrorResponse(response);
            });
        };

        $scope.createMarker = function (lctn) {
            if (lctn.lat && lctn.lng) {
                var marker = new google.maps.Marker({
                    map: $scope.map,
                    position: {lat: lctn.lat, lng: lctn.lng},
                    title: lctn.name,
                    infoWindow: new google.maps.InfoWindow({
                        content: '<h6 class="mb-1">' + lctn.name + '</h6>'
                        + '<address>' + lctn.street + ', ' + lctn.town + '</address>'
                    }),
                    id: lctn.id
                });

                marker.addListener('click', function () {
                    $scope.closeInfoWindows();
                    marker.infoWindow.open($scope.map, marker);
                    $rootScope.$emit('infoLocation', {id: lctn.id});
                });

                $scope.markers.push(marker);
            }
        };

        $rootScope.$on('activeMarker', function (event, args) {
            for (var i = 0, marker; marker = $scope.markers[i]; i++) {
                if (marker.id && marker.id === args.id && marker.infoWindow) {
                    $scope.map.setCenter(marker.getPosition());
                    $scope.map.setZoom(12);
                    marker.infoWindow.open($scope.map, marker);
                } else {
                    marker.infoWindow.close();
                }
            }
        });

        $rootScope.$on('setDefaultMap', function (event, args) {
            $scope.map.setCenter(config.DEFAULT_POSITION);
            $scope.map.setZoom(config.DEFAULT_ZOOM);
            $scope.closeInfoWindows();
        });

        $scope.closeInfoWindows = function () {
            for (var i = 0, marker; marker = $scope.markers[i]; i++) {
                marker.infoWindow.close();
            }
        };
    }]);
angular.module('pvpk')
    .controller('searchController', ['$rootScope', '$scope', '$location', 'config', 'Device', function ($rootScope, $scope, $location, config, Device) {

        this.$onInit = function () {
            $scope.config = config;
            $scope.locations = [];
            $scope.showSearchLoading = false;

            $rootScope.$emit('setSearchFromUrl', null);
        };

        $scope.searchLocations = function () {
            var params = $location.search();
            params.q = $scope.search.q;
            params.isDirection = $scope.search.isDirection ? 1 : null;
            $location.search(params);

            if (!$scope.search.q || $scope.search.q.length <= 1) {
                $scope.locations = [];
                return;
            }

            $scope.showSearchLoading = true;

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
            var params = $location.search();
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

    }]);
angular.module('pvpk')
    .component('graphAverageSpeed', {
        template: '<div><canvas id="graphAverageSpeed" class="graphSize mb-5"></canvas></div>',
        controller: ['$rootScope', '$scope', function ($rootScope, $scope) {

            $rootScope.$on('renderGraphAverageSpeed', function (event, args) {
                var canvas = document.getElementById('graphAverageSpeed').getContext('2d');

                if ($scope.graphLine)
                    $scope.graphLine.destroy();

                $scope.graphLine = new Chart(canvas, {
                    type: 'line',
                    data: args.data,
                    options: {
                        responsive: true,
                        pointDot: false,
                        legend: {
                            position: 'bottom'
                        },
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
                                    beginAtZero: true,
                                    suggestedMax: 70
                                }
                            }]
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: function (tooltipItems) {
                                    return tooltipItems.yLabel + ' km/h';
                                }
                            }
                        }
                    }
                });

            });

        }]
    });
angular.module('pvpk')
    .component('graphNumberVehicles', {
        template: '<div><canvas id="graphNumberVehicles" class="graphSize mb-5"></canvas></div>',
        controller: ['$rootScope', '$scope', function ($rootScope, $scope) {

            $rootScope.$on('renderGraphNumberVehicles', function (event, args) {
                var canvasGraphNumberVehicles = document.getElementById('graphNumberVehicles').getContext('2d');

                if ($scope.graphNumberVehicles)
                    $scope.graphNumberVehicles.destroy();

                $scope.graphNumberVehicles = new Chart(canvasGraphNumberVehicles, {
                    type: 'bar',
                    data: args.data,
                    options: {
                        responsive: true,
                        onResize: function (chart, size) {
                            chart.options.legend.display = size.height > 240;
                            chart.update();
                        },
                        legend: {
                            position: 'bottom'
                        },
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
                        },
                        tooltips: {
                            mode: 'index',
                            intersect: false
                        }
                    }
                });

            });

        }]
    });
angular.module('pvpk')
    .factory('Device', ['$resource', 'config', function ($resource, config) {
        return $resource(config.API_URL + '/devices/:id', {id: '@id', period: '@period'}, {
            'get': {
                url: config.API_URL + '/devices/:id/:period',
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
    }]);
angular.module('pvpk')
    .factory('Vehicle', ['$resource', 'config', function ($resource, config) {
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
    }]);