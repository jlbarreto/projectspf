<?php


class EmailController {

	public function welcome($to, $name) {
		try {			
			$value = array(
				'user_name' => $name
			);
			$response = Mail::send('emails.welcome', array('bmail' => $value), function ($message) use ($to, $name)
			{
			    $message->to($to, $name)->subject('Bienvenido!');
			});
			return Response::json($response, 200);
		} catch (Exception $e) {
			return Response::json(array('message_error' => $e->getMessage()), 400);
		}

	}

    public function cambioEstado($to, $name, $value) {
        try {
                $orderCod = $value['order_cod'];
                $response = Mail::send('emails.cambio', array('bmail' => $value), function ($message) use ($to, $name, $orderCod)
            {
                $message->to($to, $name)->subject('Orden codigo: '.$orderCod);
            });
            return Response::json($response, 200);
        } catch (Exception $e) {
            return Response::json(array('message_error' => $e->getMessage()), 400);
        }

    }

}
