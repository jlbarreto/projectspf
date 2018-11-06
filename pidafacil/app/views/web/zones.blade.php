@extends('general.general_white')
@section('content')

<?php
  $cart = Session::get('cart');
  $cantidad = Session::get('cart2');
?>

<style>
    .title{
        font-size: 18px;
        margin: 20px 0;
        padding: 4px;
    }
</style>

<input type="hidden" id="cont" value="{{$cantidad}}">
<nav class="navbar navbar-default" role="navigation">
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse"
            data-target=".navbar-ex1-collapse">
        <i class="fa fa-bars fa-2x"></i>
    </button>
    <a class="navbar-brand" href="{{ URL::to('') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
  </div>
  <div class="collapse navbar-collapse navbar-ex1-collapse">
    <ul class="nav navbar-nav">
      <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="hide_small"><i class="fa fa-bars fa-lg"></i></span> Inicio <b class="caret"></b></a>
                <ul class="dropdown-menu">
                <li><a href="{{ URL::to('explorar') }}">Explorar</a></li>
                <li id="descApp1" style="display:none;"><a href="https://play.google.com/store/apps/details?id=com.pidafacil.pidafacil">Descargar la App</a></li>
                <li id="descApp2" style="display:none;"><a href="https://itunes.apple.com/us/app/id990772385">Descargar la App</a></li>
                @if($promociones >= 1)<li><a href="{{ URL::to('promociones') }}">Promociones</a></li>@endif
                <li><a href="{{ URL::to('user/orders') }}">Repetir pedido</a></li>
                <li class="divider"></li>
                <li><a href="{{ URL::to('cart') }}" onclick="procesar()">Carrito de Compras</a></li>
                <li><a href="{{ URL::to('profile') }}">Mi perfil</a></li>
                    <li class="divider"></li>
                    @include('../include/linckChat')
                    <li class="divider"></li>
                @if(Auth::check())
                    <li><a href="{{ URL::to('logout') }}">Cerrar sesión</a></li>
                @else
                    <li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
                @endif
                </ul>
            </li>
            <!--<li><a href="#"><i class="fa fa-search fa-lg"></i> Buscar</a></li>-->
            <li><a href="{{ URL::to('cart') }}" onclick="procesar()"><span id="iconoContador">{{$cantidad}}&nbsp;</span><i class="fa fa-shopping-cart fa-lg"></i> Carrito de Compras</a></li>
    </ul>
    <form class="navbar-form navbar-left search-bar" role="search">
        <div class="form-group">
            <div class="input-group">
                <label for="tags" class="input-group-addon red-label">
                    <i class="fa fa-search"></i>
                </label>
                <input type="text" id="tags" name="tags" class="form-control searchTags" placeholder="Buscar">
            </div>
        </div>
    </form>
    <ul class="nav navbar-nav navbar-right">
        @if(Auth::check())
            @if(Auth::user()->name == '')
                <li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->email }} </a></li>
            @else
                <li><a href="{{ URL::to('profile') }}"> {{ Auth::user()->name.' '. Auth::user()->last_name }} </a></li>
            @endif
            <!-- <li><a href="{{ URL::to('logout') }}"> Cerrar sesión</a></li>-->
            @include('../include/linckChat')
        @else
            <li><a href="{{ URL::to('login') }}">Iniciar sesión</a></li>
            <li style="margin-right:50px;"><a href="{{ URL::to('login#signup') }}">Registrarse</a></li>
        @endif
    </ul>
  </div>
</nav>

<div class="container white_content" style="padding-bottom: 1em;">
    <h1 class="text-center">Zonas de cobertura</h1>
    <div class="row text-center types" id="zones">
        <div class="col-md-3">
            <div class="title">
                ZONA 1 <br/>
                $3.00
            </div>
            @if($zonas)
                @foreach($zonas as $zon)
                    @if($zon->shipping_price_id == 1)
                        <div class="col-md-6">{{$zon->zone}}</div>            
                    @endif
                @endforeach
            @endif
        </div>
        <div class="col-md-3">
            <div class="title">
                ZONA 2 <br/>
                $4.00
            </div>
            @if($zonas)
                @foreach($zonas as $zon)
                    @if($zon->shipping_price_id == 2)
                        <div class="col-md-12">{{$zon->zone}}</div>            
                    @endif
                @endforeach
            @endif
        </div>
        <div class="col-md-3">
            <div class="title">
                ZONA 3 <br/>
                $5.00
            </div>
            @if($zonas)
                @foreach($zonas as $zon)
                    @if($zon->shipping_price_id == 3)
                        <div class="col-md-12">{{$zon->zone}}</div>            
                    @endif
                @endforeach
            @endif
        </div>
        <div class="col-md-3">
            <div class="title">
                ZONA 4 <br/>
                $6.00
            </div>
            @if($zonas)
                @foreach($zonas as $zon)
                    @if($zon->shipping_price_id == 4)
                        <div class="col-md-12">{{$zon->zone}}</div>            
                    @endif
                @endforeach
            @endif
        </div>
    </div>
    <p style="margin-top: 1.5em;">
        * Ubicaciones fuera de las arriba mencionadas favor consultar previamente.
    </p>
    <p>
        * Nos reservamos el derecho de excluir lugares específicos aunque pertenezcan a nuestras zonas de cobertura.
    </p>
</div>

<script>

    function procesar(){
        appboy.logCustomEvent("Ir al Carrito");
    }

    $(document).ready(function(){
        var dispositivo = navigator.userAgent.toLowerCase();
        if( dispositivo.search(/iphone|ipod|ipad|android/) > -1 ){
            //alert("Esta navegando en un movil");

            var isMobile = {
                Android: function() {
                    return navigator.userAgent.match(/Android/i);
                },
                BlackBerry: function() {
                    return navigator.userAgent.match(/BlackBerry/i);
                },
                iOS: function() {
                    return navigator.userAgent.match(/iPhone|iPad|iPod/i);
                },
                Opera: function() {
                    return navigator.userAgent.match(/Opera Mini/i);
                },
                Windows: function() {
                    return navigator.userAgent.match(/IEMobile/i);
                },
                any: function() {
                    return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
                }
            };

            if(isMobile.iOS()){
                $("#descApp2").show();
                $("#descApp1").hide();
            }else if(isMobile.Android()){
                $("#descApp1").show();
                $("#descApp2").hide();
            }
        }

        if($("#cont").val() > 0){
            $("#iconoContador").show();
        }else{
            $("#iconoContador").hide();
        }
    });
</script>
@stop
