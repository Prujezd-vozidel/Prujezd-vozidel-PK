angular.module('pvpk')
    .constant('config', {
        APP_NAME: 'PVPK',
        APP_VERSION: '1.3.0',
        API_URL: API_URL,
        API_TOKEN: API_TOKEN,
        DEFAULT_POSITION: {lat: 49.53, lng: 13.3},
        DEFAULT_ZOOM: 10,
        DEFAULT_ZOOM_MIN: 7,
        DEFAULT_RANGE_DATE_DAY: {from: -30, to: -1},
        DEFAULT_RANGE_TIME_HOUR: {from: 7, to: 16}
    });