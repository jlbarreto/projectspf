<?php
// app/config/facebook.php

// Facebook app Config 
/*
return array(
	'appId' => '308079659365029',
	'secret' => 'e75b490d0a555ca5fbb6a942519a0982'
);*/

return array(
    "base_url" => "http://localhost/pidafacil/public/login/fbauth/auth",
    "providers" => array (
        "Facebook" => array (
            "enabled" => TRUE,
            "keys" => array ("id" => "308079659365029", "secret" =>"e75b490d0a555ca5fbb6a942519a0982"),
            "scope" => "public_profile,email"
        )
    )
);