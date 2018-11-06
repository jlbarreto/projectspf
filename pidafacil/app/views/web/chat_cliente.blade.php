<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Atención al Cliente PidaFacil</title>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400'; rel='stylesheet' type='text/css'>
    <style>
        body{
            font-family: Lato;
            background-image: url("images/background.jpg");
            background-repeat: no-repeat;
        }

        #contenedorWA{
            padding: 5%;
            width: 80%;
            margin: 0px auto;
            height: 70%;
        }

        .wa, img{
            width: 40%;
            height: 135px;
            background-image: url("images/whatsapp.png");
            background-repeat: no-repeat;
            background-size: 75%;
            margin-left: 35%;
        }

        #map{
            height: 300px;
            width: 500px;
            border:1px solid;
            color:black;
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
    </style>
</head>
<body>
    <div id="contenedorWA">
        <div class="wa"></div>
        <br>
        <p style="text-align:center; font-size:20pt; color:white; font-weight:bold;">
            Escr&iacute;benos por whatsapp
            <br>
            @if(isset($numero))
                @foreach($numero as $num)
                     {{$num->num_atencion_cliente}}
                @endforeach
            @else
                +503 7787-4825
            @endif
        </p>
    </div>
    <div id="test">
        <input id="pac-input" class="controls" type="text" placeholder="Search Box">
        <div id="map"></div>
        <input type="hidden" id="destination">
    </div>
</body>
<script type="text/javascript">
    function initAutocomplete(){
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 13.7064502, lng: -89.2475361},
            zoom: 13,
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
                var icon = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                markers.push(new google.maps.Marker({
                    map: map,
                    icon: icon,
                    title: place.name,
                    position: place.geometry.location
                }));

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
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBlW4UtDGbq8T5W3RkahGAAh6mtlsOf0_Q&libraries=places&callback=initAutocomplete"></script>
</html>