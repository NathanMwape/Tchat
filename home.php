<?php
session_start();
if (isset($_SESSION['username'])) {

    include 'app/db_conn.php';
    include 'app/helpers/user.php';
    include 'app/helpers/conversation.php';
    include 'app/helpers/timeAgo.php';
    include 'app/helpers/last_chat.php';

    $user = getUser($_SESSION['username'],$conn);
    $conversations = getConversation($user['user_id'],$conn);

    // print_r($user);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="icon" href="favicon.ico">
    <title>Chat App</title>
</head>
<body class="d-flex
            justify-content-center align-items-center vh-100">
            <div class="p-2 w-400 shadow rounded">
                <div >
                    <div class="d-flex mb-3 p-3 bg-light justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <img src="uploads/<?=$user['p_p']?>" class="w-25 rounded-circle">
                            <h3 class="fs-xs m-2"><?=$user['name']?></h3>
                        </div>
                        <a href="logout.php" class="btn btn-danger">Deconnexion</a>
                        
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" placeholder="recherche..." id="searchText" class="form-control">
                        <button class="btn  btn-primary" id="searchBtn">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <ul id="chatList" 
                    class="list-group mvh-50 overflow-auto">
                        <?php if (!empty($conversations)) { ?>
                        <?php foreach($conversations as $conversation){ ?>
                        <li class="list-group-item">
                            <a href="chat.php?user=<?=$conversation['username']?>" class="d-flex justity-content-between align-items-center p-2">
                                <div class="d-flex align-items-center">
                                    <img src="uploads/<?=$conversation['p_p']?>" class="w-10 rounded-circle">
                                    <h3 class="fs-xs m-2">
                                        <?=$conversation['name']?><br>
                                    </h3>
                                    <!-- <small>
                                        <?php
                                            // echo lastChat($_SESSION['user_id'],$conversation['user_id'], $conn );
                                        ?>
                                    </small> -->
                                </div>
                                <?php if(last_seen($conversation['last_seen']) == 'Active') { ?>   
                                    <div title="online">
                                        <div class="online"></div>
                                    </div>
                                <?php } ?>
                            </a>
                        </li>
                        <?php } ?>
                        <?php }else {?>
                            <div class="alert alert-info text-center">
                                <i class="fa fa-comments d-block fs-big"></i>
                                Pas de message ,demarrer la conversation 
                            </div>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        <script src="css/Jquery3.6.0 .min.js"></script>
        <head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </head>

        <script>
            $(document).ready(function(){

                let lastSeenUpdate = function(){
                    $.get("app/ajax/update_last_seen.php");
                }
                lastSeenUpdate();
                setInterval(lastSeenUpdate, 200);

                //rechercher
                $("#searchText").on("input", function(){
                    var searchText = $(this).val();
                    if(searchText == "") return;
                    $.post("app/ajax/search.php",
                        {
                            key: searchText
                        },
                        function(data, status){
                            $("#chatList").html(data);
                        }
                        );
                });

                //rechercher pour le bouton
                $("#searchBtn").on("click", function(){
                    var searchText = $("#searchText").val();
                    if(searchText == "") return;
                    $.post("app/ajax/search.php",
                        {
                            key: searchText
                        },
                        function(data, status){
                            $("#chatList").html(data);
                        }
                        );
                });

            });
        </script>
</body>
</html>

<?php
}else {
    header("Location: index.php");
    exit;
}
?>