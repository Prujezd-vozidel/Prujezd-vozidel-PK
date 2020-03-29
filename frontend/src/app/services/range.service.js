angular.module('pvpk')
  .factory('Range', ['$resource', 'config', function ($resource, config) {
    return $resource(config.API_URL + '/range', null, {
      'get': {
        url: config.API_URL + '/range',
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'jwt': config.API_TOKEN
        }
      }
    })
  }])
