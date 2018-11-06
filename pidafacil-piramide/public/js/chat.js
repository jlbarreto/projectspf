var socket = io.connect(ip);

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
socket.on("update", function(msg, type) {
    $("#msgs").append("<li class='"+type+"'>" + msg + "</li>");
});

socket.on("chat", function(msTime, person, msg, msgType) {
    $("#msgs").append("<li class='"+msgType+"'><strong><div><span class='msg-user'>" + person + "</span><span class='msg-time'>" + msTime + "</span></div></strong><p class='msg-text'>" + msg + "</p></li>");
// Clear typing field
    $("#"+person.name+"").remove();
    clearTimeout(timeout);
    timeout = setTimeout(timeoutFunction, 0);
});

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

$(function() {
    var device = "web";
    var name = $("#name").val();
    var userId = $("#userId").val();
    var pwd = "";
    var correo = $("#mail").val();
    socket.emit("joinserver", name, pwd, correo, userId, device);
});

//Envia al soporte cuando el cliente esta escrbiendo
var typing = false;
var timeout = undefined;

function timeoutFunction() {
    typing = false;
    socket.emit("typing", false);
}

$("#msg").keypress(function(e){
    if (e.which !== 13) {
        if (typing === false && $("#msg").is(":focus")) {
            typing = true;
            socket.emit("typing", true);
        } else {
            clearTimeout(timeout);
            timeout = setTimeout(timeoutFunction, 1000);
        }
    }
});

//Notifica al cliente cuando soporte finaliza
socket.on("disconnect", function(){
    $("#msgs").append("<li><strong><span class='chat-status'>El chat ha finalizado</span></strong></li>");
    $("#msg").attr("disabled", "disabled");
    $("#send").attr("disabled", "disabled");
});

socket.on("notifyNewMessage", function(room) {
    $("#conversation").animate({ scrollTop: $('#conversation')[0].scrollHeight}, 1000);
    $("li[id='"+room+"']").find(".notification-counter").remove();
    $(notificationCounter).insertAfter($("li[id='"+room+"']").find("span"));
});

$("#chatForm").submit(function(e){
    e.preventDefault();
    var msg = $("#msg").val();
    socket.emit("send", timeFormat(new Date().getTime()), msg, 0);
    $("#msg").val("");
});