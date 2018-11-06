@extends('general.general_white')
@section('content')

<?php
  $total = Session::get('total_order');
  $cart = Session::get('cart');
  $cantidad = Session::get('cart2');

  #echo $contador_add;
  $timezone = date_default_timezone_get();
  $date = date('G:i:s', time());  
?>

<input type="hidden" id="cont" value="{{$cantidad}}">
<nav class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse"
      data-target=".navbar-ex1-collapse">
      <i class="fa fa-bars fa-2x"></i>
    </button>
    <a class="navbar-brand" href="{{ URL::to('') }}" onclick="orderAb()">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
  </div>
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-bars fa-lg"></i></span> Inicio <b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li><a href="{{ URL::to('explorar') }}" onclick="orderAb()">Explorar</a></li>
          <li id="descApp1" style="display:none;"><a href="https://play.google.com/store/apps/details?id=com.pidafacil.pidafacil">Descargar la App</a></li>
          <li id="descApp2" style="display:none;"><a href="https://itunes.apple.com/us/app/id990772385">Descargar la App</a></li>
          @if($promociones >= 1)<li><a href="{{ URL::to('promociones') }}" onclick="orderAb()">Promociones</a></li>@endif
          <li><a href="{{ URL::to('user/orders') }}" onclick="orderAb()">Repetir pedido</a></li>
          <li class="divider"></li>
          <li><a href="{{ URL::to('cart') }}" onclick="procesar()">Carrito de Compras</a></li>
          <li><a href="{{ URL::to('profile') }}" onclick="orderAb()">Mi perfil</a></li>
          <li class="divider"></li>
          @include('../include/linckChat')
          <li class="divider"></li>
          @if(Auth::check())
          <li><a href="{{ URL::to('logout') }}" onclick="orderAb()">Cerrar sesión</a></li>
          @else
          <li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
          @endif
        </ul>
      </li>
      <!--<li><a href="#"><i class="fa fa-search fa-lg"></i> Buscar</a></li>-->
      <li><a href="{{ URL::to('cart') }}" onclick="procesar()"><span id="iconoContador">{{$cantidad}}&nbsp;</span><i class="fa fa-shopping-cart fa-lg"></i> Carrito de Compras</a></li>
    </ul>
    <form class="navbar-form navbar-left search-bar" role="search">
      <div class="form-group">
        <div class="input-group">
          <label for="tags" class="input-group-addon red-label">
            <i class="fa fa-search"></i>
          </label>
          <input type="text" id="tags" name="tags" class="form-control searchTags" placeholder="Buscar">
        </div>
      </div>
    </form>
    <ul class="nav navbar-nav navbar-right">
      @if(Auth::check())
        @if(Auth::user()->name == '')
          <li><a href="{{ URL::to('profile') }}" onclick="orderAb()"> {{ Auth::user()->email }} </a></li>
        @else
          <li><a href="{{ URL::to('profile') }}" onclick="orderAb()"> {{ Auth::user()->name.' '. Auth::user()->last_name }} </a></li>
        @endif
      <!-- <li><a href="{{ URL::to('logout') }}"> Cerrar sesión</a></li> -->
      @include('../include/linckChat')
      @else
      <li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
      <li style="margin-right:50px;"><a href="{{ URL::to('login#signup') }}">Registrarse</a></li>
      @endif
    </ul>
  </div>
</nav>
<div class="container white_content" style="padding-bottom: 1em;">
  <h1 class="text-center">Estás a dos pasos de completar tu orden</h1>

  @if($errors->has())
    <div class="alert alert-danger" role="alert">
      <ul style="list-style-type: square;">
        @foreach ($errors->all() as $error)
        <li style="list-style: square;">{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row">
    <div class="col-md-9">
      <div class="types table-responsive">
        @if(isset($cart) && count($cart) > 0)
          @foreach($cart as $key=>$product)
            @if(is_array($product))
              <table class="table">
                <caption><h3 style="color: #FFF;">Detalle de tu orden.</h3></caption>
                <thead>
                  <tr>
                    <th>Qty.</th>
                    <th>Producto</th>
                    <th>Condiciones</th>
                    <th>Ingredientes</th>
                    <th>Observaciones</th>
                    <th>Precio</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($product as $val=>$pro)
                    <tr>
                      <th scope="row">{{ $pro['quantity'] }}</th>
                      <td>
                        <strong>{{ $pro['product'] }}</strong><br/>
                        {{ $pro['description'] }}
                      </td>
                      <td nowrap="nowrap">
                        @if($pro['conditions'] != null)
                          @foreach($pro['conditions'] as $condi)
                            {{ $condi['condition_condition'] }}:<br/>
                            <strong>{{ $condi['condition_option'] }}</strong><br/>
                          @endforeach
                        @else
                          No aplica.
                        @endif
                      </td>
                      <td nowrap="nowrap">
                        @if($pro['ingredients'] != null)
                          @foreach($pro['ingredients'] as $ingre)
                            {{ ($ingre['active'] == 1 ? 'Con ' : 'Sin ') . $ingre['ingredient'] }}<br/>
                          @endforeach
                        @else
                          No aplica.
                        @endif
                      </td>
                      <td>{{ $pro['comment'] }}</td>
                      <td class="text-right">$&nbsp;{{ number_format($pro['unit_price'],2) }}</td>
                      <td class="text-right"><strong>$&nbsp;{{ number_format($pro['total_price'],2) }}</strong></td>
                    </tr>
                  @endforeach
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="7" class="text-right"><strong>Sub Total: ${{ number_format($total, 2) }}</strong>
                    <br><br>
                    {{ HTML::image('images/mastercardsecurity.png', 'mas', array('width' => 'auto', 'height' => 'auto')) }}
                    {{ HTML::image('images/verifiedbyvisaecurity.png', 'vis', array('width' => 'auto', 'height' => 'auto')) }}</td>
                  </tr>
                </tfoot>
              </table>
            @else
            @endif
          @endforeach
        @else
        @endif
      </div>
    </div>
    <div class="col-md-3 detail gray_content">
      <?php $show_type2 = 0; ?>

      {{ Form::open(['url' => ['order/create'], 'onSubmit'=>'return enviarCompra()','method' => 'POST', 'class' => 'checkout_order', 'id'=>'form']) }} 
      {{ Form::hidden('nombre_tarjeta', '', array('id' => 'nombre_tarjeta')) }}
      {{ Form::hidden('tipo_tarjeta', '', array('id' => 'tipo_tarjeta')) }}
      <h2>1. Tipo de servicio</h2>
      @foreach($schedule as $key=>$value)
        @if($value->service_type_id == 1 || $value->service_type_id == 3)
          @if($value->service_type_id == 3)
            <input type="hidden" id="serTipe" value="3"/>
          @else
            <input type="hidden" id="serTipe" value="2"/>
          @endif
          <?php
          $now = strtotime($date);
          $open = strtotime($value->opening_time);
          $close = strtotime($value->closing_time);

          if($open<$close){
            $mostrar = ($now>$open and $close>$now)? true:false;
          }else{
            $mostrar = ($now>$open or $close>$now)? true:false;
          }
          ?>
          @if($mostrar)
            <div style="border-bottom: 1px solid #999;" >
              <div class="radio input-lg">
                <label for="service_type_1">
                  @if($value->service_type_id == 1)
                    {{ Form::radio('service_type_id', '1', true,['id' => 'service_type_1', ]) }}
                  @else
                    {{ Form::radio('service_type_id', '3', true,['id' => 'service_type_1', ]) }}
                  @endif
                  A domicilio
                </label>
              </div>
              <div id="user-address" style="display: none; padding: 0px 1em;">
                <div class="form-group">
                  @if(is_array($usr_address) && count($usr_address) > 0)
                  <label for="selType1">Escoge tu dirección de destino:</label>
                  <select name="address_id" id="selType1" class="form-control input-lg" >
                    <!--<option selected disabled>Selecciona tú dirección</option>-->
                    @foreach($usr_address as $key => $value)
                    <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                  </select>
                  <br>
                  <label>Tiempo de entrega estimado</label>
                  <input type="text" id="tiempoEstimado" class="form-control" readonly="readonly" style="background-color:#ffffeb;" >
                </div>
                <div class="form-group">
                  <label>O ingresa una nueva dirección:</label>
                  <button type="button" class="btn btn-primary active" id="new-address" data-toggle="modal" data-target="#address">Ingresa una nueva dirección</button>
                  @else
                  <h4 class="text-warning">No existe ningún registro de direcciones</h4>
                  <button type="button"  id="new-address" data-toggle="modal" data-target="#address">Ingresa una nueva dirección</button>
                  @endif
                </div>
              </div>
            </div>
          @else
            <?php $show_type2 = 1; ?>
          @endif
        @elseif($value->service_type_id == 2)
          <div style="border-bottom: 1px solid #999;">
            <div class="radio input-lg">
              <label for="service_type_2">
                {{ Form::radio('service_type_id', '2', true,['id' => 'service_type_2']) }}
                Para llevar
              </label>
            </div>
            <div id="rest-address" style="display: none; padding: 0px 1em;">
              <div class="form-group">
                <label for="selType2">Escoge la dirección del restaurante:</label>
                <select name="restaurant_address" id="selType2" class="form-control input-lg">
                  <option selected disabled>Selecciona un restaurante</option>
                  @foreach($res_address as $key => $val)
                  <option value="{{ $key }}">{{ $val }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label>Determina una hora para recoger tu pedido:</label>
                <?php
                $otime = strtotime($value->opening_time);
                $oint = date('G', $otime);
                $ctime = strtotime($value->closing_time);
                $cint = date('G', $ctime);

                $num = idate('H');
                $min = idate('i');

                ?>
                <div class="row">
                  <div class="col-xs-6">
                    <select name="hour" id="hour_rest" class="form-control input-lg">
                      @for($i=$oint; $i < 24; $i++)

                      @if(($oint < $cint and ($i>=$num and $i<=$cint))
                      or $oint > $cint and ($i>=$num or $i<=$cint)) )
                      <option value="{{ $i }}">{{ $i }}</option>
                      @endif
                      @endfor
                    </select>
                  </div>
                  <div class="col-xs-6">
                    <select name="minutes" id="min_rest" class="form-control input-lg">
                      <option disabled>mm</option>
                      <option value="00">00</option>
                      <option value="05">05</option>
                      @for($i = 10; $i < 60; $i+=5 )
                      <?php $minutes = array($i) ?>
                      @foreach($minutes as $key=>$value)
                      <option value="{{ $value }}">{{ $value }}</option>
                      @endforeach
                      @endfor
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>
        @endif
      @endforeach
      
      <div class="row">
        <div class="col-xs-12">
          <h3>* Datos Adicionales</h3>
          <div class="form-group">
          <label>Nombre</label>
            {{ Form::text('nombre_user', null, array('id'=>'nombre_user', 'placeholder' => 'Nombre','title'=> 'Es obligatorio que ingrese un nombre', 'required', 'class' => 'form-control ')) }}
            <br>
            <label>Teléfono</label>            
            <input type="number" inputmode="numeric" pattern="/^([0-9])*$/" name="telefono_user" id="telefono_user" placeholder:"Para contactarte si es necesario" title="Es obligatorio que ingrese un número" required="required" class="form-control" onKeyPress="return soloNumeros(event)">
          </div>
        </div>
      </div>

      <div class="payment-method">
        <h2>2. Método de pago</h2>
        <div style="border-bottom: 1px solid #999;">
          <div class="radio input-lg">
            <label for="method_1">
              {{ Form::radio('payment_method_id', '2', null,['id' => 'method_1']) }}
              Tarjeta de cr&eacute;dito
            </label>
          </div>
          <div id="credit-card" style="display: none; padding: 0px 1em;">
            <div class="form-group">
              <p class="help-block">Campos marcados con * son obligatorios</p>
              {{ Form::text('user_credit', null, array('placeholder' => '* Titular de la tarjeta', 'required', 'class' => 'form-control credit_required')) }}
            </div>
            <div id="cc_frm_grp" class="form-group has-error has-feedback">
              <div class="input-group">
                <span class="input-group-addon" id="inp_grp_add">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                <input type="text" class="form-control" aria-describedby="inputGroupSuccess1Status" name="credit_card" placeholder="* Número de tarjeta" id="credit_card" autocomplete="off">
              </div>
              <span id="cc_stat_icon" class="fa fa-exclamation fa-lg form-control-feedback" aria-hidden="true" style="top: 10px;"></span>
            </div>
            <div id="ccv_grp" class="form-group has-error has-feedback">
              <input name="secure_code" placeholder="* Código de seguridad" class="form-control" id="inp_ccv" aria-describedby="inputSuccess2Status" type="text" autocomplete="off">
              <span id="ccv_icon" class="fa fa-exclamation fa-lg form-control-feedback" aria-hidden="true" style="top: 10px;"></span>
            </div>
            <div class="form-group">
              <label>Fecha de vencimiento:</label>
              <div class="row">
                <div class="col-xs-6">
                  {{ Form::select('month',[
                  null => 'Mes',
                  1 => '01- Enero',
                  2 => '02- Febrero',
                  3 => '03- Marzo',
                  4 => '04- Abril',
                  5 => '05- Mayo',
                  6 => '06- Junio',
                  7 => '07- Julio',
                  8 => '08- Agosto',
                  9 => '09- Septiembre',
                  10 => '10- Octubre',
                  11 => '11- Noviembre',
                  12 => '12- Diciembre'], null, array('id'=>'month','class'=>'form-control input-lg')) }}
                </div>
                <div class="col-xs-6">
                <?php
                  $ystart = date('Y');
                  $exp_years = array();
                  for ($y = $ystart; $y <= ($ystart + 20); $y++) {
                    $exp_years[$y] = $y;
                  }
                  $exp_years = array_add($exp_years, '', 'Año');
                  ksort($exp_years);
                ?>
                {{ Form::select('year', $exp_years, null, array('id'=>'year','class'=>'form-control input-lg')) }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <div style="border-bottom: 1px solid #999;">
        <div class="radio input-lg">
          <label for="method_3">
            {{ Form::radio('payment_method_id', '3', null,['id' => 'method_3']) }}
            Tigo Money
          </label>
        </div>
        <div id="tm" style="display:none; padding: 0px 1em;">
          <div class="form-group">
            <label>Número a debitar</label>            
            {{ Form::text('num_debitar', null, array('id'=>'num_debitar', 'placeholder' => 'Número a debitar', 'class' => 'form-control ')) }}
            <br>
            <label>Seleccionar Billetera</label>
            {{ Form::select('billetera', [
               'Dinero recibido local' => 'Dinero recibido local',
               'Mis abonos' => 'Mis abonos',
               'Dinero recibido internacionalmente' => 'Dinero recibido internacionalmente']
            ) }}
            {{ Form::hidden('cargo_uso_tigo', '', array('id' => 'cargo_uso_tigo')) }}
            {{ Form::hidden('costo_envio', '', array('id' => 'costo_envio')) }}
            {{ Form::hidden('total_previo', '', array('id' => 'total_previo')) }}
            {{ Form::hidden('antes_descuento', '', array('id' => 'antes_descuento')) }}
          </div>
        </div>
      </div>

      <div style="border-bottom: 1px solid #999;">
        <div class="radio input-lg">
          <label for="method_2">
            {{ Form::radio('payment_method_id', '1', true,['id' => 'method_2']) }}
            Efectivo
          </label>
        </div>
        <div id="cash" style="display: none; padding: 0px 1em;">
          <div class="form-group">
            <p class="help-block">* Recuerde que debe ingresar una cantidad mayor al total de su orden</p>
            <select id="cash-select" name="cash" class="form-control input-lg"></select>
          </div>
          <p id="cambio">Su cambio es de: ${{ number_format(0, 2) }}</p>
        </div>
        <div id="fullDetail">
          <div class="row">
            <div class="col-xs-8">
              <label>Sub total: </label>
            </div>
            <div class="col-xs-4 text-right">
              $ <span id="subTotal"></span>
            </div>
          </div>

          <div class="row shipping-charge">
            <div class="col-xs-8">
              <label>Cargo por envío: </label>
            </div>
            <div class="col-xs-4 text-right">
              $ <span id="shipping_charge"></span>
            </div>
          </div>

          <div class="row" id="cargo_tigo" style="display:none;">
            <div class="col-xs-8">
              <label>Cargo por uso de Tigo Money: </label>
            </div>
            <div class="col-xs-4 text-right">
              $ <span id="tigo_charge"></span>
            </div>
          </div>

          <div class="row" id="cargo_tarjeta" style="display:none;">
            <div class="col-xs-8">
              <label>Cargo por uso de tarjeta: </label>
            </div>
            <div class="col-xs-4 text-right">
              $ <span id="card_charge"></span>
            </div>
          </div>
          <div class="row" id="descuento_banco" style="display:none;">
            <div class="col-xs-8">
              <label id ="label_descuento"/>
            </div>
            <div class="col-xs-4 text-right">
              - $ <span id="discount"></span>
            </div>
          </div>
          <div class="row">
            <div class="col-xs-8">
              <label>El total a pagar es: </label>
            </div>
            <div class="col-xs-4 text-right">
              $ <span id="total"></span>
            </div>
          </div>

        </div>
      </div>
    </div>
    <div class="text-center" style="padding-top: 1em;">
      {{ Form::submit('Comprar', array('id'=>'botDesh','class'=>'btn btn-success btn-lg')) }}
    </div>
    {{ Form::close() }}
  </div>

</div>
<div class="modal fade gray_content" id="address" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  {{ Form::open(array('url' => 'user/address/create', 'onsubmit'=>'return enviar()', 'method' => 'POST', 'class' => 'new-address', 'id' => 'address-form')) }}
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4>Nueva dirección</h4>
        </div>
        <div class="modal-body" >
          <p>Completa el siguiente formulario para agregar una nueva dirección</p>
          <p class="bullet">Campos marcados con * son obligatorios</p>
          <br>
          <div class="form-group new_add_modal">
            {{ Form::text('address_name', null, array('id'=>'address_name','placeholder' => '* Nombre de registro ej. Mi casa', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
            {{ Form::text('address_1', null, array('id'=>'address_1','placeholder' => '* Dirección', 'required' => 'required', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
            <!--{{ Form::text('address_2', null, array('id'=>'address_2','placeholder' => 'Complemento de dirección', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}-->
            {{ Form::text('reference', null, array('id'=>'reference','placeholder' => 'Referencia ej. Frente a "La Chulona"...', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
            <!--{{ Form::select('state', $states, '0', array('id'=>'state','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}-->
            <!--{{ Form::select('municipality', $municipalities, NULL, array('id'=>'municipality','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}-->
            <!--{{ Form::select('zone_id', $zones, NULL, array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}-->
            {{Form::select('zone_id',$combobox, $selected,array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required'))}}
            <p>&nbsp;</p>
            <span>Para un mejor servicio, ¿Desea compartir su ubicación?
              &nbsp;<input type="radio" name="location" value="si"> Si
              &nbsp;&nbsp;&nbsp;<input type="radio" name="location" value="no"> No
            </span>
            {{ Form::hidden('coordenadas', '', array('id' => 'coordenadas')) }}
          </div>
        </div>
        <div class="modal-footer" >
          {{ Form::submit('Agregar', array('id' => 'btnSubmit','class'=>'btn btn-default button_150')) }}
        </div>
      </div>
    </div>
  {{ Form::close() }}
</div>
</div>
<input type="hidden" id="contador_direc" value="{{$show_type2}}">
<div class="modal fade" id="modalHora" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <!--<div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel" style="color:#444444;">Aviso</h4>
        </div>-->
        <div class="modal-body" style="color:#444444; text-align:center; height:55px;">
            No puede elegir una hora menor a la actual.
        </div>
          <div class="modal-footer">
            <button type="button" id="noProduct" class="btn btn-default" data-dismiss="modal">Ok</button>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="mensaje" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Aviso</h4>
            </div>
            <div class="modal-body">
                <p>Debes ingresar una dirección de entrega para tu pedido.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="http://jqueryvalidation.org/files/dist/jquery.validate.min.js"></script>
<script src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
<script>
  // just for the demos, avoids form submit
  jQuery.validator.setDefaults({
    debug: true,
    success: "valid"
  });
  $("#telefono_user").validate(function(event){
    event.preventDefault();
    rules: {
      field: {
        required: true
      }
    }
  });

  $("#nombre_user").validate(function(event){
    event.preventDefault();
    rules: {
      field: {
        required: true
      }
    }
  });

  function soloNumeros(e){ 
    //var key = window.Event ? e.which : e.keyCode 
    //return ((key >= 48 && key <= 57) || (key==8))

    k = (document.all) ? e.keyCode : e.which;
    if (k==8 || k==0) return true;
    patron = /^([0-9])*$/;
    n = String.fromCharCode(k);
    return patron.test(n);
  }


</script>

<script type="text/javascript">

  function procesar(){
    appboy.logCustomEvent("Ir al Carrito");
  }

  function orderAb(){
    appboy.logCustomEvent("Order Abandoned");
  }
  
  function valida(e){
    tecla = (document.all) ? e.keyCode : e.which;

    //Tecla de retroceso para borrar, siempre la permite
    if(tecla==8){
      return true;
    }
        
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
  }

  /*$('#modalHora').on('show.bs.modal', function () {
         $(this).find('.modal-dialog').css({
                width:'400px', //probably not needed
                height:'120px', //probably not needed 
                'max-height':'100%'
         });
         $(this).find('.modal-footer').css({
                width:'auto', //probably not needed
                height:'50px', //probably not needed 
                'max-height':'100%',
                'padding-top' : '5px',
                'padding-right' : '8px'
         });
    });

  $("#min_rest").change(function(){
    var tiempo = new Date();
    var minuto_actual = tiempo.getMinutes();
    var minuto  = this.value;

    if(minuto < minuto_actual){
      event.preventDefault();
      $('#modalHora').modal('show');
    }
  });*/

$("#btnSubmit").click(function(event){
  if($("#zone_id option:selected" ).val()== 'none'){
      event.preventDefault();
      alert("Debes elegir una zona.")
  }else{
  }

  if($("input:radio[name='location']").is(':checked')) {  
      //alert("seleccionó: " + $('input:radio[name=location]:checked').val());
      if($('input:radio[name=location]:checked').val() == 'si'){
        var coords = localStorage.getItem('coords');
        $("#coordenadas").val(coords);
      }else{
          $("#coordenadas").val('');
      }
  }else{
      event.preventDefault();
      alert("Debes elegir una opción sobre la ubicación.")  
  }
});

$("input:radio[name=location]").click(function(){
  if($('input:radio[name=location]:checked').val() == 'si'){
           
    window.open('http://pidafacil.com/soporte/ubicacion_cliente', 'chat', 'top=10px,left=20px,width=600px,height=450px');
  }else{
    $("#coordenadas").val('');
  }
});

function sumaCardCharge(){
  if($("#serTipe").val() == 3){
    if ($("#service_type_1").prop('checked')) {
      var tot = $("#subTotal").text();
      var shipping_charge = $("#shipping_charge").text();
      var cargoTarjeta = 0.00;
      var cargoTigo = 0.00;

      if ($("#method_1").prop('checked')){
        cargoTarjeta = 0.04;
        $("#cargo_tarjeta").show();
        $("#cargo_tigo").hide();

        var cargo = (parseFloat(tot) + parseFloat(shipping_charge)) * cargoTarjeta;
        
        var red = cargo.toString();
        var valor = red.split('.')
        var array = new Array();
        var array2 = new Array();
        var array3 = new Array();

        array = valor;
        array2 = array[1];
        array3 = array2;

        var totalMasCargo = (parseFloat(tot) + parseFloat(shipping_charge)) + parseFloat(cargo);
        
        console.log('valorCob: '+array)
        console.log('Cobro1: '+shipping_charge);
        console.log('Cobro2: '+array3[2]);

        if(array3[2] != 'undefined' && array3[2] <= 5){
          var resultRed = (parseFloat(cargo) + 0.01);
          var resultTot = (parseFloat(totalMasCargo) + 0.01);
          console.log('Resultado Final: '+resultRed.toFixed(2));
          console.log('Resultado total: '+resultTot.toFixed(2));
          console.log('Resultado sin red: '+totalMasCargo.toFixed(2));
        }else{
          var resultRed = cargo;
          var resultTot = totalMasCargo;
        }

        $("#card_charge").text(resultRed.toFixed(2));
        $("#total").text(resultTot.toFixed(2));
        $("#antes_descuento").val(resultTot.toFixed(2));

      }else if($("#method_3").prop('checked')){
        cargoTigo = 0.025;
        $("#cargo_tigo").show();
        $("#num_debitar").attr("required", "true");

        var cargoT = (parseFloat(tot) + parseFloat(shipping_charge)) * cargoTigo;
        var cargoFinal = (parseFloat(cargoT) + 0);
        var red = cargoT.toString();
        var valor = red.split('.')
        var array = new Array();
        var array2 = new Array();
        var array3 = new Array();

        array = valor;
        array2 = array[1];
        array3 = array2;

        var totalTMasCargo = (parseFloat(tot) + parseFloat(shipping_charge)) + parseFloat(cargoT);
        console.log('valorCob: '+array)
        console.log('Cobro1: '+shipping_charge);
        console.log('Cobro2: '+array3[2]);

        if(array3[2] != 'undefined' && array3[2] <= 5){
          var resultRed = (parseFloat(cargoT) + 0.01);
          var resultTot = (parseFloat(totalTMasCargo) + 0.01);
          console.log('Resultado Final: '+resultRed.toFixed(2));
          console.log('Resultado total: '+resultTot.toFixed(2));
          console.log('Resultado sin red: '+totalTMasCargo.toFixed(2));
        }else{
          var resultRed = cargoT;
          var resultTot = totalTMasCargo;
        }

        $("#tigo_charge").text(resultRed.toFixed(2));
        $("#total").text(resultTot.toFixed(2));
        $("#antes_descuento").val(resultTot.toFixed(2));

        $("#cargo_uso_tigo").val(cargoT.toFixed(2));
      }
    }else if ($("#service_type_2").prop('checked')) {
      var tot = $("#subTotal").text();
      //var shipping_charge = $("#shipping_charge").text();
      var cargoTarjeta = 0.00;
      var cargoTigo = 0.00;

      if ($("#method_1").prop('checked')){
        cargoTarjeta = 0.04;
        $("#cargo_tarjeta").show();
        $("#cargo_tigo").hide();

        var cargo = (parseFloat(tot)) * cargoTarjeta;
        
        var red = cargo.toString();
        var valor = red.split('.')
        var array = new Array();
        var array2 = new Array();
        var array3 = new Array();

        array = valor;
        array2 = array[1];
        array3 = array2;

        var totalMasCargo = (parseFloat(tot)) + parseFloat(cargo);
        
        console.log('valorCob: '+array)
        //console.log('Cobro1: '+shipping_charge);
        console.log('Cobro2: '+array3[2]);

        if(array3[2] != 'undefined' && array3[2] <= 5){
          var resultRed = (parseFloat(cargo) + 0.01);
          var resultTot = (parseFloat(totalMasCargo) + 0.01);
          console.log('Resultado Final: '+resultRed.toFixed(2));
          console.log('Resultado total: '+resultTot.toFixed(2));
          console.log('Resultado sin red: '+totalMasCargo.toFixed(2));
        }else{
          var resultRed = cargo;
          var resultTot = totalMasCargo;
        }

        $("#card_charge").text(resultRed.toFixed(2));
        $("#total").text(resultTot.toFixed(2));
        $("#antes_descuento").val(resultTot.toFixed(2));

      }else if($("#method_3").prop('checked')){
        cargoTigo = 0.025;
        $("#cargo_tigo").show();

        var cargoT = (parseFloat(tot)) * cargoTigo;
        var cargoFinal = (parseFloat(cargoT) + 0);
        var red = cargoT.toString();
        var valor = red.split('.')
        var array = new Array();
        var array2 = new Array();
        var array3 = new Array();

        array = valor;
        array2 = array[1];
        array3 = array2;

        var totalTMasCargo = (parseFloat(tot)) + parseFloat(cargoT);
        console.log('valorCob: '+array)
        //console.log('Cobro1: '+shipping_charge);
        console.log('Cobro2: '+array3[2]);

        if(array3[2] != 'undefined' && array3[2] <= 5){
          var resultRed = (parseFloat(cargoT) + 0.01);
          var resultTot = (parseFloat(totalTMasCargo) + 0.01);
          console.log('Resultado Final: '+resultRed.toFixed(2));
          console.log('Resultado total: '+resultTot.toFixed(2));
          console.log('Resultado sin red: '+totalTMasCargo.toFixed(2));
        }else{
          var resultRed = cargoT;
          var resultTot = totalTMasCargo;
        }

        $("#tigo_charge").text(resultRed.toFixed(2));
        $("#total").text(resultTot.toFixed(2));
        $("#antes_descuento").val(resultTot.toFixed(2));
        $("#cargo_uso_tigo").val(cargoT.toFixed(2));
      }
    }
  }
}

$("#botDesh").click(function(event){
  var test = $("#selType1").val();
  
  if(typeof(test) == 'undefined'){
    event.preventDefault();
    $('#mensaje').modal('show');
  }else{
  }
});


function enviarCompra(){
  appboy.logCustomEvent("Order Complete");

  document.getElementById("botDesh").value = "Enviando...";
  document.getElementById("botDesh").disabled = true;

  @if(isset($cart) && count($cart) > 0)
    @foreach($cart as $key=>$product)
      @if(is_array($product))
        @foreach($product as $val=>$pro)
          appboy.logPurchase("{{ $pro['product_id'] }}", {{ number_format($pro['unit_price'],2) }}, "USD", {{ $pro['quantity'] }});
        @endforeach
      @endif
    @endforeach
  @endif

  return true;
}

var sub_total = {{ number_format($total, 2) }};
var shipping_charge = 0.00;


  function mostrar_ubicacion(p){
    $("#coordenadas").val(p.coords.latitude+','+p.coords.longitude);
  }
  
$(document).ready(function(){

  // Displays a slideup type in-app message.
  /*var message = new appboy.ab.SlideUpMessage("Push notification prueba");
  message.slideFrom = appboy.ab.InAppMessage.SlideFrom.TOP;
  appboy.display.showInAppMessage(message);*/

  /*TIEMPO ESTIMADO*/
  /*$.ajax({
    url: 'getTime',
    type: 'post',
    dataType: 'json',
    data: {direccion: $("#selType1").val()}
  })
  .done(function(result){
    console.log(result);
  })
  .fail(function(result){
    console.log(result);
  });*/

  /*if (typeof navigator.geolocation == 'object'){
      navigator.geolocation.getCurrentPosition(mostrar_ubicacion);
  }*/
   
  

  $.post('{{URL::to("/order/getTime")}}',
    {'direccion':$("#selType1").val()},
    function(data){
      console.log(data);
      if(typeof data !== 'undefined' && data.length > 0){
        var sep = data[0].prom_time.split(" m");
        var tiempo = parseInt(sep[0]) + parseInt(20);
        $("#tiempoEstimado").val(tiempo + ' min');
      }else{
        $("#tiempoEstimado").val('Servicio no disponible');
      }
    }, 'json'
  );

  /*FIN DE TIEMPO ESTIMADO*/

  var tiempo = new Date();
  var hora = tiempo.getHours();

  if($("#cont").val() > 0){
    $("#iconoContador").show();
  }else{
    $("#iconoContador").hide();
  }

  var sel = $('#zone_id');
  var opts_list = sel.find('option');
  opts_list.sort(function(a, b) { return $(a).text() > $(b).text() ? 1 : -1; });
  sel.html('').append(opts_list);

  $.post('{{URL::to("/order/getUserData")}}',
    {'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val()},
    function(data){

        if(data[0].name== null || data[0].last_name== null){
          $("#nombre_user").val('');
          $("#telefono_user").val('');
          if($("#method_3").prop('checked')){
            $("#num_debitar").val('');
          }
        }else{
          $("#nombre_user").val(data[0].name+' '+data[0].last_name);
          $("#telefono_user").val(data[0].phone);
          if($("#method_3").prop('checked')){
            $("#num_debitar").val(data[0].phone);
          }
        }
    }, 'json'
  );

  if($("#contador_direc").val() == 0 && $("#serTipe").val() >= 2){
    $("#service_type_2").removeAttr('checked');
    $("#service_type_1").prop( "checked", true );
  }else if($("#serTipe").val() < 2){
    $("#service_type_2").prop( "checked", true );
  }

    setValues();

    $("#cash").show();
    $('.cash_required').attr('required', 'true');
    $("#credit-card").hide();
    $(".credit_required").removeAttr('required', 'true');
    $("#cargo_tarjeta").hide();
    $("#cargo_tigo").hide();
    setValues();

    if($("#service_type_1").prop('checked')){
      $("#user-address").show();
      $("#rest-address").hide();
      $(".shipping-charge").show();
      console.log("service type 1");
      if ($("#service_type_1").val() == 3){        
        //var subtotal = $("#subTotal").val();
          $.post('{{URL::to("/order/shipping_charge")}}',
          {'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val(), 'subtotal':localStorage.getItem("subtotal")},
          function(data){
            console.log(data);
            if (data.status){
              console.log(localStorage.getItem("subtotal"));
              $("#total_previo").val(localStorage.getItem("subtotal"));
              if(data.type == 'free'){
                //shipping_charge = data.data.shipping_charge;
                shipping_charge = 0.0;
                shipping_charge2 = data.data.monto_envio;
                
                $("#costo_envio").val(shipping_charge2);                
                setValues();
              }else if(data.type == 'no_free'){
                shipping_charge = data.data.shipping_charge;
                $("#costo_envio").val(shipping_charge);                
                setValues();
              }
              
            } else{
              alert("Error en el servidor, por favor refrescar");
            }
          }, 'json'
        );
        $.post('{{URL::to("/order/getTime")}}',
          {'direccion':$("#selType1").val()},
          function(data){
            console.log(data);
            if(typeof data !== 'undefined' && data.length > 0){
              var sep = data[0].prom_time.split(" m");
              var tiempo = parseInt(sep[0]) + parseInt(20);
              $("#tiempoEstimado").val(tiempo + ' min');
            }else{
              $("#tiempoEstimado").val('Servicio no disponible');
            }
          }, 'json'
        );
      } else{
        shipping_charge = {{$parent_shipping_cost}};
        setValues();
        if($(".shipping-charge").val()==0.0){
          $(".shipping-charge").hide();
        }
      }
    }

    if($("#service_type_2").prop('checked')){
      $("#rest-address").show();
      $("#user-address").hide();
      $(".shipping-charge").hide();
      //$("#cargo_tigo").show();
      shipping_charge = 0.0;
      $("#costo_envio").val(shipping_charge);
      setValues();

      if($("#method_3").prop('checked')){
        $("#cargo_tigo").show();
        $("#cargo_tarjeta").hide();
      }else if($("#method_1").prop('checked')){
        $("#cargo_tarjeta").show();
        $("#cargo_tigo").hide();
      }
    }

  $("#selType1").change(function(){
    if ($("#service_type_1").val() == 3){
      $.post('{{URL::to("/order/shipping_charge")}}',
        {'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val(), 'subtotal':localStorage.getItem("subtotal")},
          function(data){
            console.log(data);
            if (data.status){
              console.log(localStorage.getItem("subtotal"));
              $("#total_previo").val(localStorage.getItem("subTotal"));
              if(data.type == 'free'){
                //shipping_charge = data.data.shipping_charge;
                shipping_charge = 0.0;
                shipping_charge2 = data.data.monto_envio;
                
                $("#costo_envio").val(shipping_charge2);                
                setValues();
              }else if(data.type == 'no_free'){
                shipping_charge = data.data.shipping_charge;
                $("#costo_envio").val(shipping_charge);                
                setValues();
              }
              
            } else{
              alert("Error en el servidor, por favor refrescar");
            }
        }, 'json'
      );

      $.post('{{URL::to("/order/getTime")}}',
        {'direccion':$("#selType1").val()},
        function(data){
          console.log(data);
          if(typeof data !== 'undefined' && data.length > 0){
            var sep = data[0].prom_time.split(" m");
            var tiempo = parseInt(sep[0]) + parseInt(20);
            $("#tiempoEstimado").val(tiempo + ' min');
          }else{
            $("#tiempoEstimado").val('Servicio no disponible');
          }
        }, 'json'
      );
    }else{
      shipping_charge = {{$parent_shipping_cost}};
      $("#costo_envio").val(shipping_charge);
      setValues();
    }
  });

  if($("#method_1").prop('checked')){
    $("#credit-card").show();
    $("#cargoTarjeta").show();
    $("#cash").hide();
  }

  var dispositivo = navigator.userAgent.toLowerCase();
    if( dispositivo.search(/iphone|ipod|ipad|android/) > -1 ){
      //alert("Esta navegando en un movil");

      var isMobile = {
        Android: function() {
            return navigator.userAgent.match(/Android/i);
        },
        BlackBerry: function() {
            return navigator.userAgent.match(/BlackBerry/i);
        },
        iOS: function() {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i);
        },
        Opera: function() {
            return navigator.userAgent.match(/Opera Mini/i);
        },
        Windows: function() {
            return navigator.userAgent.match(/IEMobile/i);
        },
        any: function() {
            return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
        }
      };

      if(isMobile.iOS()){
        $("#descApp2").show();
        $("#descApp1").hide();
      }else if(isMobile.Android()){
        $("#descApp1").show();
        $("#descApp2").hide();
      }
    }
  });

$("#selType1").change(function(){
  if ($("#service_type_1").val() == 3){
    $.post('{{URL::to("/order/shipping_charge")}}',
      {'restaurant_id':{{$restaurant_id}}, 'address_id':$(this).val()},
      function(data){
        if (data.status){
          shipping_charge = data.data.shipping_charge;
          $("#costo_envio").val(shipping_charge);
          setValues();
        } else{
          alert("Error en el servidor, por favor refrescar");
        }
      }, 'json'
    );

    $.post('{{URL::to("/order/getTime")}}',
      {'direccion':$("#selType1").val()},
      function(data){
        console.log(data);
        if(typeof data !== 'undefined' && data.length > 0){
          var sep = data[0].prom_time.split(" m");
          var tiempo = parseInt(sep[0]) + parseInt(20);
          $("#tiempoEstimado").val(tiempo + ' min');
        }else{
          $("#tiempoEstimado").val('Servicio no disponible');
        }
      }, 'json'
    );
  }else{
    shipping_charge = {{$parent_shipping_cost}};
    $("#costo_envio").val(shipping_charge);
    setValues();
    $(".shipping-charge").hide();
  }
});

function setValues(){
  var total = parseFloat(sub_total) + parseFloat(shipping_charge);
  $("#subTotal").text(sub_total.toFixed(2));
  $("#total_previo").val(sub_total.toFixed(2));
  
  localStorage.setItem("subtotal", sub_total.toFixed(2));
  $("#shipping_charge").text(parseFloat(Math.round(shipping_charge * 100) / 100).toFixed(2));
  $("#total").text(total.toFixed(2));
  $("#antes_descuento").val(total.toFixed(2));
  $("#costo_envio").val(parseFloat(Math.round(shipping_charge * 100) / 100).toFixed(2));
  sumaCardCharge();
  $("#cash-select").html('');
  var v = Math.ceil(total);
  var five = 0;
  var ten = 0;
  var tweny = 0;

  $("#cash-select").append($("<option/>", {'val':total, 'text':"$"+total.toFixed(2)}));

  //Imprimir el redondeado
  if(v!=total)
  $("#cash-select").append($("<option/>", {'val':v, 'text':"$"+v.toFixed(2)}));

  //Incrementar si redondeado es multiplo de 5
  if(v%5==0){
    v++;
  }

  for(var i = v; i <=v+20; i++){
    if(five==0 && ten==0 && tweny==0 && i%5==0){
      //Es múltiplo de 5
      five=i;
      $("#cash-select").append($("<option/>", {'val':i, 'text':"$"+i.toFixed(2)}));
    }

    if(ten==0 && tweny==0 && i%10==0){
      //Es múltiplo de 10
      ten = i;

      if(five!=i)
      $("#cash-select").append($("<option/>", {'val':i, 'text':"$"+i.toFixed(2)}));
    }

    if(tweny==0 && i%20==0){
      //Es múltiplo de 20
      tweny = i;
      if(five!=i && ten!=i)
      $("#cash-select").append($("<option/>", {'val':i, 'text':"$"+i.toFixed(2)}));
    }
  }

  var str = "";
  var s = $("#cash-select").val();
  var t = parseFloat(sub_total)+parseFloat(shipping_charge);
  if (s > 0){
    var nt = s - t;
    str += "Su cambio será de: $ <strong>" + nt.toFixed(2) + "</strong>";
  } else{
    str += "Su cambio será de: <strong>$ 0.00</strong>";
  }

  $("#cambio").html(str);
}

$("#service_type_1").click(function(){
  $("#user-address").show();
  $("#rest-address").hide();
  $(".shipping-charge").show();
  console.log("service type 1");
  if ($("#service_type_1").val() == 3){
    console.log($("#selType1").val());
    $.post('{{URL::to("/order/shipping_charge")}}',
      {'restaurant_id':{{$restaurant_id}}, 'address_id':$("#selType1").val()},
      function(data){
        if (data.status){
          shipping_charge = data.data.shipping_charge;
          console.log(shipping_charge);
          setValues();
        } else{
          alert("Error en el servidor, por favor refrescar");
        }
      }, 'json'
    );

    $.post('{{URL::to("/order/getTime")}}',
      {'direccion':$("#selType1").val()},
      function(data){
        console.log(data);
        if(typeof data !== 'undefined' && data.length > 0){
          var sep = data[0].prom_time.split(" m");
          var tiempo = parseInt(sep[0]) + parseInt(20);
          $("#tiempoEstimado").val(tiempo + ' min');
        }else{
          $("#tiempoEstimado").val('Servicio no disponible');
        }
      }, 'json'
    );
  } else{
    shipping_charge = {{$parent_shipping_cost}};
    setValues();
    if($(".shipping-charge").val()==0.0){
      $(".shipping-charge").hide();
    }
  }
});

$("#service_type_2").click(function(){
  $("#rest-address").show();
  $("#user-address").hide();
  $(".shipping-charge").hide();
  shipping_charge = 0.0;
  setValues();

  if($("#method_3").prop('checked')){
    $("#cargo_tigo").show();
    $("#cargo_tarjeta").hide();
  }else if($("#method_1").prop('checked')){
    $("#cargo_tarjeta").show();
    $("#cargo_tigo").hide();
  }
});

$("#method_1").click(function(){
  $("#credit-card").show();
  $('.credit_required').attr('required', 'true');
  $("#cash").hide();
  $(".cash_required").removeAttr('required', 'true');
  $("#tm").hide();
  setValues();
});

$("#method_2").click(function(){
  $("#cash").show();
  $('.cash_required').attr('required', 'true');
  $("#credit-card").hide();
  $(".credit_required").removeAttr('required', 'true');
  $("#cargo_tarjeta").hide();
  $("#tm").hide();
  $("#cargo_tigo").hide();
  setValues();
});

$("#method_3").click(function(){
  $("#tm").show();
  $("#cargo_tigo").show();
  $('.cash_required').attr('required', 'true');
  $("#credit-card").hide();
  $(".credit_required").removeAttr('required', 'true');
  $("#cargo_tarjeta").hide();
  $("#cash").hide();
  setValues();
});

$("#cash-select").change(function() {
  var str = "";
  var s = $(this).val();
  var t = parseFloat(sub_total)+parseFloat(shipping_charge);
  if (s > 0){
    var nt = s - t;
    str += "Su cambio será de: $ <strong>" + nt.toFixed(2) + "</strong>";
  } else{
    str += "Su cambio será de: <strong>$ 0.00</strong>";
  }

  $("#cambio").html(str);

});

//se valida el codigo de seguridad para los tipos de tarjetas de credito
var ccv_n = 0;
$(function() {
  $('#credit_card').validateCreditCard(function(result) {
    $('#showmsg').html('Card type: ' + (result.card_type == null ? '-' : result.card_type.name)
    + '<br>Valid: ' + result.valid
    + '<br>Length valid: ' + result.length_valid
    + '<br>Luhn valid: ' + result.luhn_valid);
    if (result.card_type != null){
      $('#inp_grp_add').addClass(result.card_type.name);
      $("#tipo_tarjeta").val(result.card_type.name);
      if(result.card_type.name == 'amex'){
        ccv_n = 4
        $("#nombre_tarjeta").val(ccv_n);
        $('#inp_ccv').attr("minlength", ccv_n);
        $('#inp_ccv').attr("maxlength", ccv_n);
      }else{
        ccv_n = 3
        $("#nombre_tarjeta").val(ccv_n);
        $('#inp_ccv').attr("maxlength", ccv_n);
        $('#inp_ccv').attr("minlength", ccv_n);
      }
      
      if (result.valid == true){
        var dataBines = {'credit':$('#credit_card').val()};
        $.ajax({
          type: 'POST',
          data: dataBines,
          url: '{{ URL::to("cart/bines") }}',
          success: function(res){
            if(!res.error && res.discount){
              var total = $("#total").text();
              $("#antes_descuento").val(total);
              var descuento = total*(res.porcentaje/100);
              var nuevoTotal = total - descuento;
              $('#descuento_banco').show();
              $('#discount').text(descuento.toFixed(2));
              $('#total').text(nuevoTotal.toFixed(2));
              $('#label_descuento').text('Descuento del ' + res.porcentaje + '%')
              
            } 
            else{
              $('#descuento_banco').hide();
              var total_antiguo = $("#antes_descuento").val();
              $('#total').text(total_antiguo);
            }
          }
        });
        $('#cc_frm_grp').removeClass('has-error');
        $('#cc_frm_grp').addClass('has-success');
        $('#cc_stat_icon').removeClass('fa-exclamation');
        $('#cc_stat_icon').addClass('fa-check');
      } else{

        $('#descuento_banco').hide();
        var total_antiguo = $("#antes_descuento").val();
        $('#total').text(total_antiguo);

        $('#cc_frm_grp').removeClass('has-success');
        $('#cc_frm_grp').addClass('has-error');
        $('#cc_stat_icon').addClass('fa-exclamation');
        $('#cc_stat_icon').removeClass('fa-check');
      }
    } else{
      $('#inp_grp_add').removeClass();
      $('#inp_grp_add').addClass('input-group-addon');
      $('#inp_ccv').attr("maxlength", - 1);
    }
  }, { accept: ['visa', 'mastercard', 'amex'] });
});

$("#inp_ccv").keyup(function() {
  this.value = this.value.replace(/[^0-9\.]/g, '');
  var ln = $(this).val().length;
  if (ln == ccv_n){
    $('#ccv_grp').removeClass('has-error');
    $('#ccv_grp').addClass('has-success');
    $('#ccv_icon').removeClass('fa-exclamation');
    $('#ccv_icon').addClass('fa-check');
  } else{
    $('#ccv_grp').removeClass('has-success');
    $('#ccv_grp').addClass('has-error');
    $('#ccv_icon').removeClass('fa-check');
    $('#ccv_icon').addClass('fa-exclamation');
  }
});
</script>


<!-- for new addresses -->
<script>
var municipality = '';
var zone = '';
var idEdit = 0;

$(document).ready(function(){
  $("#state").change(function(){
    getMunicipalities($(this).val());
  });

  $("#municipality").change(function(){
    getZones($(this).val());
  });

  $('#address').on('hidden.bs.modal', function () {
    idEdit = 0;
    $("#address-form")[0].reset();
  });
});

function enviar(){
  var params = $("#address-form").serialize();
  if(idEdit!=0){
    params+='&address_id='+idEdit;
  }
  $.post((idEdit!=0)? '{{URL::to("user/address/edit")}}':'{{URL::to("user/address/create")}}',
  params,
  function(data){
    if(data.status){
      $("#address").modal('hide');
      location.reload();
    }else{
      var s;
      $.each(data.data, function(i, item){
        s = item;
      });

      alert(s);
    }
  }, 'json');

  return false;
}

function getMunicipalities(idState){
  $.post('{{URL::to("user/address/municipalities")}}'
  ,{'state_id':idState},
  function(data){
    if(data.status){
      $("#municipality").html('');
      $("#municipality").append($("<option/>", {'value':'', 'text':'--Seleccione un municipio--'}));
      $.each(data.data, function(i, item){
        $("#municipality").append($("<option/>", {'value':item.municipality_id, 'text':item.municipality}));
      });
      $("#municipality").val(municipality);
      municipality='';

      if(idEdit==0){
        $("#zone_id").html('');
        $("#zone_id").append($("<option/>", {'value':'', 'text':'--Seleccione una zona--'}));
        $("#zone_id").val('');
      }
    }else{
      alert("Ocurrió un error al obtener los municipios del departamento");
    }
  }, 'json'
);
}

function getZones(idMunicipality){
  $.post('{{URL::to("user/address/zonesByMunicipality")}}'
  ,{'municipality_id':idMunicipality},
  function(data){
    if(data.status){
      $("#zone_id").html('');

      $("#zone_id").append($("<option/>", {'value':'', 'text':'--Seleccione una zona--'}));

      $.each(data.data, function(i, item){
        $("#zone_id").append($("<option/>", {'value':item.zone_id, 'text':item.zone}));
      });

      $("#zone_id").val(zone);
      zone = '';
    }else{
      alert("Ocurrió un error al obtener las zonas del municipio");
    }
  }, 'json'
);
}

function edit(idAddress){
  idEdit=idAddress;
  $.get("{{URL::to('user/address/edit/')}}/"+idAddress,
  function(data){
    $("#address_name").val(data.address.address_name);
    $("#address_1").val(data.address.address_1);
    $("#address_2").val(data.address.address_2);
    $("#reference").val(data.address.reference);
    $("#state").val(data.municipality.state_id);

    municipality = data.municipality.municipality_id;
    zone = data.zone.zone_id;

    getMunicipalities(data.municipality.state_id);
    getZones(data.municipality.municipality_id);
  }, 'json');
}
</script>
@stop
