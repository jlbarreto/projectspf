require('newrelic');
var express = require('express');
var app = express();
var favicon = require('serve-favicon');
var config = require('./config');
var server = require('http').Server(app);
var io = require('socket.io').listen(server);
var npid = require('npid');
var uuid = require('node-uuid');
var Room = require('./controllers/room.js');
var Message = require('./models/message');
var User = require ('./models/user');
var _ = require('underscore');
var bodyParser = require('body-parser');
var moment = require('moment');

app.set('port', config.web.port);
app.set('ipaddr', config.web.ip);
app.use(favicon(__dirname + '/public/favicon.ico'));
app.use(express.static(__dirname + '/public'));
app.use('/components', express.static(__dirname + '/components'));
app.use('/js', express.static(__dirname + '/js'));
app.use('/icons', express.static(__dirname + '/icons'));
app.set('views', __dirname + '/views');
app.engine('html', require('ejs').renderFile);

app.use(bodyParser.urlencoded({
    extended: true
}));

app.use(bodyParser.json());

app.get('/', function(req, res) {
  res.render('index.html');
});

server.listen(app.get('port'), app.get('ipaddr'), function(){
	console.log('Express server listening on  IP: ' + app.get('ipaddr') + ' and port ' + app.get('port'));
});

io.set("log level", 1);
var people = {};
var rooms = {};
var sockets = [];
var isChatOnline = false;
var supportUserId = "";
var chatNotAvailableMsg = "Lo sentimos, el chat no está disponible en este momento, déjanos tu mensaje."

function purge(s, action) {
	/*
	The action will determine how we deal with the room/user removal.
	These are the following scenarios:
	if the user is the owner and (s)he:
		1) disconnects (i.e. leaves the whole server)
			- advise users
		 	- delete user from people object
			- delete room from rooms object
			- delete chat history
			- remove all users from room that is owned by disconnecting user
		2) removes the room
			- same as above except except not removing user from the people object
		3) leaves the room
			- same as above
	if the user is not an owner and (s)he's in a room:
		1) disconnects
			- delete user from people object
			- remove user from room.people object
		2) removes the room
			- produce error message (only owners can remove rooms)
		3) leaves the room
			- same as point 1 except not removing user from the people object
	if the user is not an owner and not in a room:
		1) disconnects
			- same as above except not removing user from room.people object
		2) removes the room
			- produce error message (only owners can remove rooms)
		3) leaves the room
			- n/a
	*/
	if (people[s.id].inroom) { // User is in a room
		var room = rooms[people[s.id].inroom]; // Check which room user is in.
		if (s.id === room.owner) { // User in room and owns room
			if (action === "disconnect") {
        chatOffline(s.id);
				var socketids = [];
				for (var i=0; i<sockets.length; i++) {
					socketids.push(sockets[i].id);
					if(_.contains((socketids)), room.people) {
						sockets[i].leave(room.name);
					}
				}
				if(_.contains((room.people)), s.id) {
					for (var i=0; i<room.people.length; i++) {
						people[room.people[i]].inroom = null;
					}
				}
				room.people = _.without(room.people, s.id); // Remove people from the room:people{}collection
				delete rooms[people[s.id].owns]; // Delete the room
				delete people[s.id]; // Delete user from people collection
				sizePeople = _.size(people);
				sizeRooms = _.size(rooms);
				io.sockets.emit("update-people", {people: people, count: sizePeople});
				io.sockets.emit("roomList", {rooms: rooms, count: sizeRooms});
				var o = _.findWhere(sockets, {'id': s.id});
				sockets = _.without(sockets, o);
			} else if (action === "removeRoom") { // Room owner removes room
				var socketids = [];
				for (var i=0; i<sockets.length; i++) {
					socketids.push(sockets[i].id);
					if(_.contains((socketids)), room.people) {
						sockets[i].leave(room.name);
					}
				}

				if(_.contains((room.people)), s.id) {
					for (var i=0; i<room.people.length; i++) {
						people[room.people[i]].inroom = null;
					}
				}
				delete rooms[people[s.id].owns];
				people[s.id].owns = null;
				room.people = _.without(room.people, s.id); // Remove people from the room:people{}collection
				sizeRooms = _.size(rooms);
				io.sockets.emit("roomList", {rooms: rooms, count: sizeRooms});
			} else if (action === "leaveRoom") { // Room owner leaves room
				var socketids = [];
				for (var i=0; i<sockets.length; i++) {
					socketids.push(sockets[i].id);
					if(_.contains((socketids)), room.people) {
						sockets[i].leave(room.name);
					}
				}

				if(_.contains((room.people)), s.id) {
					for (var i=0; i<room.people.length; i++) {
						people[room.people[i]].inroom = null;
					}
				}
				delete rooms[people[s.id].owns];
				people[s.id].owns = null;
				room.people = _.without(room.people, s.id); // Remove people from the room:people{}collection
				sizeRooms = _.size(rooms);
				io.sockets.emit("roomList", {rooms: rooms, count: sizeRooms});
			}
		} else { // User in room but does not own room
			if (action === "disconnect") {
        chatOffline(s.id);
				if (_.contains((room.people), s.id)) {
					var personIndex = room.people.indexOf(s.id);
					room.people.splice(personIndex, 1);
					s.leave(room.name);
				}
				delete people[s.id];
				sizePeople = _.size(people);
				io.sockets.emit("update-people", {people: people, count: sizePeople});
				var o = _.findWhere(sockets, {'id': s.id});
				sockets = _.without(sockets, o);
			} else if (action === "removeRoom") {
				s.emit("update", "Only the owner can remove a room.");
			} else if (action === "leaveRoom") {
				if (_.contains((room.people), s.id)) {
					var personIndex = room.people.indexOf(s.id);
					room.people.splice(personIndex, 1);
					people[s.id].inroom = null;
					s.leave(room.name);
				}
			}
		}
	} else {
		// The user isn't in a room, but maybe he just disconnected, handle the scenario:
		if (action === "disconnect") {
			io.sockets.emit("update", people[s.id].name + " ha abandonado el chat.", "msg-info");
			delete people[s.id];
			sizePeople = _.size(people);
			io.sockets.emit("update-people", {people: people, count: sizePeople});
			var o = _.findWhere(sockets, {'id': s.id});
			sockets = _.without(sockets, o);
		}
	}
}

function chatOffline(person) {
  if (people[person].id == supportUserId) {
    isChatOnline = false;
    console.log("Chat offline");
    io.sockets.emit("update", chatNotAvailableMsg, "msg-info");
  }
}

io.sockets.on("connection", function (socket) {

	socket.on("joinserver", function(name, pwd, email, userId, device) {
		var exists = false;
		var ownerRoomID = inRoomID = null;
		var screenName = null;

		_.find(people, function(key,value) {
			if (key.name.toLowerCase() === name.toLowerCase())
				return exists = true;
		});
		// Provide unique username:
		if (exists) {
			var randomNumber=Math.floor(Math.random()*1001)
			do {
				proposedName = name+randomNumber;
				_.find(people, function(key,value) {
					if (key.name.toLowerCase() === proposedName.toLowerCase())
						return exists = true;
				});
			} while (!exists);
			socket.emit("exists", {msg: "El nombre de usuario ya existe, por favor elige otro.", proposedName: proposedName});
		} else {
			User.find({ username: name, password: pwd}, function(err, users) {
				if (err) throw err;
				if (users.length > 0) {
					for (var u=0; u<users.length; u++) {
						var user = users[u];
            userId = user._id;
						screenName = user.screenName;
						socket.emit("rooms", true);
            isChatOnline = true;
            supportUserId = userId;
            socket.broadcast.emit("update", "¡Bienvenido(a)! ¿En que podemos ayudarte?", "msg-welcome");
            console.log("Chat online");
					}
				} else {
					screenName = name;
				}

				// Create new room
				var id = "";
				if (userId == "" || userId == null) {
					id = uuid.v4();
				} else {
					id = userId;
				}

        people[socket.id] = {"id" : id, "name" : screenName, "owns" : ownerRoomID, "inroom": inRoomID, "device": device};

        var room = new Room(name, id, socket.id);
				room.tag = email
				rooms[id] = room;
				sizeRooms = _.size(rooms);
				io.sockets.emit("roomList", {rooms: rooms, count: sizeRooms});
				// Add room to socket, and auto join the creator of the room
				socket.room = name;
				socket.join(socket.room);
				people[socket.id].owns = id;
				people[socket.id].inroom = id;
				room.addPerson(socket.id);
				socket.emit("sendRoomData", {id: id, owner: room.owner, option: false});
				sizeRooms = _.size(rooms);
				socket.emit("roomList", {rooms: rooms, count: sizeRooms});
				sockets.push(socket);

        // Validate if support agent user is online
        if (isChatOnline == true) {
          socket.emit("update", "¡Bienvenido(a)! ¿En que podemos ayudarte?", "msg-welcome");
        } else {
          socket.emit("update", chatNotAvailableMsg, "msg-info");
        }
			});
		}
	});

	socket.on("typing", function(data) {
		if (typeof people[socket.id] !== "undefined") {
			socket.broadcast.to(socket.room).emit("isTyping",
				{isTyping: data,
				 person: people[socket.id].name,
				 id: people[socket.id].id});
		}
	});

	socket.on("send", function(msTime, msg, id) {
    // Validate if support agent user is online
    if (isChatOnline != true) {
      socket.emit("update", chatNotAvailableMsg, "msg-info");
    }
    var found = false;
		var room = '';
		var msgStyle = '';
		var currentRoom = rooms[people[socket.id].inroom];
		if (typeof currentRoom !== "undefined") {
			if (people[socket.id].owns) {
				var room = rooms[people[socket.id].inroom];
				room = room.id
				msgStyle = "msg";
			} else {
				msgStyle = "msg-support"
			}
			var message = new Message({
				content: msg
				, sender: people[socket.id].name
				, receiver: socket.room
				, room: currentRoom.id
				, type: msgStyle
			});
			message.save(function(err) {
				if (err) throw err;
			});
			io.sockets.in(socket.room).emit("chat", msTime, people[socket.id].name, msg, msgStyle);
			io.sockets.emit("notifyNewMessage", room);
			socket.emit("isTyping", false);
		} else {
      io.sockets.in(socket.room).emit("chat", msTime, people[socket.id].name, "Lo sentimos, en este momento el chat esta fuera de servicio. ¡Volvemos pronto!", msgStyle);
    }
	});

	socket.on("disconnect", function() {
		if (typeof people[socket.id] !== "undefined") { // This handles the refresh of the name screen
			purge(socket, "disconnect");
		}
	});

	// Room functions
	socket.on("createRoom", function(name) {
		if (people[socket.id].inroom) {
			socket.emit("update", "You are in a room. Please leave it first to create your own.");
		} else if (!people[socket.id].owns) {
			var id = uuid.v4();
			var room = new Room(name, id, socket.id);
			rooms[id] = room;
			sizeRooms = _.size(rooms);
			io.sockets.emit("roomList", {rooms: rooms, count: sizeRooms});
			// Add room to socket, and auto join the creator of the room
			socket.room = name;
			socket.join(socket.room);
			people[socket.id].owns = id;
			people[socket.id].inroom = id;
			room.addPerson(socket.id);
			socket.emit("sendRoomData", {id: id, owner: room.owner, option: false});
		} else {
			socket.emit("update", "You have already created a room.");
		}
	});

	socket.on("check", function(name, fn) {
		var match = false;
		_.find(rooms, function(key,value) {
			if (key.name === name)
				return match = true;
		});
		fn({result: match});
	});

	socket.on("joinRoom", function(id) {
		if (typeof people[socket.id] !== "undefined") {
			var room = rooms[id];
			if (typeof room !== "undefined") {
				if (socket.id === room.owner) {
					socket.emit("update", "You are the owner of this room and you have already been joined.");
				} else {
					if (_.contains((room.people), socket.id)) {
						socket.emit("update", "You have already joined this room.");
					} else {
						if (people[socket.id].inroom !== null) {
							socket.emit("update", "You are already in a room ("+rooms[people[socket.id].inroom].name+"), please leave it first to join another room.");
				    	} else {
							room.addPerson(socket.id);
							people[socket.id].inroom = id;
							socket.room = room.name;
							socket.join(socket.room);
							user = people[socket.id];
							socket.emit("sendRoomData", {id: id, owner: room.owner, option: true});
              // Notify room owner when support agent is back
              //io.to(room.owner).emit("update", "¡Bienvenido " + socket.room + "! ¿En que podemos ayudarte?", "msg-welcome");
							// Get current date 1 hour behind
							var dateFromHistory = moment().add(-1, 'hours');

							Message.find({room: id, date: {$gte: dateFromHistory.toDate()}},
							 function(err, messages) {
								if (err) throw err;
								socket.emit("history", messages);
							});
						}
					}
				}
			}
		} else {
			socket.emit("update", "Inicia sesión nuevamente.");
		}
	});

	socket.on("leaveRoom", function(id) {
		var room = rooms[id];
		if (room)
			purge(socket, "leaveRoom");
	});

	socket.on("disconnectUser", function(roomOwner) {
		if (typeof roomOwner !== "undefined") {
      if (typeof people[roomOwner] !== "undefined") {
        var room = rooms[people[roomOwner].inroom]; // Check which room user is in.
  			var socketids = [];
  			for (var i=0; i<sockets.length; i++) {
  				socketids.push(sockets[i].id);
  				if(_.contains((socketids)), room.people) {
  					sockets[i].leave(room.name);
  				}
  			}
  			if(_.contains((room.people)), roomOwner) {
  				for (var i=0; i<room.people.length; i++) {
  					people[room.people[i]].inroom = null;
  				}
  			}
  			room.people = _.without(room.people, roomOwner); // Remove people from the room:people{}collection
  			delete rooms[people[roomOwner].owns]; // Delete the room
  			delete people[roomOwner]; // Delete user from people collection
  			sizePeople = _.size(people);
  			sizeRooms = _.size(rooms);
  			io.sockets.emit("update-people", {people: people, count: sizePeople});
  			io.sockets.emit("roomList", {rooms: rooms, count: sizeRooms});
  			var o = _.findWhere(sockets, {'id': roomOwner});
  			sockets = _.without(sockets, o);
  			socket.emit("sendRoomData", {id: null, owner: null, option: false});
  			socket.emit("chatEnded", "El chat ha finalizado.", "msg-info");
  			// Disconnect room owner from server
  			o.disconnect()
      }
		}
	});
});
