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