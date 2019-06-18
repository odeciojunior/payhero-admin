$(document).ready(function(){

    updateValues();

    $("#company").on("change",function(){

        updateValues();
    });

    function updateValues(){

        $.ajax({
            method: "POST",
            url: "/dashboard/getvalues",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { company: $('#company').val() },
            error: function(){
                //
            },
            success: function(data){

                $(".moeda").html(data.currency);
                $("#pending_money").html(data.future_balance);
                $("#antecipation_money").html(data.future_balance);
                $("#available_money").html(data.available_balance);
                $("#total_money").html(data.available_balance);
            }

        });

    }

    Pusher.logToConsole = false;

    var pusher = new Pusher('339254dee7e0c0a31840', {
        cluster: 'us2',
        forceTLS: true
    });

    var channel = pusher.subscribe('channel-{!! \Auth::user()->id !!}');

    channel.bind('my-event', function(data) {
        alertPersonalizado('success','Nova venda realizada');
        clear_map_points();
        updateLastSales();
    });



    // var map = new ol.Map({
    //     target: 'mapa',
    //     layers: [
    //     new ol.layer.Tile({
    //         source: new ol.source.OSM()
    //     })
    //     ],
    //     view: new ol.View({
    //     center: ol.proj.fromLonLat([-47.7, -23.7]),
    //     zoom: 3
    //     })
    // });

    // var vectorLayers = new Array();

    // function add_map_point(lat, lng) {
    //     var vectorLayer = new ol.layer.Vector({
    //     source:new ol.source.Vector({
    //         features: [new ol.Feature({
    //         geometry: new ol.geom.Point(ol.proj.transform(
    //             [parseFloat(lng), parseFloat(lat)], 'EPSG:4326', 'EPSG:3857')),
    //         })]
    //     }),
    //     style: new ol.style.Style({
    //         image: new ol.style.Icon({
    //         anchor: [0.5, 35],
    //         anchorXUnits: "fraction",
    //         anchorYUnits: "pixels",
    //         src: "/assets/img/marker.png"
    //         })
    //     })
    //     });

    //     map.addLayer(vectorLayer);
    //     vectorLayers.push(vectorLayer);

    // }

    // function clear_map_points(){
        
    //     vectorLayers.forEach((vectorLayer) => {
    //     var features = vectorLayer.getSource().getFeatures();
    //     features.forEach((feature) => {
    //         vectorLayer.getSource().removeFeature(feature);
    //     });
    //     });
    //     vectorLayers.length = 0;
    // }

    // function updateLastSales(){

    //     $.ajax({
    //         method: "POST",
    //         url: "/dashboard/lastsales",
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         data: { empresa: $('#select_empresas').val() },
    //         error: function(){
    //             //
    //         },
    //         success: function(data){

    //             $('#last_sales_table').html(table_data);

    //             var table_data = "";
    //             $.each(data, function(i, item) {
    //                 table_data += "<tr>";
    //                 table_data += "<td>"+data[i].start_date+"</td>";
    //                 table_data += "<td>"+data[i].project+"</td>";
    //                 table_data += "<td>"+data[i].total_paid_value+"</td>";
    //                 table_data += "<td>"+data[i].payment_form+"</td>";
    //                 table_data += "</tr>"; 

    //                 if(data[i].ip != null){
    //                     $.ajax({
    //                         url : "https://ipapi.co/"+data[i].ip+"/json",
    //                         type : "GET",
    //                         success : function(response) {
    //                         add_map_point(response.latitude, response.longitude);
    //                         }
                
    //                     });
    //                 }
    //             });

    //             $('#last_sales_table').html(table_data);

    //         }

    //     });

    // }

    // updateLastSales();

});
