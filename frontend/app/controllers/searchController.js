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