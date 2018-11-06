@extends('general.admin_layout')
@section('content')

<div class="center_content">

    <h1 class="title">Crear Restaurante</h1>

    <a href ="{{ URL::to('admin/restaurant/list/') }}" class="btn btn-success">Ir a lista de restaurantes</a>
    <br/>
    <br/>
    <br/>
    
    {{ Form::open(array('url' => 'admin/restaurant/store', 'files'=>true, 'id'=>'formEnv')) }}
        @include('partials.forms.restaurant')
        @include('partials.forms.webcontent')
        {{ Form::submit('Insertar Restaurante', ['class' => 'btn btn-primary']) }}
    {{ Form::close() }}
</div>
<script>
    $(document).ready(function(){
        $("input[name='parent_restaurant_id']").val("0");
        $("input[name='orders_allocator_id']").val("0");
    });
</script>
@stop