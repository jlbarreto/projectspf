<footer>
    <div class="container-fluid white_content" style="background-color: #1a1a1a; padding: 25px;">
        <div class="container">
            <div class="row">
                <!-- <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                    <p style="color: #5f5f5f;">Acerca de nosotros</p>
                    <ul>
                        <a href="#"><li>Contacto</li></a>
                        <a href="#"><li>Prensa</li></a>
                        <a href="#"><li>Terminos y condiciones</li></a>
                        <a href="#"><li>Privacidad</li></a>
                    </ul>
                </div> -->
                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                    <!--<p style="color: #5f5f5f;">Si tienes dificultades en el sitio:</p>-->
                    <ul>
                        <!-- <a href="#"><li>Preguntas frecuentes</li></a>
                        <a href="#"><li>Mapa del sitio</li></a> -->
                        <li><a href="#" id="CopyURL">Copiar URL</a></li>
                        <a href="{{URL::to('/zones')}}">
                            <li>Zonas de cobertura</li>
                        </a>
                    </ul>
                </div>
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="row" >
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <p style="color: #5f5f5f;">Disponible en:</p>
                            <a href="https://play.google.com/store/apps/details?id=com.pidafacil.pidafacil" target="_blank">
                                {{ HTML::image('images/icons/play_store.png', 'andr', array('width' => '135px', 'height' => '41px')) }}</a>
                            <a href="https://itunes.apple.com/us/app/pidafacil-comida-domicilio/id990772385" target="_blank">{{ HTML::image('images/icons/appstore.svg')}}</a>
                        </div>
                    </div>
                    <p style="color: #5f5f5f;">Estamos en:</p>
                    <a href="https://www.facebook.com/pidafacilsv" target="_blank">{{ HTML::image('images/socials/facebook64.png', 'facebook pidafacil', array('width' => '32px')) }}</a>
                    <a href="https://twitter.com/pidafacil">{{ HTML::image('images/socials/twitter64.png', 'twitter pidafacil', array('width' => '32px')) }}</a>                    
                    <a href="https://instagram.com/pidafacil">{{ HTML::image('images/socials/instagram64.png', 'instagram pidafacil', array('width' => '32px')) }}</a>
                </div>
                <div class="col-md-4">
                <br>
                    <span>Certificado de seguridad</span>
                    <ul>
                        <li>
                        {{ HTML::image('images/new_credit_card.png', 'vis', array('width' => '205x', 'height' => 'auto')) }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
{{ HTML::script('js/details-shim.js') }}
{{ HTML::script('js/boobstrap/bootstrap.js') }}
{{ HTML::script('js/jquery.validate.min.js') }}
{{ HTML::script('js/ajax.js') }}

<script>
    $("#CopyURL").click(function(e){
        e.preventDefault();
        prompt('Ctrl+c Para copiar URL', '{{Request::url()}}');
    });
</script>
