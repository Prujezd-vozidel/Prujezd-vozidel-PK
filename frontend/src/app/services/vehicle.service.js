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
    })
  }])
