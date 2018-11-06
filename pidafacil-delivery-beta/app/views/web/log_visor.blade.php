@extends('general.master_page')
@section('fContent')

    @if($errors->first('email'))
        <div class="alert alert-warning">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            {{ $errors->first('email') }}
        </div>
    @endif
        @if($errors->first('password'))
            <div class="alert alert-warning">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                {{ $errors->first('password') }}
            </div>
        @endif
    @if(Session::has('flash_message'))
        <div class="alert alert-danger">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            {{ Session::get('flash_message')  }}
        </div>
        <?php Session::forget('flash_message'); ?>
    @endif
    <nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target=".navbar-ex1-collapse">
                <i class="fa fa-bars fa-2x"></i>
            </button>
            <a class="navbar-brand" href="{{ URL::to('') }}">{{ HTML::image('images/logo.svg', '', array('class'=>'nav_logo')) }}</a>
        </div>
    </nav>
    <div class="container below_bar white_content">
        <div class="center_content" id="login">

            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="types">

                        <h1>Iniciar sesión</h1>

                        <div class="row">
                            <div class="col-sm-8 col-sm-offset-2 space_50">
                                {{ Form::open(array('doLogin','post', 'role' => 'form')) }}
                                <div class="form-group">
                                    {{ Form::email('email', null, array('class' => 'form-control', 'placeholder' => 'Email')) }}
                                </div>
                                <div class="form-group">
                                    {{ Form::password('password', array('class' => 'form-control', 'placeholder' => 'Password')) }}
                                </div>
                                <p class="help-block"><a href="{{ URL::to('password/remind') }}">&iquest;Has olvidado tu contrase&ntilde;a?</a></p>
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