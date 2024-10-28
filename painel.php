<?php
session_start();
include_once('config.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['email'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: login.html');
    exit;
}

// Atribui o e-mail do usuário logado a uma variável
$logado = $_SESSION['email'];

// Busca o nome do usuário e a permissão no banco de dados
$sql = "SELECT * FROM user WHERE email = ? LIMIT 1";
$stmtUser = $conexao->prepare($sql);
$stmtUser->bind_param("s", $logado);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows > 0) {
    $row = $resultUser->fetch_assoc();
    $user_Id = $row['user_id']; // ID do usuário
    $nomeUsuario = htmlspecialchars($row['user_nome']);
    $isAdmin = $row['is_admin']; // Supondo que este campo exista
} else {
    // Se não encontrar o usuário, encerra a sessão e redireciona
    session_destroy();
    header('Location: login.html');
    exit;
}

// Verifica se o usuário é administrador
if ($isAdmin != 1) {
    // Se não for administrador, redireciona para a página inicial ou outra página
    header('Location: perfil.php'); // Altere para a página desejada
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENNA | PAINEL</title>
    <link rel="stylesheet" href="assets/css/style-painel.css">
    <link rel="shortcut icon" href="assets/images/icones/icone.png" type="image/x-icon">
</head>
<body>
    <h1>Bem-vindo(a)</h1>
    <h2 class='nome'>
        <p><?php echo $nomeUsuario; ?></p>
    </h2>
    <div class="box">
        <a href="estoque.php">Adicionar produtos</a>
        <br>
        <a href="usuarios.php">Lista de usuários</a>
        <br>
        <a href="logs.php">Logs de pagamento</a>
        <br>
        <a href="sair.php">Logout</a>
    </div>
</body>
</html>
