<?php
try {
    $conn = new PDO("mysql:host=localhost;dbname=chat_app_db","root","");
} catch (PDOException $e) {
    echo 'Erreur : '.$e->getMessage();
}
?>
