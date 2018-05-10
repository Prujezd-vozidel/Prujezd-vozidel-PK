<!doctype html>
<html ng-app="pvpk" lang="cs">
<head>
    <meta charset="utf-8">
    <title>Průjezd vozidel - Plzeňský kraj</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="description" content="Zobrazení dat o průjezdu vozidel pro Plzeňský kraj">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="./assets/img/favicon.png">
    <link rel="icon" href="./assets/img/favicon.png">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
          integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
    <link rel="stylesheet" media="screen" href="./assets/css/main.css">

    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-route.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-resource.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-sanitize.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>

    <script>
        <?php
        /* JEN PRO TESTOVANI, POZDEJI SE ODSTRANIT, A NAHRADIT NASDILENOU KNIHOVNOU */
        $base_url = './../backend/public/api/v1';

        include_once '../backend/lib/generateToken.php';
        $token = generateToken();
        ?>
        var API_URL = '<?=$base_url ?>';
        var API_TOKEN = '<?=$token ?>';
    </script>

    <script src="./app.js"></script>
</head>
<body ng-controller="mainController" class="container-fluid">




<div id="loadingScreen" ng-show="showLoadingScreen">
    <h1 id="logo">
        <img src="./assets/img/favicon.png" alt="logo"> Průjezd vozidel
        <small class="text-muted">Plzeňský kraj</small>
    </h1>
    <div class="loading"></div>
    <noscript id="pvpk_noscript">Aplikace vyžaduje Javascript. Aktivujte Javascript a znovu načtěte tuto stránku.</noscript>
</div>

<div class="row h-100">

    <!--SEARCH section-->
    <section class="search col-12 col-sm-6 col-lg-3" id="search" ng-controller="searchController"
             ng-class="{ 'col-sm-12': $root.selectDevice==null, 'col-sm-6': $root.selectDevice!=null }">

        <div class="w-100 searchWrapper">
            <header class="mt-2">
                <h1>
                    <img src="./assets/img/favicon.png" alt="logo"> Průjezd vozidel
                    <small class="text-muted">Plzeňský kraj</small>
                </h1>
            </header>

            <form class="mb-4 mt-4">
                <div class="form-group">
                    <label for="searchLocation" class="h5">Hledání - lokalit</label>
                    <input type="search" id="searchLocation" name="location"
                           class="form-control form-control-sm" placeholder="Město, ulice, ..."
                           ng-model="search.location" required maxlength="255" autocomplete="off"
                           ng-change="searchLocations(true)">
                </div>

                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" id="searchDirection" name="searchDirection" class="custom-control-input"
                           checked ng-model="search.direction" required ng-change="searchLocations()">
                    <label for="searchDirection" class="custom-control-label">Rozlišovat směr</label>
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="searchFromDate">Období</label>
                        <input type="date" id="searchFromDate" name="fromDate"
                               class="form-control form-control-sm" ng-model="search.fromDate" required
                               ng-class="{ 'is-invalid': search.fromDate>search.toDate}"
                               max="{{maxDate | date:'yyyy-MM-dd'}}">
                        <div class="invalid-feedback">
                            Tento datum musí být menší.
                        </div>
                    </div>

                    <div class="form-group col">
                        <label for="searchToDate" class="invisible">Období</label>
                        <input type="date" id="searchToDate" name="toDateTime"
                               class="form-control form-control-sm" ng-model="search.toDate" required
                               ng-class="{ 'is-invalid': search.fromDate>search.toDate}"
                               max="{{maxDate | date:'yyyy-MM-dd'}}">
                        <div class="invalid-feedback">
                            Tento datum musí být vetší.
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="searchFromTime">Časové rozmezí dne</label>
                        <input type="time" id="searchFromTime" class="form-control form-control-sm"
                               ng-model="search.fromTime" required
                               ng-class="{'is-invalid': search.fromTime>search.toTime}">
                        <div class="invalid-feedback">
                            Tento čas musí být menší.
                        </div>
                    </div>

                    <div class="form-group col">
                        <label for="searchToTime" class="invisible">Časové rozmezí dne</label>
                        <input type="time" id="searchToTime" class="form-control form-control-sm"
                               ng-model="search.toTime" required
                               ng-class="{'is-invalid': search.fromTime>search.toTime}">
                        <div class="invalid-feedback">
                            Tento čas musí být vetší.
                        </div>
                    </div>
                </div>
            </form>

            <div class="result-locations mb-5 mt-5">
                <h5>Lokality</h5>

                <div class="list-group" ng-show="locations.length>0 && !showSearchLoading">
                    <a href="" id="location-{{location.id}}"
                       class="list-group-item list-group-item-action flex-column align-items-start"
                       ng-repeat="location in locations"
                       ng-click="selectDevice(location.id)"
                       ng-class="{'active': $root.selectDevice.id == location.id}">

                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{location.name}}</h6>
                            <small ng-show="search.direction">{{location.direction ==1 ? 'po směru': 'proti směru' }}
                            </small>
                        </div>
                        <small>
                            <address>{{location.street}}, {{location.town}}</address>
                        </small>
                    </a>
                </div>

                <div ng-show="locations.length==0 && !showSearchLoading">
                    <small class="form-text text-muted text-center">Žádná lokalita</small>
                </div>

                <div class="loading" ng-show="showSearchLoading"></div>
            </div>
        </div>
        <footer class="text-center mb-2 mt-2 w-100">
            <small class="text-muted">2018 © FAV, ZČU</small>
        </footer>
    </section>


    <!--INFO section-->
    <section class="info col-12 col-sm-6 col-lg-3" id="info" ng-show="$root.selectDevice!=null"
             ng-controller="infoController">

        <header class="mt-2">
            <h5>{{$root.selectDevice.name}}
                <button type="button" class="close" aria-label="Close" ng-click="infoClose()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </h5>
            <small>
                <address>{{$root.selectDevice.street}}, {{$root.selectDevice.town}}</address>
            </small>
        </header>

        <div class="loading" ng-show="showInfoLoading"></div>

        <h3 class="mt-5">Graf #1</h3>

        <h3 class="mt-5">Graf #2</h3>
        <div class="form-group">
            <label for="searchVehicle">Vozidla</label>
            <select id="searchVehicle" class="custom-select custom-select-sm" ng-model="search.vehicle">
                <option value="">Všechna vozidla</option>
                <option ng-repeat="vehicle in vehicles" value="{{vehicle.id}}">{{vehicle.name}}</option>
            </select>
        </div>

        <h3 class="mt-5">Graf #3</h3>

    </section>


    <!--MAP section-->
    <section class="map col-12 col-sm-12" id="map"
             ng-class="{ 'col-lg-9': $root.selectDevice==null, 'col-lg-6': $root.selectDevice!=null }"
             ng-controller="mapController">
    </section>
</div>

<div class="modal fade" id="modalError" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">{{modalError.title}}</h5>
            </div>
            <div class="modal-body">
                <p ng-bind-html="modalError.body"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="{{modalError.clickButton ? '' : 'modal'}}"
                        ng-click="modalError.clickButton && modalError.clickButton()">{{modalError.button}}
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"
        integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T"
        crossorigin="anonymous"></script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSx7hyAzQiG5uocJTeZgf1Z3lpDy4kpEk"
        type="text/javascript"></script>

<script type="text/javascript" src="./assets/libs/gmaps.min.js"></script>

</body>
</html>