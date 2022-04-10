<?php
session_start();
## verification de tous les champs
    if (isset($_POST['username']) && isset($_POST['password'])) {
        #inclusion de la base de donnees

        include '../db_conn.php';

        # ici on met les champs saisis dans les variables
        $username = $_POST['username'];
        $password = $_POST['password'];
        if (empty($username)) {
            #message d'erreur
            $em = "nom d'utilisateur est obligatoire";
            header("Location: ../../index.php?error=$em&$data");
            exit;
        }elseif (empty($password)) {
            #message d'erreur
            $em = "mot de passe est obligatoire";
            header("Location: ../../index.php?error=$em&$data");
            exit;
        }else {
            $sql = "SELECT * FROM users WHERE username = ?";
            $stm = $conn-> prepare($sql);
            $stm->execute([$username]);

            if ($stm ->rowCount() === 1){
                $user = $stm->fetch();

                if ($user['username'] === $username) {
                    if (password_verify($password,$user['password'])) {
                        # Creation de la session
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['name'] = $user['name'];
                        $_SESSION['user_id'] = $user['user_id'];
                        header("Location: ../../home.php");
                    }elseif ($user['username'] === $username && $user['password']) {
                        $em = "nom utilisateur ou mot de passe incorrect";
                        header("Location: ../../index.php?error=$em&$data");
                        exit;
                    }else {
                        $em = "nom utilisateur ou mot de passe incorrect";
                        header("Location: ../../index.php?error=$em&$data");
                        exit;
                    }
                }else {
                    #message d'erreur
                    $em = "nom utilisateur ou mot de passe est obligatoire";
                    header("Location: ../../index.php?error=$em&$data");
                    exit;
                }
            }
        }

    }else {
        header("Location: ../../index.php");
        exit;
    }
?>