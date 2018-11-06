var socket = io();

socket.on("newOrders", function(data) {
  if (data.lenght !== 0) {
    var qty = 0;
    $("#content").html("");
    $.each(data, function(data, order){
        qty += 1;
        $("#content").append("<div class='order'><span class='order-code'>#"+order.code+"</span><span class='order-time'>"+order.time+"</span><p class='order-title'>"+order.restaurant+"</p><p class='order-service-type'>"+order.service+"</p></div>")
    });
    if (qty == 0) {
      $("#orders-qty").html("").append("No hay nuevas ordenes por el momento.");
    } else {
      $("#orders-qty").html("").append("Mostrando " + qty + " ordenes que no han sido aceptadas.");
    }
  }
});
