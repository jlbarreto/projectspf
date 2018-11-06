<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Estado de Orden</title>
</head>
<body>
<div style="width: 640px; font-family: Arial, Helvetica, sans-serif; font-size: 12px; ">
    <div style="background-color: black; text-align: center; padding: 1em 0;">
        <img src="http://pf.wisedigitalmedia.com/images/logo.png" />
    </div>
    <h3><strong>Su orden, codigo {{ $bmail['order_cod'] }}</strong></h3>
    @if($bmail['status'] == 3) <p> Fue aceptada y se encuntra en proceso de entrega </p>
    @elseif($bmail['status'] == 6) <p>Fue cancelada</p>
    @elseif($bmail['status'] == 7) <p>Fue rechazada </p>
    @elseif($bmail['status'] == 8) <p>Es incobrable </p>
    @endif
    @if($bmail['status'] == 6 )
        <p>Motivo de cancelacion:
            {{$bmail['motivo']}}
        </p>
    @elseif($bmail['status'] == 7)
        <p>Motivo de Rechazo:
            @if($bmail['motivoRechazo'] == 1 || $bmail['motivoRechazo'] == 2)
                Zona fuera del area de cobertura.
            @elseif($bmail['motivoRechazo'] == 3 || $bmail['motivoRechazo'] == 4)
                Uno o varios productos seleccionados se encentran agotados.
            @endif
        </p>
        <p>
            {{$bmail['motivo']}}
        </p>
    @elseif($bmail['status'] == 8)
        <p>Porque es incobrable:
            {{$bmail['motivo']}}
        </p>
    @endif
    <p><a href="http://pf.wisedigitalmedia.com/user/orders">Orden codigo: {{ $bmail['order_cod'] }}</a></p>
    <div style="border-top: 1px solid #333; text-align:right; background-color: #CCCCCC">
        <div style="font-style: italic; margin: 5px 5px;">
            <span style="">powered by</span> <a href="http://wisedigitalmedia.com">Wise Digital Media</a>
        </div>
    </div>
</div>
</body>
</html>