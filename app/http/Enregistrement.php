<?php
## verification de tous les champs
    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['name'])) {
        #inclusion de la base de donnees

        include '../db_conn.php';

        # ici on met les champs saisis dans les variables
        $name = $_POST['name'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        $data = 'name='.$name.'&username='.$username;

        if (empty($name)) {
            #message d'erreur
            $em = "Nom est obligatoire";
            header("Location: ../../sing-up.php?error=$em&$data");
            exit;
        }elseif (empty($username)) {
            #message d'erreur
            $em = "nom d'utilisateur est obligatoire";
            header("Location: ../../sing-up.php?error=$em&$data");
            exit;
        }elseif (empty($password)) {
            #message d'erreur
            $em = "mot de passe est obligatoire";
            header("Location: ../../sing-up.php?error=$em&$data");
            exit;
        }else {
            $sql = "SELECT username FROM users WHERE username = ?";
            $stm = $conn-> prepare($sql);
            $stm->execute([$username]);

            if ($stm ->rowCount() > 0) {
                $em = "l'utilisateur ($username) existe";
                header("Location: ../../sing-up.php?error=$em&$data");
                exit;
            }else {
                #aploader l'image
                if (isset($_FILES['pp'])) {
                    $img_name = $_FILES['pp']['name'];
                    $tmp_name = $_FILES['pp']['tmp_name'];
                    $error = $_FILES['pp']['error'];

                    if ($error === 0) {
                        $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                        $img_ex_lc = strtolower($img_ex);

                        $allow_exs = array("jpg","jpeg","png");
                        if (in_array($img_ex_lc,$allow_exs)) {
                            $new_img_name = $username.'.'.$img_ex_lc;
                            $img_upload_file = '../../uploads/'.$new_img_name ;

                            move_uploaded_file($tmp_name,$img_upload_file);
                        } else {
                            $em = "ce type de fichier n'est pas pris en charge ";
                            header("Location: ../../sing-up.php?error=$em&$data");
                            exit;
                        }
                        
                    }
                }
                #hashage du mot de passe
                $password = password_hash($password,PASSWORD_DEFAULT);

                if (isset($new_img_name)) {
                    $sql = "INSERT INTO users(name,username,password,p_p) 
                            VALUES (?,?,?,?)";
                    $stmt = $conn->prepare($sql);
                    $stmt ->execute([$name,$username,$password,$new_img_name]);
                }else{
                    $sql = "INSERT INTO users(name,username,password) 
                            VALUES (?,?,?)";
                    $stmt = $conn->prepare($sql);
                    $stmt ->execute([$name,$username,$password]);
                }
                #message de reussite
                $sm = "compte creer avec succee !";
                #redirection sur la page de connexion
                header("Location: ../../index.php?success=$sm");
                exit;
            }
        }
    }else {
        header("Location: ../../sing-up.php");
        exit;
    }