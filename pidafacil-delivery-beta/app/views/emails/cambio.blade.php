<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Estado de Orden</title>
    <style>
        .number
        {
            text-align: right;
            border: solid 1px;
        }
    </style>
</head>
<body>
<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 12px; ">
    <div style=" background-position: center; background-image: url('http://pf.techmov.co/images/background.jpg'); background-size: 100%; text-align: center; padding: 1em 0; width: 100%;">
        <img style="max-height: 50px;" src="http://images.pf.techmov.co/pidafacil_logo.png" />
    </div>
    <h3 style="font-size: 2em;"><strong>Hola {{ $bmail['user_name'] }} tu orden código {{ $bmail['order_cod'] }} ha sido @if($bmail['status'] == 3)aceptada y se encuentra en proceso de entrega @elseif($bmail['status'] == 6) cancelada @elseif($bmail['status'] == 7) rechazada @elseif($bmail['status'] == 8) declarada como incobrable @endif . </strong></h3>
    <h4> Detalle de la orden </h4>
    <ul>
        <li>Tipo de servicio: {{ $bmail['tipoSer'] }}</li>
        <li>Total: ${{ $bmail['total'] }}</li>
    </ul>
    <h4>Productos</h4>
    <table style="border-collapse: collapse;">
        <th>
            <tr>
                <th style="width: 25%; border: solid 1px; ">Nombre</th>
                <th style="width: 25%; border: solid 1px; ">Cantidad</th>
                <th style="width: 25%; border: solid 1px; ">Precio</th>
            </tr>
        </th>
        @foreach($bmail['productos'] as $k => $v)
            <tr>
                <td style="border: solid 1px; ">{{ $v->product }}</td>
                <td style="text-align: right; border: solid 1px; ">{{ $v->quantity }}</td>
                <td style="text-align: right; border: solid 1px; ">$ {{ $v->unit_price }}</td>
            </tr>
        @endforeach
    </table>
    @if($bmail['status'] == 6 )
        <p style="font-size: 1.5em;">Motivo de cancelacion:
            {{$bmail['motivo']}}
        </p>
    @elseif($bmail['status'] == 7)
        <p style="font-size: 1.5em;">Motivo de Rechazo:
            @if($bmail['motivoRechazo'] == 1 || $bmail['motivoRechazo'] == 2)
                Zona fuera del area de cobertura.
            @elseif($bmail['motivoRechazo'] == 3 || $bmail['motivoRechazo'] == 4)
                Uno o varios productos seleccionados se encentran agotados.
            @endif
        </p>
        <p style="font-size: 1.5em;">
            {{$bmail['motivo']}}
        </p>
    @elseif($bmail['status'] == 8)
        <p style="font-size: 1.5em;" >¿Porque es incobrable?:
            {{$bmail['motivo']}}
        </p>
    @endif
    <p><a href="http://www.pidafacil.com/user/orders"  style="font-size: 2em;">Orden codigo: {{ $bmail['order_cod'] }}</a></p>
    <div style="border-top: 1px solid #333; text-align:right; background-color: #CCCCCC">
        <div style="font-style: italic; margin: 5px 5px;">
            <a href="http://pidafacil.com/soporte" target="chat" onclick="window.open('', 'chat', 'top=10px,left=20px,width=600px,height=450px')">Atención al Cliente PidaFacil</a>
        </div>
    </div>
</div>
</body>
</html>