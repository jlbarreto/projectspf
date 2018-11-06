var mongoose = require('mongoose')
, Schema = mongoose.Schema
, ObjectId = Schema.ObjectId

var userSchema = new Schema({
	id : ObjectId
	, username   : String
	, password   : String
	, screenName : String
});

var User = mongoose.model('User', userSchema);

module.exports = User;
