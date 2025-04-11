<style>
    #MapsAddressFinderModal {
        height: 400px;
        width: 100%;
        margin-bottom: 20px;
    }

    .pac-container {
        z-index: 10000 !important;
    }
</style>
<div class="modal fade" id="modalAddressFinder" tabindex="-1" aria-labelledby="myLargeModalLabel" style="display: none;"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myLargeModalLabel">Address Finder</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control mb-1" placeholder="Search your address here..."
                    name="AddressSearchColumnModal" id="AddressSearchColumnModal">
                <div id="MapsAddressFinderModal"></div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnCloseModalAddresFinder" class="btn btn-success"
                    data-bs-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>


<script>
    var map;
    var marker;

    function initMap() {

        var existingLat = parseFloat(document.getElementById('Latitude').value);
        var existingLng = parseFloat(document.getElementById('Longitude').value);

        if (isNaN(existingLat) || isNaN(existingLng)) {
            existingLat = -6.8837859188198784;
            existingLng = 107.5403487263912;
        }

        map = new google.maps.Map(document.getElementById('MapsAddressFinderModal'), {
            center: {
                lat: existingLat,
                lng: existingLng
            },
            zoom: 15
        });

        var input = document.getElementById('AddressSearchColumnModal');
        var autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.bindTo('bounds', map);


        marker = new google.maps.Marker({
            map: map,
            draggable: true,
            position: {
                lat: existingLat,
                lng: existingLng
            },
            visible: true
        });

        var geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            'location': {
                lat: existingLat,
                lng: existingLng
            }
        }, function(results, status) {
            if (status === 'OK' && results[0]) {
                var address = results[0].formatted_address;
                document.getElementById('AddressSearchColumnModal').value = address;
                document.getElementById('AddressSearchColumn').value = address;
            } else {
                console.error('Geocoder failed due to: ' + status);
            }
        });

        autocomplete.addListener('place_changed', function() {
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                console.error("Place details not found");
                return;
            }

            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            marker.setPosition(place.geometry.location);
            marker.setVisible(true);


            var address = place.formatted_address;
            var latitude = place.geometry.location.lat();
            var longitude = place.geometry.location.lng();


            document.getElementById('AddressSearchColumnModal').value = address;
            document.getElementById('AddressSearchColumn').value = address;
            document.getElementById('Latitude').value = latitude;
            document.getElementById('Longitude').value = longitude;
        });


        google.maps.event.addListener(marker, 'dragend', function() {
            var position = marker.getPosition();
            map.panTo(position);


            var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'location': position
            }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        var address = results[0].formatted_address;
                        var latitude = position.lat();
                        var longitude = position.lng();


                        document.getElementById('AddressSearchColumnModal').value = address;
                        document.getElementById('AddressSearchColumn').value = address;
                        document.getElementById('Latitude').value = latitude;
                        document.getElementById('Longitude').value = longitude;
                    }
                } else {
                    console.error('Geocoder failed due to: ' + status);
                }
            });
        });
    }

    function openAddressModal() {
        $('#modalAddressFinder').modal('show');
        setTimeout(function() {
            $("#AddressSearchColumnModal").focus();
        }, 3000);
    }

    $("#AddressSearchColumn").on("click", function() {
        openAddressModal();
    });

    $("#btnAddress").on("click", function() {
        openAddressModal();
    });
</script>
<script async
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBR3Yg_71CuguqVxUXnUmxGI1pEPr4Cqmg&libraries=places&callback=initMap">
</script>
