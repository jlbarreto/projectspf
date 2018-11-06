@extends('general.admin_layout')
@section('content')

<div class="center_content">

    <h1   class="title">Condiciones para {{ $restaurant->name }}</h1>
    
    <button onclick='window.location.href ="{{ URL::to('admin/restaurant/edit/'.$restaurant->slug) }}"' class="btn btn-success">Ir a restaurante</button>
    <br/>
    <br/>
    <br/>
    
    <div class="panel panel-default normalContet">
        <div class="panel-heading">
            <h3 class="panel-title">Condiciones existentes en el restaurante</h3>
        </div>
        <div class="panel-body">
            <ul id="sortable">
               
            </ul>
        </div>
        
        <div class="panel-footer">
            <button onclick='createCondition(event)' class="btn btn-success">Agregar condicion</button>
        </div>
    </div>

</div>

@include('partials.forms.condition');

<script>
    function cargarConditionList(){
        $("#sortable").html('');
        $.each(conditionList, function(i, item){
            $("#sortable").append($("<li/>", {'class':'ui-state-default', 'css':{'display': 'inline-block', 'width': '100%'}}).html(
                '<div class="col-md-6 text-left">'+
                        item.nombre+
                    '</div>'+
                    '<div class="col-md-6 text-right">'+
                        '<button type="button" onclick="editarCondition(\''+item.nombre+'\', \''+item.id+'\')" class="btn btn-default" aria-label="Editar">'+
                            '<span class="fa fa-pencil fa-fw" aria-hidden="true"></span>'+
                        '</button>'+
                        '<button type="button" onclick="delCondition(\''+item.id+'\')" class="btn btn-default" aria-label="Eliminar">'+
                            '<span class="fa fa-trash fa-fw" aria-hidden="true"></span>'+
                        '</button>'+
                    '</div>'    
                ));
        });
    }
    
    function delCondition(id){
        if(confirm("Eliminar la condición?")){
            $.get('{{ URL::to("admin/condition/destroy/") }}/'+id, function(data){
                if(typeof data.status!= 'undefined' && data.status==true){
                    var c = conditionList.length - 1;
                    
                    for(var i =c; i>=0; i--){
                        if(conditionList[i].id==data.id){
                            conditionList.splice(i, 1);
                        }
                    }
                    cargarList();
                }else{
                    alert("No se pudo eliminar la condición, si hay productos que usen esas condición no se podrá eliminar");
                }
            }, 'json');
        }
    }
</script>
@stop