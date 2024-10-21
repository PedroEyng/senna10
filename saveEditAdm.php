<?php
// isset -> serve para saber se uma variável está definida
include_once('config.php');

if (isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $user_nome = $_POST['user_nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    // Verifica se a checkbox admin foi marcada
    $admin = isset($_POST['admin']) ? 1 : 0;

    // Prepara a query de atualização
    $sqlUpdate = "UPDATE user 
                  SET user_nome='$user_nome', senha='$senha', email='$email', admin='$admin' 
                  WHERE user_id=$user_id";
    
    // Executa a query
    $result = $conexao->query($sqlUpdate);

    // Verifica se a operação foi bem-sucedida
    if ($result) {
        // Redireciona para a página de usuários com sucesso
        header('Location: usuarios.php');
        exit;
    } else {
        // Mensagem de erro em caso de falha
        echo "Erro: " . $conexao->error;
    }
} else {
    // Redireciona se a variável de atualização não estiver definida
    header('Location: login.html');
    exit;
}
?>
