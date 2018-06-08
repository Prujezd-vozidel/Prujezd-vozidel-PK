angular.module('pvpk')
    .factory('Device', ['$resource', 'config', function ($resource, config) {
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
    }]);