<?php
    session_start();
    if (isset($_SESSION['username'])) {
        include 'app/db_conn.php';
        include 'app/helpers/user.php';
        include 'app/helpers/timeAgo.php';
        include 'app/helpers/chat.php';
        include 'app/helpers/opened.php';


        if(!isset($_GET['user'])){
            header("Location: home.php");
            exit;
        }
        $chatwith = getUser($_GET['user'], $conn);

        if(empty($chatwith)){
            header("Location: home.php");
            exit;
        }

        $chats = getChats($_SESSION['user_id'],$chatwith['user_id'], $conn);

        opened($chatwith['user_id'],$conn,$chats);
?>
    <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="bootstrap-5.0.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style2.css">
        <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
        <link rel="icon" href="favicon.ico">
        <title>Chat App chat</title>
    </head>
    <body class="d-flex justify-content-center align-items-center vh-100">
            <div class="w-400 p-4  shadow rounded">
                <a href="home.php" class="fs-4 link-dark">&#8592;</a>
                <div class="d-flex  align-items-center">
                    <img src="uploads/<?=$chatwith['p_p'];?>" class="w-15 rounded-circle">
                    <h3 class="display-4 fs-sm m-2">
                        <?=$chatwith['name'];?><br>
                        <div class="d-flex  align-items-center" title="online">
                            <?php 
                                if(last_seen($chatwith['last_seen']) == "Active") {
                            ?>
                            <div class="online"></div>
                            <small class="d-block p-1">Enligne</small>
                            <?php }else {?>
                                <small class="d-block p-1">
                                    Vue
                                    <?=last_seen($chatwith['last_seen'])?>
                                </small>
                                

                            <?php } ?>
                        </div>
                    </h3>
                </div>
                <div class="shadow p-4 rounded bg-light
                            d-flex flex-column
                             mt-2 chat-box" id="chatBox">
                             <?php 
                                 if (!empty($chats)) {
                                     foreach ($chats as $chat) {
                                         if ($chat['from_id'] == $_SESSION['user_id']) {
                                             ?>
                                             <p class="rtext  align-self-end border rounded p-2 mb-1">
                                                <?=$chat['message'];?>
                                                <small class="d-block">
                                                    <?=$chat['create_at'];?>
                                                </small>
                                            </p>
                                       <?php  }else{ ?>
                                        <p class="ltext border rounded p-2 mb-1">
                                            <?=$chat['message'];?>
                                            <small class="d-block"><?=$chat['create_at'];?></small>
                                        </p>

                                      <?php }
                                     }
                   }else { ?> 
                        <div class="alert alert-info text-center">
                                <i class="fa fa-comments d-block fs-big"></i>
                                Pas de message ,demarrer la conversation 
                        </div>
                    <?php } ?>
                </div>
                <div class="input-group mb-3">
                    <textarea cols="3" class="form-control" id="message"></textarea>
                    <button class="btn btn-primary" id="sendBtn">
                        <i class="fa fa-paper-plane"></i>
                    </button>

                </div>
            </div>
        <script src="css/Jquery3.6.0 .min.js"></script>
        <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>

        <script>
        // maintient du dernier message ecrit
            var scrollDown = function() {
                let chatBox = document.getElementById('chatBox');
                chatBox.scrollTop = chatBox.scrollHeight;
            }
            scrollDown();

            $(document).ready(function(){
                $("#sendBtn").on('click', function(){
                    message = $("#message").val();

                    if(message == "") return;
                    $.post("app/ajax/insert.php",
                    {
                        message : message,
                        to_id: <?=$chatwith['user_id']?>
                    },
                    function(data, status){
                        $("#message").val("");
                        $("#chatBox").append(data);
                        scrollDown();
                    });
                });

                let lastSeenUpdate = function(){
                    $.get("app/ajax/update_last_seen.php");
                }
                lastSeenUpdate();

                setInterval(lastSeenUpdate, 1000);

                // actualiser les messages automatiquement
                let fechData = function(){
                    $.post("app/ajax/getMessage.php",
                    {
                        id_2: <?=$chatwith['user_id']?>
                    },
                    function(data, status){
                        $("#chatBox").append(data);
                        if(data != "") scrollDown();
                    });  
                }
                fechData();
                setInterval(fechData, 500);
            });
        </script>
    </body>
</html>
    <?php }else {
        header("Location: ../../index.php");
        exit;
    }