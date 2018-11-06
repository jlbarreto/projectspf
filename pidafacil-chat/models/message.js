var mongoose = require('mongoose')
, Schema = mongoose.Schema
, ObjectId = Schema.ObjectId

mongoose.connect('mongodb://localhost/chat');

var messageSchema = new Schema({
	id : ObjectId
	, content  : String
	, sender   : String
	, receiver : String
	, room     : String
	, type     : String
	, date     : { type: Date, default: Date.now }
});

var Message = mongoose.model('Message', messageSchema);

module.exports = Message;