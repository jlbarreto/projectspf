var express = require("express");
var mysql = require('mysql');
var app = express();
var port = 3020;
/*Configuracion de la base de datos.*/
var config = require('./configVar.js');

var connection = mysql.createConnection({
    host     : config.host,
    user     : config.user,
    password : config.passw,
    database : config.db
});
var server = app.listen(port);
var io = require('socket.io').listen(server);
var people = {};
io.on('connection', function(socket)
{
        setInterval(function()
        {
// Delivery pidafacil
            connection.query('SELECT req_orders.order_id , req_orders.order_cod FROM req_orders WHERE req_orders.service_type_id = 3 and req_orders.viewed_pidafacil = 0 GROUP BY req_orders.order_id', function(err, rows, fields) {
                if (!err)
                {
                    if(rows.length > 0)
                    {
                        rows.forEach(function(value)
                        {
                            var id = value.order_id;
                            var codigo = value.order_cod;
                            io.emit('delivery_pidafacil', codigo);
                            connection.query('UPDATE `req_orders` SET `viewed_pidafacil`= 1 WHERE `order_id` = '+id);
                        });
                    }
                }
                else
                    console.log('Error while performing Query.');
            });
        }, 15000);


    // delivery del restaurante
    var id_rest = 0;
    socket.on('delivery_restaurant', function(msg)
    {
        var consulta = 'SELECT req_orders.order_id, req_orders.order_cod FROM req_orders  WHERE ((req_orders.service_type_id = 3 and req_orders.viewed_pidafacil = 1 and req_orders.viewed_restaurants = 0) or (req_orders.service_type_id <> 3 and req_orders.viewed_restaurants = 0) or ((req_orders.service_type_id = 2 or req_orders.service_type_id = 1) and req_orders.viewed_restaurants = 0)) and restaurant_id = '+msg[0]+' GROUP BY req_orders.order_id'
        connection.query(consulta, function(err, rows, fields)
        {
            if (!err)
            {
                if(rows.length > 0)
                {
                    rows.forEach(function (value)
                    {
                        connection.query('SELECT MAX(req_order_status_logs.order_status_id) as ultimo FROM req_order_status_logs WHERE  req_order_status_logs.order_id = '+value.order_id, function(err2, rows2, fields2) {
                            if(!err2)
                            {
                                if(rows2.length > 0)
                                {
                                    rows2.forEach(function(value2)
                                    {
                                        if((value.service_type_id != 3 && value2.ultimo == 1) || value.service_type_id == 3 && value2.ultimo > 1)
                                        {
                                            var id = value.order_id;
                                            var codigo = value.order_cod;
                                            io.to(socket.id).emit('delivery_restaurant', codigo)
                                            connection.query('UPDATE `req_orders` SET `viewed_restaurants`= 1 WHERE `order_id` = ' + id);
                                        }

                                    });
                                }
                            }
                        });
                    });
                }
            }
            else
                console.log('Error while performing Query.');
        });
    });

});
