//Codigo para reporte por restaurante, tipo pago y rango de fechas  - 06 abril
$("#generar_reporte_rest_pago").on('click', function(){
    $("#formularioExc").show();

    if($('#cuerpoT').length == 0){
    }else{
        $("#cuerpoT").empty();
    }            
    
    var motivo = $("#motivo_busqueda option:selected").val();
    
    if(motivo == "fecha"){
        var fecha1 = $("#datepicker11").val();
        var fecha2 = $("#datepicker22").val();

        $("#motivoiva").val(motivo);
        $("#fechaiva1").val(fecha1);
        $("#fechaiva2").val(fecha2);

        var caracter = "T";
        var fecha_ini = fecha1.replace(caracter,' ');
        var fecha_f = fecha2.replace(caracter,' ');
        var sumPago = 0;
        var sumIva = 0;
        var sumPagoF = 0;
        var totalIva = 0;

        $.ajax({
            url: 'datos_iva',
            type: 'post',
            dataType: 'json',
            data: {
               motivo : motivo,
               fecha_inicio : fecha_ini,
               fecha_fin : fecha_f  
            }
        })
        .done(function ( result ){
            console.log(result.entregadas);
            
            if(result.ordenes.length == ""){
                $("#tablaReporte>tbody").append("<tr><td colspan='10' style='font-size:15pt; text-align:center;'>No hay resultados de pedidos</td></tr>");
            }else{
                $.each(result.ordenes, function(index, value){                            
                    totalC = parseFloat(value.comision_restaurante) + parseFloat(value.comision_envio);
                    totalIva = parseFloat(value.pago_restaurante) * 0.13;
                    totalCIva = parseFloat(value.pago_restaurante) / 1.13;

                    $("#tablaReporte>tbody").append("<tr><td>"+ value.fecha +"</td><td>"+ value.restaurante +"</td><td>$" + value.pago_restaurante + "</td><td>$"+ totalIva.toFixed(2) +"</td><td>$"+ totalCIva.toFixed(2) +"</td></tr>");

                    sumPago = (sumPago + parseFloat(value.pago_restaurante));
                    sumIva = (sumIva + parseFloat(totalIva));
                    sumPagoF = (sumPagoF + parseFloat(totalCIva));
                });
                
                $("#tablaReporte>tbody").append("<tr><th style='text-align:right;'>Total</th><td>&nbsp;</td><td>$"+sumPago.toFixed(2)+"</td><td>$"+sumIva.toFixed(2)+"</td><td>$ "+sumPagoF.toFixed(2)+"</td></tr>");                        
            }
        })
        .fail(function (result){
            alert("Error al consultar los datos");
        });        
    }else if(motivo == "mes"){
        var mes = $("#meses1 option:selected").val();
        var anio = $("#anio1 option:selected").val();
        var rest = $("#restauranteSel option:selected").val();
        
        //$("#textoRest").text('* '+ $("#restauranteSel option:selected").text());                
        var sumatoria = 0;
        var sumPago = 0;
        var sumIva = 0;
        var sumPagoF = 0;
        var totalIva = 0;
        var totalIva = 0;

        $("#motivoiva").val(motivo);
        $("#mesiva").val(mes);
        $("#anioiva").val(anio);
        $("#restiva").val(rest);

        console.log(mes +' '+anio);

        $.ajax({
            url: 'datos_iva',
            type: 'post',
            dataType: 'json',
            data: {
               motivo : motivo,
               mes : mes,
               anio : anio,
               rest : rest                    
            }
        })
        .done(function ( result ){
            console.log(result);
            
            if(result.ordenes.length == ""){
                $("#tablaReporte>tbody").append("<tr><td colspan='10' style='font-size:15pt; text-align:center;'>No hay resultados de pedidos</td></tr>");
            }else{
                $.each(result.ordenes, function(index, value){                            
                    totalC = parseFloat(value.comision_restaurante) + parseFloat(value.comision_envio);
                    totalIva = parseFloat(value.pago_restaurante) * 0.13;
                    totalCIva = parseFloat(value.pago_restaurante) / 1.13;

                    $("#tablaReporte>tbody").append("<tr><td>"+ value.fecha +"</td><td>"+ value.restaurante +"</td><td>$" + value.pago_restaurante + "</td><td>$"+ totalIva.toFixed(2) +"</td><td>$"+ totalCIva.toFixed(2) +"</td></tr>");
                    
                    sumPago = (sumPago + parseFloat(value.pago_restaurante));
                    sumIva = (sumIva + parseFloat(totalIva));
                    sumPagoF = (sumPagoF + parseFloat(totalCIva));                            
                });

                $("#tablaReporte>tbody").append("<tr><th style='text-align:right;'>Total</th><td>&nbsp;</td><td>$"+sumPago.toFixed(2)+"</td><td>$"+sumIva.toFixed(2)+"</td><td>$ "+sumPagoF.toFixed(2)+"</td></tr>");
            }
        })
        .fail(function (result){
          alert("Error al consultar los datos");
        });
    }
});