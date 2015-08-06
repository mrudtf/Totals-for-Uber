(function($) {

    $(window).load(function() {
        $("#page-loading").fadeOut("slow");
    })

    $( document ).ready(function() {

        var positions = [
            [40.7033127, -73.979681], // NY
            [34.0204989, -118.4117325], // LA
            [18.4469284, 100.1933182], // SEA
            [8.8588589, 2.3470599], // Paris
            [37.5651, 126.98955], // Seoul
            [-26.5935356, 136.1055972], // Australia
            [-28.4792811, 24.6722268], // South Africa
            [-2.548926, 118.0148634], // Indonesia
            [-74.7131969, 0], // Antarctica
        ];

        function showGoogleMaps() {

            var position = positions[Math.floor(Math.random()*positions.length)];
            var latLng = new google.maps.LatLng(position[0], position[1]);

            var mapOptions = {
                zoom: 5,
                scrollwheel: false,
                disableDefaultUI: true,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                center: latLng,
                styles: [{"featureType":"all","elementType":"all","stylers":[{"gamma":"1.00"},{"saturation":"0"},{"visibility":"on"}]},{"featureType":"all","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"color":"#000000"},{"lightness":13}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#144b53"},{"lightness":14},{"weight":1.4}]},{"featureType":"administrative","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#08304b"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#0c4152"},{"lightness":5}]},{"featureType":"road","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#0b434f"},{"lightness":25}]},{"featureType":"road.highway","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"road.highway.controlled_access","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#0b3d51"},{"lightness":16}]},{"featureType":"road.arterial","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"road.local","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"color":"#146474"}]},{"featureType":"transit","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"transit.line","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#021019"}]}],
            };

            map = new google.maps.Map(document.getElementById('googlemaps'), mapOptions);

        }

        google.maps.event.addDomListener(window, 'load', showGoogleMaps);

        /**
         * Public or Private
         */
        $("a.pubpriv").on("click", function (e) {
            e.preventDefault();
            form = $(this);
            var csrf = form.attr("data-csrf");
            var utid = form.attr("data-utid");
            var status = form.attr("data-status");
            $.ajax({
                type: 'POST',
                url: form.attr("href"),
                cache: false,
                dataType: 'json',
                data: { _token: csrf, utid: utid, status: status },
                success: function (data) {
                    form.after('<div class="change-done"><i class="fa fa-check-circle"></i> Done!</div>');
                    form.css("display", "none");
                }
            })
        });

    });

})(jQuery);