@extends('general.admin_layout')
@section('content')

<div class="container below_bar white_content">
    <div class="center_content">
        <h1>Bienvenido a la administración, {{ Auth::user()->name }}</h1>
        
        <h3>Selecciona alguna de las siguientes opciones para administrar</h3>
        
        <div class="actions space">
            <a class="btn btn-primary button_200 user_actions" href="{{ URL::to('admin/restaurant/list') }}">Restaurantes</a>
            <a class="btn btn-primary button_200 user_actions" href="#">Productos</a>
            <a class="btn btn-primary button_200 user_actions" href="#">Mis Comentarios</a>
            <a class="btn btn-primary button_200 user_actions" href="#">Tags</a>
            <a class="btn btn-primary button_200 user_actions" href="{{ URL::to('admin/restaurant/horarios') }}">Horarios</a>
        </div>
        <a class="btn btn-link button_150 space_50" href="{{ URL::to('logout') }}">Cerrar sesión</a>
    </div>
</div>
@stop