@extends('general.admin_layout')
@section('content')

<div class="center_content">

    <h1 class="title">Editar producto</h1>

    {{ Form::model($product, [
    'url' => ['admin/product/update', $product->product_id],
    'files'=>true,
    'id'=>'formEnv'
]) }}

        @include('partials.forms.product')

        {{ Form::submit('Actualizar Producto', ['class' => 'btn btn-primary']) }}

    {{ Form::close() }}
</div>

<script>
    $(document).ready(function(){
        //Cargando los valores seleccionados de base de datos
        @foreach($tags as $tag)
            $("input[name='tags[]'][value='{{$tag->tag_id}}']").prop('checked', true);
        @endforeach
        
        @foreach($ingredients_selected as $ingredient)
            $("input[name='ingredients[{{ $ingredient->ingredient_id }}]']").prop('checked', true);
            @if($ingredient->pivot->removable==1)
                $("input[name='removables[{{ $ingredient->ingredient_id }}]']").prop('checked', true);
            @endif
        @endforeach
        
        @foreach($conditions_selected as $condition)
            $("input[name='conditions[]'][value='{{ $condition->condition_id }}']").prop('checked', true);
        @endforeach
        
        $("#section_id").val({{ $product->section_id }});
    });
</script>

@stop