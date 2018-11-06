<footer>
    <div class="container-fluid white_content" style="background-color: #1a1a1a; padding: 25px;">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                    <p style="color: #5f5f5f;">Acerca de nosotros</p>
                    <ul>
                        <a href="#"><li>Contacto</li></a>
                        <a href="#"><li>Prensa</li></a>
                        <a href="#"><li>Terminos y condiciones</li></a>
                        <a href="#"><li>Privacidad</li></a>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                    <p style="color: #5f5f5f;">Si tienes dificultades en el sitio:</p>
                    <ul>
                        <a href="#"><li>Preguntas frecuentes</li></a>
                        <a href="#"><li>Mapa del sitio</li></a>
                        <li><a href="#" id="CopyURL">Copiar URL</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="row" >
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p style="color: #5f5f5f;">Disponible en:</p>
                            <a href="https://play.google.com/store/apps/details?id=Pidafacil">
                                {{ HTML::image('https://developer.android.com/images/brand/es-419_generic_rgb_wo_45.png')}}</a>
                            <a href="#">{{ HTML::image('images/icons/appstore.svg')}}</a>
                        </div>
                    </div>
                    <p style="color: #5f5f5f;">Estamos en:</p>
                    <a href="#">{{ HTML::image('images/socials/twitter64.png', 'twitter pidafacil', array('width' => '32px')) }}</a>
                    <a href="#">{{ HTML::image('images/socials/facebook64.png', 'facebook pidafacil', array('width' => '32px')) }}</a>
                    <a href="#">{{ HTML::image('images/socials/youtube64.png', 'youtube pidafacil', array('width' => '32px')) }}</a>
                    <a href="#">{{ HTML::image('images/socials/google_plus64.png', 'google plus pidafacil', array('width' => '32px')) }}</a>
                    <a href="#">{{ HTML::image('images/socials/instagram64.png', 'instagram pidafacil', array('width' => '32px')) }}</a>
                </div>
            </div>
        </div>
    </div>
</footer>
{{ HTML::script('js/details-shim.js') }}
{{ HTML::script('js/boobstrap/bootstrap.js') }}
{{ HTML::script('js/jquery.validate.min.js') }}
{{ HTML::script('js/ajax.js') }}
{{ HTML::script('js/moment.js') }}

<script>
    $("#CopyURL").click(function(e){
        e.preventDefault();
        prompt('Ctrl+c Para copiar URL', '{{Request::url()}}');
    });

</script>
