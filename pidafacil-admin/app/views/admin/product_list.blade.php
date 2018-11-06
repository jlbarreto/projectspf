@extends('general.admin_layout')
@section('content')

<div class="center_content">

    
    <h1 class="title">Productos de 
        @if(isset($restaurant))
        restaurante {{ $restaurant->name }}
        @else
        sección {{ $section->section }}
        @endif
    </h1>

    <button onclick='window.location.href ="{{ URL::to('admin/restaurant/edit/'.$restaurant->slug) }}"' class="btn btn-success">Ir a restaurante</button>
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
                    <button onclick='window.location.href ="{{ URL::to('admin/product/create/'.$section->section_id) }}"' class="btn btn-success">Agregar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Productos</h3>
        </div>

        <div class="panel-body">
            <ul>
                @forelse($products as $product)
                <li class="ui-state-default" style="display: inline-block; width: 100%;">
                    <div class="col-md-6 text-left">
                        {{ $product->product }}
                    </div>
                    <div class="col-md-6 text-right">
                        <button type="button" onclick="window.location.href ='{{ URL::to('admin/product/edit/'.$product->product_id) }}'" class="btn btn-default" aria-label="Editar" title='Editar'>
                            <span class="fa fa-pencil fa-fw" aria-hidden="true"></span>
                        </button>
                        <button type="button" onclick="window.location.href ='{{ URL::to('admin/product/changeActivate/'.$product->product_id) }}'" class="btn btn-<?php echo ($product->activate == 1) ? 'default' : 'succes' ?>" title='<?php echo ($product->activate == 1) ? 'Desactivar' : 'Activar' ?>'>
                            <i class="fa fa-check-circle fa-fw" aria-hidden="true"></i>
                        </button>
                    </div>
                </li>
                @empty
                <p>No hay productos definidos para la sección</p>
                @endforelse
            </ul>
        </div>
    </div>


</div>


@stop