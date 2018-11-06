@extends('general.general_white')
@section('content')
<?php
$total = Session::get('total_order');
$cart = Session::get('cart');

$timezone = date_default_timezone_get();
$date = date('G:i:s', time());
?>
<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse"
                data-target=".navbar-ex1-collapse">
            <i class="fa fa-bars fa-2x"></i>
        </button>
        <a class="navbar-brand" href="{{ URL::to('') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
    </div>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-bars fa-lg"></i></span> Inicio <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="{{ URL::to('/') }}">Inicio</a></li>
                    <!-- <li><a href="{{ URL::to('promociones') }}">Promociones</a></li> -->
                    <li><a href="{{ URL::to('user/orders') }}">Repetir pedido</a></li>
                    <li><a href="{{ URL::to('explorar') }}">Explorar</a></li>
                    <li class="divider"></li>
                    <li><a href="{{ URL::to('cart') }}">Carrito</a></li>
                    <li><a href="{{ URL::to('profile') }}">Mi perfil</a></li>
                    <li class="divider"></li>
                    @include('../include/linckChat')
                    <li class="divider"></li>
                    @if(Auth::check())
                    <li><a href="{{ URL::to('logout') }}">Cerrar sesión</a></li>
                    @else
                    <li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
                    @endif
                </ul>
            </li>
            <!--<li><a href="#"><i class="fa fa-search fa-lg"></i> Buscar</a></li>-->
            <li><a href="{{ URL::to('cart') }}"><i class="fa fa-shopping-cart fa-lg"></i> Carrito</a></li>
        </ul>
        <form class="navbar-form navbar-left search-bar" role="search">
            <div class="form-group">
                <div class="input-group">
                    <label for="tags" class="input-group-addon red-label">
                        <i class="fa fa-search"></i>
                    </label>
                    <input type="text" id="tags" name="tags" class="form-control searchTags" placeholder="Search">
                </div>
            </div>
        </form>
        <ul class="nav navbar-nav navbar-right">
            @if(Auth::check())
            <li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->name.' '. Auth::user()->last_name }} </a></li>
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
    <h1 class="text-center">Estas a dos pasos de completar tu orden</h1>

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
                            <td colspan="7" class="text-right"><strong>Sub Total: ${{ number_format($total, 2) }}</strong></td>
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

            {{ Form::open(array('url' => 'order/create', 'onSubmit'=>'return enviarCompra()','method' => 'POST', 'class' => 'checkout_order', 'id'=>'form')) }}

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
                        {{ Form::radio('service_type_id', '1', null,['id' => 'service_type_1', ]) }}
                        @else
                        {{ Form::radio('service_type_id', '3', null,['id' => 'service_type_1', ]) }}
                        @endif
                        A domicilio
                    </label>
                </div>
                <div id="user-address" style="display: none; padding: 0px 1em;">
                    <div class="form-group">
                        @if(is_array($usr_address) && count($usr_address) > 0)
                        <label for="selType1">Escoge tú dirección de destino:</label>
                        <select name="address_id" id="selType1" class="form-control input-lg">
                            <option selected disabled>Selecciona tú dirección</option>
                            @foreach($usr_address as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>O ingresa una nueva dirección:</label>
                        <button type="button" class="btn btn-primary active" id="new-address" data-toggle="modal" data-target="#address">Ingresa una nueva dirección</button>
                        @else
                        <h4 class="text-warning">No existe ningún registro de direcciones</h4>
                        <p>Da click aqui para crear una nueva dirección</p>
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
                        {{ Form::radio('service_type_id', '2', null,['id' => 'service_type_2']) }}
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

                        ?>
                        <div class="row">
                            <div class="col-xs-6">
                                <select name="hour" class="form-control input-lg">
                                    @for($i=0; $i < 24; $i++)

                                        @if(($oint < $cint and ($i>=$oint and $i<=$cint))
                                            or $oint > $cint and ($i>=$oint or $i<=$cint)) )
                                        <option value="{{ $i }}">{{ $i }}</option>
                                        @endif
                                    @endfor
                                </select>
                            </div>
                            <div class="col-xs-6">
                                <select name="minutes" class="form-control input-lg">
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
													1 => 'Enero',
													2 => 'Febrero',
													3 => 'Marzo',
													4 => 'Abril',
													5 => 'Mayo',
													6 => 'Junio',
													7 => 'Julio',
													8 => 'Agosto',
													9 => 'Septiembre',
													10 => 'Octubre',
													11 => 'Noviembre',
													12 => 'Diciembre'], null, array('class'=>'form-control input-lg')) }}
                                </div>
                                <div class="col-xs-6"><?php
                                    $ystart = date('Y');
                                    $exp_years = array();
                                    for ($y = $ystart; $y <= ($ystart + 20); $y++) {
                                        $exp_years[$y] = $y;
                                    }
                                    $exp_years = array_add($exp_years, '', 'Año');
                                    ksort($exp_years);
                                    ?>
                                    {{ Form::select('year', $exp_years, null, array('class'=>'form-control input-lg')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="border-bottom: 1px solid #999;">
                    <div class="radio input-lg">
                        <label for="method_2">
                            {{ Form::radio('payment_method_id', '1', null,['id' => 'method_2']) }}
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
                        <div class="row">
                            <div class="col-xs-8">
                                <label>Cargo por envío: </label>
                            </div>
                            <div class="col-xs-4 text-right">
                                $ <span id="shipping_charge"></span>
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
                {{ Form::submit('Comprar', array('class'=>'btn btn-default btn-lg')) }}
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
                            {{ Form::text('address_2', null, array('id'=>'address_2','placeholder' => 'Complemento de dirección', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
                            {{ Form::text('reference', null, array('id'=>'reference','placeholder' => 'Referencia ej. Frente a "La Chulona"...', 'class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15')) }}
                            {{ Form::select('state', $states, '0', array('id'=>'state','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}
                            {{ Form::select('municipality', $municipalities, NULL, array('id'=>'municipality','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}
                            {{ Form::select('zone_id', $zones, NULL, array('id'=>'zone_id','class'=>'col-lg-12 col-md-12 col-sm-12 col-xs-12 form-control space_15', 'required'=>'required')) }}
                            </div>
                    </div>
                <div class="modal-footer" >
                    {{ Form::submit('Agregar', array('class'=>'btn btn-default button_150')) }}
                </div>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>
<script type="text/javascript">

    function sumaCardCharge()
    {
        if($("#serTipe").val() == 3) {
            if ($("#service_type_1").prop('checked')) {
                var tot = $("#subTotal").text();
                var shipping_charge = $("#shipping_charge").text();
                var cargoTarjeta = 0.04;
                var cargo = (parseFloat(tot) + parseFloat(shipping_charge)) * cargoTarjeta;
                var totalMasCargo = (parseFloat(tot) + parseFloat(shipping_charge)) + parseFloat(cargo);
                $("#card_charge").text(cargo.toFixed(2));
                $("#total").text(totalMasCargo.toFixed(2));
            }
        }
    }

    function enviarCompra(){
        appboy.logCustomEvent("Order Complete");

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
            $(document).ready(function(){
            setValues();
            $("#selType1").change(function(){

    if ($("#service_type_1").val() == 3){
        $.post('{{URL::to("/order/shipping_charge")}}',
        {'restaurant_id':{{$restaurant_id}}, 'address_id':$(this).val()},
            function(data){
                if (data.status){
                shipping_charge = data.data.shipping_charge;
                        setValues();
                } else{
                alert("Error en el servidor, por favor refrescar");
                }
            }, 'json'
        );
        } else{
                shipping_charge = {{$parent_shipping_cost}};
                setValues();
        }
    });
    });
            function setValues(){
                var total = parseFloat(sub_total) + parseFloat(shipping_charge);
                $("#subTotal").text(sub_total.toFixed(2));
                $("#shipping_charge").text(parseFloat(Math.round(shipping_charge * 100) / 100).toFixed(2));
                $("#total").text(total.toFixed(2));
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
            }

    $("#service_type_1").click(function(){
    $("#user-address").show();
            $("#rest-address").hide();
    });
            $("#service_type_2").click(function(){
                $("#rest-address").show();
                $("#user-address").hide();
            });
            $("#method_1").click(function(){
                $("#credit-card").show();
                $('.credit_required').attr('required', 'true');
                $("#cash").hide();
                $(".cash_required").removeAttr('required', 'true');

                $("#cargo_tarjeta").show();
                sumaCardCharge();

            });
            $("#method_2").click(function(){
                $("#cash").show();
                $('.cash_required').attr('required', 'true');
                $("#credit-card").hide();
                $(".credit_required").removeAttr('required', 'true');

                $("#cargo_tarjeta").hide();
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
                //alert( "Handler for .change() called." );
            });

            var ccv_n = 0;
            $(function() {
            $('#credit_card').validateCreditCard(function(result) {
            $('#showmsg').html('Card type: ' + (result.card_type == null ? '-' : result.card_type.name)
                    + '<br>Valid: ' + result.valid
                    + '<br>Length valid: ' + result.length_valid
                    + '<br>Luhn valid: ' + result.luhn_valid);
                    if (result.card_type != null){
            $('#inp_grp_add').addClass(result.card_type.name);
                    if (result.card_type.name == 'amex'){
            ccv_n = 4
                    $('#inp_ccv').attr("maxlength", ccv_n);
            } else{
            ccv_n = 3
                    $('#inp_ccv').attr("maxlength", ccv_n);
            }
            if (result.valid == true){
                $('#cc_frm_grp').removeClass('has-error');
                $('#cc_frm_grp').addClass('has-success');
                $('#cc_stat_icon').removeClass('fa-exclamation');
                $('#cc_stat_icon').addClass('fa-check');
            } else{
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


                    //Sino se edita
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
