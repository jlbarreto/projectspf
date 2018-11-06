/*
Functions
*/

function toggleNameForm() {
   $("#login-screen").toggle();
}

function toggleChatWindow() {
  $("#main-chat-screen").toggle();
}

// Pad n to specified size by prepending a zeros
function zeroPad(num, size) {
  var s = num + "";
  while (s.length < size)
    s = "0" + s;
  return s;
}

// Format the time specified in ms from 1970 into local HH:MM:SS
function timeFormat(msTime) {
  var date = new Date(msTime);
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? 'PM' : 'AM';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? '0'+minutes : minutes;
  var strTime = hours + ':' + minutes + ' ' + ampm;
  return strTime;
}

$(document).ready(function() {

  // Global variables
  var socket = io.connect(IP);
  var myRoomID = null;
  var roomOwner = null;
  var notificationCounter = "<span class='notification-counter'></span>";

  $("form").submit(function(event) {
    event.preventDefault();
  });

  $("#conversation").bind("DOMSubtreeModified",function() {
    $(this).scrollTop($(this)[0].scrollHeight);
  });

  $("#main-chat-screen").hide();
  $("#errors").hide();
  $("#username").focus();
  $("#join").attr('disabled', 'disabled');

  if ($("#username").val() === "") {
    $("#join").attr('disabled', 'disabled');
  }

  // Main screen
  $("#nameForm").submit(function() {
    var name = $("#username").val();
    var pwd = $("#password").val();
    var device = "desktop";
    if (navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i)) {
      device = "mobile";
    }
    if (name === "" || name.length < 2) {
      $("#errors").empty();
      $("#errors").append("Please enter a name");
      $("#errors").show();
    } else {
      socket.emit("joinserver", name, pwd, "", "", device);
      toggleNameForm();
      toggleChatWindow();
      $("#msg").focus();
    }
  });

  $("#username").keypress(function(e){
    var name = $("#username").val();
    if(name.length < 2) {
      $("#join").attr('disabled', 'disabled');
    } else {
      $("#errors").empty();
      $("#errors").hide();
      $("#join").removeAttr('disabled');
    }
  });

  // Main chat screen
  $("#chatForm").submit(function() {
    var msg = $("#msg").val();
    if (msg !== "") {
      if (roomOwner !== null) {
        socket.emit("send", timeFormat(new Date().getTime()), msg, 0);
      }
      $("#msg").val("");
    }
  });

  // 'Is typing' message
  var typing = false;
  var timeout = undefined;

  function timeoutFunction() {
    typing = false;
    socket.emit("typing", false);
  }

  $("#msg").keypress(function(e){
    if (e.which !== 13) {
      if (typing === false && myRoomID !== null && $("#msg").is(":focus")) {
        typing = true;
        socket.emit("typing", true);
      } else {
        clearTimeout(timeout);
        timeout = setTimeout(timeoutFunction, 1000);
      }
    }
  });

  $("#rooms").on('click', '.list-group-item', function() {
    var roomName = $(this).find("span").text();
    var roomID = $(this).attr("id");
    var currentRoomID = myRoomID;
    $("#msgs").html("");
    $("#current-chat").html(roomName);
    // Leave current room to join another
    socket.emit("leaveRoom", currentRoomID);
    // Join new room
    socket.emit("joinRoom", roomID);
  });

  $("#endChat").on('click', function(){
    // Disconect room and owner
    socket.emit("disconnectUser", roomOwner);
  });

  /* Sockets */

  socket.on("isTyping", function(data) {
    if (data.isTyping) {
      if ($("#"+data.person+"").length === 0) {
        $("#updates").append("<li id='"+ data.id +"'><span><small><i></i> " + data.person + " esta escribiendo...</small></li>");
        timeout = setTimeout(timeoutFunction, 1000);
      }
    } else {
      $("#"+data.id+"").remove();
    }
  });

  socket.on("exists", function(data) {
    $("#errors").empty();
    $("#errors").show();
    $("#errors").append(data.msg + " Try <strong>" + data.proposedName + "</strong>");
      toggleNameForm();
      toggleChatWindow();
  });

  socket.on("notifyNewMessage", function(room) {
    $("li[id='"+room+"']").find(".notification-counter").remove();
    $(notificationCounter).insertAfter($("li[id='"+room+"']").find("span"));
  });

  socket.on("history", function(data) {
    if (data.length !== 0) {
      $.each(data, function(data, msg) {
        $("#msgs").append("<li class='"+msg.type+"'><strong><div><span class='msg-user'>" + msg.sender + "</span><span class='msg-time'>" + timeFormat(msg.date) + "</span></div></strong><p class='msg-text'>" + msg.content + "</p></li>");
      });
    } else {
      $("#msgs").append("<li><strong><span class='chat-status'>No hay mensajes aun.</li>");
    }
  });

  socket.on("update", function(msg, type) {
    $("#msgs").append("<li class='"+type+"'>" + msg + "</li>");
  });

  socket.on("chat", function(msTime, person, msg, msgType) {
    $(".chat-status").remove();
    $("#msgs").append("<li class='"+msgType+"'><strong><div><span class='msg-user'>" + person + "</span><span class='msg-time'>" + msTime + "</span></div></strong><p class='msg-text'>" + msg + "</p></li>");
    // Clear typing field
    $("#"+person.name+"").remove();
    clearTimeout(timeout);
    timeout = setTimeout(timeoutFunction, 0);
  });

  socket.on("roomList", function(data) {
    $("#rooms").text("");
     if (!jQuery.isEmptyObject(data.rooms)) {
      $.each(data.rooms, function(id, room) {
        if (room.tag == "" || room.tag == null) {
          $('#rooms').append("<li id="+id+" class=\"list-group-item\"><span>" + room.name + "</span></li>");
        } else {
          $('#rooms').append("<li id="+id+" class=\"list-group-item\"><span>" + room.name + "</span><p>" + room.tag + "</p></li>");
        }
      });
    } else {
      $("#rooms").append("<li class=\"list-group-item\">No hay usuarios conectados.</li>");
    }
  });

  socket.on("chatEnded", function(msg, type) {
    $("#msgs").append("<li class='"+type+"'>" + msg + "</li>");
  });

  socket.on("sendRoomData", function(data) {
    var showEndBtn = Boolean(data.option);
    myRoomID = data.id;
    roomOwner = data.owner;
    $("li[id='"+myRoomID+"']").find(".notification-counter").remove();
    // Validate current socket is current room owner
    if (showEndBtn == false) {
      $("#endChat").hide();
    } else {
      $("#endChat").show();
    }
  });

  socket.on("disconnect", function(){
    $("#msgs").append("<li><strong><span class='chat-status'>El chat no esta disponible</span></strong></li>");
    $("#msg").attr("disabled", "disabled");
    $("#send").attr("disabled", "disabled");
  });

  socket.on("rooms", function(data) {
    if (data) {
      $(".rooms").show();
    }
  });
});
