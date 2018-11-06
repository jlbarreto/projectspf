@extends('general.master_page')
@section('fContent')
<nav class="navbar navbar-default" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse"
                data-target=".navbar-ex1-collapse">
            <i class="fa fa-bars fa-2x"></i>
        </button>
        <a class="navbar-brand" href="{{ URL::to('') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
    </div>
</nav>
@include('partials.alerts.error')

@if(Session::has('flash_message'))
<div class="alert alert-success">
    <a href="#" class="close" data-dismiss="alert">&times;</a>
    {{ Session::get('flash_message') }}
</div>
@endif
<div class="container below_bar white_content">
    <div class="center_content" id="login">

        <div class="row">
            <div style="width: 600px; position: relative; left: 50%; margin-left: -300px;">
                <div class="types">

                    <h1>Iniciar sesión</h1>
                    <p>Ingresa a través de cuenta de correo.</p>
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 space_50">
                            {{ Form::open(array('url'=>'/login','method'=>'post', 'role' => 'form')) }}
                            <div class="form-group">
                                {{ Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'Email')) }}
                            </div>
                            <div class="form-group">
                                {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) }}
                            </div>
                            <div class="checkbox">
                                <label for="">
                                    {{ Form::checkbox('stay-logged', 1,['class'=>'checkbox no_align']) }} Recordar mi contraseña
                                </label>
                            </div>
                            {{ Form::submit('Login', array('class' => 'btn btn-primary button_150')) }}
                            {{ Form::close() }}	
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@stop