let app = angular.module('pvpk', ['ngRoute', 'ngResource', 'ngSanitize']);

app.constant('config', {
    APP_NAME: 'PVPK',
    APP_VERSION: '1.2.0',
    API_URL: API_URL,
    API_TOKEN: API_TOKEN,
    DEFAULT_POSITION: {LAT: 49.53, LNG: 13.3},
    DEFAULT_ZOOM: 10,
    DEFAULT_ZOOM_MAX: 7,
});

app.controller('mainController', function ($rootScope, $scope, $location, $window) {

    this.$onInit = function () {
        $scope.showLoadingScreen = true;
    };

    $window.onload = function () {
        let params = $location.search();
        if (params.deviceId) {
            $rootScope.$emit('activeMarker', {id: params.deviceId});
        }

        $scope.$apply(function () {
            $scope.showLoadingScreen = false;
        });
    };

    $rootScope.$on('$locationChangeSuccess', function (event, newUrl, oldUrl) {
        let params = $location.search();

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
        $scope.config = config;
        $scope.locations = [];
        $scope.showSearchLoading = false;

        $rootScope.$emit('setSearchFromUrl', null);
    };

    $scope.searchLocations = function () {
        let params = $location.search();
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
        $scope.filterVehicles = [];
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
        let params = $location.search();
        let defaultRange = $scope.defaultRange();

        $scope.range = {
            fromDate: moment(params.fromDate, 'YYYY-MM-DD').isValid() ? moment(params.fromDate).toDate() : defaultRange.fromDate.toDate(),
            toDate: moment(params.toDate, 'YYYY-MM-DD').isValid() ? moment(params.toDate).toDate() : defaultRange.toDate.toDate(),
            fromTime: moment(params.fromTime, 'HH:mm').isValid() ? moment(params.fromTime, 'HH:mm').toDate() : defaultRange.fromTime.toDate(),
            toTime: moment(params.toTime, 'HH:mm').isValid() ? moment(params.toTime, 'HH:mm').toDate() : defaultRange.toTime.toDate(),
            isTime: params.isTime == 0 ? false : defaultRange.isTime
        };

    });

    $rootScope.$on('infoLocation', function (event, args) {
        $scope.showInfoLoading = true;

        let params = $location.search();
        params.deviceId = args.id;
        params.direction = args.direction;
        $location.search(params);

        let range = $scope.getRange();

        let query = {
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

            $scope.renderGraphAverageSpeed();
            $scope.renderGraphNumberVehicles();

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
        let relativeUrl = '/devices/:id/:period/csv?'.replace(':id', query.id).replace(':period', query.period);
        delete query.id;
        delete query.period;

        let paramsUrl = jQuery.param(query);
        return config.API_URL + relativeUrl + paramsUrl;
    };

    $scope.changeRange = function () {
        if ($scope.range.fromDate >= $scope.range.toDate || ($scope.range.isTime && $scope.range.fromTime >= $scope.range.toTime)) {
            $rootScope.selectDevice.traffics = [];
            return;
        }

        let range = $scope.getRange();

        let params = $location.search();
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
        let defaultRange = $scope.defaultRange();

        return {
            fromDate: moment($scope.range.fromDate).isValid() ? moment($scope.range.fromDate) : defaultRange.fromDate,
            toDate: moment($scope.range.toDate).isValid() ? moment($scope.range.toDate) : defaultRange.toDate,
            fromTime: moment($scope.range.fromTime).isValid() ? moment($scope.range.fromTime) : defaultRange.fromTime,
            toTime: moment($scope.range.toTime).isValid() ? moment($scope.range.toTime) : defaultRange.toTime,
            isTime: $scope.range.isTime ? true : false
        };
    };

    $scope.defaultRange = function () {
        return {
            fromDate: moment().day(-30),
            toDate: moment().day(-1),
            fromTime: moment({hour: 7}),
            toTime: moment({hour: 16}),
            isTime: true
        };
    };

    $scope.renderGraphAverageSpeed = function () {

        let t = $rootScope.selectDevice.traffics.reduce(function (l, r) {
            let key = $scope.range.isTime ? r.timeFrom : r.date;
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
            return $scope.range.isTime ? d.timeFrom : moment(d.date, 'YYYY-MM-DD').format('D.M.YYYY');
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
                    pointRadius: 0
                }]
            },
            options: {
                responsive: true,
                pointDot: false,
                legend: {
                    display: false
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
                            max: Math.max(Math.round((Math.max.apply(null, data) + 10) / 10) * 10, 70)
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
    };


    $scope.renderGraphNumberVehicles = function () {
        let color = ['rgba(158, 158, 158, #alpha)', 'rgba(213, 0, 0, #alpha)', 'rgba(0, 123, 255, #alpha)', 'rgba(170, 0, 255, #alpha)',
            'rgba(0, 200, 83, #alpha)', 'rgba(255, 214, 0, #alpha)', 'rgba(255, 109, 0, #alpha)',
            'rgba(174, 234, 0, #alpha)', 'rgba(98, 0, 234, #alpha)', 'rgba(255, 171, 0, #alpha)', 'rgba(100, 221, 23, #alpha)', 'rgba(0, 184, 212, #alpha)'];


        let labels = jQuery.unique($rootScope.selectDevice.traffics.map(function (d) {
            return $scope.range.isTime ? d.timeFrom : moment(d.date, 'YYYY-MM-DD').format('D.M.YYYY');
        }));

        let useVehiclesIds = jQuery.unique($rootScope.selectDevice.traffics.map(function (d) {
            return d.typeVehicleId;
        }));

        $scope.filterVehicles = jQuery.grep($scope.vehicles, function (n) {
            return useVehiclesIds.indexOf(n.id) >= 0;
        });

        let datasets = [];
        for (let i = 0, vehicle; vehicle = $scope.filterVehicles[i]; i++) {
            let dataset = {
                label: vehicle.name,
                backgroundColor: color[vehicle.id].replace("#alpha", "0.3"),
                borderColor: color[vehicle.id].replace("#alpha", "1"),
                borderWidth: 2,
                data: []
            };

            let l = 0;
            for (let j = 0, traffic; traffic = $rootScope.selectDevice.traffics[j]; j++) {
                if (($scope.range.isTime && labels[l] !== traffic.timeFrom) || (!$scope.range.isTime && labels[l] !== moment(traffic.date, 'YYYY-MM-DD').format('D.M.YYYY'))) {
                    l++;
                    if (dataset.data.length < l) {
                        dataset.data.push(0);
                    }
                }
                if (traffic.typeVehicleId === vehicle.id) {
                    dataset.data.push($scope.range.isTime ? traffic.numberVehicleAverage : traffic.numberVehicle);
                }
            }
            datasets.push(dataset);
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
                responsive: true,
                onResize: function (chart, size) {
                    chart.options.legend.display = size.height > 240;
                    chart.update();
                },
                legend: {
                    position: 'bottom',
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
        $scope.markers = [];

        $scope.map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: config.DEFAULT_POSITION.LAT, lng: config.DEFAULT_POSITION.LNG},
            zoom: config.DEFAULT_ZOOM,
            minZoom: config.DEFAULT_ZOOM_MAX,
            zoomControl: true,
            mapTypeControl: false,
            scaleControl: false,
            streetViewControl: false,
            rotateControl: false,
            fullscreenControl: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
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
            let marker = new google.maps.Marker({
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
        let id = args.id;
        for (let i = 0, marker; marker = $scope.markers[i]; i++) {
            if (marker.id && marker.id === id && marker.infoWindow) {
                $scope.map.setCenter(marker.getPosition());
                $scope.map.setZoom(12);
                marker.infoWindow.open($scope.map, marker);
            } else {
                marker.infoWindow.close();
            }
        }
    });

    $rootScope.$on('setDefaultMap', function (event, args) {
        $scope.map.setCenter({lat: config.DEFAULT_POSITION.LAT, lng: config.DEFAULT_POSITION.LNG});
        $scope.map.setZoom(config.DEFAULT_ZOOM);
        $scope.closeInfoWindows();
    });

    $scope.closeInfoWindows = function () {
        for (let i = 0, marker; marker = $scope.markers[i]; i++) {
            marker.infoWindow.close();
        }
    };
});


app.factory('Device', function ($resource, config) {
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