@extends('general.general')
@section('content')
<!-- Inicio pagina de impresion -->
@foreach($orders as $key=>$order)
  <div style="width:250px;border:1.5px solid black;">
    <table class="order-print" style="width:250px;border-collapse: collapse;" border="1">
      <tr>
        <td style="font-weight:bold;font-size:15px;font-family:Arial;">NO DE PEDIDO:</td>
        <td style="font-size:15px;font-family:Arial;" align="right">{{ $order->order_cod }}</td>
      </tr>
      <tr>
        <td style="font-weight:bold;font-size:15px;font-family:Arial;">FECHA:</td>
        <td style="font-size:15px;font-family:Arial;" align="right">{{ date('m/d/Y', strtotime($order->created_at)) }}</td>
      </tr>
      <tr>
        <td style="font-weight:bold;font-size:15px;font-family:Arial;">HORA:</td>
        <td style="font-size:15px;font-family:Arial;" align="right">{{ date('h:i A', strtotime($order->created_at)) }}</td>
      </tr>
      <tr>
        <td colspan="75%" style="font-weight:bold;font-size:15px;font-family:Arial;">NOMBRE DEL CLIENTE:</td>
      </tr>
      <tr align="right">
        <td colspan="75%" style="font-size:15px;font-family:Arial;">PIDAFACIL DELIVERY</td>
      </tr>
      <tr>
        <td colspan="75%" style="font-weight:bold;font-size:15px;font-family:Arial;">TELEFONO:</td>
      </tr>
      <tr align="right">
        <td colspan="75%" style="font-size:15px;font-family:Arial;">7787-4825</td>
      </tr>
      <tr>
        <td colspan="75%" style="font-weight:bold;font-size:15px;font-family:Arial;">FORMA DE PAGO:</td>
      </tr>
      <tr align="right">
        <td colspan="75%" style="font-size:15px;font-family:Arial;">EFECTIVO</td>
      </tr>
      <tr>
        <td colspan="75%" align="Center" style="font-weight:bold;font-size:15px;font-family:Arial;">PEDIDO</td>
      </tr>
      <!-- PRODUCTOS -->
      @foreach($order->products as $ke=>$products)
          @foreach($products->conditions as $k=>$condition)
              <?php $conditions = $condition; ?>
          @endforeach
          @foreach($products->ingredients as $k=>$v)
            <?php $ingredients = $v; ?>
          @endforeach
        <tr>
          <td colspan="75%" style="border-top:2px solid black;font-size:15px;font-family:Arial;">{{ $products->quantity }}x {{ Str::upper($products->product) }}</td>
        </tr>
        <!-- CONDICIONES -->
        @if(isset($products->conditions) && count($products->conditions) > 0)
            @foreach($products->conditions as $k=>$condition)
                @for($i = 0; $i < count($condi);$i++)
                    @if($condi[$i]->condition_id == $condition->condition_id)
                        <tr>
                          <td style="font-size:15px;font-family:Arial;">{{ Str::upper($condition->condition) }}</td>
                          <td style="font-size:15px;font-family:Arial;" align="right">{{ Str::upper($condition->condition_option) }}</td>
                        </tr>
                    @endif
                @endfor
            @endforeach
        @else
          <tr>
            <td style="font-size:15px;font-family:Arial;" colspan="75%">NO TIENE CONDICIONES.</td>
          </tr>
        @endif
        <!-- FIN DE CONDICIONES -->
        <!-- INGREDIENTES -->
        <tr>
          <td style="font-size:15px;font-family:Arial;" colspan="75%" align="center">INGREDIENTES</td>
        </tr>
        @if(isset($products->ingredients) && count($products->ingredients) > 0)
            @foreach($products->ingredients as $k=>$v)
              @if($v->remove == 0)
                <tr>
                  <td style="font-size:15px;font-family:Arial;">{{ Str::upper($v->ingredient) }}</td>
                  <td></td>
                </tr>
              @endif
            @endforeach
        @else
          <tr>
            <td style="font-size:15px;font-family:Arial;" colspan="75%">NO TIENE INGREDIENTES.</td>
          </tr>
        @endif
        <!-- FIN DE INGREDIENTES -->
        <!-- SUBTOTAL -->
        <tr>
          <td style="font-size:15px;font-family:Arial;font-family:Arial;">SUB TOTAL</td>
          <td style="font-size:15px;font-family:Arial;font-family:Arial;" align="right">$ {{ $products->unit_price }}</td>
        </tr>
        <!-- FIN DE SUBTOTAL -->
        <!-- COMENTARIOS -->
        <tr>
          <td colspan="75%" style="border-top:2px solid black;font-size:15px;font-family:Arial;" align="center">COMENTARIOS</td>
        </tr>
        <tr>
          <td colspan="75%" style="padding-bottom:15px;font-size:15px;font-family:Arial;">{{ Str::upper($products->comment) }}</td>
        </tr>
        <!-- FIN DE COMENTARIOS -->
      @endforeach
      <!-- TOTAL DE ORDEN -->
      <tr style="border: 2px solid black;">
        <td style="font-size:15px;font-family:Arial;" align="center">TOTAL</td>
        <td style="font-size:15px;font-family:Arial;" align="right">$ {{ $order->order_total }}</td>
      </tr>
      <!-- FIN DE TOTAL -->
    </table>
  </div>
@endforeach
<!-- Final pagina de impresion -->
<script type="text/javascript">
  window.print();
</script>
@stop
