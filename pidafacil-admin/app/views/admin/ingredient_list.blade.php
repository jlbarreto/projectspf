@extends('general.admin_layout')
@section('content')

<div class="center_content">

    <h1   class="title">Ingredientes para {{ $restaurant->name }}</h1>
    
    <button onclick='window.location.href ="{{ URL::to('admin/restaurant/edit/'.$restaurant->slug) }}"' class="btn btn-success">Ir a restaurante</button>
    <br/>
    <br/>
    <br/>
    
    <div class="panel panel-default normalContet">
        <div class="panel-heading">
            <h3 class="panel-title">Ingredientes existentes en el restaurante</h3>
        </div>
        <div class="panel-body">
            <ul id="sortable">
               
            </ul>
        </div>
        
        <div class="panel-footer">
            <button onclick='dialogForm(event)' class="btn btn-success">Agregar ingrediente</button>
        </div>
    </div>

</div>

@include('partials.forms.ingredient');

<script>
    function cargarList(){
        $("#sortable").html('');
        $.each(ingredientList, function(i, item){
            $("#sortable").append($("<li/>", {'class':'ui-state-default', 'css':{'display': 'inline-block', 'width': '100%'}}).html(
                '<div class="col-md-6 text-left">'+
                        item.nombre+
                    '</div>'+
                    '<div class="col-md-6 text-right">'+
                        '<button type="button" onclick="editarIngredient(\''+item.nombre+'\', \''+item.id+'\')" class="btn btn-default" aria-label="Editar" title="Editar">'+
                            '<span class="fa fa-pencil fa-fw" aria-hidden="true"></span>'+
                        '</button>'+
                        '<button id="ingredient_'+ item.id +'" type="button" onclick="activeToggle(\''+item.id+'\')" class="btn btn-'+((typeof item.active!='undefined' && item.active==1)? 'default':'succes')+'" aria-label="Activar/Desactivar" title="Activar/Desactivar">'+
                            '<span class="fa fa-check-circle fa-fw" aria-hidden="true"></span>'+
                        '</button>'+
                    '</div>'    
                ));
        });
    }
    
    function activeToggle(id){
        $.get('{{ URL::to("admin/ingredient/activateToggle/") }}/'+id,
        function(data){

            if (data.error==false){
                var ele = $("#ingredient_"+data.ingredient.ingredient_id);
                ele.removeClass('btn-default');
                ele.removeClass('btn-succes');
                
                if(data.ingredient.active==1){
                    ele.addClass('btn-default');
                }else{
                    ele.addClass('btn-succes');
                }
            }

        }, 'json');
    }
</script>
@stop