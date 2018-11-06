
<tr>

    @if(!isset($_GET['fillter']) || $_GET['fillter'] < 5)
        <td class="timercont" data-timer="00:00:00" data-idorden="{{ $order->order_id }}">
    @else
        <td>
    @endif
        <strong>{{ $order->order_cod }}</strong>
        <input type="hidden" value="time" id="urltime_{{$order->order_id}}" />
    </td>

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
            <i class="fa fa-money fa-2x" style="color:gray;"></i>
        @else
            <i class="fa fa-credit-card fa-2x" style="color:gray;"></i>
        @endif
    </td>
    <td>@if($order->service_type_id == 3) PidaFacil Delivery @else{{ $users->name . ' ' . $users->last_name }}@endif</td>
    <td>@if($order->service_type_id == 3) En Restaurante @else {{ $order->address }} @endif</td>
    <td>
        <button id="order_id_{{ $order->order_id }}" class="btn btn_edit button_100" data-toggle="modal" data-target="#order_detail_{{ $order->order_id.$order->service_type_id }}">Ver detalle</button>
    </td>
    @if(!isset($_GET['fillter']) || (isset($_GET['fillter']) and $_GET['fillter'] == 1))
            @if(((Auth::user()->role_id != 1 && Auth::user()->role_id != 3) || $restaurantes[$id]['parent'] != $id) || !isset($sucursales[1]))
                <td>
                    {{ Form::submit('Aceptar', array('class'=>'btn btn_accepted button_100 aceptar_order','id'=>'aceptar_'.$order->order_id)) }}
                </td>
               <td>
                    <button id="cancelled_by_{{ $order->order_id }}" class="btn btn_cancelled button_100" data-toggle="modal" data-target="#cancel_this_{{ $order->order_id }}">Rechazar</button>
                </td>
            @endif
        @if(isset($sucursales[1]))
            @if(Auth::user()->role_id != 2)
                <td>
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
                    <td> <button id="entregada_{{ $order->order_id }}" class="entregada_rejected btn button_100 botton_completed">Entregada</button> </td>
                @endif
            @endif
    @endif
</tr>
<div class="modal fade" id="order_detail_{{ $order->order_id.$order->service_type_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div  class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><strong>Código de Orden: {{ $order->order_cod }}</strong></h4>
            </div>
            <div class="modal-body">
                <!--
                <div class="row">
                    <div class="col-lg-2" style="color:#ef4036; font-size:18px;">Usuario:</div>
                    <div class="col-lg-3" style="color:#ef4036; font-size:18px;">{{ $users->name.' '.$users->last_name }}</div>
                    <div class="col-lg-2" style="color:#ef4036; font-size:18px;">Dirección:</div>
                    <div class="col-lg-5" style="color:#ef4036; font-size:18px;">{{ $order->address }}</div>
                </div> -->
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <h1>{{ $users->name. ' ' .$users->last_name  }}</h1>
                        <p>{{ $order->address }}</p>
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


                <div class="row" style="border-top:solid 1px #cccccc;">
                    <div class="col-lg-1 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                        Cantidad
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                        Producto
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                        Condiciones
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                        Ingredientes
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                        Comentario
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                        Precio Unidad
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 col-xs-1" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
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
                    <div class="col-lg-1 col-md-1" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                        <br>
                        {{ $products->quantity }}
                    </div>
                    <div class="col-lg-3 col-md-3" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                        <br>
                        {{ $products->product }}
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">

                        @if(isset($products->conditions) && count($products->conditions) > 0)
                            @foreach($products->conditions as $k=>$condition)
                                @for($i = 0; $i < count($condi);$i++)
                                    @if($condi[$i]->condition_id == $condition->condition_id)
                                        {{$condition->condition_option}}<br />
                                    @endif
                                @endfor
                            @endforeach
                            @else
                            No tiene condiciones
                        @endif
                    </div>
                    <div class="col-lg-2 col-md-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px;overflow-y: scroll;">
                        @if(isset($products->ingredients) && count($products->ingredients) > 0)
                        @foreach($products->ingredients as $k=>$v)
                        {{ $v->ingredient }}<br/>
                        @endforeach
                        @else
                        No tiene ingredientes
                        @endif
                    </div>
                    <div class="col-lg-2 col-md-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px; overflow-y: scroll;">
                        {{$products->comment}}
                    </div>
                    <div class="col-lg-1 col-md-1" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                        <br>
                        $ {{ $products->unit_price }}
                    </div>
                    <div class="col-lg-1 col-md-1" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                        <br>
                        $ {{ number_format($products->total_price,2) }}
                    </div>
                </div>
                @endforeach
                @if($order->payment_method_id == 2 && $order->service_type_id == 3)
                <h3>Datos de pago</h3>
                <div class="row" style="border-top:solid 1px #cccccc;">
                    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                        Total Cliente
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                        Cambio
                    </div>
                </div>
                <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                    <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                        $ {{ number_format($order->order_total,2) }}
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
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
                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                            Total Cliente
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                            Nombre tarjeta
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                            Numero tarjeta
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                            Mes
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                            Año
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                            Codigo de seguridad
                        </div>
                    </div>
                    <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                            $ {{ number_format($order->order_total,2) }}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                            {{ $order->credit_name }}
                        </div>
                        <div class="col-lg-2 col-md-3 col-sm-3 col-xs-3" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                            {{ $order->credit_card }}
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                            {{ $order->credit_expmonth }}
                        </div>
                        <div class="col-lg-1 col-md-2 col-sm-2 col-xs-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                            {{ $order->credit_expyear }}
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                            {{ $order->secure_code }}
                        </div>
                    </div>
                @else
                    <h3>Datos de pago</h3>
                    <div class="row" style="border-top:solid 1px #cccccc;">
                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                            Total Cliente
                        </div>
                        @if($order->service_type_id != 3)
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="text-align:center; border-left:solid 1px #cccccc; height: 45px;">
                                Cambio
                            </div>
                        @endif
                    </div>
                    <div class="row" style="border-top:solid 1px #cccccc; border-bottom: solid 1px #cccccc;">
                        <div class="col-lg-2 col-md-4 col-sm-4 col-xs-4" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                            $ {{ number_format($order->order_total,2) }}
                        </div>
                        @if($order->service_type_id != 3)
                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="text-align:center; border-left:solid 1px #cccccc; height:70px;">
                                $ {{ number_format($order->pay_change,2) }}
                            </div>
                        @endif
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
                        <option value="2">Producto agotado</option>
                        <option value="2">Falta ingrediente</option>
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

</script>
<!-- Fin del dialogo -->