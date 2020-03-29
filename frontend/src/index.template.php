<!doctype html>
<html ng-app="pvpk" lang="cs">
<head>
    <meta charset="utf-8">
    <title>Průjezd vozidel - Plzeňský kraj</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="description" content="Zobrazení dat o průjezdu vozidel pro Plzeňský kraj">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    include_once __TOKEN_GENERATOR_PATH__;
    $token = generateToken();
    ?>
    <script>
        var __API_TOKEN__ = '<?=$token ?>';
    </script>
</head>
<body ng-controller="mainController" class="container-fluid">

<div id="loadingScreen" ng-show="showLoadingScreen">
    <h1 id="logo">
        <img src="./assets/images/logo.png" alt="logo" width="20" height="18">
        &nbsp;Průjezd vozidel&nbsp;
        <small>Plzeňský kraj</small>
    </h1>
    <div class="loading"></div>
    <noscript id="noscript">Aplikace vyžaduje Javascript. Aktivujte Javascript a znovu načtěte tuto stránku.
    </noscript>
</div>

<div class="row h-100" ng-init="load()">

    <!--SEARCH section-->
    <section class="search col-12 col-lg-3" id="search" ng-controller="searchController">

        <div class="w-100 searchWrapper">
            <header class="mt-2">
                <h1>
                    <img src="./assets/images/logo.png" alt="logo" width="20" height="18">&nbsp;Průjezd vozidel&nbsp;
                    <small>Plzeňský kraj</small>
                </h1>
            </header>

            <div class="mb-4 mt-4">
                <div class="form-group">
                    <label for="searchLocation" class="h5">Hledání - lokalit</label>
                    <input type="search" id="searchLocation" name="location"
                           class="form-control form-control-sm" placeholder="Město, ulice, ..."
                           ng-model="search.q" required maxlength="255" autocomplete="off"
                           ng-change="searchLocations()"
                           ng-model-options="{debounce: 600}">
                </div>
                <div class="custom-control custom-checkbox mb-3">
                    <input type="checkbox" id="searchIsDirection" name="searchIsDirection" class="custom-control-input"
                           checked required
                           ng-model="search.isDirection"
                           ng-change="searchLocations()"
                           ng-model-options="{debounce: 600}">
                    <label for="searchIsDirection" class="custom-control-label">Rozlišovat směr</label>
                </div>
            </div>

            <div class="result-locations mb-4 mt-4">
                <h5>Lokality</h5>

                <div class="list-group" ng-show="locations.length>0 && !showSearchLoading">
                    <a href="" id="location-{{location.id}}"
                       class="list-group-item list-group-item-action flex-column align-items-start"
                       ng-repeat="location in locations"
                       ng-click="selectDevice(location.id,location.direction)"
                       ng-class="{'active': $root.selectDevice.id == location.id && (!$root.selectDevice.direction  || $root.selectDevice.direction ==location.direction)}">

                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">{{location.name}}</h6>
                            <small ng-show="search.isDirection">{{location.direction ==1 ? 'po směru': 'proti směru' }}
                            </small>
                        </div>
                        <address class="small">{{location.street}}, {{location.town}}</address>

                    </a>
                </div>

                <div class="form-text text-center small" ng-show="locations.length==0 && !showSearchLoading">
                    Žádná lokalita
                </div>

                <div class="loading" ng-show="showSearchLoading"></div>
            </div>
        </div>
        <footer class="text-center text-muted mb-2 mt-2 w-100 small">
            © 2018 FAV, ZČU • version: {{ config.APP_VERSION }}
        </footer>
    </section>


    <!--INFO section-->
    <section class="info col-12 col-lg-5" id="info" ng-show="$root.selectDevice!=null"
             ng-controller="infoController">

        <header class="mt-2">
            <h4>{{$root.selectDevice.name}}
                <button type="button" class="close" aria-label="Close" ng-click="infoClose()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </h4>
            <address>{{$root.selectDevice.street}}, {{$root.selectDevice.town}}</address>
        </header>

        <div class="form-inline mb-4 mt-2">
            <label for="selectDeviceDirection" class="=hidden"></label>
            <select id="selectDeviceDirection" class="custom-select custom-select-sm"
                    ng-model="$root.selectDevice.direction"
                    ng-change="changeDirection(direction.id)"
                    ng-options="direction.id as direction.name for direction in directions"
                    ng-model-options="{updateOn: 'default', allowInvalid: true, debounce: 600}">
            </select>
        </div>


        <div class="alert alert-warning" role="alert"
             ng-show="!(range.fromDate >= range.minDate && range.toDate <= range.maxDate && range.toDate >= range.minDate && range.fromDate <= range.maxDate)">

            Data jsou k dispozici jen v rosahu {{range.minDate | date:"dd.MM.yyyy"}} - {{range.maxDate| date:"dd.MM.yyyy"}}

        </div>


        <div class="mb-4 mt-4" ng-form="rangeForm">
            <div class="form-row">
                <div class="form-group col">
                    <label for="rangeFromDate">Období</label>
                    <input type="date" id="rangeFromDate"
                           class="form-control form-control-sm" ng-model="range.fromDate" required
                           ng-class="{ 'is-invalid': range.fromDate>range.toDate}"
                           ng-change="changeRange()"
                           ng-model-options="{updateOn: 'default', allowInvalid: true, debounce: 600}">
                    <div class="invalid-feedback">
                        Tento datum musí být menší.
                    </div>
                </div>

                <div class="form-group col">
                    <label for="rangeToDate" class="invisible">Období</label>
                    <input type="date" id="rangeToDate"
                           class="form-control form-control-sm" ng-model="range.toDate" required
                           ng-class="{ 'is-invalid': range.fromDate>range.toDate}"
                           ng-change="changeRange()"
                           ng-model-options="{updateOn: 'default', allowInvalid: true, debounce: 600}">
                    <div class="invalid-feedback">
                        Tento datum musí být vetší.
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col">
                    <label for="rangeFromTime">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" id="rangeIsTime" name="rangeIsTime" class="custom-control-input"
                                   checked required
                                   ng-model="range.isTime"
                                   ng-model-options="{debounce: 300}"
                                   ng-change="changeRange()">
                            <label for="rangeIsTime" class="custom-control-label">Zobrazit časové rozmezí dne</label>
                        </div>
                    </label>
                    <input type="time" id="rangeFromTime" class="form-control form-control-sm"
                           ng-model="range.fromTime" required
                           ng-class="{'is-invalid': range.fromTime>=range.toTime}"
                           ng-change="changeRange()"
                           ng-model-options="{debounce: 600}"
                           ng-show="range.isTime">
                    <div class="invalid-feedback" ng-show="range.isTime">
                        Tento čas musí být menší.
                    </div>
                </div>

                <div class="form-group col">
                    <label for="rangeToTime" class="invisible">Časové rozmezí dne</label>
                    <input type="time" id="rangeToTime" class="form-control form-control-sm"
                           ng-model="range.toTime" required
                           ng-class="{'is-invalid': range.fromTime>=range.toTime}"
                           ng-change="changeRange()"
                           ng-model-options="{debounce: 600}"
                           ng-show="range.isTime">
                    <div class="invalid-feedback" ng-show="range.isTime">
                        Tento čas musí být vetší.
                    </div>
                </div>
            </div>
        </div>

        <div class="loading" ng-show="showInfoLoading"></div>

        <div id="graphs" ng-show="$root.selectDevice!=null && $root.selectDevice.traffics.length>0 && !showInfoLoading">
            <h4 class="mt-4">{{range.isTime ? "Průměrná rychlost za den" : "Průměrná rychlost za jednotlivé dny"}}</h4>
            <graph-average-speed></graph-average-speed>

            <h4 class="mt-4">{{range.isTime ? "Počet vozidel za den" : "Průměrná rychlost za jednotlivé dny"}}</h4>
            <graph-number-vehicles></graph-number-vehicles>

            <div class="text-center">
                <a class="btn btn-dark" href="{{ urlExportCsv }}" role="button">Export CSV</a>
            </div>

            <div class="text-center mb-2 mt-2 w-100 small">
                zdroj dat: <a class="source-link" target="_blank" rel="noopener"
                              href="https://doprava.plzensky-kraj.cz">doprava.plzensky-kraj.cz</a>

            </div>
        </div>

        <div class="form-text text-center small"
             ng-show="$root.selectDevice && $root.selectDevice.traffics.length==0 && !showInfoLoading">
            Data nejsou k dispozici
        </div>
    </section>

    <!--MAP section-->
    <section class="map col-12" id="map"
             ng-class="{ 'col-lg-9': $root.selectDevice==null, 'col-lg-4': $root.selectDevice!=null }"
             ng-controller="mapController">
    </section>
</div>

<div class="modal fade" id="modalError" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{modalError.title}}</h5>
            </div>
            <div class="modal-body">
                <p>{{modalError.body}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="{{modalError.clickButton ? '' : 'modal'}}"
                        ng-click="modalError.clickButton && modalError.clickButton()">{{modalError.button}}
                </button>
            </div>
        </div>
    </div>
</div>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCSx7hyAzQiG5uocJTeZgf1Z3lpDy4kpEk"
        type="text/javascript"></script>

</body>
</html>
