<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Compartir ubicación</title>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400'; rel='stylesheet' type='text/css'>
    <link href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css'; rel='stylesheet' type='text/css'>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <style>
        body{
            font-family: Lato;
            background-image: url("http://pidafacil.com/images/background.jpg");
            background-repeat: no-repeat;
            padding: 2%;
        }

        #map{
            height: 350px;
            width: 100%;
            border:1px solid;
            color:black;
        }

        .controls {
            margin-top: 10px;
            border: 1px solid transparent;
            border-radius: 2px 0 0 2px;
            box-sizing: border-box;
            -moz-box-sizing: border-box;
            height: 32px;
            outline: none;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
        }

        #pac-input {
            background-color: #fff;
            font-family: Roboto;
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 300px;
        }

        #pac-input:focus {
            border-color: #4d90fe;
        }

        .pac-container {
            font-family: Roboto;
        }

        #type-selector {
            color: #fff;
            background-color: #4d90fe;
            padding: 5px 11px 0px 11px;
        }

        #type-selector label {
            font-family: Roboto;
            font-size: 13px;
            font-weight: 300;
        }

        #btnSave{
            text-align: center;
            margin-left: 40%;
            margin-top: 2%;
        }
    </style>
</head>
<body>    
    <div class="container">
        <input id="pac-input" class="controls" type="text" placeholder="Search Box">
        <div id="map"></div>
        <input type="hidden" id="destination">
        <input type="hidden" id="coordenadas">
        <div class="row">           
            <button class="btn btn-primary" id="btnSave">Guardar</button>
        </div>        
        <div class="row">
            <p style="color:white; padding-top:3px;">* Haga la búsqueda y luego arrastre el marcador hasta su ubicación exacta.</p>
        </div>
    </div>
</body>

<script type="text/javascript">

    var marker;
    function initAutocomplete(){
        var map = new google.maps.Map(document.getElementById('map'),{
            zoom: 13,
            center: new google.maps.LatLng(13.7023931, -89.2388219),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        // Create the search box and link it to the UI element.
        var input = document.getElementById('pac-input');
        var searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener('bounds_changed', function(){
            searchBox.setBounds(map.getBounds());
        });

        var markers = [];

        // [START region_getplaces]
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener('places_changed', function(){
            var places = searchBox.getPlaces();
            if(places.length == 0){
                return;
            }

            // Clear out the old markers.
            markers.forEach(function(marker){
                marker.setMap(null);
            });
            markers = [];

            // For each place, get the icon, name and location.
            var bounds = new google.maps.LatLngBounds();
            places.forEach(function(place){

                $("#coordenadas").val(place.geometry.location);
                localStorage.setItem('coords', place.geometry.location);

                if(!place.geometry) {
                    console.log("Returned place contains no geometry");
                    return;
                }

                // Create a marker for each place.
                /*markers.push(new google.maps.Marker({
                    map: map,
                    title: place.name,
                    draggable: true,
                    zoom: 14,
                    animation: google.maps.Animation.DROP,
                    position: place.geometry.location
                }));*/

                var marcador = new google.maps.Marker({
                    map: map,
                    zoom: 13,
                    title: place.name,
                    draggable: true,
                    animation: google.maps.Animation.DROP,
                    position: place.geometry.location
                });
                google.maps.event.addListener(marcador, 'dragend', function (event) {
                    console.log(this.getPosition().lat());
                    console.log(this.getPosition().lng());
                    $("#coordenadas").val(this.getPosition().lat()+','+this.getPosition().lng());
                    localStorage.setItem('coords', $("#coordenadas").val());
                });

                if(place.geometry.viewport){
                    //Only geocodes have viewport.
                    bounds.union(place.geometry.viewport);
                }else{
                    bounds.extend(place.geometry.location);
                }
            });
            map.fitBounds(bounds);
        });
        // [END region_getplaces]
    }

    $("#btnSave").click(function(){
        window.close();
    });
</script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlW4UtDGbq8T5W3RkahGAAh6mtlsOf0_Q&libraries=places&callback=initAutocomplete"></script>
</html>