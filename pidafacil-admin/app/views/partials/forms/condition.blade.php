<div id="dialogCondition" title="Condición" style="display: none;">
    <div class="form-group">
        <div class="input-group ui-widget" style="width: 100%;">
            {{ Form::label('condition', 'Nombre:', ['class' => 'input-group-addon']) }}
            {{ Form::text('condition', null, ['class' => 'form-control']) }}

            <input type="hidden" id="idCondition" value="0" />
        </div>
    </div>
    <div>
        <ul id="optionList">
        </ul>
    </div>
    <div class="input-group" style="width: 100%;">
        {{ Form::label('nameOption', 'Opción:', ['class' => 'input-group-addon']) }}
        {{ Form::text('nameOption', null, ['class' => 'form-control']) }}
        <span class="input-group-addon" onclick="saveOption()">
            <button class="btn btn-success" type="button">Guardar</button>
        </span>
        <input type="hidden" id="idOption" value="0" />
        <input type="hidden" id="restaurant_id" value="{{ $restaurant->restaurant_id }}" />
    </div>
    
    <div id='conditionError' class="error"></div>
</div>

<script>
            var conditionList = [
                    @foreach($conditions as $condition)
            {'id':"{{ $condition->condition_id }}", 'nombre':"{{ $condition->condition }}",
            },
                    @endforeach
            ];
            
            var optionesSel = [];
            
            //Para guardar las opciones que se eliminarán
            var optionDeleteList = [];
            
            $(document).ready(function(){
    cargarConditionList();
            $("#nameOption").keyup(function(e){
    if (e.keyCode == 13){
    saveOption();
    }
    });
    });
            function saveOption(){
                var name = $("#nameOption").val();
                var id = $("#idOption").val();
                
                if(name==""){
                    return;
                }
            
                var crash = false;
                $.each(optionesSel, function(o, obj){
                    if (obj.nombre == name){
                        crash = true;
                    }
                });
                if (crash){
                    alert('Ya existe la opción ' + name);
                    return;
                } else{
                    addOptionToList(name, id);
                }
            }


    function delOptionFromList(id, nombre){

        //Eliminando de el ul
        $.each($("li[name='option_" + id + "']"), function(i, item){
            if ($(item).children('.contNombre').text() == nombre){
                item.remove();
            }
        });
    
        //Eliminando de las variables
        var c = optionesSel.length-1;
        
        for(var i = c; i>=0; i--){
            if (optionesSel[i].id == id && optionesSel[i].nombre == nombre){
                optionesSel.splice(i, 1);
            }
        }
    }

    function addOptionToList(nombre, id, active){
        var found = false;
        var crash = false;
        
        $.each(optionesSel, function(o, obj){

            //Si no es nuevo y el id coincide actualiza
            if (id != "0" && obj.id == id){
                found = true;
                optionesSel[o].nombre = nombre;
            }
        });
        
        if (!found){
            optionesSel.push({'id':id, 'nombre':nombre, 'active':active});
        }
    
            $("#optionList").append(
            $("<li/>", {'name':'option_' + id, 'class':'ui-state-default', 'css':{'display': 'inline-block', 'width': '100%'}})
            .html(
                    '<div class="col-md-6 text-left contNombre">' +
                    nombre +
                    '</div>' +
                    '<div class="col-md-6 text-right">' +
                    '<button type="button" onclick="editOption(\'' + nombre + '\', \'' + id + '\')" class="btn btn-default" aria-label="Editar">' +
                    '<span class="fa fa-pencil fa-fw" aria-hidden="true"></span>' +
                    '</button>' +
                    '<button type="button" onclick="preDelOption(\'' + id + '\', \'' + nombre + '\')" class="btn btn-default" aria-label="Eliminar">' +
                    '<span class="fa fa-trash fa-fw" aria-hidden="true"></span>' +
                    '</button>' +
                    '<button id="option_'+ id +'" type="button" onclick="activeToggleOption(\'' + id + '\')" class="btn btn-'+((typeof active!='undefined' && active==1)? 'default':'succes')+'" aria-label="'+((typeof active!='undefined' && active==1)? 'Desactivar':'Activar')+'">' +
                    '<span class="fa fa-check-circle fa-fw" aria-hidden="true"></span>' +
                    '</button>' +
                    '</div>'
                    )
            );
            $("#nameOption").val('');
            $("#idOption").val('0');
    }
    
    function preDelOption(id, nombre){
        if(confirm("Está seguro de eliminar la opción?")){
            
            delOptionFromList(id, nombre);
        }
    }

    function editOption(nombre, id){
            $("#nameOption").val(nombre);
            $("#idOption").val(id);
            delOptionFromList(id, nombre);
    }

    function editarCondition(nombre, id){
            $("#condition").val(nombre);
            $("#idCondition").val(id);
            
            $.get('{{ URL::to("admin/condition/options/") }}/'+id, function(data){
                optionesSel = data.options;
                $("#optionList").html('');
                
                $.each(optionesSel, function(o, obj){
                    addOptionToList(obj.nombre, obj.id, obj.active);
                });
            }, 'json');
            
            
            
            $("#dialogCondition").dialog({
            minWidth: 600,
            resizable: true,
            modal: true,
            buttons: {
            "Aceptar": function () {
                var datos = {'condition':$("#condition").val(), 'restaurant_id':$("#restaurant_id").val()};

                datos.options = JSON.stringify(optionesSel);

                $.post('{{ URL::to("admin/condition/update/") }}/' + $("#idCondition").val(),
                        datos,
                        function(data){
                            
                            if (data.status==true){
                                $.each(conditionList, function(i, item){
                                    if (item.id == $("#idCondition").val()){
                                        item.nombre=$("#condition").val();
                                    }
                                });

                                cargarConditionList();
                                $("#dialogCondition").dialog("close");
                            } else{
                            ul = $("<ul/>");
                                    $.each(data.data, function(i, item){
                                    ul.append($("<li/>", {'text':item[0]}))
                                    });
                                    $("#conditionError").html(ul);
                            }
                        }, 'json');
                },
                Cancel: function () {

                    $(this).dialog("close");
                }
            }
    });
    }
    
    
    function createCondition(e){
        e.preventDefault();
            $("#condition").val('');
            $("#idCondition").val('0');
            optionesSel=[];
            $("#optionList").html('');
            
            
            $("#dialogCondition").dialog({
            minWidth: 600,
            resizable: true,
            modal: true,
            buttons: {
            "Aceptar": function () {
                var datos = {'condition':$("#condition").val(), 'restaurant_id':$("#restaurant_id").val()};

                datos.options = JSON.stringify(optionesSel);

                $.post('{{ URL::to("admin/condition/store/") }}',
                        datos,
                        function(data){
                            
                            if (data.status==true){
                                conditionList.push({'id':data.id, 'nombre':data.nombre});

                                cargarConditionList();
                                $("#dialogCondition").dialog("close");
                            } else{
                            ul = $("<ul/>");
                                    $.each(data.data, function(i, item){
                                    ul.append($("<li/>", {'text':item[0]}))
                                    });
                                    $("#conditionError").html(ul);
                            }
                            
                        }, 'json');
                },
                Cancel: function () {

                    $(this).dialog("close");
                }
            }
    });
    }
    
    function activeToggleOption(id){
        $.get('{{ URL::to("admin/condition/activateToggleOption/") }}/'+id,
        function(data){

            if (data.error==false){
                var ele = $("#option_"+data.option.condition_option_id);
                ele.removeClass('btn-default');
                ele.removeClass('btn-succes');
                
                if(data.option.active==1){
                    ele.addClass('btn-default');
                }else{
                    ele.addClass('btn-succes');
                }
            }

        }, 'json');
    }
</script>