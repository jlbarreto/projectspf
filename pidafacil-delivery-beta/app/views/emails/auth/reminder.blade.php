<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>Restablece tu contraseña en Pidafacil</h2>

		<div>
            <p>
                Este correo electrónico ha sido enviado porque has solicitado un cambio de contraseña.
                Por favor, ingresa a este enlace para restablecerla:
            </p>{{ URL::to('password/reset', array($token)) }}.<br/>
            El link expira en {{ Config::get('auth.reminder.expire', 60) }} Minutos.

		</div>
	</body>
</html>
