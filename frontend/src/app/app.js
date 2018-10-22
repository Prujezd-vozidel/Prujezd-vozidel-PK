
require('../assets/styles/sass/main.scss')

/* Bootstrap 4 */
// require('bootstrap/dist/js/bootstrap.min.js')
require('jquery')
// require('bootstrap/js/dist/alert')
// require('bootstrap/js/dist/button')
// require('bootstrap/js/dist/carousel')
// require('bootstrap/js/dist/collapse')
// require('bootstrap/js/dist/dropdown')
require('bootstrap/js/dist/modal')
// require('bootstrap/js/dist/popover')
// require('bootstrap/js/dist/scrollspy')
// require('bootstrap/js/dist/tab')
// require('bootstrap/js/dist/tooltip')
// require('bootstrap/js/dist/util')

/* AngularJS */
require('angular/angular.min')
require('angular-resource/angular-resource.min')
require('moment')
require('chart.js/dist/Chart.min')

angular.module('pvpk', ['ngResource'])

require('./app.config')

/* services */
require('./services/device.service')
require('./services/range.service')
require('./services/vehicle.service')

/* components */
require('./components/graph-average-speed.component')
require('./components/graph-number-vehicles.component')

/* controllers */
require('./controllers/main.controller')
require('./controllers/info.controller')
require('./controllers/search.controller')
require('./controllers/map.controller')
