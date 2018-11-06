@extends('general.admin_layout')
@section('content')

<div class="center_content">

    <h1 class="title">Actualizar Restaurante {{ $restaurant->name }}</h1>

    <a href ="{{ URL::to('admin/restaurant/list/') }}" class="btn btn-success">Ir a lista de restaurantes</a>
    <br/>
    <br/>
    <br/>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Operaciones básicas</h3>
        </div>

        <div class="panel-body">
            <div class="form-group">
                <div class="input-group ui-widget">

                    @if($restaurant->activate==1)
                    <button onclick='window.location.href ="{{ URL::to('admin/restaurant/desactivate/'.$restaurant->restaurant_id) }}"' class="btn btn-danger">Desactivar</button>
                    @else
                    <button onclick='window.location.href ="{{ URL::to('admin/restaurant/activate/'.$restaurant->restaurant_id) }}"' class="btn btn-success">Activar</button>
                    @endif
                    <button onclick='window.location.href ="{{ URL::to('admin/restaurant/define_horarios/'.$restaurant->slug) }}"' class="btn btn-success">Definir horarios</button>
                    <button onclick='window.location.href ="{{ URL::to('admin/restaurant/sections/edit/'.$restaurant->slug) }}"' class="btn btn-success">Administrar Secciones</button>
                    <button onclick='window.location.href ="{{ URL::to('admin/ingredient/restaurant/'.$restaurant->slug) }}"' class="btn btn-success">Administrar ingredientes</button>
                    <button onclick='window.location.href ="{{ URL::to('admin/condition/restaurant/'.$restaurant->slug) }}"' class="btn btn-success">Administrar Condiciones</button>
                    <button onclick='window.location.href ="{{ URL::to('admin/restaurant/products/'.$restaurant->slug) }}"' class="btn btn-success">Administrar Productos</button>
                </div>
            </div>
        </div>
    </div>

    {{ Form::model($restaurant, [
    'url' => ['admin/restaurant/update', $restaurant->restaurant_id],
    'files'=>true,
    'id'=>'formEnv'
]) }}


    @include('partials.forms.restaurant')

    {{ Form::model($webcontent, [
    'url' => ['admin/restaurant/update', $restaurant->restaurant_id],
    'files'=>true
]) }}
    @include('partials.forms.webcontent')

    {{ Form::submit('Actualizar Restaurante', ['class' => 'btn btn-primary']) }}

    {{ Form::close() }}

</div>

<script>
            $(document).ready(function(){
                
    $("#padre").val("{{ $parent->name }}");
            $("#procesaOrdenes").val("{{ $orders_allocator->name }}");
            @foreach($services_type as $service_type)
            $("input[name='service_types[]'][value='{{$service_type->service_type_id}}']").prop('checked', true);
            @endforeach

            @foreach($payment_methods_selected as $payments)
            $("input[name='payment_methods[]'][value='{{$payments->payment_method_id}}']").prop('checked', true);
            @endforeach
            
            @foreach($tags as $tag)
            $("input[name='tags[]'][value='{{$tag->tag_id}}']").prop('checked', true);
            @endforeach
            
            @for ($i = 1; $i <= count($contacts); $i++)
                $("input[name='contact_id[{{$i}}]']").val('{{$contacts[$i-1]->contact_id}}');
                $("input[id='contact_name[{{$i}}]']").val('{{$contacts[$i-1]->contact_name}}');
                $("input[id='contact_celular[{{$i}}]']").val('{{$contacts[$i-1]->contact_celular}}');
                $("input[id='contact_phone[{{$i}}]']").val('{{$contacts[$i-1]->contact_phone}}');
                $("input[id='contact_email[{{$i}}]']").val('{{$contacts[$i-1]->contact_email}}');
            @endfor
            });
            
</script>

@if($restaurant->landing_page_id!=0)
<script>
            $(document).ready(function(){
    $("#webContent").prop('checked', true);
            $("#contFormWeb").fadeIn();
    });
            $("#webContent").click(function(){
    if (!$(this).is(":checked")){
    $("#dialog").dialog({
    resizable: false,
            height:140,
            modal: true,
            buttons: {
            "Aceptar": function() {
            $(this).dialog("close");
            },
                    Cancel: function() {
                    $("#webContent").prop('checked', true);
                            setTimeout(function(){
                            $("#contFormWeb").fadeIn();
                            }, 300);
                            $(this).dialog("close");
                    }
            }
    });
    }
    });
</script>

<div id="dialog" title="Se eliminará el web content" style="display: none;">
    <p>Se eliminará el web content que tenía definido antes</p>
</div>
@endif

@stop