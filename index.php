<?php
session_start();
if (!isset($_SESSION['username'])) {
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="bootstrap-5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="favicon.ico">
    <title>chat ESIS connexion</title>
</head>
<body class="d-flex
            justify-content-center align-items-center vh-100">
    <div class="w-400 p-5 shadow rounded">
        <form action="app/http/authentification.php" method="post">
            <div class='d-flex justify-content-center align-items-center flex-column'> 
                <img src="image/avatar.png" class="w-25 align-center">
                <h3 class="h3 mb-3 fw-normal">Connexion  </h3>
                
                <?php if(isset($_GET['error'])){;?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($_GET['error']);?>
                </div>
                <?php }?>
                <?php if(isset($_GET['success'])){;?>
                <div class="alert alert-success" role="alert">
                    <?php echo htmlspecialchars($_GET['success']);?>
                </div>
                <?php }?>
            </div>
            <div class="mb-3 form-floating">
                <input type="text" name="username" class="form-control" autocomplete="off" placeholder="Utilisateur">
                <label class="form-label">Utilisateur</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="password" name="password" class="form-control" placeholder="Mot de passe">
                <label class="form-label">Mot de passe</label>
            </div>
            <button type="submit" class="btn-lg btn-primary">Connecter</button>
            <a href="sing-up.php">S'enregistrer</a>
        </form><br>
        <p class="mt-5 mb-3 text-muted text-center">&copy; copy right 2022</p>

    </div>
</body>
</html>

<?php
}else {
    header("Location: home.php");
    exit;
}
?>