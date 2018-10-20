angular.module('pvpk')
    .controller('infoController', ['$rootScope', '$scope', '$location', 'config', 'Device', 'Vehicle', 'Range', function ($rootScope, $scope, $location, config, Device, Vehicle, Range) {

        this.$onInit = function () {
            $rootScope.selectDevice = null;
            $scope.showInfoLoading = false;
            $scope.vehicles = [];
            $scope.urlExportCsv = null;
            $scope.directions = [
                {id: undefined, name: 'po směru i proti směru'},
                {id: 1, name: 'po směru'},
                {id: 2, name: 'proti směru'}];
            $scope.isLoadRange = false;

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
                isTime: params.isTime == 0 ? false : true,
                maxDate: $scope.range == null ? null : $scope.range.maxDate,
                minDate: $scope.range == null ? null : $scope.range.minDate
            };

            if (!$scope.isLoadRange) {
                Range.get(null, function (data) {
                    $scope.range.fromDate = moment.max(moment(data.last_date).add(config.DEFAULT_RANGE_DATE_DAY.from, 'd'), moment(data.first_date)).toDate();
                    $scope.range.toDate = moment.min(moment($scope.range.toDate), moment(data.last_date)).toDate();
                    $scope.range.maxDate = moment(data.last_date).toDate();
                    $scope.range.minDate = moment(data.first_date).toDate();
                    $scope.isLoadRange = true;
                }, function (response) {
                    console.log('Error api get Range');
                    $rootScope.handleErrorResponse(response);
                });
            }
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

        $scope.changeDirection = function () {

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
                            datasetAverageSpeed.data.push(0);
                        }
                    }
                    if (traffic.typeVehicleId === vehicle.id) {
                        datasetNumberVehicles.data.push($scope.range.isTime ? traffic.numberVehicleAverage : traffic.numberVehicle);
                        datasetAverageSpeed.data.push(traffic.speedAverage <= 0 ? 0 : traffic.speedAverage);
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
