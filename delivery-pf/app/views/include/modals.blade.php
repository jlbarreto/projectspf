<div class="modal fade" id="order_detail_{{$order->order_id.$order->service_type_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div  class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><strong>Código de Orden: {{ $order->order_cod }}</strong></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                @if ($order->customer == NULL)
                  <h2>{{ $users->name . ' ' . $users->last_name }}</h2>
                @else
                  <h1>{{ isset($order->customer) ? Str::length($order->customer) > 2 ? $order->customer : $users->name . ' ' . $users->last_name : $order->customer }}</h1>
                @endif
                <p>{{ $order->address }}</p>
                <p><strong>Fecha:</strong> {{ date('d/m/y g:i a', strtotime($order->created_at)) }}</p>
              </div>
              <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="text-align:right;">
                <h1 style="width:100%;">$ {{ $order->order_total }}</h1>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12">
                <h3>Detalle de los productos</h3>
              </div>
            </div>
            <div class="table-resposive">
              <table class="table">
                <tr>
                  <th>Cantidad</th>
                  <th>Producto</th>
                  <th>Condiciones</th>
                  <th>Ingredientes</th>
                  <th>Comentario</th>
                  <th>Precio Unidad</th>
                  <th>Precio Total</th>
                </tr>
                <tbody>
                  @foreach($order->products as $ke=>$products)
                    @foreach($products->conditions as $k=>$condition)
                      <?php $conditions = $condition; ?>
                    @endforeach
                    @foreach($products->ingredients as $k=>$v)
                      <?php $ingredients = $v; ?>
                    @endforeach
                  <tr>
                    <td>{{ $products->quantity }}</td>
                    <td>{{ $products->product }}</td>
                    <td>@if(isset($products->conditions) && count($products->conditions) > 0)
                          @foreach($products->conditions as $k=>$condition)
                            @for($i = 0; $i < count($condi);$i++)
                              @if($condi[$i]->condition_id == $condition->condition_id)
                              {{ $condition->condition}}: {{ $condition->condition_option }} <br />
                              @endif
                            @endfor
                          @endforeach
                        @else
                          No tiene condiciones
                        @endif
                    </td>
                    <td>@if(isset($products->ingredients) && count($products->ingredients) > 0)
                        @foreach($products->ingredients as $k=>$v)
                        @if($v->remove == 0)
                          {{ $v->ingredient }}<br />
                        @endif
                        @endforeach
                        @else
                        No tiene ingredientes
                        @endif
                    </td>
                    <td>{{$products->comment}}</td>
                    <td>$ {{ $products->unit_price }}</td>
                    <td>$ {{ $products->total_price }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            

          <!---------------------------------------------------------------------------------------->

            @if($order->payment_method_id == 2)
            <h3>Datos de tarjeta</h3>
            <div class="table-resposive">
              <table class="table">
                <tr>
                  <th>Monto a pagar</th>
                  <th>Nombre Tarjeta</th>
                  <th>N&uacute;mero tarjeta</th>
                  <th>Mes</th>
                  <th>Año</th>
                  <th>C&oacute;digo de Seguridad</th>
                </tr>
                <tbody>
                  <tr>
                    <td>$ {{ number_format($order->order_total + $order->shipping_charge,2) }}</td>
                    <td>{{ $order->credit_name }}</td>
                    <td>{{ $order->credit_card }}</td>
                    <td>{{ $order->credit_expmonth }}</td>
                    <td>{{ $order->credit_expyear }}</td>
                    <td>{{ $order->secure_code}}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="table-resposive">
              <h3>Datos de pago</h3>
              <table class="table">
                <tr>
                  <th>Restaurante</th>
                  <th>Comisión</th>
                  <th>Costo de envío</th>
                  <th>Cargo de tarjeta</th>
                  <th>Total Cliente</th>
                </tr>
                <?php $comision = round((($order->order_total) * $order->restaurant->commission_percentage)/100,2);
                      $card_charge = round((($order->order_total + $order->shipping_charge) * 0.04),2);
                ?>
                <tbody>
                  <tr>
                    <td>$ {{ number_format($order->order_total - $comision,2) }}</td>
                    <td>$ {{ number_format($comision,2) }}</td>
                    <td>$ {{ number_format($order->shipping_charge,2) }}</td>
                    <td>$ {{ number_format($card_charge,2) }}</td>
                    <td>$ {{ number_format($order->order_total + $order->shipping_charge + $card_charge,2) }}</td>
                  </tr>
                </tbody>
              </table>
            </div>  
              <!------------------------------------------------ -->
            @else
              <div class="table-resposive">
                <h3>Datos de pago</h3>
                <table class="table">
                  <tr>
                    <th>Restaurante</th>
                    <th>Comisión</th>
                    <th>Costo de envío</th>
                    <th>Total Cliente</th>
                    <th>Cambio</th>
                  </tr>
                  <?php $comision = round((($order->order_total) * $order->restaurant->commission_percentage)/100,2); ?>
                  <tbody>
                      <tr>
                          <td>$ {{ number_format($order->order_total - $comision,2) }}</td>
                          <td>$ {{ number_format($comision,2) }}</td>
                          <td>$ {{ number_format($order->shipping_charge,2) }}</td>
                          <td>$ {{ number_format($order->order_total + $order->shipping_charge,2) }}</td>
                          <td>$ {{ number_format($order->pay_change,2) }}</td>
                      </tr>
                  </tbody>
                </table>
              </div>
            @endif
            <!-------------------------------------------------------- -->
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default button_150" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>