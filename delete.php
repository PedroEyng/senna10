<?php

    if(!empty($_GET['user_id']))
    {
        include_once('config.php');

        $user_id = $_GET['user_id'];

        $sqlSelect = "SELECT *  FROM user WHERE user_id=$user_id";

        $result = $conexao->query($sqlSelect);

        if($result->num_rows > 0)
        {
            $sqlDelete = "DELETE FROM user WHERE user_id=$user_id";
            $resultDelete = $conexao->query($sqlDelete);
        }
    }
    header('Location: usuarios.php');
   
?>