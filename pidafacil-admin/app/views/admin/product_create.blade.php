@extends('general.admin_layout')
@section('content')

<div class="center_content">

    <h1 class="title">Crear producto</h1>

    {{ Form::open(array('url' => 'admin/product/store', 'files'=>true, 'id'=>'formEnv')) }}

        @include('partials.forms.product')

        {{ Form::submit('Insertar Producto', ['class' => 'btn btn-primary']) }}

    {{ Form::close() }}
</div>

<script>
    $(document).ready(function(){
        $("#section_id").val({{ $section->section_id }});
    });
</script>
@stop