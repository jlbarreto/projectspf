<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	/*'mailgun' => array(
		'domain' => 'sandbox81324d8005df4eb2a763d5a2354f36b1.mailgun.org',
		'secret' => 'key-b4f03d4e67bad2720c476ac7ba705471',
	),*/
	
	'mailgun' => array(
		'domain' => 'sandbox40816a1770954c74956797a1fbee3391.mailgun.org',
		'secret' => 'key-f523411c75757b5bbd3a2a85e50e4a8f',
	),
	
	'mandrill' => array(
		'secret' => '',
	),

	'stripe' => array(
		'model'  => 'User',
		'secret' => '',
	),

);
