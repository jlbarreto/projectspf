<tr id="cuerpoTabla">
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
    foreach ($arrayComplete as $key => $value){
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
    foreach ($arrayCancel as $key => $value){
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
    foreach ($arrayRechazo as $key => $value){
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
    foreach ($arrayIncobrable as $key => $value){
      $incobrable = $value['created_at'];
      $dateIn = new DateTime($incobrable);
      $horaFinalIn = $dateIn->diff($datetime1);
      $horaFinalIn2 = $horaFinalIn->format("%H:%I:%S");
    }

    /*Traigo la hora a la que se hizo sin motorista la orden*/
    $horaSM = DB::select('
      Select ords.created_at from pf.req_order_status_logs as ords
      where ords.order_id = "'.$order->order_id.'" AND ords.order_status_id = 12
    ');
    $arraySM = json_decode(json_encode($horaSM), true);
    foreach ($arraySM as $key => $value){
      $sm = $value['created_at'];
      $dateSM = new DateTime($sm);
      $horaFinalSM = $dateSM->diff($datetime1);
      $horaFinalSM2 = $horaFinalSM->format("%H:%I:%S");
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
  @elseif(!isset($_GET['fillter']) || $_GET['fillter'] == 12)
    <td>
    <strong>{{ $order->order_cod }}</strong>
    <br>
    <p class="fa fa-clock-o" aria-hidden="true" style="color:#70bccc;"> {{ $horaFinalSM2 }}</p>
  @elseif(!isset($_GET['fillter']) || $_GET['fillter'] == 14)
    <td>
    <strong>{{ $order->order_cod }}</strong>
    <br>
  @endif
      <input type="hidden" value="restaurant-orders/time" id="urltime_{{$order->order_id}}" />
      <input type="hidden" id="idOrden" value="{{$order->order_id}}"/>
    </td>
    <td>{{ date('d/m/y g:i a', strtotime($order->created_at)) }}</td>
    <td>
      @if($order->service_type_id == 1 || $order->service_type_id==3)
      <i class="fa fa-motorcycle fa-2x fa-green"></i>
      @else
      <i class="fa fa-street-view fa-2x fa-yellow"></i>
      @endif
    </td>
    <?php
      $usuario = DB::select('
          SELECT usr.user_id, usr.sospecha from pf.com_users as usr 
          inner join pf.diner_addresses as direc on usr.user_id = direc.user_id
          where direc.address_id="'.$order->address_id.'"
        ');
    


	?>
    <td>
    @if($order->service_type_id == 3 || $order->service_type_id == 1)  
      @if($order->payment_method_id == 1 && $usuario[0]->sospecha == 1)
        <i class="fa fa-money fa-2x" style="color:red;"></i>
      @elseif($order->payment_method_id == 1 && $usuario[0]->sospecha != 1)
        <i class="fa fa-money fa-2x" style="color:green;"></i>
      @elseif($order->payment_method_id == 2 && $usuario[0]->sospecha == 1)
        <i class="fa fa-credit-card fa-2x" style="color:red;"></i>
      @elseif($order->payment_method_id == 2 && $usuario[0]->sospecha != 1)
        <i class="fa fa-credit-card fa-2x" style="color:#0040FF;"></i>
      @elseif($order->payment_method_id == 3)
        <i>{{ HTML::image('images/tm.png', '', array('style' => 'width:62px; margin-left: -20px; margin-top:-8px;')) }}</i>
      @endif
    @else
      @if($order->payment_method_id == 1)
        <i class="fa fa-money fa-2x" style="color:green;"></i>
      @elseif($order->payment_method_id == 3)
        <i>{{ HTML::image('images/tm.png', '', array('style' => 'width:62px; margin-left: -20px; margin-top:-8px;')) }}</i>
      @else
        <i class="fa fa-credit-card fa-2x" style="color:#0040FF;"></i>
      @endif
    @endif
    </td>
      <?php 
        $direccion = $order->address_id;
        $referencia = DB::select('
          Select ref.reference from pf.diner_addresses as ref
          inner join pf.req_orders as do ON ref.address_id = do.address_id
          where ref.address_id = "'.$direccion.'"
        ');
        $array = json_decode(json_encode($referencia), true);
        foreach($array as $key => $value){
          $ref = $value['reference'];
        }
      ?>
      @if($order->service_type_id != 2)
        @if(isset($ref) && $ref != '') 
          <td>{{ $order->address.' '.$ref }}</td>
        @else
          <td>{{ $order->address}}</td>
        @endif
      @else
        <td>En Restaurante</td>
      @endif
    <?php 
      $direccion = $order->address_id;
      $nombre_zona = DB::select('
        Select zon.zone from pf.diner_addresses as direc
        inner join pf.com_zones as zon ON direc.zone_id = zon.zone_id
        where direc.address_id = "'.$direccion.'"
      ');
      $array = json_decode(json_encode($nombre_zona), true);
      foreach ($array as $key => $value){
        $zonaF = $value['zone'];
      }
    ?>
    @if(isset($zonaF) && $zonaF !='' and $order->service_type_id==3)
      <td>{{$zonaF}}</td>
    @else
      <td>Sin Zona</td>
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

    <?php
      $nombre_mt = $order->order_id;
      $motorista_name = DB::select('select mo.nombre FROM pf.motoristas as mo inner join pf.mensajero as rm
        ON mo.motorista_id = rm.motorista_id where rm.order_id = "'.$nombre_mt.'" 
      ');
      $nombre_moto = json_decode(json_encode($motorista_name), true);
      foreach ($nombre_moto as $key => $value){
        $nombreMot = $value['nombre'];
      }

      if(Restaurant::where('parent_restaurant_id', $order->restaurant_id)->get()) {
        $sucursales = Restaurant::where('parent_restaurant_id', $order->restaurant_id)->where('activate', 1)->where('map_coordinates', '!=', 0)->get();
      }
    ?>
    @if(isset($free))
      @if(number_format($order->shipping_charge, 2) == '0.00')
        <td style="font-weight:bold;">GRATIS</td>
      @else
        <td style="font-weight:bold;">NO ES GRATIS</td>
      @endif
    @endif
    <td>
      <button id="order_id_{{ $order->order_id }}" class="btn btn_edit button_100" data-toggle="modal" data-target="#order_detail_{{ $order->order_id.$order->service_type_id }}">Ver detalle</button>
    </td>
    <!--Aqui debe ir el codigo para asignar a sucursal-->

    @if(isset($sucursales[1]))
      @if(Auth::user()->role_id != 2 && (($order->service_type_id == 1) || ($order->service_type_id == 3)))
        <td colspan="3" id="selectSuc">
          <select name="asignar_{{ $order->order_id }}" class="asignar" id="asignar_{{ $order->order_id }}" style="width: auto;" data-toggle="modal" data-target="#asig_confirm_{{ $order->order_id }}">
            <option value="0">Asignar a una sucursal</option>
            @foreach($sucursales as $k => $sucursal)
            <option value="{{$sucursal->restaurant_id}}">{{$sucursal->name}}</option>
            @endforeach
          </select>
        </td>
      @endif      
    @else
      <div id="datosOrden"> 
        @if(!isset($_GET['fillter']) || (isset($_GET['fillter']) and ($_GET['fillter'] > 1 and $_GET['fillter'] < 12 and $_GET['fillter'] != 14)))
          @if(isset($nombreMot))
            <td>{{$nombreMot}}</td>
          @else
            <td>No Asignado</td>
          @endif
        @endif
        
        @if(!isset($_GET['fillter']) || (isset($_GET['fillter']) and ($_GET['fillter'] == 1 or $_GET['fillter'] == 12 or $_GET['fillter'] == 14 )))
          @if($order->service_type_id == 3)
            @if(isset($motoristas[0]))
              <td>
                <select name="moto_{{ $order->order_id }}" class="motorista" id="moto_{{ $order->order_id }}" style="width: auto;" data-toggle="modal" data-target="#moto_confirm{{ $order->order_id }}">
                  <option value="0">Asignar a motorista</option>
                  @foreach($motoristas as $k => $motorista)
                    <?php Log::info($motorista) ?>
                    <option value="{{$motorista->motorista_id}}">{{$motorista->nombre}}</option>
                  @endforeach
                </select>
              </td>
            @else
              <td>No hay motoristas disponibles</td>
            @endif
          @else
            <td>No Asignado</td>
          @endif          
          <td>
            {{ Form::submit('Aceptar', array('class'=>'btn btn_accepted aceptar_order','id'=>'aceptar_'.$order->order_id,'data-dismiss'=>"modal",'data-loading-text'=>"Espera...")) }}
            {{ Form::hidden('registrar_'.$order->order_id, 2, array('id'=>'registrar_2_'.$order->order_id, 'class'=>'registrar_2_hidden')) }}
            <input type="hidden" id="tipo_orden_id" value="{{$order->service_type_id}}">
          </td>
          <td>
            <button id="cancelled_by_{{ $order->order_id }}" class="btn btn_cancelled button_100" data-toggle="modal" data-target="#cancel_this_{{ $order->order_id }}">Rechazar</button>
          </td>
        @endif
      </div>
    @endif
      <!--Aqui termina el código para sucursal-->
    @if(!isset($_GET['fillter']) || $_GET['fillter'] <= 10)
      <td>
        <button id="note_by_{{ $order->order_id }}" class="btn btn-success button_100 note_by" data-toggle="modal" data-target="#add_note_{{ $order->order_id }}" style="padding:10px;">Observación</button>
      </td>
    @endif
    <td><button onClick="editOrder('{{ $order->order_id }}')" class="btn btn-info" id="btnEdit"><i class="fa fa-pencil-square-o fa-2x"></i></button></td>
    </tr>
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
                  <h1>{{ $users->name . ' ' . $users->last_name }}</h1>
                @else
                  <h1>{{ isset($order->customer) ? Str::length($order->customer) > 2 ? $order->customer : $users->name . ' ' . $users->last_name : $order->customer }}</h1>
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
                    $zonaF2 = $value['zone'];
                  }
                ?>
                <?php 
                  $direccion = $order->address_id;
                  $referencia = DB::select('
                    Select ref.reference from pf.diner_addresses as ref
                    inner join pf.req_orders as do ON ref.address_id = do.address_id
                    where ref.address_id = "'.$direccion.'"
                  ');
                  $array = json_decode(json_encode($referencia), true);
                  foreach ($array as $key => $value){
                    $ref = $value['reference'];
                  }
                ?>
                <p>{{ $order->address }}</p>

                @if(isset($ref) && $ref !='')
                  <p>Referencia: {{$ref}}</p>
                @else
                  <p>Referencia:</p>
                @endif

                @if(isset($zonaF2) && $zonaF2 !='')
                  <p>Zona: {{$zonaF2}}</p>
                @else
                  <p>Zona:</p>
                @endif

                @if($order->service_type_id == 2)
                  @if($order->pickup_min == 0)
                    <p><strong>Hora de recogido:</strong> {{$order->pickup_hour}}:00 min</p>
                  @else
                    <p><strong>Hora de recogido:</strong> {{$order->pickup_hour}}:{{$order->pickup_min}} min</p>
                  @endif
                @else
                @endif

                @if(isset($_GET['fillter']))
                  @if ($order->customer_phone == NULL)
                    <p>{{ $users->phone }} | {{ $users->email }}</p>
                  @else
                    <p>{{ $order->customer_phone }} | {{ $users->email }}</p>
                  @endif
                @else
                @endif
                
                <p><strong>Fecha:</strong> {{ date('d/m/y g:i a', strtotime($order->created_at)) }}</p>
              </div>
              @if($order->payment_method_id == 2)
                <?php $card_charge = round((($order->order_total + $order->shipping_charge) * 0.04),2); ?>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="text-align:right;">
                  <h1 style="width:100%;">$ {{ number_format($order->order_total + $order->shipping_charge + $order->credit_charge,2) }}</h1>
                </div>
              @elseif($order->payment_method_id == 3)
                <?php $card_charge = round((($order->order_total + $order->shipping_charge) * 0.025),2); ?>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="text-align:right;">
                  <h1 style="width:100%;">$ {{ number_format($order->order_total + $order->shipping_charge + $order->tigo_money_charge,2) }}</h1>
                </div>
              @else
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12" style="text-align:right;">
                  <h1 style="width:100%;">$ {{ number_format($order->order_total + $order->shipping_charge,2) }}</h1>
                </div>
              @endif
            </div>
            <div class="row">
              <div class="col-lg-12">
                <h3>Detalle de los productos</h3>
              </div>
            </div>
            <!--<div id="contenedorH" style="overflow-x: scroll;"><-->
              <div class="row" style="border-top:solid 1px #cccccc;">
                <div class="col-md-1" id="responsive_div1" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Qty
                </div>
                <div class="col-md-3" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Producto
                </div>
                <div class="col-md-2" id="responsive_div3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Condiciones
                </div>
                <div class="col-md-2" id="responsive_div4" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Ingredientes
                </div>
                <div class="col-md-2" id="responsive_div5" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Comentario
                </div>
                <div class="col-md-1" id="precioOc2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Precio Unidad
                </div>
                <div class="col-md-1" id="precioOc2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
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
                <div class="col-md-1" id="responsive_div1" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <br>
                  {{ $products->quantity }}
                </div>
                <div class="col-md-3" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
                  <br>
                  {{ $products->product }}
                </div>
                <div class="col-md-2" id="responsive_div3" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
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
                <div class="col-md-2" id="responsive_div4" style="text-align:center; border-left:solid 1px #cccccc; height:70px;overflow-y: scroll;">
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
                <div class="col-md-2" id="responsive_div5" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
                  @if($products->comment=='')
                    No hay ningún comentario
                   @else
                    {{$products->comment}}
                  @endif
                </div>
                <div class="col-md-1" id="precioOc" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <br>
                  $ {{ $products->unit_price }}
                </div>
                <div class="col-md-1" id="precioOc" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <br>
                  $ {{ $products->total_price }}
                </div>
              </div>
            @endforeach

            @if($order->payment_method_id == 2)
              <h3>Datos de tarjeta</h3>
              <div class="row" style="border-top:solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div6" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Monto a pagar
                </div>
                <!--<div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Nombre tarjeta
                </div>-->
                <div class="col-md-3" id="responsive_div8" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Numero tarjeta
                </div>
                <div class="col-md-1" id="responsive_div9" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Mes
                </div>
                <div class="col-md-1" id="responsive_div01" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Año
                </div>
                <div class="col-md-2" id="responsive_div02" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Codigo de seguridad
                </div>
                <div class="col-md-2" id="responsive_div02" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold; background-color: #cdd6e2;">
                  Transaction ID
                </div>
              </div>
              <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div6" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <?php 
                    $card_charge = round((($order->order_total + $order->shipping_charge) * 0.04),2);
                  ?>
                  $ {{ number_format($order->order_total + $order->shipping_charge + $order->credit_charge,2) }}
                </div>
                <!--<div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  {{ $order->credit_name }}
                </div>-->
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
                  @if(isset($_GET['fillter']) && $_GET['fillter'] >= 4)
                    <?php 
                      echo '0';
                    ?>
                  @else
                    {{ $order->credit_expmonth }}
                  @endif
                </div>
                <div class="col-md-1" id="responsive_div01" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  @if(isset($_GET['fillter']) && $_GET['fillter'] >= 4)
                    <?php 
                      echo '0';
                    ?>
                  @else
                    {{ $order->credit_expyear }}
                  @endif
                </div>
                <div class="col-md-2" id="responsive_div02" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  @if(isset($_GET['fillter']) && $_GET['fillter'] >= 4)
                    <?php 
                      echo '0';
                    ?>
                  @else
                    {{ $order->secure_code}}
                  @endif
                </div>
                <div class="col-md-2" id="responsive_div02" style="text-align:center; border-left:solid 1px #cccccc; height:70px; background-color:#cdd6e2;">
                <?php
                  $result = DB::select('
                              SELECT MAX(req_order_status_logs.order_status_id) as ultimo
                              FROM req_order_status_logs WHERE order_status_id < 8 AND order_id = ?', array($order->order_id)
                            );
                ?>
                  @if($order->transaction_id != '')
                    {{ $order->transaction_id }}
                  @else
                    Cobrar en POS
                    @if($result[0]->ultimo < 4 && $order->service_type_id != 2)
                      &nbsp;
                      {{ Form::open(array('url' => 'pagoAuto','onSubmit'=>'return enviarCompra()')) }}
                        {{ Form::hidden('order_total', $order->order_total, array('id' => 'order_total')) }}
                        {{ Form::hidden('shipping_charge', $order->shipping_charge, array('id' => 'shipping_charge')) }}
                        {{ Form::hidden('credit_charge', $order->credit_charge, array('id' => 'credit_charge')) }}
                        {{ Form::hidden('ccnumber', $order->credit_card, array('id' => 'ccnumber')) }}
                        {{ Form::hidden('month', $order->credit_expmonth, array('id' => 'month')) }}
                        {{ Form::hidden('year', $order->credit_expyear, array('id' => 'year')) }}
                        {{ Form::hidden('cvv', $order->secure_code, array('id' => 'cvv')) }}
                        {{ Form::hidden('order_id', $order->order_id, array('id' => 'order_id')) }}
                        {{ Form::hidden('address_id', $order->address_id, array('id' => 'address_id'))}}
                        {{ Form::hidden('user_id', $usuario[0]->user_id, array('id' => 'user_id'))}}
                        {{ Form::submit('Cobrar', array('id'=>'botDesh','class'=>'btn btn-success')) }}
                      {{ Form::close() }}
                    @endif
                  @endif
                </div>
              </div>
              <h3>Datos de pago</h3>
              <div class="row" style="border-top:solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Pago Rest + envio
                </div>
                <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Restaurante
                </div>
                <div class="col-md-1" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Comisión
                </div>
                <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Envío Cliente
                </div>
                <div class="col-md-1" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Envío Rest
                </div>
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Cargo de tarjeta
                </div>
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Total Cliente
                </div>
              </div>
              <?php $comision = round((($order->order_total) * $order->restaurant->commission_percentage)/100,2);
                $card_charge = round((($order->order_total + $order->shipping_charge) * 0.04),2);
              ?>
              <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <?php 
                    if(!empty($free)){
                      if (number_format($order->shipping_charge,2) == '0.00') {
                        foreach ($free as $key => $value){
                          if ($order->restaurant_id == $value->restaurant_id) {
                            echo '$'.number_format($order->order_total - $comision -  $value->monto_envio,2);
                          }else{
                          }
                        }
                      }else{
                        echo "$". number_format($order->order_total - $comision,2);
                      }
                    }
                  ?>
                </div>
                <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->order_total - $comision,2) }}
                </div>
                <div class="col-md-1" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($comision,2) }}
                </div>
                <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->shipping_charge,2) }}
                </div>
                <div class="col-md-1" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <?php 
                    if(!empty($free)){
                      if (number_format($order->shipping_charge,2) == '0.00') {
                        foreach ($free as $key => $value) {
                          if ($order->restaurant_id == $value->restaurant_id) {
                            echo '$'.$value->monto_envio;
                          }else{                          
                          }                        
                        }  
                      }else{
                        echo "$0.00";
                      }
                    }
                  ?>
                </div>
                <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->credit_charge,2) }}
                </div>
                <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->order_total + $order->shipping_charge + $order->credit_charge,2) }}
                </div>
              </div>
            @elseif($order->payment_method_id == 3)
              <h3>Datos de Tigo Money</h3>
              <div class="row" style="border-top:solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div6" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Número de Cliente
                </div>
                <!--<div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Nombre tarjeta
                </div>-->
                <div class="col-md-3" id="responsive_div8" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Billetera Seleccionada
                </div>
              </div>
              <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div6" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  {{$order->num_tigo_money}}
                </div>
                <!--<div class="col-md-3" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  {{ $order->credit_name }}
                </div>-->
                <div class="col-md-3" id="responsive_div8" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  {{$order->billetera_user}} 
                </div>
              </div>
              <h3>Datos de pago</h3>
              <div class="row" style="border-top:solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Pago Rest + envio
                </div>
                <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Restaurante
                </div>
                <div class="col-md-1" id="responsive_div2" style="text-align:left; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Comisión
                </div>
                <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Envío Cliente
                </div>
                <div class="col-md-1" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Envío Rest
                </div>
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Cargo Tigo Money
                </div>
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Total Cliente
                </div>
              </div>
              <?php 
                $comision = round((($order->order_total) * $order->restaurant->commission_percentage)/100,2);
                $card_charge = round((($order->order_total + $order->shipping_charge) * 0.025),2);
              ?>
              <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <?php 
                    if(!empty($free)){
                      if (number_format($order->shipping_charge,2) == '0.00') {
                        foreach ($free as $key => $value) {
                          if ($order->restaurant_id == $value->restaurant_id) {
                            echo '$'.number_format($order->order_total - $comision -  $value->monto_envio,2);
                          }else{
                          }
                        }  
                      }else{
                        echo "$". number_format($order->order_total - $comision,2);
                      }
                    }
                  ?>
                </div>
                <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->order_total - $comision,2) }}
                </div>
                <div class="col-md-1" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($comision,2) }}
                </div>
                <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->shipping_charge,2) }}
                </div>
                <div class="col-md-1" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <?php 
                    if(!empty($free)){
                      if (number_format($order->shipping_charge,2) == '0.00') {
                        foreach ($free as $key => $value) {
                          if ($order->restaurant_id == $value->restaurant_id) {
                            echo '$'.$value->monto_envio;
                          }else{                          
                          }                        
                        }  
                      }else{
                        echo "$0.00";
                      }
                    }
                  ?>
                </div>
                <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->tigo_money_charge,2) }}
                </div>
                <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->order_total + $order->shipping_charge + $order->tigo_money_charge,2) }}
                </div>
              </div>
            @else
              <h3>Datos de pago</h3>
              <div class="row" style="border-top:solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Pago Rest + envio
                </div>
                <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Restaurante
                </div>
                <div class="col-md-1" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Comisión
                </div>
                <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Envío Cliente
                </div>
                <div class="col-md-2" id="responsive_div2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Envío Rest
                </div>
                <div class="col-md-2" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Total Cliente
                </div>                
                <div class="col-md-1" id="responsive_div7" style="text-align:center; border-left:solid 1px #cccccc; height: 45px; font-weight:bold;">
                  Cambio
                </div>
              </div>
              <?php 
                $comision = round((($order->order_total) * $order->restaurant->commission_percentage)/100,2); 
              ?>
              <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <?php 
                    if(!empty($free)){
                      if (number_format($order->shipping_charge,2) == '0.00') {
                        foreach ($free as $key => $value) {
                          if ($order->restaurant_id == $value->restaurant_id) {
                            echo '$'.number_format($order->order_total - $comision -  $value->monto_envio,2);
                          }else{
                          }
                        }  
                      }else{
                        echo "$". number_format($order->order_total - $comision,2);
                      }
                    }
                  ?>
                </div>
                <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->order_total - $comision,2) }}
                </div>
                <div class="col-md-1" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($comision,2) }}
                </div>
                <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->shipping_charge,2) }}
                </div>
                <div class="col-md-2" id="responsive_div2" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  <?php 
                    if(!empty($free)){
                      if (number_format($order->shipping_charge,2) == '0.00') {
                        foreach ($free as $key => $value) {
                          if ($order->restaurant_id == $value->restaurant_id) {
                            echo '$'.$value->monto_envio;
                          }else{                          
                          }                        
                        }  
                      }else{
                        echo "$0.00";
                      }
                    }
                  ?>
                </div>
                <div class="col-md-2" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->order_total + $order->shipping_charge,2) }}
                </div>                
                <div class="col-md-1" id="responsive_div7" style="padding-top:2%; text-align:center; border-left:solid 1px #cccccc; height:70px;">
                  $ {{ number_format($order->pay_change,2) }}
                </div>
              </div>
            @endif
          </div>
          <div class="modal-footer">            
            <button type="button" class="btn btn-default button_150" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="cancel_this_{{ $order->order_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                  <input type="hidden" value="cancel" id="urlCancel_{{$order->order_id}}" name="urlCancel_{{$order->order_id}}" >
                </div>
              </div>
            </div>
            <br>
            <div style="display: none;" id="divMotivo_{{$order->order_id}}">
              <label style="color:red;" for="motivoRechazo">Motivo de rechazo</label>
              <br>
              <select name="motivoRechazo" id="motivoRechazo_{{$order->order_id}}" required="required">
                <option value="none">Seleccione un motivo</option>
                <option value="1">Zona peligrosa</option>
                <option value="2">Fuera de area de cobertura</option>
                <option value="3">Producto agotado</option>
                <option value="4">Falta ingrediente</option>
              </select>
            </div>
            <br>
            <div class="form-group">
              <label style="color:red;" for="razon">Escriba a continuación el motivo de la acción</label>
              {{ Form::textarea('comment', null, array('class'=>'form-control', 'required', 'id'=>'comment_'.$order->order_id, 'title'=> 'Es obligatorio que ingrese un comentario.',)) }}
            </div>
            <div class="form-group">
              {{ Form::submit('Ejecutar', array('class'=>'btn btn-default ejectar_rejected','id'=>'ejectar_rejected_'.$order->order_id,'data-dismiss'=>"modal")) }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--MODAL PARA AGREGAR OBSERVACION A LA ORDEN-->
    <div class="modal fade" id="add_note_{{ $order->order_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog obs">
        <div class="modal-content">
          <div  class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><strong>Agregar observación a la orden N° {{ $order->order_cod }}<strong></h4>
          </div>
          <div class="modal-body">
            <div id="caja1" style="float:left; width:50%; height:auto; border:;">
              <p>Motivo</p>
              <select id="motivoObservacion_{{ $order->order_id }}" class="motivo_ob" style="margin:0px auto;">
                <option value="none">-- Elija un motivo</option>
                <option value="Costo de envío">Costo de envío</option>
                <option value="Comisión">Comisión</option>
                <option value="Variación de precios">Variación de precios</option>
                <option value="Producto Agregado">Cobro Adicional</option>
                <option value="Otros">Otros</option>
              </select>
              <div id="agregarP_{{ $order->order_id }}" style="display:none;">
                <input type="text" class="col-md-4 form-control space_15" id="productoAdd_{{ $order->order_id }}" placeholder="Nombre de Producto" required="required" style="display:none;">
                <br>
                <input type="text" class="col-md-4 form-control space_15" id="precioAdd_{{ $order->order_id }}" placeholder="Precio de Produco" required="required" style="display:none;">
              </div>
              <br /><br />
              <p>Comentario</p>
              <textarea id="comentObservacion_{{ $order->order_id }}" style="width:250px; height:100px;"></textarea>
            </div>
            <div id="caja2" style="float:left; width:50%; height:auto; text-align:center;">
              <div id="cargarAccN_{{ $order->order_id }}" style="display:none;">
                <p style="text-align:center;">Nuevo Producto</p>
                <div class="col-md-4" id="nomaccc">  
                  <input type="text" class="form-control input-sm" name="nombrePro[]" style="height: 35px !important;" placeholder="Producto" />
                </div>
                <div class="col-md-2" id="capaccc">    
                  <input type="text" class="form-control input-sm" name="precioPro[]" style="height: 35px !important;" placeholder="$ Costo" />
                </div>
                <div class="col-md-2" id="capaccc2">    
                  <input type="text" class="form-control input-sm" name="precioProV[]" style="height: 35px !important;" placeholder="$ Venta" />
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-default btn-sm" onclick="addCamposN();" style="display:inline-block;">
                    <i class="fa fa-plus"></i>
                  </button>
                  <button type="button" class="btn btn-default btn-sm" onclick="removeCamposN();" style="display:inline-block;">
                    <i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
              <div id="obs1">
                <label>Total de Observaciones:</label>
                <span id="num_total_{{ $order->order_id }}"></span>
                <br>
                <button id="ver_obs_{{ $order->order_id }}" class="btn btn-info button_150 ver_obs" data-toggle="modal" data-target="#view_note_{{ $order->order_id }}" style="padding:10px;">Ver Observaciones</button>
                <br><br>              
              </div>
            </div>
          </div>
          <div class="modal-footer" style="clear:both;">
            <div id="obs2" style="float:left; display:none;">
              <label>Total de Observaciones:</label>
              <span id="num_total2_{{ $order->order_id }}"></span>
              &nbsp;<button id="ver_obs_{{ $order->order_id }}" class="btn btn-info button_150 ver_obs" data-toggle="modal" data-target="#view_note_{{ $order->order_id }}" style="padding:10px;">Ver Observaciones</button>
            </div>
            {{ Form::submit('Agregar', array('class'=>'btn btn-success button_100 add_note','id'=>'add_note_'.$order->order_id)) }}
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
            <div id="contenedorTabla_{{ $order->order_id }}"></div>
          </div>
          <div class="modal-footer" style="clear:both;">
            <button type="button" class="btn btn-default button_150" data-dismiss="modal">Salir</button>
          </div>
        </div>
      </div>
    </div>

    <!--MODAL PARA EDITAR UNA ORDEN-->
    <div class="modal fade" id="editOrden_{{$order->order_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog" style="color: #3a3a3a; text-align:left;">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4>Editar Orden_{{ $order->order_id }}</h4>
          </div>
          <div class="modal-body" >
            <p>Completa el siguiente formulario para editar una orden</p>
            <div class="form-group new_add_modal">                
              <input type="text" id="cliente_name_{{ $order->order_id }}" class="col-md-8 form-control space_15" placeholder="Nombre Cliente" required="required">
              @if($order->service_type_id != 2)
                <input type="text" id="cliente_address_{{ $order->order_id }}" class="col-md-8 form-control space_15" placeholder="Dirección Cliente" required="required">
                {{Form::select('zoneid_',$combobox, $selected,array('id'=>'zoneid_'.$order->order_id,'class'=>'col-md-8 form-control space_15', 'required'=>'required'))}}
              @else
              @endif
              <input type="text" id="cliente_phone_{{ $order->order_id }}" class="col-md-8 form-control space_15" placeholder="Teléfono Cliente" required="required">
              <br>
              @if($order->service_type_id != 2)
                @if(isset($_GET['fillter']) && $_GET['fillter'] > 1 && $_GET['fillter'] <= 3)
                  @if(isset($motoristas[0]) )
                    Reasignar orden a motorista&nbsp;<input type="checkbox" id="editMoto_{{ $order->order_id }}" value="moto" style="-webkit-transform: scale(1.4); -moz-transform: scale(1.4);">
                    &nbsp;&nbsp;&nbsp;
                    <select name="motoSelect_{{ $order->order_id }}" class="space_15" id="motoSelect_{{ $order->order_id }}" required="required" disabled="disabled">
                      <option value="0">Asignar a motorista</option>
                      @foreach($motoristas as $k => $motorista)
                        <option value="{{$motorista->motorista_id}}">{{$motorista->nombre}}</option>
                      @endforeach
                    </select>                  
                    <br>
                  @else
                  @endif
                @endif
              @endif
              @if(isset($_GET['fillter']) && $_GET['fillter'] > 1 && $_GET['fillter'] <= 4)
                Cambiar estado del pedido&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" id="editEstado_{{ $order->order_id }}" value="estado" style="-webkit-transform: scale(1.4); -moz-transform: scale(1.4);">
                &nbsp;&nbsp;&nbsp;
                <?php
                  $result = DB::select('
                              SELECT MAX(req_order_status_logs.order_status_id) as ultimo
                              FROM req_order_status_logs WHERE order_status_id < 8 AND order_id = ?', array($order->order_id)
                            );

                  /*$statusOr = DB::select('
                            select * FROM req_order_status where order_status_id >= "'.$result[0]->ultimo.'" AND order_status_id < 8
                          ');*/
                  $statusOrd = OrderStatus::where('order_status_id','>=',$result[0]->ultimo)->where('order_status_id','<',8)->take(2)->get();
                ?>

                <select name="estadoOrden_{{ $order->order_id }}" class="space_15" id="estadoOrden_{{ $order->order_id }}" required="required" disabled="disabled">
                  <option value="0">Cambiar estado</option>
                  @foreach($statusOrd as $k => $estado)
                    <option value="{{$estado->order_status_id}}">{{$estado->order_status}}</option>
                  @endforeach
                  <option value="6">Cancelada</option>
                  <option value="7">Rechazada</option>
                </select>
                <br>
              @endif
              @if($order->service_type_id == 3 || $order->service_type_id == 1)
                @if($usuario[0]->sospecha != 1)
                  <input type="checkbox" id="badCliente_{{$order->order_id}}" value="{{$order->address_id}}" style="margin-top:20px;">&nbsp;Informar de cliente sospechoso
                @else
                @endif
              @endif
            </div>
          </div>
          <div class="modal-footer">
            <button id="actualizarOrd_{{ $order->order_id }}" class="btn btn-default button_150 space actualizarOrd">Actualizar</button>              
          </div>
        </div>
      </div>
    </div>

    <!--FIN MODAL EDITAR UNA ORDEN-->
    <script>
      // Dialogo para confirmar el motorista
      $("#moto_{{ $order->order_id }}").change(function(e){
        if($("#moto_{{ $order->order_id }}").val()>0) {
          var res = confirm("¿Esta seguro que quiere asignar a " + $("#moto_{{ $order->order_id }} option:selected").html() + " a esta orden?");
          if(!res){
            $('#moto_{{ $order->order_id }}').val(0);
          }
        }
      });

      $("#asignar_{{ $order->order_id }}").change(function(e){
        if($("#asignar_{{ $order->order_id }}").val()>0) {
          var res = confirm("¿Esta seguro que quiere pasar esta orden a la sucursal: " + $("#asignar_{{ $order->order_id }} option:selected").html() + "?");
          if(!res){
            $('#asignar_{{ $order->order_id }}').val(0);
          }else{
            asigSucursal('{{ $order->order_id }}');
          }
        }
      });

      function editOrder(idOrder){
        idEdit=idOrder;
        $.get("{{URL::to('edit_orden')}}/"+idOrder,
        function(data){
          $("#cliente_name_"+idOrder).val(data.datos.customer);
          $("#cliente_address_"+idOrder).val(data.datos.address);
          $("#zoneid_"+idOrder+" option[value="+data.zone.zone_id+"]").attr("selected", true);
          $("#cliente_phone_"+idOrder).val(data.datos.customer_phone);
          if(typeof data.moto[0] == "undefined"){
            $("#motoSelect_"+idOrder).attr('disabled', true);
          }else{
            $("#motoSelect_"+idOrder+" option[value="+data.moto[0].motoID+"]").attr("selected", true);
          }
          
          $("#estadoOrden_"+idOrder+" option[value="+data.estado.order_status_id+"]").attr("selected", true);
          $('#editOrden_'+idOrder).modal('show');
            
        }, 'json');
      }

      $("#btnEdit").click(function(){
        $('#editOrden').modal('show');
      });

      $('#editMoto_{{ $order->order_id }}').on('click', function(){
        if($(this).is(':checked') ){
          // Hacer algo si el checkbox ha sido seleccionado
          $("#motoSelect_{{ $order->order_id }}").attr('disabled', false);
          $("#motoSelect_{{ $order->order_id }}").css("border-color", "#b2151b");
          $("#motoSelect_{{ $order->order_id }}").css("border-width", "1px");
        }else{
          // Hacer algo si el checkbox ha sido deseleccionado
          $("#motoSelect_{{ $order->order_id }}").attr('disabled', true);
          $("#motoSelect_{{ $order->order_id }}").css("border-color", "#666666");
          $("#motoSelect_{{ $order->order_id }}").css("border-width", "1px");
        }
      });

      $('#editEstado_{{ $order->order_id }}').on('click', function(){
        if($(this).is(':checked')){
          // Hacer algo si el checkbox ha sido seleccionado
          $("#estadoOrden_{{ $order->order_id }}").attr('disabled', false);
          $("#estadoOrden_{{ $order->order_id }}").css("border-color", "#b2151b");
          $("#estadoOrden_{{ $order->order_id }}").css("border-width", "1px");
        }else{
          // Hacer algo si el checkbox ha sido deseleccionado
          $("#estadoOrden_{{ $order->order_id }}").attr('disabled', true);
          $("#estadoOrden_{{ $order->order_id }}").css("border-color", "#666666");
          $("#motoSelect_{{ $order->order_id }}").css("border-width", "1px");
        }
      });


      //Codigo para select de observacion
      $(".motivo_ob").change(function(){
        var id_elemento = $(this).attr('id');
        var id_pedido = id_elemento.split("_");
        var valor = $(this).val();

        if(valor == 'Producto Agregado'){
          /*$("#agregarP_"+id_pedido[1]).show();
          $("#productoAdd_"+id_pedido[1]).show();
          $("#precioAdd_"+id_pedido[1]).show();*/
          $("#cargarAccN_"+id_pedido[1]).show();
          $('.obs').css('width', '950px');
          $('#caja1').css('width', '35%');
          $('#caja2').css('width', '65%');
          $("#obs1").hide();
          $("#obs2").show();
        }else{
          /*$("#agregarP_"+id_pedido[1]).hide();
          $("#productoAdd_"+id_pedido[1]).hide();
          $("#precioAdd_"+id_pedido[1]).hide();*/
          $("#cargarAccN_"+id_pedido[1]).hide();
          $('.obs').css('width', '600px');
          $('#caja1').css('width', '50%');
          $('#caja2').css('width', '50%');
          $("#obs2").hide();
          $("#obs1").show();
        }
      });

      function addCamposN(){
        campon = "<input type='text' class='form-control input-sm' id='nombre_accN' placeholder='Producto' name='nombrePro[]' style='margin-top:5px; height: 35px !important;' />";
        campon1 = "<input type='text' class='form-control input-sm' id='capacidad_accN' style='margin-top:5px; height: 35px !important;' placeholder='$ Costo' name='precioPro[]' />";
        campon2 = "<input type='text' class='form-control input-sm' id='capacidad_accN' style='margin-top:5px; height: 35px !important;' placeholder='$ Venta' name='precioProV[]' />";
          
        $("#nomaccc").append(campon);
        $("#capaccc").append(campon1);
        $("#capaccc2").append(campon2);
          
        //$("#cargarAccN").append(campon); 
        //$("#cargarAccN").append(campon1); 
        //$("#cargarAccN").append(campon2); 
      }

      function removeCamposN(){
        $('#nomaccc > #nombre_accN:last-child').remove();
        $('#capaccc > #capacidad_accN:last-child').remove();
        $('#capaccc2 > #capacidad_accN:last-child').remove();
      }

      function enviarCompra(){
        
        document.getElementById("botDesh").value = "Enviando...";
        document.getElementById("botDesh").disabled = true;
      }

    </script>
      <!-- Fin del dialogo -->
