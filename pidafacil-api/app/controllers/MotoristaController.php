<?php

class MotoristaController extends \BaseController {

    public function show() {
        $input = Input::all();
        try {
            $statusCode = 200;
            $motorista = Motorista::where('motorista_id', $input['motorista_id'])
                    ->first(array('nombre', 'apellido', 'username', 'ref_user_id', 'estado'));

            $response = array(
                "status" => true,
                "data" => $motorista
            );
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    public function get_orders() {
        $input = Input::all();
        try {
            $statusCode = 200;
            $date=date('Y-m-d');//'2016-01-29';
            $orders = Motorista::findOrFail($input['motorista_id'])
                    ->orders()
                    ->with('products')
                    ->where('req_orders.created_at','LIKE','%'.$date.'%')
                    ->get(array('req_orders.order_id', 'req_orders.restaurant_id',
                                'req_orders.address_id', 'req_orders.order_cod',
                                'req_orders.address', 'req_orders.order_total',
                                'req_orders.shipping_charge', 'req_orders.pay_change',
                                'req_orders.created_at', 'req_orders.updated_at',
                                'req_orders.customer', 'req_orders.payment_method_id','req_orders.customer_phone','req_orders.credit_charge'));

            Log::info('Todas las ordenes asignadas al motorista: '.$orders);

            foreach ($orders as $order) {
                //Obteniendo dirección y zona del cliente
                if ($order->address_id != null) {
                    $order->address_detail = Address::where('address_id', $order->address_id)->with("Zone")->first(array('diner_addresses.reference', 'diner_addresses.user_id', 'diner_addresses.zone_id', 'diner_addresses.address_id', 'diner_addresses.map_coordinates'));
                    $order->user = User::where('user_id', $order->address_detail->user_id)->first(array('user_id', 'email', 'name', 'last_name'));
                } else {
                    $order->address_detail = null;
                    $order->user = null;
                }

                Log::info("Tiene direccion de cliente");

                //Obteniendo el restaurant
                $order->restaurant = Restaurant::where('restaurant_id', $order->restaurant_id)
                        ->first(array('name', 'commission_percentage', 'address', 'phone', 'map_coordinates'));

                Log::info("Tiene restaurante");
                Log::info("Coordenadas: ".$order->restaurant);

                //Obteniendo el estatus de la orden
                $order->status = OrderStatusLog::where('order_id', $order->order_id)->orderBy('created_at', 'desc')->first(array('order_status_id', 'comment', 'created_at'));

                $card_charge = 0.00;
                if ($order->pay_change == NULL || $order->pay_change == "null" || $order->pay_change == "") {
                  $card_charge = (substr($order->order_total, 1) + $order->shipping_charge) * 0.04;
                }

                //Total a pagar
                $order->total = substr($order->order_total, 1) + $order->shipping_charge + $card_charge;
                $order->pay_to_restaurant = substr($order->order_total, 1) *
                        ((100 - $order->restaurant->commission_percentage) / 100);

                //Eliminando campos no necesarios para la app
                unset($order->pivot);

                foreach ($order->products as $product) {
                    unset($product->order_det_id);
                    unset($product->order_id);
                    unset($product->product_id);
                    unset($product->created_at);
                    unset($product->updated_at);
                }
            }

            $response = array(
                "status" => true,
                "data" => $orders
            );
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

    public function doLogin() {
        $input = Input::all();
        try {
            $statusCode = 200;
            // validate the info, create rules for the inputs
            $rules = array(
                'username' => 'required',
                'password' => 'required|alphaNum|min:4'
            );

            // run the validation rules on the inputs from the form
            $validator = Validator::make($input, $rules);

            if ($validator->fails()) {
                //error
                $response['status'] = false;
                $response['data'] = $validator->messages();
            } else {

                $password = $input['password'];

                $usuario =  Motorista::where('username', $input['username'])
                        ->where('password', $input['password'])
                        ->first(array('motorista_id', 'nombre', 'apellido',
                            'username', 'ref_user_id', 'estado'));

                // attempt to do the logins
                if ($usuario!=null) {
                    $response['status'] = true;
                    $response['data'] = $usuario;
                } else {
                    // validation not successful, send back to form
                    $response['status'] = false;
                    $response['data'] = "Tu usuario y clave no coinciden. Vuelve a intentarlo.";
                }
            }
        } catch (Exception $e) {
            $statusCode = 400;
            $response = array(
                "status" => false,
                "data" => $e->getMessage()
            );
        }
        return Response::json($response, $statusCode);
    }

}
