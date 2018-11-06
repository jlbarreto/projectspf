@extends('general.admin_layout')
@section('content')

<div class="center_content">

    <h1  class="title">Secciones de Restaurante {{ $restaurant->name }}</h1>

    <button onclick='window.location.href ="{{ URL::to('admin/restaurant/edit/'.$restaurant->slug) }}"' class="btn btn-success">Ir a restaurante</button>
    <br/>
    <br/>
    <br/>
    <div id='formEnv'>
        {{ Form::open(array('url' => 'admin/restaurant/sections/add/'.$restaurant->slug)) }}

        <div id="formul" class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Nueva sección</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="input-group ui-widget">
                        {{ Form::label('name', 'Nombre:', ['class' => 'input-group-addon']) }}
                        {{ Form::text('name', null, ['class' => 'form-control']) }}
                    </div>
                </div>
                {{ Form::hidden('id', 0) }}
            </div>
            <div class="panel-footer text-right">
                <button id='clear' class="btn btn-danger">Limpiar</button>
                {{ Form::submit('Aceptar', ['class' => 'btn btn-success']) }}
            </div>
        </div>
        {{ Form::close() }}

        
        {{ Form::open(array('url' => 'admin/restaurant/sections/positions')) }}
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Secciones</h3>
            </div>

            <div class="panel-body">
                <ul id="sortable">
                    @forelse($sections_selected as $section)
                    <li class="ui-state-default" style="display: inline-block; width: 100%;">
                        {{ Form::hidden('position['.$section->section_id.']', $section->section_order_id) }}
                        <div class="col-md-6 text-left">
                            <span class="fa fa-sort fa-lg" style="color:black;"></span>
                            {{ $section->section }}
                        </div>
                        <div class="col-md-6 text-right">
                            <button type="button" onclick="window.location.href ='{{ URL::to('admin/product/section/'.$section->section_id) }}'" class="btn btn-default" aria-label="Productos" title='Productos'>
                                <span class="fa fa-cog fa-fw" aria-hidden="true"></span>
                            </button>
                            <button type="button" onclick="editar('{{ $section->section }}', '{{ $section->section_id }}')" class="btn btn-default" aria-label="Editar" title='Editar'>
                                <span class="fa fa-pencil fa-fw" aria-hidden="true"></span>
                            </button>
                            <button type="button" onclick="window.location.href ='{{ URL::to('admin/restaurant/sections/changeActivate/'.$section->section_id) }}'" class="btn btn-<?php echo ($section->activate == 1) ? 'default' : 'succes' ?>" title='<?php echo ($section->activate == 1) ? 'Desactivar' : 'Activar' ?>'>
                                <i class="fa fa-check-circle fa-fw" aria-hidden="true"></i>
                            </button>
                        </div>
                    </li>
                    @empty
                    <p>No hay secciones definidas para el restaurante</p>
                    @endforelse
                </ul>
            </div>
            <div class="panel-footer text-right">
                {{ Form::submit('Guardar posiciones', ['class' => 'btn btn-success']) }}
            </div>
        </div>
        {{ Form::close() }}

    </div>
</div>

<script>
        $(document).ready(function () {
                $("#clear").click(function (ev) {
                        $("input[name='name']").val("");
                        $("input[name='id']").val("0");
                        ev.preventDefault();
                });
                
//            $("#btnPosition").click(function(){
//                
//                if ($(this).text() == 'Editar posiciones'){
//                    $(this).text('Aceptar');
                    $("#sortable").sortable({
                            update: function( event, ui ) {
                                $.each($(".ui-sortable-handle"), function(index, item){
                                    
                                    $(item).children('input[name^="position["]').val(index);
                                });
                            }
                        });
                    $("#sortable").disableSelection();
//                } else{
//                    $(this.text('Editar posiciones'));
//                }
//                
//            });
        });
            function editar(nombre, id){
            $("#name").val(nombre);
                    $("input[name='id']").val(id);
                    $('html, body').animate({
            scrollTop: $("#formul").offset().top
            }, 1000);
            }
</script>
@stop