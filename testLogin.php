<?php
session_start();
if (isset($_POST['submit']) && !empty($_POST['email']) && !empty($_POST['senha'])) {
    // Inclui a configuração do banco de dados
    include_once('config.php');
    
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verifica se o usuário existe no banco de dados
    $sql = "SELECT * FROM user WHERE email = '$email' AND senha = '$senha'";
    $result = $conexao->query($sql);

    if (mysqli_num_rows($result) < 1) {
        // Se não encontrar o usuário, redireciona para a página de login
        unset($_SESSION['email']);
        unset($_SESSION['senha']);
        header('Location: indexLogado.html');
        exit(); // Encerra o script após o redirecionamento
    } else {
        // Se encontrar o usuário, obtém os dados
        $user_data = mysqli_fetch_assoc($result);

        // Armazena as informações do usuário na sessão
        $_SESSION['email'] = $email;
        $_SESSION['senha'] = $senha;

        // Verifica se o usuário é admin
        if ($user_data['admin'] == 1) {
            header('Location: painel.php'); // Redireciona para a página de admin
            exit(); // Encerra o script após o redirecionamento
        } else {
            header('Location: perfil.php'); // Redireciona para a página de usuário comum
            exit(); // Encerra o script após o redirecionamento
        }
    }
}