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
    <title>chat ESIS inscription</title>
</head>
<body class="d-flex justify-content-center align-items-center vh-100">
    <div class="w-400 p-5 shadow rounded">
        <form action="app/http/Enregistrement.php" method="post" enctype="multipart/form-data">
            <div class='d-flex justify-content-center align-items-center flex-column'>
                 <img src="image/avatar.png" class="w-25">
                <h3 class="h3 mb-3 fw-normal">Inscription </h3>
            </div>
            <?php if(isset($_GET['error'])){;?>
            <div class="alert alert-danger" role="alert">
                <?php echo htmlspecialchars($_GET['error']);?>
            </div>
            <?php }
                if(isset($_GET['name'])) {
                    $name = $_GET['name'];
                }else $name ='';
                if(isset($_GET['username'])) {
                    $username = $_GET['username'];
                }else $username ='';
            ?>
            <div class="mb-3 form-floating">
                <input type="text" name="name" value="<?=$name?>" class="form-control" placeholder="Nom">
                <label class="form-label">Nom</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="text" class="form-control" name="username" value="<?=$username?>" placeholder="Nom Utilisateur">
                <label class="form-label">Nom Utilisateur</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="password" class="form-control" name="password" placeholder="Mot de passe">
                <label class="form-label">Mot de passe</label>
            </div>
            <div class="mb-3 form-floating">
                <input type="text" class="form-control" name="m_magique" placeholder="Mot magique">
                <label class="form-label">Mot magique</label>
            </div>
            <div class="mb-3">
                <label class="form-label">Photo de Profil</label>
                <input type="file" class="form-control" name="pp" placeholder="fichier">
            </div>
            <button type="submit" class="btn btn-primary">Enregistrer</button>
            <a href="index.php">Connexion</a>
        </form>
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