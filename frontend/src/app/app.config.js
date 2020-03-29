angular.module('pvpk')
  .constant('config', {
    APP_NAME: 'PVPK',
    APP_VERSION: __VERSION__,
    API_URL: __API_URL__,
    API_TOKEN: __API_TOKEN__,
    DEFAULT_POSITION: { lat: 49.53, lng: 13.3 },
    DEFAULT_ZOOM: 10,
    DEFAULT_ZOOM_MIN: 7,
    DEFAULT_RANGE_DATE_DAY: { from: -30, to: -1 },
    DEFAULT_RANGE_TIME_HOUR: { from: 7, to: 16 }
  })

angular.module('pvpk')
  .config(['$httpProvider', function ($httpProvider) {
    $httpProvider.interceptors.push('HttpInterceptor')
  }])

angular.module('pvpk').factory('HttpInterceptor', ['$q', '$rootScope', '$injector',
  function ($q, $rootScope, $injector) {
    return {
      'request': function (config) {
        $rootScope.loadingCount++

        return config
      },
      'requestError': function (rejection) {
        if (canRecover(rejection)) {
          return responseOrNewPromise
        }
        return $q.reject(rejection)
      },
      'response': function (response) {
        $rootScope.loadingCount--

        return response
      },
      'responseError': function (rejection) {
        $rootScope.loadingCount--
        $rootScope.handleErrorResponse(rejection)

        if (canRecover(rejection)) {
          return responseOrNewPromise
        }
        return $q.reject(rejection)
      }
    }
  }
])
