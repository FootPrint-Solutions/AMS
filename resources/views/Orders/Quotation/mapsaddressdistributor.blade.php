<style type="text/css">
    #MapShowMarkerDistributor {
        width: 100%;
        height: 350px;
    }
</style>

<h6>Our Distributor Partner</h6>
<div id="MapShowMarkerDistributor"></div>
<script>
    var map;
    var markers = [];
    var infoWindow;
    var locationSelect;
    var latititudeCustomer = <?php echo $latitude; ?>;
    var longitudeCustomer = <?php echo $longitude; ?>;
    var customerMarker; // ini buat marker customer
    var circle;

    function initMap() {
        var myOptions = {
            zoom: 13,
            center: {
                lat: latititudeCustomer,
                lng: longitudeCustomer
            },
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById("MapShowMarkerDistributor"), myOptions);
        infoWindow = new google.maps.InfoWindow();

        var distributorData = <?php echo json_encode($datalatlong); ?>;
        for (var i = 0; i < distributorData.length; i++) {
            var lat = parseFloat(distributorData[i].latitude);
            var lng = parseFloat(distributorData[i].longitude);
            var latLng = new google.maps.LatLng(lat, lng);
            var marker = createMarker(latLng, distributorData[i].name, distributorData[i].address, '',
                distributorData[i].contact);
            markers.push(marker);
        }


        var customerLatLng = new google.maps.LatLng(latititudeCustomer, longitudeCustomer);
        customerMarker = new google.maps.Marker({
            position: customerLatLng,
            map: map,
            icon: {
                url: 'https://i.ibb.co/dprhv35/image-removebg-preview-1.png',
                scaledSize: new google.maps.Size(60, 60)
            },
            title: 'Lokasi Pelanggan'
        });
        markers.push(customerMarker);

        circle = new google.maps.Circle({
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
            map: map,
            center: customerLatLng,
            radius: 2000
        });
    }

    function createMarker(latlng, name, address, iconUrl, DisributorPhone) {
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            icon: {
                url: iconUrl ||
                    'https://i.ibb.co/59CPMMK/png-transparent-white-and-green-house-house-symbol-home-icon-green-marker-s-building-text-triangle-1.png',
                scaledSize: new google.maps.Size(40, 60)
            }
        });

        var contentString = '<div><h4>' + name + '</h4><p>' + address + '</p>';
        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });

        marker.addListener('click', function() {
            var distance = google.maps.geometry.spherical.computeDistanceBetween(latlng, customerMarker
                .getPosition());
            var distanceInKm = (distance / 1000).toFixed(2);
            var infoContent = contentString + '<p>Distance to customer location : ' + distanceInKm +
                ' km</p> Contact Distributor : <a href=' + DisributorPhone +
                ' target=_blank>Contact Here </a> </div>';
            infowindow.setContent(infoContent);
            infowindow.open(map, marker);
        });

        return marker;
    }
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAlBnX9jmy3JurAGnyIAFNSyS7i5cgfzA&libraries=geometry&callback=initMap">
</script>
