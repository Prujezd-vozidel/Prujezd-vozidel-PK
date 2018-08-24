angular.module('pvpk')
    .component('graphNumberVehicles', {
        template: '<div><canvas id="graphNumberVehicles" class="graph-size mb-5"></canvas></div>',
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