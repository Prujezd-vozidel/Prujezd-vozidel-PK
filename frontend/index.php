<!doctype html>
<html ng-app="pvpk" class="no-js" lang="cs">
<head>
    <meta charset="utf-8">
    <title>Průjezd vozidel - Plzeňský kraj</title>
    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="description" content="Zobrazení dat o průjezdu vozidel pro Plzeňský kraj">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" href="./assets/img/favicon.png">
    <link rel="icon" href="./assets/img/favicon.png">

    <script>
        document.documentElement.className = document.documentElement.className.replace("no-js", "js");
    </script>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css"
          integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    <link rel="stylesheet" media="screen" href="./assets/css/main.css">


    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-route.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular-resource.min.js"></script>

    <script>
        <?php
        /* JEN PRO TESTOVANI, POZDEJI SE ODSTRANIT, A NAHRADIT NASDILENOU KNIHOVNOU */
        $base_url = 'http://localhost/pvpk/backend/public/api/v1';

        include_once '../backend/lib/generateToken.php';
        $token = generateToken();
        ?>
        var API_URL = '<?=$base_url ?>';
        var API_TOKEN = '<?=$token ?>';
    </script>

    <script src="./app.js"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
</head>
<body ng-controller="mainController" class="container-fluid">




<div id="loadingScreen" ng-show="showLoadingScreen">
    <div class="loading"></div>
</div>
<div class="row h-100">

    <section class="search col-12 col-sm-6 col-lg-3" id="search" ng-controller="searchController">

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
                           ng-change="search.location.length>2 && searchLocations(true)">
                </div>

                <div class="custom-control custom-checkbox mb-3">
                    <!-- ng-true-value="ofCourse" ng-false-value="iWish"  -->
                    <input type="checkbox" id="searchDirection" name="searchDirection" class="custom-control-input"
                           checked ng-model="search.direction" required ng-change="searchLocations()">
                    <label for="searchDirection" class="custom-control-label">Rozlišovat směr</label>
                </div>

                <div class="form-row">
                    <div class="form-group col">
                        <label for="searchFromDate">Období</label>
                        <input type="date" id="searchFromDate" name="fromDate"
                               class="form-control form-control-sm" ng-model="search.fromDate" required
                               ng-class="{ 'is-invalid': search.fromDate>search.toDate}">
                        <div class="invalid-feedback">
                            Tento datum musí být menší.
                        </div>
                    </div>

                    <div class="form-group col">
                        <label for="searchToDate" class="invisible">Období</label>
                        <input type="date" id="searchToDate" name="toDateTime"
                               class="form-control form-control-sm" ng-model="search.toDate" required
                               ng-class="{ 'is-invalid': search.fromDate>search.toDate}">
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

                <!--<input type="submit" value="Vyhledat" class="btn btn-primary btn-block"-->
                <!--ng-disabled="search.fromDate>search.toDate || search.fromTime>search.toTime">-->
            </form>


            <div class="result-locations mb-5 mt-5">
                <h5>Lokality</h5>

                <div class="list-group" ng-show="locations.length>0 && !showLocationsLoading">
                    <!-- class = active -->
                    <a href="" id="location-{{location.id}}"
                       class="list-group-item list-group-item-action flex-column align-items-start"
                       ng-repeat="location in locations"
                       ng-click="selectDevice(location.id)"
                       ng-class="{'active': deviceId == location.id}">

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

                <div ng-show="locations.length==0 && !showLocationsLoading">
                    <small class="form-text text-muted text-center">Žádná lokalita</small>
                </div>

                <div class="loading" ng-show="showLocationsLoading"></div>

            </div>

        </div>
        <footer class="text-center mb-2 mt-2 w-100">
            <small class="text-muted">2018 © FAV, ZČU</small>
        </footer>
    </section>


    <!--graph section-->
    <section class="graph col-12 col-sm-6 col-lg-3" id="graph" ng-show="$root.graphShow"
             ng-controller="graphController">

        <header class="mt-2">

            <h4>Grafy
                <button type="button" class="close" aria-label="Close" ng-click="$root.graphShow = !$root.graphShow">
                    <span aria-hidden="true">&times;</span>
                </button>
            </h4>
        </header>

        <form>
            <div class="form-group">
                <label for="searchVehicle">Vozidla</label>
                <select id="searchVehicle" class="custom-select custom-select-sm" ng-model="search.vehicle">
                    <option value="">Všechna vozidla</option>
                    <option ng-repeat="vehicle in vehicles" value="{{vehicle.id}}">{{vehicle.name}}</option>
                </select>
            </div>

        </form>

        <div class="loading"></div>

        <!--<canvas id="myChart" width="100%" height="60"></canvas>-->
        <!--<script>-->
        <!--var ctx = document.getElementById("myChart").getContext('2d');-->
        <!--var myChart = new Chart(ctx, {-->
        <!--type: 'bar',-->
        <!--data: {-->
        <!--labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange"],-->
        <!--datasets: [{-->
        <!--label: '# of Votes',-->
        <!--data: [12, 19, 3, 5, 2, 3],-->
        <!--backgroundColor: [-->
        <!--'rgba(255, 99, 132, 0.2)',-->
        <!--'rgba(54, 162, 235, 0.2)',-->
        <!--'rgba(255, 206, 86, 0.2)',-->
        <!--'rgba(75, 192, 192, 0.2)',-->
        <!--'rgba(153, 102, 255, 0.2)',-->
        <!--'rgba(255, 159, 64, 0.2)'-->
        <!--],-->
        <!--borderColor: [-->
        <!--'rgba(255,99,132,1)',-->
        <!--'rgba(54, 162, 235, 1)',-->
        <!--'rgba(255, 206, 86, 1)',-->
        <!--'rgba(75, 192, 192, 1)',-->
        <!--'rgba(153, 102, 255, 1)',-->
        <!--'rgba(255, 159, 64, 1)'-->
        <!--],-->
        <!--borderWidth: 1-->
        <!--}]-->
        <!--},-->
        <!--options: {-->
        <!--scales: {-->
        <!--yAxes: [{-->
        <!--ticks: {-->
        <!--beginAtZero: true-->
        <!--}-->
        <!--}]-->
        <!--}-->
        <!--}-->
        <!--});-->
        <!--</script>-->

    </section>


    <!--map section-->
    <!-- ng-class="textType" -->
    <section class="map col-12 col-sm-12" id="map"
             ng-class="{ 'col-lg-9': !$root.graphShow, 'col-lg-6': $root.graphShow }" ng-controller="mapController">

        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1326226.1771813703!2d11.996870042985256!3d49.51688547407959!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x470abf4bb3db6569%3A0x100af0f6614a850!2zUGx6ZcWIc2vDvSBrcmFq!5e0!3m2!1scs!2scz!4v1523814169200"
                width="100%" height="100%" frameborder="0" style="border:0" allowfullscreen></iframe>
    </section>


</div>

<div class="modal fade" id="modalExpiredToken" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Platnost webové aplikace vypršela</h5>
            </div>
            <div class="modal-body">
                <p>Pro obnovení platnosti stačí stisknout tlačítko <strong>Obnovit</strong>.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" ng-click="reloadApp()">Obnovit</button>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/js/bootstrap.min.js"
        integrity="sha384-uefMccjFJAIv6A+rW+L4AHf99KvxDjWSu1z9VI8SKNVmz4sk7buKt/6v9KI65qnm"
        crossorigin="anonymous"></script>


</body>
</html>