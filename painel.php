<?php
session_start();
include_once('config.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: login.html');
    exit;
}

$logado = $_SESSION['email'];

// Buscar o nome do usuário do banco de dados
$sql = "SELECT * FROM user WHERE email = ? LIMIT 1";
$stmtUser = $conexao->prepare($sql);
$stmtUser->bind_param("s", $logado);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows > 0) {
    $row = $resultUser->fetch_assoc();
    $user_Id = $row['user_id']; // ID do usuário
    $nomeUsuario = htmlspecialchars($row['user_nome']);
}
?>


<!DOCTYPE html>
<html lang="PT-BR
">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SENNA | PAINEL</title>
    <link rel="stylesheet" href="assets/css/style-painel.css">
    <link rel="shortcut icon" href="assets/images/icones/icone.png" type="image/x-icon">
</head>
<body>
    <h1>Bem Vindo(a)</h1>
    <h2 class='nome'>
                        <p><?php echo htmlspecialchars($nomeUsuario); ?></p>
                    </h2>
    <div class="box">
        <a href="estoque.php">Adicionar produtos</a>
        <br>
        <a href="usuarios.php">lista de usuários</a>
        <br>
        <a href="logs.php">Logs de pagamento</a>
        <br>
        <a href="sair.php">Logout</a>

    </div>
</body>
</html>