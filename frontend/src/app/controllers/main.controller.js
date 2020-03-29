angular.module('pvpk')
  .controller('mainController', ['$rootScope', '$scope', '$location', '$window', 'config', function ($rootScope, $scope, $location, $window, config) {
    this.$onInit = function () {
      $scope.config = config
      $scope.showLoadingScreen = true
      $rootScope.loadingCount = 0
      $scope.isOnLine = true

      $rootScope.$watch('loadingCount', function (newValue, oldValue) {
        if (newValue !== oldValue && $rootScope.loadingCount === 0) {
          $scope.showLoadingScreen = false
        }
      })
    }

    $window.onload = function () {
      var params = $location.search()
      if (params.deviceId) {
        $rootScope.$emit('activeMarker', { id: params.deviceId })
      }

      if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('./sw.js')
      }
    }

    $rootScope.$on('$locationChangeSuccess', function (event, newUrl, oldUrl) {
      var params = $location.search()

      if (newUrl !== oldUrl && $scope.historyUrl) {
        if ($scope.historyUrl.q !== $scope.historyUrl.q || $scope.historyUrl.isDirection != params.isDirection) {
          $rootScope.$emit('setSearchFromUrl', null)
        }

        if ($scope.historyUrl.fromDate !== params.fromDate || $scope.historyUrl.toDate !== params.toDate ||
                    $scope.historyUrl.fromTime !== params.fromTime || $scope.historyUrl.toTime !== params.toTime) {
          $rootScope.$emit('setRangeFromUrl', null)
          if (params.deviceId) {
            $rootScope.$emit('infoLocation', { id: params.deviceId, direction: params.direction })
          }
        } else if (params.deviceId && ($scope.historyUrl.deviceId !== params.deviceId || $scope.historyUrl.direction !== params.direction)) {
          $rootScope.$emit('infoLocation', { id: params.deviceId, direction: params.direction })
          $rootScope.$emit('activeMarker', { id: params.deviceId })
        } else if (!params.deviceId && $scope.historyUrl.deviceId) {
          $rootScope.selectDevice = null
          $rootScope.$emit('setDefaultMap', null)
        }
      } else if (params.deviceId) {
        $rootScope.$emit('infoLocation', { id: params.deviceId, direction: params.direction })
      }

      $scope.historyUrl = $location.search()
    })

    $rootScope.handleErrorResponse = function (response) {
      var modalError = jQuery('#modalError')
      switch (response.status) {
        case 400:
          console.log('API ERROR 400')
          $scope.modalError = {
            title: 'Neplatný požadavek',
            body: 'Požadavek nemůže být vyřízen, poněvadž byl syntakticky nesprávně zapsán.',
            button: 'OK'
          }
          modalError.modal('show')
          break
        case 401:
          $scope.modalError = {
            title: 'Platnost webové aplikace vypršela',
            body: 'Pro obnovení platnosti stačí stisknout tlačítko Obnovit.',
            button: 'Obnovit',
            clickButton: $scope.reloadApp
          }
          modalError.modal({ backdrop: 'static', keyboard: false })
          break
        case 404:
          console.log('API ERROR 404')
          $scope.modalError = { title: 'Nenalezen', body: 'Záznam nebyl nalezen.', button: 'OK' }
          modalError.modal('show')
          break
        case 500:
          console.log('API ERROR 500')
          $scope.modalError = { title: 'Chyba', body: 'Chyba serveru. Zopakujte akci později.', button: 'OK' }
          modalError.modal('show')
          break
        case -1:
          console.log('API NOT CONNECTED')
          $scope.modalError = {
            title: 'Připojení k internetu',
            body: 'Nejste připojeni k internetu. Zkontrolujte připojení.',
            button: 'OK'
          }
          modalError.modal('show')
          break
        default:
          console.log('API UNKNOWN ERROR')
          $scope.modalError = { title: 'Neočekávaná chyba', body: 'Nastala neočekávaná chyba.', button: 'OK' }
          modalError.modal('show')
          break
      }
    }

    $scope.reloadApp = function () {
      $window.location.reload()
    }
  }])
