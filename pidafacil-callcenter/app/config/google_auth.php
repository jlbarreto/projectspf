<?php

return array(
    "base_url" => "http://localhost/pidafacil/public/login/gauth/auth",
    "providers" => array (
        "Google" => array (
            "enabled" => true,
            "keys"    => array ( "id" => "827265132301-o745doq2lrl2htpqv5cnpd36sb4qhn7e.apps.googleusercontent.com", "secret" => "7UYZOgyf8Fs4IYfByFyxxIaF" ),
            "scope"           => "https://www.googleapis.com/auth/userinfo.profile ". // optional
                "https://www.googleapis.com/auth/userinfo.email"

        )));