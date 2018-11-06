
<div id="dialog" title="Ingrediente" style="display: none;">
        <div class="form-group">
            <div class="input-group ui-widget" style="width: 100%;">
                {{ Form::label('ingredient', 'Nombre:', ['class' => 'input-group-addon']) }}
                {{ Form::text('ingredient', null, ['class' => 'form-control']) }}
            </div>
        </div>
    <div id='ingredientError' class="error"></div>
    {{ Form::hidden('restaurant_id', $restaurant->restaurant_id, ['class' => 'form-control']) }}

</div>


<script>
    var ingredientList = [
            @foreach($ingredients as $ingredient)
            {'id':"{{ $ingredient->ingredient_id }}", 'nombre':"{{ $ingredient->ingredient }}", 'active':"{{ $ingredient->active }}"},
            @endforeach
            ];
    $(document).ready(function(){
        cargarList();
    });
    
    function serializeForm(){
        return {'ingredient':$("#ingredient").val(), 'restaurant_id':$("input[name='restaurant_id']").eq(0).val()};
    }
    
    function dialogForm(e) {
        e.preventDefault();
        $("#dialog").dialog({
            resizable: true,
            modal: true,
            buttons: {
                "Aceptar": function () {
                    sIngredient();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function sIngredient(){
        $.post('{{ URL::to("admin/ingredient/store") }}', 
                    serializeForm(),
                    function(data){
                        if(data.status){
                            
                            ingredientList.push({'id':data.id, 'nombre':data.nombre});
                            
                            $("#ingredient").val("");
                            cargarList();
                            $("#dialog").dialog("close");
                        }else{
                            ul = $("<ul/>");
                            $.each(data.data, function(i, item){
                                ul.append($("<li/>", {'text':item[0]}))
                            });
                            
                            $("#ingredientError").html(ul);
                        }
                    }, 'json');
    }
    
    function editarIngredient(nombre, id){
        $("#ingredient").val(nombre);
        
        $("#dialog").dialog({
            resizable: true,
            modal: true,
            buttons: {
                "Aceptar": function () {
                    uIngredient();
                },
                Cancel: function () {
                    $(this).dialog("close");
                }
            }
        });
    }
    
    function uIngredient(){
        $.post('{{ URL::to("admin/ingredient/update") }}/'+id, 
                    serializeForm(),
                    function(data){
                        if(data.status){
                            $.each(ingredientList, function(i, item){
                                if(item.id==data.id) item.nombre=data.nombre;
                            });
                            
                            $("#ingredient").val("");
                            cargarList();
                            $("#dialog").dialog("close");
                        }else{
                            ul = $("<ul/>");
                            $.each(data.data, function(i, item){
                                ul.append($("<li/>", {'text':item[0]}))
                            });
                            
                            $("#ingredientError").html(ul);
                        }
                    }, 'json');
    }
</script>