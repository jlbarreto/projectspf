<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Atención al Cliente PidaFacil</title>
    <link href='http://fonts.googleapis.com/css?family=Lato:300,400'; rel='stylesheet' type='text/css'>
    <style>
        body {
            font-family: Lato;
        }
        #main-chat-screen {
            background-color: #fff;
        }
        .chat-header {
            background-color: #E74C3C;
            color: #fff;
            font-size: 1.2em;
            padding :18px;
        }
        .chat {
            border-left: 3px;
            solid: #BDC3C7;
            float:left;
            width: 100%;
        }
        #conversation {
            font-size: 18px;
            height: 300px;
            overflow: auto;
            padding: 0 2%;
        }
        #updates {
            color: #95a5a6;
            position: relative;
            padding: 0.4em;
        }
        .send-button {
             background-color: #2ecc71;
             border: none;
             border-radius: 6px;
             color: #fff;
            font-size: 1em;
            padding: 14px 30px;
        }
        .glowing-border {
            border: 2px solid #dadada;
            border-radius: 7px;
            width: 70%;
            height: 30px;
            padding: 5px;
        }
        .glowing-border:focus {
            outline: none;
            border-color: #9ecaed;
            box-shadow: 0 0 10px #9ecaed;
        }
        ul{
            list-style-type: none;
            margin: 0;
            padding: 0;
        }
        .msg-support div {
            margin-bottom: -10px
        }
        .msg-support span[class='msg-user'] {
            color: #e74c3c
        }
        .msg-support .msg-text {
            background: #f24e4e;
            float: right
        }
        .msg-text {
            color: #fff;
            border-radius: 10px;
            background: #69d2e7;
            padding: 1em;
            width: 50%
        }
        .msg-time {
            color: #95a5a6;
            font-size: .8em
        }
        .msg-user {
            color: #3498db;
            line-height: 15px;
            margin-right: 5px
        }
        .msg-info {
            color: #95a5a6;
            margin: 15px
        }
        .notification-counter {
            border-radius: 1em;
            background-color: #e74c3c;
            float: right;
            height: 10px;
            margin-left: 10px;
            margin-top: 10px;
            width: 10px
        }
        .rooms {
            color: #404749;
            font-size: 1em;
            float: left;
            width: 25%
        }
        .final {
            font-color: #000
        }
        .interim {
            font-color: #f00
        }
        .list-group-item {
            cursor: pointer;
            border-bottom: 1px solid #ecf0f1;
            padding: 6% 10%;
        }
        .list-group-item:hover {
            background-color: #e8e8e8
        }
        .list-group-item p {
            color: #95a5a6;
            line-height: 0
        }
        .list-group-item span {
            font-size: 1.2em;
            font-weight: bold
        }
        .main {
            text-align: center
        }
        .msg {
            margin: 20px 10px 30px 10px;
        }
        .msg div {
            margin-bottom: -10px
        }
        .msg-support {
            display: inline-block;
            margin: 10px 0 10px 0;
            text-align: right;
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="main-chat-screen">
        <div class="chat-header">
            <span id="current-chat">Atención al Cliente PidaFacil</span>
        </div>
        <div class="chat">
            <div id="conversation">
                <ul id="msgs" class="list-unstyled">
                </ul>
            </div>
            <form id="chatForm">
                <div>
                    <ul id="updates"></ul>
                </div>
                <input type="text" placeholder="Escribe aquí" id="msg" class="glowing-border" autocomplete="off" style="font-size: 1.2em;">
                <input type="submit" class="send-button" name="send" id="send" value="Enviar">
            </form>
        </div>
    </div>
    <input type="hidden" id="name" value="{{ Auth::user()->name.' '. Auth::user()->last_name }}"/>
    <input type="hidden" id="mail" value="{{ Auth::user()->email }}"/>
    <input type="hidden" id="userId" value="{{ Auth::user()->user_id }}"/>
    {{ HTML::script('js/jquery.min.js') }}
    {{ HTML::script('http://cdn.socket.io/socket.io-1.2.0.js') }}
    {{ HTML::script('js/chatURL.js')}}
    {{ HTML::script('js/chat.js')}}
</body>
</html>