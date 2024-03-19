<style type="text/css">
    #MapShowMarkerDistributor {
        width: 100%;
        height: 350px;
    }

    .info-window {
        background-color: #fff;
        padding: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        font-family: Arial, sans-serif;
    }

    .info-window h4 {
        margin: 0 0 5px;
        font-size: 18px;
    }

    .info-window p {
        margin: 0 0 10px;
        font-size: 14px;
    }

    .info-window a {
        color: #007bff;
        text-decoration: none;
    }

    .info-window a:hover {
        text-decoration: underline;
    }

    .copy-button {
        margin-top: 10px;
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .copy-button:hover {
        background-color: #0056b3;
    }

    .copy-button-green {
        margin-top: 10px;
        background-color: #28a745;
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 5px;
        cursor: pointer;
    }

    .copy-button-green:hover {
        background-color: #218838;
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
                distributorData[i].contact, distributorData[i].id);
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

    function createMarker(latlng, name, address, iconUrl, DisributorPhone, Id) {
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            icon: {
                url: iconUrl ||
                    'https://i.ibb.co/59CPMMK/png-transparent-white-and-green-house-house-symbol-home-icon-green-marker-s-building-text-triangle-1.png',
                scaledSize: new google.maps.Size(40, 60)
            }
        });

        var contentString = '<div class="info-window"><h4>' + name + '</h4><p>' + address + '</p>';
        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });

        marker.addListener('click', function() {
            var distance = google.maps.geometry.spherical.computeDistanceBetween(latlng, customerMarker
                .getPosition());
            var distanceInKm = (distance / 1000).toFixed(2);
            var infoContent = contentString + '<p>Distance to customer location : ' + distanceInKm +
                ' km</p> Contact Distributor :  62' + DisributorPhone +
                '&nbsp;&nbsp;</div><button class="copy-button" onclick="copyInfo()"><i class="fa fa-copy"></i> Copy Info</button>&nbsp;&nbsp;<a href="62' +
                DisributorPhone +
                '"  target="_blank" class="copy-button-green"><i class="fa fa-phone"></i> Contact</a>&nbsp;&nbsp;<label><input class="form-check-input" type="checkbox" name="CheckDistributor[]" value="' +
                Id + '"> Choose this distributor </label>';
            infowindow.setContent(infoContent);
            infowindow.open(map, marker);
        });

        return marker;
    }

    function copyInfo() {
        var range = document.createRange();
        range.selectNode(document.querySelector('.info-window'));
        window.getSelection().removeAllRanges();
        window.getSelection().addRange(range);
        document.execCommand('copy');
        window.getSelection().removeAllRanges();
        alert('Info copied to clipboard!');
    }
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCAlBnX9jmy3JurAGnyIAFNSyS7i5cgfzA&libraries=geometry&callback=initMap">
</script>
