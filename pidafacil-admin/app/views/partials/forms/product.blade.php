{{HTML::style('css/jquery.datetimepicker.css')}}
{{ HTML::script('js/jquery.datetimepicker.js', array("type" => "text/javascript")) }}

<a href ="{{ URL::to('admin/restaurant/sections/edit/'.$restaurant->slug) }}" class="btn btn-success">Ir a secciones</a>
<a href ="{{ URL::to('admin/product/section/'.$section->section_id) }}" class="btn btn-success">Ir a productos de sección</a>
<a href ="{{ URL::to('admin/restaurant/edit/'.$restaurant->slug) }}" class="btn btn-success">Ir a restaurante</a>
<a href ="{{ URL::to('admin/restaurant/products/'.$restaurant->slug) }}" class="btn btn-success">Ir a productos del restaurante</a>
<a href="{{ URL::to('admin/product/create/'.$section->section_id) }}" class="btn btn-success">Agregar</a>
<br/>
<br/>
<br/>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Detalles del producto</h3>
    </div>
    
    <div class="panel-body">
        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('product', 'Nombre:', ['class' => 'input-group-addon']) }}
                {{ Form::text('product', null, ['class' => 'form-control']) }}
            </div>
        </div>
        
        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('section_id', 'Sección:', ['class' => 'input-group-addon']) }}
                {{ Form::select('section_id', $sections, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('description', 'Descripción:', ['class' => 'input-group-addon']) }}
                {{ Form::textarea('description', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('value', 'Precio:', ['class' => 'input-group-addon']) }}
                {{ Form::text('value', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('activate', 'Activar:', ['class' => 'input-group-addon']) }}
                {{ Form::checkbox('activate', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('promotion', 'Promocion:', ['class' => 'input-group-addon']) }}
                {{ Form::checkbox('promotion', null, false, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('start_date', 'Fecha de inicio:', ['class' => 'input-group-addon']) }}
                {{ Form::text('start_date', null, ['class' => 'form-control']) }}
            </div>
        </div>

        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('end_date', 'Fecha de final:', ['class' => 'input-group-addon']) }}
                {{ Form::text('end_date', null, ['class' => 'form-control']) }}
            </div>
        </div>
        
        <div class="form-group">
            <div class="input-group ui-widget">
                {{ Form::label('imagen', 'Imagen:', ['class' => 'input-group-addon']) }}
                {{ Form::file('imagen', null, ['class' => 'form-control']) }}
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

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Ingredientes</h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <div class="input-group ui-widget" style="width: 100%;">
                <ul id="listIngredients">
                </ul>
            </div>
        </div>
    </div>
        
        <div class="panel-footer text-right">
            <button onclick='dialogForm(event)' class="btn btn-success">Agregar ingrediente</button>
        </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Condiciones</h3>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <div class="input-group ui-widget" style="width: 100%;">
                <ul id="listConditions">
                </ul>
            </div>
        </div>
    </div>
        
        <div class="panel-footer text-right">
            <button onclick='createCondition(event)' class="btn btn-success">Agregar condición</button>
        </div>
</div>


@include('partials.forms.ingredient')
@include('partials.forms.condition')

<script>
    var selectedIngredients = [
        @foreach($ingredients_selected as $ingredient)
            @if($ingredient->pivot->removable==1)
                {'id':'{{ $ingredient->ingredient_id }}', 'removable':true},
            @else
                {'id':'{{ $ingredient->ingredient_id }}', 'removable':false},
            @endif
        @endforeach
    ];
    
    var selectedConditions = [
        @foreach($conditions_selected as $condition)
            {'id':'{{ $condition->condition_id }}', 'order':'{{ $condition->pivot->condition_order }}'},
        @endforeach
    ];
    
    $(document).ready(function(){
        $("#start_date").datetimepicker({
            format:'d/m/Y H:i',
            lang:'es'
        });
        $("#end_date").datetimepicker({
            format:'d/m/Y H:i',
            lang:'es'
        });
        
        //Si selecciona un ingrediente
        $("input[name^='ingredients[']").click(function(){
            var ele = $(this);
            var id=ele.prop('name').substring(12, ele.prop('name').length-1);
            
            var co = selectedIngredients.length - 1;
            for(var i=co;i>=0;i--){
                if(id==selectedIngredients[i].id) 
                    selectedIngredients.splice(i, 1);
            }
            
            if($(this).is(':checked')){
                selectedIngredients.push({'id':id, 'removable':$("input[name='removables["+id+"]']").is(':checked')});
            }
        });
        
        //Si define un ingrediente como removible
        $("input[name^='removables[']").click(function(){
            var ele = $(this);
            var id=ele.prop('name').substring(11, ele.prop('name').length-1);

            $.each(selectedIngredients, function(i, item){
                if(id==item.id) item.removable = ele.is(':checked');
            });
        });
    });

    function cargarList(){
        $("#listIngredients").html('');
        $.each(ingredientList, function(i, item){
            $("#listIngredients").append($("<li/>", {'class':'ui-state-default', 'css':{'display': 'inline-block', 'width': '100%'}}).html(
                '<div class="col-md-6 text-left">'+
                '<input type="checkbox" value="1" name="ingredients['+item.id+']">'+
                        item.nombre+
                    '</div>'+
                    '<div class="col-md-6 text-right">'+
                        '<input type="checkbox" value="1" name="removables['+item.id+']">'+
                    '</div>'    
                ));
        });
        
        $.each(selectedIngredients, function(i, item){
            $("input[name='ingredients["+item.id+"]']").prop('checked', true);
            $("input[name='removables["+item.id+"]']").prop('checked', item.removable);
        });
    }

    function cargarConditionList(){
    
    //TODO: No reselecciona los seleccionados por el usuario
        $("#listConditions").html('');
        $.each(conditionList, function(i, item){
            var cont = $("<div/>");
            var sel = $("<select/>", {'name':'positions['+item.id+']'});
            
            for(var q=1; q<=conditionList.length; q++){
                sel.append($("<option/>", {'text':q, 'val':q}));
            }
            
            cont.append(sel);
    
            $("#listConditions").append($("<li/>", {'class':'ui-state-default', 'css':{'display': 'inline-block', 'width': '100%'}}).html(
                '<div class="col-md-6 text-left">'+
                '<input type="checkbox" value="'+item.id+'" name="conditions[]">'+
                        item.nombre+
                    '</div>'+
                    '<div class="col-md-6 text-left">'+
                        cont.html()+
                    '</div>'
                ));
        });
        
        $.each(selectedConditions, function(i, item){
            $("input[name='conditions[]'][value='"+item.id+"']").prop('checked', true);
            $("select[name='positions["+item.id+"]']").val(item.order);
        });
    }
</script>