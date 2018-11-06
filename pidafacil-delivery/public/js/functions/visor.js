/**
* Created by lac on 07-10-15.
*/

function startTime(){
  var today=new Date();
  var h=today.getHours();
  var m=today.getMinutes();
  var s=today.getSeconds();
  // add a zero in front of numbers<10
  h=checkTime(h);
  m=checkTime(m);
  s=checkTime(s);
  //document.getElementById('txt').innerHTML=h+":"+m+":"+s;
  t=setTimeout(function(){startTime()},500);
}

function checkTime(i){
  if (i<10)
  {
    i="0" + i;
  }
  return i;
}

//Ajax para asignar orden a otra sucursal
function asigSucursal(id_pedido){
  $.ajax({
    url: 'asignar',
    type: 'post',
    dataType: 'json',
    data: {id_sucursal: $("#asignar_"+id_pedido).val(), id_order:id_pedido,id_rest:id_restaurante}
  })
  .done(function(result) {
    nuevosValores(result);
    $("#asignar_"+id_pedido).parent().parent().hide(1000, function(){ $(this).remove(); } );
  })
  .fail(function(result) {
    alert("No se pudo asignar esta orden a la sucursal");
    console.log(result);
  });
}

//Pasa nuevos valores a los filtros
function nuevosValores(stats)
{
  var url = window.location.href;
  if(url.split('?'))
  {
    var arrURL = url.split('?');
    if(arrURL.indexOf(1) != -1)
    {
      var arrGets = arrURL[1].split('&');
      if(arrGets.indexOf(0) != -1)
      {
        var arrFillter = arrGets[0].split('=');
      }
    }
  }

  $("#pending").html(stats.pending.fillter);
  $("#accepted").html(stats.accepted.fillter);
  $("#delivered").html(stats.delivered.fillter);
  $("#cancelled").html(stats.cancelled.fillter);
  $("#rejected").html(stats.rejected.fillter);
  $("#uncollectible").html(stats.uncollectible.fillter);

  if (arrFillter != undefined && arrFillter.indexOf(1) != -1) {
    if(arrFillter[1] == 1)
    {
      $("#delivery").html(stats.pending.delivery);
      $("#pickup").html(stats.pending.pickup);
    }else if(arrFillter[1] == 3)
    {
      $("#delivery").html(stats.accepted.delivery);
      $("#pickup").html(stats.accepted.pickup);
    }else if(arrFillter[1] == 5)
    {
      $("#delivery").html(stats.delivered.delivery);
      $("#pickup").html(stats.delivered.pickup);
    }else if(arrFillter[1] == 6)
    {
      $("#delivery").html(stats.cancelled.delivery);
      $("#pickup").html(stats.cancelled.pickup);
    }else if(arrFillter[1] == 7)
    {
      $("#delivery").html(stats.rejected.delivery);
      $("#pickup").html(stats.rejected.pickup);
    }else if(arrFillter[1] == 8)
    {
      $("#delivery").html(stats.uncollectible.delivery);
      $("#pickup").html(stats.uncollectible.pickup);
    }
  }
}


$(document).ready(function(){

  $(".asignar").change( function(){
    var id_elemento = $(this).data('target');
    var id_pedido = id_elemento.split("_");

    var nombre_sucursal = $("#asignar_" + id_pedido[2] + " option:selected").html();
    $("#sucursal_" + id_pedido[2]).html(nombre_sucursal);
  });

  //Ajax para aceptar orden
  $(".aceptar_order").on('click', function(){
    var $btn = $(this);
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    $btn.button('loading');
    $.ajax({
      url: 'forward',
      type: 'post',
      dataType: 'json',
      data: {idA: id_pedido[1],id_rest:id_restaurante}
    })
    .done(function(result) {
      nuevosValores(result);
      $("#aceptar_"+id_pedido[1]).parent().parent().hide(1000, function(){ $(this).remove(); } );
      location.reload();
    })
    .fail(function(result) {
      $btn.button('reset');
      alert("No se puede aceptar esta orden");
    });
  });

  //Ajax para cambiar de estado a, incobrable/cancelado/rechazada
  $(".ejectar_rejected").on('click', function(){
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    var comment = $("#comment_"+id_pedido[2]).val();
    var rejected = $("input:radio[name='rejected_"+id_pedido[2]+"']:checked").val();
    var motivoRechazo = $("#motivoRechazo_"+id_pedido[2]).val();
    var motivo = $("#motivoRechazo_"+id_pedido[2]+" option:selected").text();
    var url = $("#urlCancel_"+id_pedido[2]).val();
    $.ajax({
      url: url,
      type: 'post',
      dataType: 'json',
      data: {idA: id_pedido[2],comment:comment,rejected:rejected,id_rest:id_restaurante,motivoRechazo:motivoRechazo,motivo:motivo}
    })
    .done(function(result) {
      nuevosValores(result);
      $("#aceptar_"+id_pedido[2]).parent().parent().hide(1000, function(){ $(this).remove(); } );
      location.reload();
    })
    .fail(function(result) {
      alert("No se puede realizar acción");
      console.log(result);
    });
  });
  //Ajax para cambiar estado a Completada.
  $(".entregada_rejected").on('click', function(){
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    var comment = "";
    var rejected = 5;
    var motivoRechazo = "";
    $.ajax({
      url: 'cancel',
      type: 'post',
      dataType: 'json',
      data: {idA: id_pedido[1],comment:comment,rejected:rejected,id_rest:id_restaurante,motivoRechazo:motivoRechazo}
    })
    .done(function(result) {
      nuevosValores(result);
      $("#entregada_"+id_pedido[1]).parent().parent().hide(1000, function(){ $(this).remove(); } );
    })
    .fail(function(result) {
      //alert("No se puede realizar acción");
      console.log(result);
    });
  });
  //Cambiar estado a regitrado, para enviarlo al visor del restaurante.
  $(".ejectar_registrada").on('click', function(){
    var $btn = $(this);
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    var comment = $("#comment_"+id_pedido[2]).val();
    var rejected = $("#registrar_2_"+id_pedido[2]).val();
    var motivoRechazo = $("#motivoRechazo_"+id_pedido[2]).val();
    var motorista_id = $('#moto_'+id_pedido[2]).val();
    if(motorista_id > 0) {
      $btn.button('loading');
      $.ajax({
        url: 'restaurant-orders/cancel',
        type: 'post',
        dataType: 'json',
        data: {
          idA: id_pedido[2],
          comment: comment,
          rejected: rejected,
          id_rest: id_restaurante,
          motivoRechazo: motivoRechazo,
          motorista_id:motorista_id,
          borrarT:true
        }
      })
      .done(function (result) {
        nuevosValores(result);
        $("#ejectar_registrada_" + id_pedido[2]).parent().parent().hide(1000, function () {
          $(this).remove();
        });
      })
      .fail(function (result) {
        $btn.button('reset');
        alert("No se pudo realizar acción, por favor intenta de nuevo");
      });
    }else{
      alert("Seleccione un motorista antes de enviarlo al restaurante");
    }
  });


  //hacer visible opciones para rechazar pedidos
  $(".rejected_7_radio").on('click', function(){
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    $("#divMotivo_"+id_pedido[2]).show(1000);
  });
  //hacer ocultar opciones para rechazar pedidos
  $(".rejected_6_radio").on('click', function(){
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    $("#divMotivo_"+id_pedido[2]).hide(1000);
  });
  //hacer ocultar opciones para rechazar pedidos
  $(".rejected_8_radio").on('click', function(){
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    $("#divMotivo_"+id_pedido[2]).hide(1000);
  });


  /*Buscar*/
  $('#buscar').keypress(function(e){
    if(e.which == 13){
      busqueda();
    }
  });
  $("#btnBuscar").click(function(e){
    e.preventDefault();

    busqueda();
  });

  //tiempo.
  var tdTimers = $(".timercont");
  tdTimers.each(function(key, value){
    var id = $(value).attr('data-idorden');
    var url = $("#urltime_"+id).val();
    $.ajax({
      url: url,
      type: 'post',
      dataType: 'json',
      data: {id:id}
    })
    .done(function(result) {
      setInterval(function(){
        var da = result[0].created_at;
        var ultimo = new Date(da.substr(0,4)+"/"+da.substr(5,2)+"/"+da.substr(8,2)+" "+da.substr(11,8));
        var actual = new Date();
        var diff=new Date(actual - ultimo);
        var hora=0;
        var minutos=0;
        var segundos=0;
        if(diff.getUTCHours() <= 9){
          hora = "0"+diff.getUTCHours();
        }else{
          hora = diff.getUTCHours();
        }

        if(diff.getUTCMinutes() <= 9){
          minutos = "0"+diff.getUTCMinutes();
        }else{
          minutos = diff.getUTCMinutes();
        }

        if(diff.getUTCSeconds() <= 9){
          segundos = "0"+diff.getUTCSeconds();
        }else{
          segundos = diff.getUTCSeconds();
        }
        $(value).attr("data-timer", hora+":"+minutos+":"+segundos);
      },1000)
    })
    .fail(function(result) {
      console.log(result);
      $(value).attr("data-timer","Error con el conteo");
    });
  });

  //Agregar una observación a la orden
  $(".add_note").on('click', function(){
    var $btn = $(this);
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    var motivo = $("#motivoObservacion_"+id_pedido[2]+" option:selected" ).val();
    var comentario = $("#comentObservacion_"+id_pedido[2]+"").val();

    if(motivo != 'none') {
      $btn.button('loading');
      $.ajax({
        url: 'restaurant-orders/comment',
        type: 'post',
        dataType: 'json',
        data: {
          orden_id: id_pedido[2],
          motivo: motivo,
          comment: comentario
        }
      })
      .done(function (result) {
        alert("Observación agregada correctamente.");
        location.reload();
      })
      .fail(function (result) {
        $btn.button('reset');
        alert("No se pudo realizar la acción, por favor intenta de nuevo");
      });
    }else{
      alert("Debes seleccionar un motivo de la observación.");
    }
  });

  $(".note_by").on('click', function(){
    var $btn = $(this);
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    
    if(id_pedido != '') {
      $.ajax({
        url: 'restaurant-orders/total_obs',
        type: 'post',
        dataType: 'json',
        data: {
          orden_id: id_pedido[2]
        }
      })
      .done(function (result) {
        $("#num_total_"+id_pedido[2]+"").text(result);
      })
      .fail(function (result) {
        $btn.button('reset');
        alert("No se pudo realizar la acción, por favor intenta de nuevo");
      });
    }else{
      alert("Error con el número de orden.");
    }
  });

  $(".ver_obs").on('click', function(){
    var $btn = $(this);
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    
    if(id_pedido != '') {
      $.ajax({
        url: 'restaurant-orders/observaciones',
        type: 'post',
        dataType: 'json',
        data: {
          orden_id: id_pedido[2]
        }
      })
      .done(function (result) {
        $("#contenedorTabla_"+id_pedido[2]+"").html(result);
      })
      .fail(function (result) {
        $btn.button('reset');
        alert("No se pudo realizar la acción, por favor intenta de nuevo");
      });
    }else{
      alert("Error con el número de orden.");
    }
  });

  /*OBSERVACIONES EN CALL CENTER*/
  //Agregar una observación a la orden
  $(".add_note2").on('click', function(){
    var $btn = $(this);
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    var motivo = $("#motivoObservacion_"+id_pedido[2]+" option:selected" ).val();
    var comentario = $("#comentObservacion_"+id_pedido[2]+"").val();

    if(motivo != 'none') {
      $btn.button('loading');
      $.ajax({
        url: 'comment',
        type: 'post',
        dataType: 'json',
        data: {
          orden_id: id_pedido[2],
          motivo: motivo,
          comment: comentario
        }
      })
      .done(function (result) {
        alert("Observación agregada correctamente.");
        location.reload();
      })
      .fail(function (result) {
        $btn.button('reset');
        alert("No se pudo realizar la acción, por favor intenta de nuevo");
      });
    }else{
      alert("Debes seleccionar un motivo de la observación.");
    }
  });

  $(".note_by2").on('click', function(){
    var $btn = $(this);
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    
    if(id_pedido != '') {
      $.ajax({
        url: 'total_obs',
        type: 'post',
        dataType: 'json',
        data: {
          orden_id: id_pedido[2]
        }
      })
      .done(function (result) {
        $("#num_total_"+id_pedido[2]+"").text(result);
      })
      .fail(function (result) {
        $btn.button('reset');
        alert("No se pudo realizar la acción, por favor intenta de nuevo");
      });
    }else{
      alert("Error con el número de orden.");
    }
  });

  $(".ver_obs2").on('click', function(){
    var $btn = $(this);
    var id_elemento = $(this).attr('id');
    var id_pedido = id_elemento.split("_");
    
    if(id_pedido != '') {
      $.ajax({
        url: 'observaciones',
        type: 'post',
        dataType: 'json',
        data: {
          orden_id: id_pedido[2]
        }
      })
      .done(function (result) {
        $("#contenedorTabla_"+id_pedido[2]+"").html(result);
      })
      .fail(function (result) {
        $btn.button('reset');
        alert("No se pudo realizar la acción, por favor intenta de nuevo");
      });
    }else{
      alert("Error con el número de orden.");
    }
  });

});