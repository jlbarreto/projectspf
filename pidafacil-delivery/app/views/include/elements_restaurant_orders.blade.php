  <tr>
  <?php 
  $date2 = date("Y-m-d H:i:s");
    $date3 = $order->created_at;
    $date4 = $order->updated_at;
    $datetime1 = new DateTime($date3);
    $datetime2 = new DateTime($date2);
    $datetime3 = new DateTime($date4);

    $dteDiff  = $datetime2->diff($datetime1); 
    $horaF = $dteDiff->format("%H:%I:%S");

    $horaFinal = $datetime3->diff($datetime1);
    $horaFinal2 = $horaFinal->format("%H:%I:%S");

    /*Traigo la hora a la que se completó la orden*/
    $horaComplete = DB::select('
      Select ords.created_at from pf.req_order_status_logs as ords
      where ords.order_id = "'.$order->order_id.'" AND ords.order_status_id = 5
    ');

    $arrayComplete = json_decode(json_encode($horaComplete), true);
      foreach ($arrayComplete as $key => $value) {
        $completo = $value['created_at'];
        $dateCo = new DateTime($completo);
        $horaFinalCo = $dateCo->diff($datetime1);
        $horaFinalCo2 = $horaFinalCo->format("%H:%I:%S");

      }

    /*Traigo la hora a la que se cancelo la orden*/
    $horaCancel = DB::select('
      Select ords.created_at from pf.req_order_status_logs as ords
      where ords.order_id = "'.$order->order_id.'" AND ords.order_status_id = 6
    ');
    
    $arrayCancel = json_decode(json_encode($horaCancel), true);
      foreach ($arrayCancel as $key => $value) {
        $cancel = $value['created_at'];
        $dateCa = new DateTime($cancel);
        $horaFinalCa = $dateCa->diff($datetime1);
        $horaFinalCa2 = $horaFinalCa->format("%H:%I:%S");
      }

    /*Traigo la hora a la que se rechazo la orden*/
    $horaRechazo = DB::select('
      Select ords.created_at from pf.req_order_status_logs as ords
      where ords.order_id = "'.$order->order_id.'" AND ords.order_status_id = 7
    ');
    $arrayRechazo = json_decode(json_encode($horaRechazo), true);
      foreach ($arrayRechazo as $key => $value) {
        $rechazo = $value['created_at'];
        $dateRe = new DateTime($rechazo);
        $horaFinalRe = $dateRe->diff($datetime1);
        $horaFinalRe2 = $horaFinalRe->format("%H:%I:%S");
      }

    /*Traigo la hora a la que se hizo incobrable la orden*/
    $horaIncobrable = DB::select('
      Select ords.created_at from pf.req_order_status_logs as ords
      where ords.order_id = "'.$order->order_id.'" AND ords.order_status_id = 8
    ');
    $arrayIncobrable = json_decode(json_encode($horaIncobrable), true);
      foreach ($arrayIncobrable as $key => $value) {
        $incobrable = $value['created_at'];
        $dateIn = new DateTime($incobrable);
        $horaFinalIn = $dateIn->diff($datetime1);
        $horaFinalIn2 = $horaFinalIn->format("%H:%I:%S");
      }
  ?>

  @if(!isset($_GET['fillter']) || $_GET['fillter'] < 5)
    <td>
      <strong>{{ $order->order_cod }}</strong>
      <br>
      <p class="timercont" data-timer="{{$horaF}}" data-idorden="{{ $order->order_id }}"></p>
  @elseif(!isset($_GET['fillter']) || $_GET['fillter'] == 5)
    <td>
    <strong>{{ $order->order_cod }}</strong>
    <br>
    <p class="fa fa-clock-o" aria-hidden="true" style="color:#70bccc;"> {{ $horaFinalCo2 }}</p>
  @elseif(!isset($_GET['fillter']) || $_GET['fillter'] == 6)
    <td>
    <strong>{{ $order->order_cod }}</strong>
    <br>
    <p class="fa fa-clock-o" aria-hidden="true" style="color:#70bccc;"> {{ $horaFinalCa2 }}</p>
  @elseif(!isset($_GET['fillter']) || $_GET['fillter'] == 7)
    <td>
    <strong>{{ $order->order_cod }}</strong>
    <br>
    <p class="fa fa-clock-o" aria-hidden="true" style="color:#70bccc;"> {{ $horaFinalRe2 }}</p>
  @elseif(!isset($_GET['fillter']) || $_GET['fillter'] == 8)
    <td>
    <strong>{{ $order->order_cod }}</strong>
    <br>
    <p class="fa fa-clock-o" aria-hidden="true" style="color:#70bccc;"> {{ $horaFinalIn2 }}</p>
  @endif
      <input type="hidden" value="time" id="urltime_{{$order->order_id}}" />
    </td>
    <td>{{ date('d/m/y g:i a', strtotime($order->created_at)) }}</td>
    <td>
      @if($order->service_type_id == 1)
      <i class="fa fa-motorcycle fa-2x fa-green"></i>
      @else
      <i class="fa fa-street-view fa-2x fa-yellow"></i>
      @endif
    </td>
    <td>
      @if($order->payment_method_id == 1)
      <i class="fa fa-money fa-2x" style="color:gray;"></i>
      @elseif($order->payment_method_id == 2 && $order->service_type_id == 3)
      <i class="fa fa-credit-card fa-2x" style="color:gray;"></i>
      @else
      <i class="fa fa-credit-card fa-2x" style="color:gray;"></i>
      @endif
    </td>
    @if(Auth::user()->role_id == 1)
      @if(isset($_GET['fillter']) && $_GET['fillter']<= 4)
        @if ($order->customer == NULL)
          <td>{{ $users->name . ' ' . $users->last_name }}</td>
        @else
          <td>{{ isset($order->customer) ? Str::length($order->customer) > 2 ? $order->customer : $users->name . ' ' . $users->last_name : $order->customer }}</td>
        @endif
      @else
      @endif
      <!--<td>{{ $order->address }}</td>-->
      @if($order->pickup_hour == 0 && $order->pickup_min == 0)
        <td>00:00</td>
      @else
        @if($order->pickup_min == 0)
          <td>{{$order->pickup_hour.':'.$order->pickup_min.'0'}}</td>
        @else
          <td>{{$order->pickup_hour.':'.$order->pickup_min}}</td>
        @endif
      @endif
      <td>
        @for($i=0;$i<count($res);$i++)
        @if($order->restaurant_id == $res[$i]->restaurant_id)
        <?php
        $aloId = $res[$i]->orders_allocator_id;
        ?>
        @for($j=0;$j<count($res);$j++)
        @if($aloId == $res[$j]->restaurant_id)
        {{ $res[$j]->name }} <br />{{ $res[$j]->address }}
        @endif
        @endfor
        @endif
        @endfor
      </td>
    @else
      @if(Auth::user()->role_id != 1 && (isset($_GET['fillter']) && $_GET['fillter']<= 4))
        <td>@if($order->service_type_id == 3 || $order->service_type_id == 1) 
              PidaFacil Delivery 
            @elseif($order->service_type_id == 2)  
              {{ $users->name . ' ' . $users->last_name }}
            @endif</td>
      @else
      @endif
      @if($order->pickup_hour == 0 && $order->pickup_min == 0)
        <td>00:00</td>
      @else
        @if($order->pickup_min == 0)
          <td>{{$order->pickup_hour.':'.$order->pickup_min.'0'}}</td>
        @else
          <td>{{$order->pickup_hour.':'.$order->pickup_min}}</td>
        @endif
      @endif
      <td>@if($order->service_type_id == 3) En Restaurante @else {{ $order->address }} @endif</td>
    @endif

    <?php
      $nombre_mt = $order->order_id;
      $motorista_name = DB::select('select mo.nombre FROM pf.motoristas as mo inner join pf.req_order_motorista as rm
        ON mo.motorista_id = rm.motorista_id where rm.order_id = "'.$nombre_mt.'" 
        ');
      $nombre_moto = json_decode(json_encode($motorista_name), true);
        foreach ($nombre_moto as $key => $value){
          $nombreMot = $value['nombre'];
        }
      ?>
      
      @if(!isset($_GET['fillter']) || (isset($_GET['fillter']) and $_GET['fillter'] > 1))
        @if(isset($nombreMot))
          <td>{{$nombreMot}}</td>
        @else
          <td>No Asignado</td>
        @endif
      @endif
    <td>
      <button id="order_id_{{ $order->order_id }}" class="btn btn_edit button_100" data-toggle="modal" data-target="#order_detail_{{ $order->order_id.$order->service_type_id }}">Ver detalle</button>
    </td>
    @if(!isset($_GET['fillter']) || (isset($_GET['fillter']) and $_GET['fillter'] == 1))
    
      @if(((Auth::user()->role_id != 1 && Auth::user()->role_id != 3) || $restaurantes[$id]['parent'] != $id) || !isset($sucursales[1]))
        @if(($order->service_type_id == 3 && $order->viewed_pidafacil == 1))
          <td>
            {{ Form::submit('Aceptar', array('class'=>'btn btn_accepted button_100 aceptar_order','id'=>'aceptar_'.$order->order_id,'data-loading-text'=>"Espera...")) }}
          </td>
          <!--<td>
            <button id="cancelled_by_{{ $order->order_id }}" class="btn btn_cancelled button_100" data-toggle="modal" data-target="#cancel_this_{{ $order->order_id }}">Rechazar</button>
          </td>-->
          <td></td>
        @elseif($order->service_type_id=2)
          <td>
            {{ Form::submit('Aceptar', array('class'=>'btn btn_accepted button_100 aceptar_order','id'=>'aceptar_'.$order->order_id,'data-loading-text'=>"Espera...")) }}
          </td>
          <!--<td>
            <button id="cancelled_by_{{ $order->order_id }}" class="btn btn_cancelled button_100" data-toggle="modal" data-target="#cancel_this_{{ $order->order_id }}">Rechazar</button>
          </td>-->
          <td></td>
        @else
        @endif
      @endif
      @if(isset($sucursales[1]))
        @if(Auth::user()->role_id != 2 && (($order->service_type_id == 1) || ($order->service_type_id == 3 && $order->viewed_pidafacil ==1)))
          <td colspan="3">
            <select name="asignar_{{ $order->order_id }}" class="asignar" id="asignar_{{ $order->order_id }}" style="width: auto;" data-toggle="modal" data-target="#asig_confirm_{{ $order->order_id }}">
              <option value="0">Asignar a una sucursal</option>
              @foreach($sucursales as $k => $sucursal)
              <option value="{{$sucursal->restaurant_id}}">{{$sucursal->name}}</option>
              @endforeach
            </select>
          </td>
        @endif
      @endif
      @else
        @if($order->service_type_id == 2)
          @if(!isset($_GET['fillter']) || $_GET['fillter'] < 5)
            <td>
              <button id="entregada_{{ $order->order_id }}" class="entregada_rejected btn button_100 botton_completed">Entregada</button>
            </td>
          @endif
        @endif
    @endif
    @if((!isset($_GET['fillter']) || $_GET['fillter'] <= 5) && Auth::user()->role_id == 1)
      <td>
        <button id="note_by_{{ $order->order_id }}" class="btn btn-success button_100 note_by2" data-toggle="modal" data-target="#add_note_{{ $order->order_id }}" style="padding:10px;">Observación</button>
      </td>
    @endif
  </tr>
  <div class="modal fade " id="order_detail_{{ $order->order_id.$order->service_type_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="detalle_{{ $order->order_id }}">
      <div class="modal-content">
        <div  class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><strong>Código de Orden: {{ $order->order_cod }}</strong></h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
              @if(Auth::user()->role_id == 1)
                @if(isset($_GET['fillter']) && $_GET['fillter']<= 4)
                  @if ($order->customer == NULL)
                    <h1>{{ $users->name . ' ' . $users->last_name }}</h1>
                  @else
                    <h1>{{ isset($order->customer) ? Str::length($order->customer) > 2 ? $order->customer : $users->name . ' ' . $users->last_name : $order->customer }}</h1>
                  @endif
                  @else
                @endif
                <?php 
                $direccion = $order->address_id;
                $nombre_zona = DB::select('
                  Select zon.zone from pf.diner_addresses as direc
                  inner join pf.com_zones as zon ON direc.zone_id = zon.zone_id
                  where direc.address_id = "'.$direccion.'"
                ');
                $array = json_decode(json_encode($nombre_zona), true);
                foreach ($array as $key => $value) {
                  $zonaF = $value['zone'];
                }
                ?>
                <p>{{$order->address}}</p>
                @if(isset($zonaF) && $zonaF !='')
                  <p>Zona: {{$zonaF}}</p>
                @else
                  <p></p>
                @endif
                
                @if(isset($_GET['fillter']) && $_GET['fillter']<= 4)
                  @if ($order->customer_phone == NULL)
                    <p>{{ $users->phone }} | {{ $users->email }}</p>
                  @else
                    <p>{{ $order->customer_phone }} | {{ $users->email }}</p>
                  @endif
                @else
                @endif
              @else
                <h1>@if($order->service_type_id == 3) PidaFacil Delivery @else{{ $users->name . ' ' . $users->last_name }}@endif </h1>
                <p>@if($order->service_type_id == 3) En Restaurante @else {{ $order->address }} @endif </p>
              @endif
              <p><strong>Fecha:</strong> {{ date('d/m/y g:i a', strtotime($order->created_at)) }}</p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="text-align:right;">
              @if($order->service_type_id == 3)
              <?php $q = ($order->order_total * $order->restaurant->commission_percentage)/100 ?>
              <h1 style="width:100%;">$ {{ number_format($order->order_total-$q,2) }}</h1>
              @else
              <h1 style="width:100%;">$ {{ number_format($order->order_total,2) }}</h1>
              @endif
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12">
              <h3>Detalle de los productos</h3>
            </div>
          </div>
          <div class="row" style="border-top:solid 1px #cccccc;">
              <div class="col-lg-1 col-md-3 col-sm-3 col-xs-3" id="responsive_div1" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                Qty
              </div>
              <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                Producto
              </div>
              <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" id="responsive_div3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                Condiciones
              </div>
              <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" id="responsive_div4" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                Ingredientes
              </div>
              <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" id="responsive_div5" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                Comentario
              </div>
              <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" id="precioOc2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                Precio Unidad
              </div>
              <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" id="precioOc2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                Precio Total
              </div>
            </div>
          @foreach($order->products as $ke=>$products)
          @foreach($products->conditions as $k=>$condition)
          <?php $conditions = $condition; ?>
          @endforeach
          @foreach($products->ingredients as $k=>$v)
          <?php $ingredients = $v; ?>
          @endforeach
          <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
            <div class="col-lg-1 col-md-1" id="responsive_div1" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                <br>
                {{ $products->quantity }}
              </div>
              <div class="col-lg-3 col-md-3" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
                <br>
                {{ $products->product }}
              </div>
              <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3" id="responsive_div3" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
                @if(isset($products->conditions) && count($products->conditions) > 0)
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
              </div>
              <div class="col-lg-2 col-md-2" id="responsive_div4" style="text-align:center; border-left:solid 1px #cccccc; height:70px;overflow-y: scroll;">
                @if(isset($products->ingredients) && count($products->ingredients) > 0)
                  @foreach($products->ingredients as $k=>$v)
                    @if($v->remove == 0)
                      {{ $v->ingredient }}<br />
                    @endif
                  @endforeach
                @else
                  No tiene ingredientes
                @endif
              </div>
              <div class="col-lg-2 col-md-2" id="responsive_div5" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
                {{$products->comment}}
              </div>
              <div class="col-lg-1 col-md-1" id="precioOc" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                <br>
                $ {{ $products->unit_price }}
              </div>
              <div class="col-lg-1 col-md-1" id="precioOc" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                <br>
                $ {{ $products->total_price }}
              </div>
          </div>
          @endforeach
          @if(Auth::user()->role_id == 1)
            @if($order->payment_method_id == 2)
            <h3>Datos de tarjeta</h3>
            <div class="row" style="border-top:solid 1px #cccccc;">
              <div class="col-md-2" id="responsive_div6" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Monto a pagar
              </div>
              <div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Nombre tarjeta
              </div>
              <div class="col-md-3" id="responsive_div8" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Numero tarjeta
              </div>
              <div class="col-md-1" id="responsive_div9" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Mes
              </div>
              <div class="col-md-1" id="responsive_div9" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Año
              </div>
              <div class="col-md-2" id="responsive_div02" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Codigo de seguridad
              </div>
            </div>
            <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
              <div class="col-md-2" id="responsive_div6" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($order->order_total + $order->shipping_charge,2) }}
              </div>
              <div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                {{ $order->credit_name }}
              </div>
              <div class="col-md-3" id="responsive_div8" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                @if(isset($_GET['fillter']) && $_GET['fillter'] >= 4)
                  <?php 
                    $sub_credit_card = substr($order->credit_card, -4);
                    $new_credit_card = "***********".$sub_credit_card;
                    echo $new_credit_card;
                  ?>
                  @else
                    {{ $order->credit_card }}
                  @endif
              </div>
              <div class="col-md-1" id="responsive_div9" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                {{ $order->credit_expmonth }}
              </div>
              <div class="col-md-1" id="responsive_div9" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                {{ $order->credit_expyear }}
              </div>
              <div class="col-md-2" id="responsive_div02" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                {{ $order->secure_code}}
              </div>
            </div>
            <h3>Datos de pago</h3>
            <div class="row" style="border-top:solid 1px #cccccc;">
              <div class="col-md-3" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Restaurante
              </div>
              <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Comisión
              </div>
              <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Costo de envío
              </div>
              <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Cargo de tarjeta
              </div>
              <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Total Cliente
              </div>
            </div><?php $comision = round((($order->order_total) * $order->restaurant->commission_percentage)/100,2);
            $card_charge = round((($order->order_total + $order->shipping_charge) * 0.04),2);
            ?>
            <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
              <div class="col-md-3" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($order->order_total - $comision,2) }}
              </div>
              <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($comision,2) }}
              </div>
              <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($order->shipping_charge,2) }}
              </div>
              <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($card_charge,2) }}
              </div>
              <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($order->order_total + $order->shipping_charge + $card_charge,2) }}
              </div>
            </div>
            @else
            <h3>Datos de pago</h3>
            <div class="row" style="border-top:solid 1px #cccccc;">
              <div class="col-md-3" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Restaurante
              </div>
              <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Comisión
              </div>
              <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Costo de envío
              </div>
              <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Total Cliente
              </div>
              <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                Cambio
              </div>
            </div><?php $comision = round((($order->order_total) * $order->restaurant->commission_percentage)/100,2); ?>
            <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
              <div class="col-md-3" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($order->order_total - $comision,2) }}
              </div>
              <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($comision,2) }}
              </div>
              <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($order->shipping_charge,2) }}
              </div>
              <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($order->order_total + $order->shipping_charge,2) }}
              </div>
              <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                $ {{ number_format($order->pay_change,2) }}
              </div>
            </div>
            @endif
          @else
            @if($order->payment_method_id == 2 && $order->service_type_id == 3)
              <h3>Datos de pago</h3>
              <div class="row" style="border-top:solid 1px #cccccc;">
                <div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Total Cliente
                </div>
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Cambio
                </div>
              </div>
              <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                <div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  @if($order->service_type_id == 3)
                  <?php $totO = ($order->order_total * $order->restaurant->commission_percentage)/100 ?>
                  $ {{ number_format($order->order_total-$totO,2) }}
                  @else
                  $ {{ number_format($order->order_total,2) }}
                  @endif
                </div>
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  @if(isset($order->pay_change))
                  $ {{ number_format($order->pay_change,2) }}
                  @else
                  $ 0
                  @endif
                </div>
              </div>
            @elseif($order->payment_method_id == 2)
              <h3>Datos de tarjeta</h3>
              <div class="row" style="border-top:solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div6" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Total Cliente
                </div>
                <div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Nombre tarjeta
                </div>
                <div class="col-md-3" id="responsive_div8" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Numero tarjeta
                </div>
                <div class="col-md-1" id="responsive_div9" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Mes
                </div>
                <div class="col-md-1" id="responsive_div9" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Año
                </div>
                <div class="col-md-2" id="responsive_div02" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Codigo de seguridad
                </div>
              </div>
              <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div6" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  @if($order->service_type_id == 3)
                    <?php $totO = ($order->order_total * $order->restaurant->commission_percentage)/100 ?>
                    $ {{ number_format($order->order_total-$totO,2) }}
                  @else
                    $ {{ number_format($order->order_total,2) }}
                  @endif
                </div>
                <div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  {{ $order->credit_name }}
                </div>
                <div class="col-md-3" id="responsive_div8" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  {{ $order->credit_card }}
                </div>
                <div class="col-md-1" id="responsive_div9" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  {{ $order->credit_expmonth }}
                </div>
                <div class="col-md-1" id="responsive_div9" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  {{ $order->credit_expyear }}
                </div>
                <div class="col-md-2" id="responsive_div02" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  {{ $order->secure_code }}
                </div>
              </div>
            @else
              <h3>Datos de pago</h3>
              <div class="row" style="border-top:solid 1px #cccccc;">
                <div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Total Cliente
                </div>
                @if($order->service_type_id != 3)
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Cambio
                </div>
                @endif
              </div>
              <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                <div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  @if($order->service_type_id == 3)
                    <?php $totO = ($order->order_total * $order->restaurant->commission_percentage)/100 ?>
                    $ {{ number_format($order->order_total-$totO,2) }}
                  @else
                    $ {{ number_format($order->order_total,2) }}
                  @endif
                </div>
                @if($order->service_type_id != 3)
                  <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                    $ {{ number_format($order->pay_change,2) }}
                  </div>
                @endif
              </div>
            @endif
          @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default button_150" data-dismiss="modal">Salir</button>
          <a href="{{ URL::to('/order/print/'. $order->order_id) }}" target="_blank" type="button" class="btn btn-default button_150" id="{{$order->order_id}}">Imprimir</a>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade " id="cancel_this_{{ $order->order_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div  class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><strong>Cancelando orden N° {{ $order->order_cod }}<strong></h4>
          </div>
          <div class="modal-body">
            <div class="container">
              <label style="color:red;" for="razon">Seleccione la acción a ejecutar</label>
              <div class="row">
                <div class="form-inline">
                  <div class="btn btn-default button_150">
                    {{ Form::radio('rejected_'.$order->order_id, 6, true, array('id'=>'rejected_6_'.$order->order_id, 'class'=>'rejected_6_radio')) }} <label for="rejected_6_{{ $order->order_id }}">Cancelar</label>
                  </div>
                  <div class="btn btn-default button_150">
                    {{ Form::radio('rejected_'.$order->order_id, 7, false, array('id'=>'rejected_7_'.$order->order_id, 'class'=>'rejected_7_radio')) }} <label for="rejected_7_{{ $order->order_id }}">Rechazar</label>
                  </div>
                  @if($order->payment_method_id == 2)
                  <div class="btn btn-default button_150">
                    {{ Form::radio('rejected_'.$order->order_id, 8, false, array('id'=>'rejected_8_'.$order->order_id, 'class'=>'rejected_8_radio')) }} <label for="rejected_8_{{ $order->order_id }}">Incobrable</label>
                  </div>
                  @endif
                  <input type="hidden" value="cancel" id="urlCancel_{{$order->order_id}}" nameid="urlCancel_{{$order->order_id}}" >
                </div>
              </div>
            </div>
            <br>
            <div style="display: none;" id="divMotivo_{{$order->order_id}}">
              <label style="color:red;" for="motivoRechazo">Motivo de rechazo</label>
              <br>
              <select name="motivoRechazo" id="motivoRechazo_{{$order->order_id}}" required>
                <option value>Seleccione un motivo</option>
                <option value="1">Zona peligrosa</option>
                <option value="2">Fuera de area de cobertura</option>
                <option value="3">Producto agotado</option>
                <option value="4">Falta ingrediente</option>
              </select>
            </div>
            <br>
            <div class="form-group">
              <label style="color:red;" for="razon">Escriba a continuación el motivo de la acción</label>
              {{ Form::textarea('comment', null, array('class'=>'form-control', 'required', 'id'=>'comment_'.$order->order_id)) }}
            </div>
            <div class="form-group">
              {{ Form::submit('Ejecutar', array('class'=>'btn btn-default ejectar_rejected','id'=>'ejectar_rejected_'.$order->order_id,'data-dismiss'=>"modal")) }}
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Dialogo para confirmar el paso a sucrusal -->

    <!--MODAL PARA AGREGAR OBSERVACION A LA ORDEN-->
    <div class="modal fade" id="add_note_{{ $order->order_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div  class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><strong>Agregar observación a la orden N° {{ $order->order_cod }}<strong></h4>
          </div>
          <div class="modal-body">
            <div id="caja1" style="float:left; width:50%; height:auto;">
              <p>Motivo</p>
              <select id="motivoObservacion_{{ $order->order_id }}" style="margin:0px auto;">
                <option value="none">-- Elija un motivo</option>
                <option value="Costo de envío">Costo de envío</option>
                <option value="Comisión">Comisión</option>
                <option value="Variación de precios">Variación de precios</option>
                <option value="Cobro adicional">Cobro Adicional</option>
                <option value="Otros">Otros</option>
              </select>
              <br><br>
              <p>Comentario</p>
              <textarea id="comentObservacion_{{ $order->order_id }}" style="width:250px; height:100px;"></textarea>
            </div>
            <div id="caja2" style="float:left; width:50%; height:auto; text-align:center;">
              <br><br><br>
              <label>Total de Observaciones:</label>
              <span id="num_total_{{ $order->order_id }}"></span>
              <br>
              <button id="ver_obs_{{ $order->order_id }}" class="btn btn-info button_150 ver_obs2" data-toggle="modal" data-target="#view_note_{{ $order->order_id }}" style="padding:10px;">Ver Observaciones</button>
            </div>
          </div>
          <div class="modal-footer" style="clear:both;">
            {{ Form::submit('Agregar', array('class'=>'btn btn-success button_100 add_note2','id'=>'add_note_'.$order->order_id)) }}
            <button type="button" class="btn btn-default button_100" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>
    <!--FIN MODAL DE OBSERVACION-->
    <div class="modal fade" id="view_note_{{ $order->order_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div  class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><strong>Observaciones de la orden N° {{ $order->order_cod }}<strong></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="col-md-3">
                  Fecha
                </div>
                <div class="col-md-3">
                  Motivo
                </div>
                <div class="col-md-3">
                  Comentario
                </div>
                <div class="col-md-3">
                  Usuario
                </div>
              </div>
            </div>
            <br>
            <div id="contenedorTabla_{{ $order->order_id }}"></div>
          </div>
          <div class="modal-footer" style="clear:both;">
            <button type="button" class="btn btn-default button_150" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>

    <script>
    $("#asignar_{{ $order->order_id }}").change(function(e){
      if($("#asignar_{{ $order->order_id }}").val()>0) {
        var res = confirm("¿Esta seguro que quiere pasar esta orden a la sucursal: " + $("#asignar_{{ $order->order_id }} option:selected").html() + "?");
        if (!res) {
          $('#asignar_{{ $order->order_id }}').val(0);
        } else {
          asigSucursal('{{ $order->order_id }}');
        }
      }
    });

    /*$("#ejectar_rejected_{{$order->order_id}}").click(function(){
      location.reload();
    });*/
    </script>
    <!-- Fin del dialogo -->
