<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Detalles del restaurante</h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('name', 'Nombre:', ['class' => 'input-group-addon']) }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('phone', 'Teléfono:', ['class' => 'input-group-addon']) }}
                {{ Form::text('phone', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('address', 'Dirección:', ['class' => 'input-group-addon']) }}
                {{ Form::text('address', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('commission_percentage', 'Porcentaje de comisión:', ['class' => 'input-group-addon']) }}
                {{ Form::text('commission_percentage', null, ['class' => 'form-control', 'required']) }}
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Contactos</h3>
    </div>
    <div class="panel-body">
        <?php for($i=1; $i<=2; $i++){ ?>
        <div class="panel panel-default">
            <div class="panel-body">
                {{ Form::hidden('contact_id['.$i.']', null, ['class' => 'form-control']) }}
                <div class="form-group">
                    <div class="input-group ui-widget">
                        {{ Form::label('contact_name['.$i.']', 'Nombre del contacto '.$i.':', ['class' => 'input-group-addon']) }}
                        {{ Form::text('contact_name['.$i.']', null, ['class' => 'form-control', ($i==1)? 'required':'']) }}
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-group ui-widget">
                        {{ Form::label('contact_celular['.$i.']', 'Celular del contacto '.$i.':', ['class' => 'input-group-addon']) }}
                        {{ Form::text('contact_celular['.$i.']', null, ['class' => 'form-control', ($i==1)? 'required':'']) }}
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-group ui-widget">
                        {{ Form::label('contact_phone['.$i.']', 'Teléfono del contacto '.$i.':', ['class' => 'input-group-addon']) }}
                        {{ Form::text('contact_phone['.$i.']', null, ['class' => 'form-control', ($i==1)? 'required':'']) }}
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-group ui-widget">
                        {{ Form::label('contact_email['.$i.']', 'Email del contacto '.$i.':', ['class' => 'input-group-addon']) }}
                        {{ Form::text('contact_email['.$i.']', null, ['class' => 'form-control', ($i==1)? 'required':'']) }}
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Sobre restaurante padre</h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <div class="input-group ui-widget">
                <label for="padre" class="input-group-addon">
                    <span class="hide_small"> Restaurante padre:</span>
                </label>
                <input id="padre" type="text" class="form-control searchTags" placeholder="Vacío significa que es un restaurante individual" />
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                <label for="procesaOrdenes" class="input-group-addon">
                    <span class="hide_small"> Procesa Ordenes:</span>
                </label>
                <input id="procesaOrdenes" type="text" class="form-control searchTags" placeholder="Vacío significa que él mismo procesa" />
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Sobre el servicio a domicilio</h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('delivery_time', 'Tiempo de entrega:', ['class' => 'input-group-addon']) }}
                {{ Form::text('delivery_time', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('guarantee_time', 'Tiempo de garantía:', ['class' => 'input-group-addon']) }}
                {{ Form::text('guarantee_time', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('shipping_cost', 'Costo de envío:', ['class' => 'input-group-addon']) }}
                {{ Form::text('shipping_cost', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('minimum_order', 'Mínimo de orden:', ['class' => 'input-group-addon']) }}
                {{ Form::text('minimum_order', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('service_type', 'Tipo de servicio:', ['class' => 'input-group-addon']) }}
                @foreach($services_types as $service)
                {{ Form::checkbox(
                   'service_types[]',
                   $service->service_type_id)
                }}
                {{ Form::label($service->service_type, null) }}
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Métodos de pago</h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('payment_methods', 'Métodos de pago:', ['class' => 'input-group-addon']) }}
                @foreach($payment_methods as $payment_method)
                {{ Form::checkbox(
                   'payment_methods[]',
                   $payment_method->payment_method_id)
                }}
                {{ Form::label($payment_method->payment_method, null) }}
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Tags</h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <div class="input-group ui-widget" style="width: 100%;">
                @foreach($tag_types as $tag_type)
                <div class="col-md-4 text-left" id="tag_type_id_{{ $tag_type->tag_type_id }}">
                        <h1>{{ $tag_type->tag_type }}</h1>
                        @foreach($tag_type->tags as $tag)
                            {{ Form::checkbox('tags[]', $tag->tag_id) }}
                            {{ Form::label($tag->tag_name, null) }}<br/>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{ Form::hidden('map_coordinates', '0') }}
{{ Form::hidden('parent_restaurant_id') }}
{{ Form::hidden('orders_allocator_id') }}

<script>
    $(document).ready(function () {
        //Validación de los tags
        $('input[type="submit"]').click(function(e){
            var sele = false;
            $.each($("#tag_type_id_1 > input[type='checkbox']"), function(){
                if($(this).is(':checked')){
                    sele = true;
                }
            });
            
            if(!sele){
                e.preventDefault();
                alert('Debe seleccionar almenos un grupo de comida!');
            }
        });
        $("input[name='service_types[]']").click(function () {
            if ($(this).val() == 1) {
                $("input[name='service_types[]'][value=3]").prop('checked', false);
            } else if ($(this).val() == 3) {
                $("input[name='service_types[]'][value=1]").prop('checked', false);
            }
        });
    });
</script>