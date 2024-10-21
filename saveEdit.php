<?php
    // isset -> serve para saber se uma variável está definida
    include_once('config.php');
    if(isset($_POST['update']))
    {
        $user_id = $_POST['user_id'];
        $user_nome = $_POST['user_nome'];
        $email = $_POST['email'];
        $senha = $_POST['senha'];
        
        $sqlInsert = "UPDATE user 
        SET user_nome='$user_nome',senha='$senha',email='$email'
        WHERE user_id = $user_id";
        $result = $conexao->query($sqlInsert);
        print_r($result);
    }
    header('Location: login.html');

?>