{{-- Favicon --}}
<link rel="shortcut icon" href="/img/logos/32x32.png">

{{-- Fontfamily --}}
<link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,400;0,500;0,700;0,900;1,400;1,500;1,700&amp;display=swap"
    rel="stylesheet">

{{-- Bootstrap CSS --}}
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

{{-- Custom CSS --}}
<link rel="stylesheet" href="{{ asset('css/custom.css') }}">

{{-- jQuery --}}
<script src="{{ asset('/js/jquery-3.7.1.min.js') }}"></script>

{{-- Bootstrap Core JS --}}
<script src="{{ asset('/js/bootstrap.bundle.min.js') }}"></script>
<style>
    #live-tracking {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin-bottom: 1rem;
        margin-top: 1rem;
    }

    .tracking-title {
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .tracking-map {
        width: 100%;
        height: 500px;
    }

    .tracking-map-container {
        border: 1px dashed #000;
        width: 100%;
        /* padding */
        padding: 1rem;
    }

    #loading-tracking {
        display: inline-block;
        margin-left: 1rem;
    }
</style>

{{-- Live Tracking --}}
<div id="live-tracking">
    <h1 class="tracking-title">Live Tracking <div id="loading-tracking"></div>
    </h1>
    <div class="tracking-map-container">
        <div id="map" class="tracking-map"></div>
    </div>

    {{-- button refresh manual to get the latest location --}}
    <button class="btn btn-primary mt-3" onclick="getLatestLocation()">Refresh</button>
</div>


{{-- lat lng start --}}
<input type="hidden" name="latitude_start" id="latitude_start" value="{{ $tracking->latitude_start }}">
<input type="hidden" name="longitude_start" id="longitude_start" value="{{ $tracking->longitude_start }}">

{{-- lat lng end --}}
<input type="hidden" name="latitude_end" id="latitude_end" value="{{ $tracking->latitude_end }}">
<input type="hidden" name="longitude_end" id="longitude_end" value="{{ $tracking->longitude_end }}">

{{-- lat lng destination --}}
<input type="hidden" name="latitude_destination" id="latitude_destination"
    value="{{ $tracking->latitude_destination }}">
<input type="hidden" name="longitude_destination" id="longitude_destination"
    value="{{ $tracking->longitude_destination }}">

{{-- lat lng current --}}
<input type="hidden" name="latitude_current" id="latitude_current" value="{{ $tracking->latitude_current }}">
<input type="hidden" name="longitude_current" id="longitude_current" value="{{ $tracking->longitude_current }}">


{{-- Google Maps API --}}
<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAlBnX9jmy3JurAGnyIAFNSyS7i5cgfzA&libraries=places&callback=initMap">
</script>

{{-- JavaScript --}}
<script>
    // google maps api
    function initMap() {
        // map options bandung ( -6.914689745762283, 107.61396939831049 )
        var options = {
            zoom: 19,
            center: {
                lat: parseFloat(document.getElementById('latitude_current').value),
                lng: parseFloat(document.getElementById('longitude_current').value)
            }
        }

        // new map
        var map = new google.maps.Map(document.getElementById('map'), options);

        // add marker start
        var marker_start = new google.maps.Marker({
            position: {
                lat: parseFloat(document.getElementById('latitude_start').value),
                lng: parseFloat(document.getElementById('longitude_start').value)
            },
            map: map,
            icon: {
                url: "{{ url('img/icon-marker/distributor-akikita.svg') }}",
                scaledSize: new google.maps.Size(60, 60),
            },
            optimized: false
        });
        // add marker end
        var marker_end = new google.maps.Marker({
            position: {
                lat: parseFloat(document.getElementById('latitude_end').value),
                lng: parseFloat(document.getElementById('longitude_end').value)
            },
            map: map,
            icon: {
                url: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
                scaledSize: new google.maps.Size(60, 60),
            },
        });

        // add marker destination
        var marker_destination = new google.maps.Marker({
            position: {
                lat: parseFloat(document.getElementById('latitude_destination').value),
                lng: parseFloat(document.getElementById('longitude_destination').value)
            },
            map: map,
            icon: {
                url: "{{ url('img/icon-marker/customer-icon.svg') }}",
                scaledSize: new google.maps.Size(60, 60),
            },
            scaledSize: new google.maps.Size(60, 60)
        });

        // add marker current
        var marker_current = new google.maps.Marker({
            position: {
                lat: parseFloat(document.getElementById('latitude_current').value),
                lng: parseFloat(document.getElementById('longitude_current').value)
            },
            map: map,
            icon: {
                url: "{{ url('img/icon-marker/technician.svg') }}",
                scaledSize: new google.maps.Size(60, 60)
            }
        });

        // polyline path start to end
        var path = [{
                lat: parseFloat(document.getElementById('latitude_start').value),
                lng: parseFloat(document.getElementById('longitude_start').value)
            },
            {
                lat: parseFloat(document.getElementById('latitude_end').value),
                lng: parseFloat(document.getElementById('longitude_end').value)
            }
        ];

        // add info window
        var infoWindow = new google.maps.InfoWindow({
            content: '<h1>My Location</h1>'
        });
    }

    function getLatestLocation() {
        // loading maps
        document.getElementById('loading-tracking').innerHTML =
            '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        $.ajax({
            url: '/tracking/live/{{ $tracking->work_order_id }}',
            type: 'GET',
            success: function(response) {
                // set value lat lng current
                document.getElementById('latitude_current').value = response.tracking.latitude_current;
                document.getElementById('longitude_current').value = response.tracking
                    .longitude_current;

                // move marker current
                initMap();

                // remove loading maps
                document.getElementById('loading-tracking').innerHTML = '';
            }
        });
    }

    // auto refresh every 5 seconds
    setInterval(function() {
        getLatestLocation();
    }, 5000);
</script>
