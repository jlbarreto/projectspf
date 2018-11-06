var express = require('express');
var app = express();
var server = require('http').Server(app);
var config = require('./config');
var io = require('socket.io').listen(server);
var mysql      = require('mysql');
var connection = mysql.createConnection({
  host     : '104.130.175.228',
  user     : 'p1d@F@c1L',
  password : 'Oz021LNL98E4e9SZ6tG07zg33x3C6Pr',
  database : 'pf'
});

var restaurantsSQL = "select o.order_id, o.order_cod as code, r.name as restaurant, s.service_type as service, DATE_FORMAT(o.created_at, '%d-%b %h:%i %p') as time " +
                     "from req_orders o " +
                     "inner join res_restaurants r on o.restaurant_id = r.restaurant_id " +
                     "inner join res_service_types s on s.service_type_id = o.service_type_id " +
                     "inner join (select order_id, max(order_status_id) as order_status_id " +
			               "from req_order_status_logs " +
			               "group by order_id) l on l.order_id = o.order_id " +
                     "where l.order_status_id < 3 " +
                     "order by o.created_at;"

app.set('port', config.web.port);
app.set('ipaddr', config.web.ip);
app.use(express.static(__dirname + '/public'));
app.use('/components', express.static(__dirname + '/components'));
app.use('/js', express.static(__dirname + '/js'));

app.get('/', function(req, res) {
  res.render('index.html');
});

server.listen(app.get('port'), app.get('ipaddr'), function(){
	console.log('Express server listening on  IP: ' + app.get('ipaddr') + ' and port ' + app.get('port'));
});

connection.connect(function(err){
  if(!err) {
      console.log("Database is connected ... \n\n");
  } else {
      console.log("Error connecting database ... \n\n");
  }
});

io.sockets.on("connection", function (socket) {
  console.log("New socket connected");

  connection.query(restaurantsSQL, function(err, rows, fields) {
    if (!err) {
      console.log("Getting new orders ...");
      io.sockets.emit("newOrders", rows);
    } else {
      console.log('Error while performing Query.');
    }
  });

  setInterval(function() {
    connection.query(restaurantsSQL, function(err, rows, fields) {
      if (!err) {
        console.log("Getting new orders ...");
        io.sockets.emit("newOrders", rows);
      } else {
        console.log('Error while performing Query.');
      }
    });
  }, 5000);
});
