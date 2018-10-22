angular.module('pvpk')
  .component('graphAverageSpeed', {
    template: '<div><canvas id="graphAverageSpeed" class="graph-size mb-5"></canvas></div>',
    controller: ['$rootScope', '$scope', function ($rootScope, $scope) {
      $rootScope.$on('renderGraphAverageSpeed', function (event, args) {
        var canvas = document.getElementById('graphAverageSpeed').getContext('2d')

        if ($scope.graphLine) { $scope.graphLine.destroy() }

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
                  return tooltipItems.yLabel + ' km/h'
                }
              }
            }
          }
        })
      })
    }]
  })
