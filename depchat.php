<?php
$servername = "localhost";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!doctype html>
<html lang="en-us">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>Connect</title>

    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script src="http://js.pusher.com/3.0/pusher.min.js"></script>
    <script src="css/bootstrap.min.js"></script>
    <style type="text/css">
        .content > div {
            border: 1px inset black;
        }
        body ul>li {
            list-style-type: none;
        }
        .chat-header {
            margin: 0px;
            background-color: rgba(0, 0, 0, 0.8);
            opacity:0.8;
            color:white;
            height: 2em;
            padding-top: 0.4em;
        }
        #chat-msgs {
            border-right:2px inset #e3e3e3;
            height: 35em;
            overflow: auto;
        }
        #online-users {
            display: inline;
        }
        #online-users-list {
            padding-left: 10px;
            margin-right: 10px;
        }
        #chat-msgs > li {
            margin-top: 1em;
            margin-left: 5px;
            display: block;
        }
        #online-users-list > li:before {
            content: "\25CF";
            font-weight: 900;
            margin-right: 10px;
            font-weight: bold;
            color: #38DF38;
            font-size: large;
        }
        #online-users-list:hover {
            border: 1px solid #38DF38;
            border-radius: 10px;
            box-shadow: 2px 2px 1px #38DF38;
        }
        .chat-input {
            display: inline;
            width:84%;
            resize: none;
        }
        .chat-send-btn {
            display: inline;
            width:16%;
            float: right;
        }
        .online-user {
            height: 2em;
            vertical-align: middle;
            line-height: 2em;
            cursor: pointer;
            height: 2em;
        }
        .chat-user {
            vertical-align: middle;
            background-color: white;
            padding: 5px;
            display: inline-flex;
            margin-bottom: 1.3em;
        }
        .chat-msg {
            vertical-align: middle;
        }
        .chatmessage {
            background-color: #86BB71;
            color: white;
            padding: 18px 20px;
            line-height: 26px;
            font-size: 16px;
            border-radius: 7px;
            margin-bottom: 30px;
            width: 90%;
            position: relative;
        }
        .chatmessage:before {
            border: solid transparent;
            content: " ";
            height: 0;
            width: 0;
            position: absolute;
            pointer-events: none;
            border-bottom-color: #86BB71;
            border-width: 10px;
            margin-left: -10px;
            margin-top: -33px;
        }
        .my-chat-user {
            vertical-align: middle;
            background-color: white;
            padding: 5px;
            display: inline-flex;
            margin-bottom: 1.3em;
        }
        .my-chat-msg > .chatmessage {
            background-color: #94C2ED !important;
        }
        .my-chat-msg > .chatmessage:before {
            border-bottom-color: #94C2ED !important;
        }
        .my-chat-msg {
            vertical-align: middle;
        }
        .pvt-chatbox {
            height: 15em;
        }
    </style>
</head>
<body class="container-fluid">
    <div class="row content">
        <div class="col-md-10" style="padding:0px;">
            <h3 class="chat-header">Chat Widget</h3>
            <div class="row">
                <ul class="col-md-10" id="chat-msgs">
                </ul>
                <div class="col-md-2" id="online-users">
                    <div style="text-align: center;">Count: <span id="members-count">0</span></div>
                    <div>
                        Users
                    </div>
                    <ul id="online-users-list"></ul>
                </div>
            </div>
            <div>
                <textarea class="form-control chat-input" rows="3" placeholder="Type Chat Message Here"></textarea>
                <button class="btn btn-default chat-send-btn" onclick="sendMsg()">Send</button>
            </div>
        </div>
        <!-- <div class="col-md-2" id="private-chats" style="padding: 0.45em;"></div> -->
    </div>
</body>
<script>

<?php
    $sql = "SELECT regdno, username, message FROM cc.conversation ORDER BY TIMESTAMP DESC";
    $result = $conn->query($sql);
    $chatmsgs = "var chatmsgs = [";
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $chatmsgs .= "{userId: '" . $row["regdno"]. "', username: '" . $row["username"]. "', message: '" . preg_replace("/\r\n|\r|\n/", "", $row["message"]). "'},";
        }
        $chatmsgs = substr($chatmsgs, 0, -1);
    }
    $chatmsgs .= "]";
    echo $chatmsgs;
?>

    // Chat ID's
    var userId = <?php echo "'".(isset($_COOKIE["rid"]) ? $_COOKIE["rid"] : "")."'"; ?>;
    var username = <?php echo "'".(isset($_COOKIE["rid"]) ? $_COOKIE["username"] : "")."'"; ?>;
    var groupId = <?php echo "'".(isset($_COOKIE["rid"]) ? $_COOKIE["did"] : "")."'"; ?>;
    var app_key = "e721d40bbc86918310ab";
    var presenceChannelName = "presence-" + groupId;

    var pusher = new Pusher(app_key, { authEndpoint: 'chat.php' });
    var presenceChannel = pusher.subscribe(presenceChannelName);

    presenceChannel.bind('pusher:subscription_succeeded', function(members) {
      update_member_count(members.count);

      members.each(function(member) {
        add_member(member);
      });
    })

     presenceChannel.bind('public_message', function(message) {
      addMessage(message);
    })

    presenceChannel.bind('pusher:member_added', function(member) {
      add_member(member);
    });

    presenceChannel.bind('pusher:member_removed', function(member) {
      remove_member(member.id);
    });

    function update_member_count(count) {
        $("#members-count").text(count);
    }

    function add_member(member) {
        if(member.id != userId) {
            $("#online-users-list").append("<li class='online-user' id='"
                                                + member.id
                                                + "' onclick='return;initChat(event)'>"
                                                + member.info.username
                                                + "</li>");
        }
        else {
            $("#online-users-list").append("<li class='online-user' id='"
                                                + member.id
                                                + "' onclick='return;initChat(event)'>"
                                                + member.info.username
                                                + "</li>");
        }
    }

    function remove_member(id) {
        $("#" + id).remove();
    }

    function initChat(event) {
        var name = $(event.target).text();
        var id = event.target.id;
        $.ajax({
            url: 'privateChat.php',
            type: 'POST',
            data: {channel_name: id, event_name: 'private_chat'},
            success: function(id) {
                $("#private-chats iframe").hide();
                $("#private-chats").prepend("<div id='chat-windows'><div class='chat-user' onclick='openchatbox(event)'>" + name + "</div><iframe class='pvt-chatbox' src='pvtChatWindow.php?channel=" + id + "'></iframe></div>");
            }
        });
    }

    function openchatbox(event) {
        $(event.target).siblings("iframe").slideToggle();
    }

    function initAddMessage(message) {
        if(message.userId != userId) {
            $("#chat-msgs").prepend("<li class='chat-msg'><span class='chat-user'>"
                                       + message.username
                                       + "</span> <br /><span class='chatmessage'>"
                                       + message.message
                                       + "</span></li>");
        }
        else {
            $("#chat-msgs").prepend("<li class='my-chat-msg'><span class='my-chat-user'>me</span> <br /><span class='chatmessage'>"
                                       + message.message
                                       + "</span></li>");
        }
        scrollToLatest()
    }

    function addMessage(message) {
        if(message.userId != userId) {
            $("#chat-msgs").append("<li class='chat-msg'><span class='chat-user'>"
                                       + message.username
                                       + "</span> <br /><span class='chatmessage'>"
                                       + message.message
                                       + "</span></li>");
        }
        else {
            $("#chat-msgs").append("<li class='my-chat-msg'><span class='my-chat-user'>me</span> <br /><span class='chatmessage''>"
                                       + message.message
                                       + "</span></li>");
        }
        scrollToLatest();
    }

    function sendMsg() {
        $.ajax({
            url: 'publicChat.php',
            type: 'POST',
            data: {channel_name: presenceChannelName, event_name: 'public_message', message: $(".chat-input").val(), userId: userId, username: username},
            success: function() {
                $(".chat-input").val("");
            }
        });
    }

    function scrollToLatest() {
        var elem = document.getElementById('chat-msgs');
        elem.scrollTop = elem.scrollHeight;
    }

    for(var i = 0; i < chatmsgs.length; i++)
        initAddMessage(chatmsgs[i]);

</script>
</html>