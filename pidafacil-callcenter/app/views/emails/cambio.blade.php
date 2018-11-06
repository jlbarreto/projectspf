<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Orden PidaFacil</title>
</head>
<body style="background-color: #F6F6F6; padding: 10px;">
    <div style="background-color: #FFF; font-family: Arial, Helvetica, sans-serif; font-size: 12px; padding:10px; border-radius: 10px;">
        <img src="http://images.pf.techmov.co/pidafacil_logo.png" style="width:150px; height:80px; "/>        <p style="color:#474843; font-size: 1.5em;">
          <strong>Hola {{ $bmail['user_name'] }},</strong> tu orden <strong>#{{ $bmail['order_cod'] }}</strong> ha sido 
            @if(($bmail['status'] == 2 || $bmail['status'] == 3) && $bmail['service_type'] !== 2)
              aceptada y se encuentra en proceso de entrega.
            @elseif($bmail['status'] == 3 && $bmail['service_type'] == 2)
              aceptada, puedes recoger tu pedido a la hora indicada.
            @elseif($bmail['status'] == 4)
              despachada.
            @elseif($bmail['status'] == 5)
              completada.
            @elseif($bmail['status'] == 6)
              cancelada.
            @elseif($bmail['status'] == 7)
              rechazada.
            @elseif($bmail['status'] == 8)
              declarada como incobrable.
            @endif</p>
        @if($bmail['status'] == 3 )
          <p style="color:#474843; font-size: 1.5em;">Tipo de servicio: {{ $bmail['tipoSer'] }}.</p>
          <table style="border-collapse: collapse; margin-bottom: 30px;">
              <tr>
                  <th align="left" style="background-color: #C2CBCE; color:#474843; width: 25%; border: solid 1px; font-size: 1.5em; padding-left:5px;">Producto</th>
                  <th align="left" style="background-color: #C2CBCE; color:#474843; width: 25%; border: solid 1px; font-size: 1.5em; padding-left:5px;">Cantidad</th>
                  <th align="left" style="background-color: #C2CBCE; color:#474843; width: 25%; border: solid 1px; font-size: 1.5em; padding-left:5px;">Precio</th>
              </tr>
              @foreach($bmail['productos'] as $k => $v)
                  <tr>
                      <td style="border: solid 1px; font-size: 1.5em; padding-left:5px; color: #474843">{{ $v->product }}</td>
                      <td style="border: solid 1px; font-size: 1.5em; padding-left:5px; color: #474843">{{ $v->quantity }}</td>
                      <td style="border: solid 1px; font-size: 1.5em; padding-left:5px; color: #474843">${{ $v->unit_price }}</td>
                  </tr>
              @endforeach
          </table>
          <p style="color:#474843; font-size: 1.5em; line-height:5px;">Subtotal: ${{ $bmail['subtotal'] }}</p>
          @if($bmail['service_type'] !== 2)
            <p style="color:#474843; font-size: 1.5em; line-height:5px;">Costo de envio: ${{ $bmail['shipping_charge'] }}</p>
          @endif
          @if($bmail['card_charge'] !== '0.00')
            <p style="color:#474843; font-size: 1.5em; line-height:5px;">Cargo por tarjeta: ${{ $bmail['card_charge'] }}</p>
          @endif
          <p style="color:#474843; font-size: 1.5em; line-height:5px;"><strong>Total a pagar: ${{ $bmail['total'] }}</strong></p>
        @endif
        @if($bmail['status'] == 6 )
            <p style="color:#474843; font-size: 1.5em;"><strong>Motivo de cancelacion:</strong>
                {{$bmail['motivo']}}.
            </p>
        @elseif($bmail['status'] == 7)
        <p style="color:#474843; font-size: 1.5em;"><strong>Motivo de Rechazo:</strong>
            @if($bmail['motivoRechazo'] == 1 || $bmail['motivoRechazo'] == 2)
                Tu dirección esta fuera de nuestra area de cobertura. Para mayor información entra a <a href="http://www.pidafacil.com/zones"  style="color:blue;font-size:1.5em;font-size:1em;text-decoration:none;border-bottom:1.5px solid;">www.pidafacil.com/zones</a>
            @elseif($bmail['motivoRechazo'] == 3 || $bmail['motivoRechazo'] == 4)
                Uno o varios de tus productos seleccionados se han agotado en el restaurante.
            @endif
        </p>
        <p style="color:#474843; font-size: 1.5em;">
            {{$bmail['motivo']}}
        </p>
        @elseif($bmail['status'] == 8)
            <p style="color:#474843; font-size: 1.5em;" ><strong>¿Porque es incobrable?:</strong>
                {{$bmail['motivo']}}
            </p>
        @endif
        <p style="margin-top: 30px;"><a href="http://www.pidafacil.com/user/orders"  style="color:blue; font-size:1em; font-size:1.5em; text-decoration:none; border-bottom:1.5px solid;">Ver tu orden en pidafacil.com</a></p>
    </div>
</body>
</html>
