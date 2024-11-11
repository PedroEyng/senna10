<?php
include_once('config.php'); 
session_start();

// Verifica se o usuário está logado, caso contrário redireciona para o login
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
   unset($_SESSION['email']);
   unset($_SESSION['senha']);
   header('Location: login.html');
   exit;
}

$email = $_SESSION['email'];
$senha = $_SESSION['senha'];

// Usa prepared statement para prevenir SQL Injection
$stmt = $conexao->prepare("SELECT * FROM user WHERE email = ? AND senha = ?");
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result_user = $stmt->get_result();

if ($result_user->num_rows > 0) {
   $row = $result_user->fetch_assoc();
   $usernome = $row['user_nome']; // Define o nome do usuário

   // Verifica se o usuário é administrador
   if ($row['admin'] == 1) {
      if (basename($_SERVER['PHP_SELF']) !== 'log.php') { // Evita redirecionamento em loop
         header('Location: log.php');
         exit;
      }
   } else {
      if (basename($_SERVER['PHP_SELF']) !== 'perfil.php') { // Evita redirecionamento em loop
         header('Location: perfil.php');
         exit;
      }
   }
} else {
   header('Location: login.html');
   exit;
}

// Consulta SQL para exibir os pedidos com o nome do usuário
if (!empty($_GET['search'])) {
    $data = $_GET['search'];
    $sql = "SELECT o.*, p.produto_nome AS produto_nome, u.user_nome AS user_nome 
            FROM orders o 
            JOIN produtos p ON o.produto_id = p.produto_id 
            JOIN user u ON o.user_id = u.user_id 
            WHERE o.order_id LIKE '%$data%' OR p.produto_nome LIKE '%$data%' 
            ORDER BY o.order_id DESC";
} else {
    $sql = "SELECT o.*, p.produto_nome AS produto_nome, u.user_nome AS user_nome 
            FROM orders o 
            JOIN produtos p ON o.produto_id = p.produto_id 
            JOIN user u ON o.user_id = u.user_id 
            ORDER BY o.order_id DESC";
}

$result_orders = $conexao->query($sql);

// Consulta para calcular o valor total de todos os pagamentos
$sql_total = "SELECT SUM(total) AS total_pagamentos FROM orders";
$result_total = $conexao->query($sql_total);
$total = 0;

if ($result_total && $result_total->num_rows > 0) {
    $row_total = $result_total->fetch_assoc();
    $total = $row_total['total_pagamentos'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-admin.css"> 
    <link rel="stylesheet" href="assets/css/orders.css">
    <title>SENNA | LOGS DE COMPRAS</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="assets/images/icones/header-logo.png" alt=""></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
        <div class="d-flex">
            <a href="painel.php" class="btn btn-danger me-5">Painel</a>
            <a href="estoque.php" class="btn btn-danger me-5">Lista de Produtos</a>
            <a href="logs.php" class="btn btn-danger me-5">Logs de pagamento</a>
            <a href="sair.php" class="btn btn-danger me-5">Sair</a>
        </div>
    </nav>
    <br>
    <?php
    echo "<h1 style='justify-self:center;'>Bem vindo <u>$usernome</u></h1>";
    echo "<br>";
    echo "<h4 style='justify-self:center;'>Valor total de todos os pagamentos: R$ <u>$total</u></h4>";
    ?>

    <br>
   
    <div class="m-5" style="font-size:1pc;">
        <table class="table text-white table-bg">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Usuário</th>
                    <th scope="col">Produto</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Preço Total</th>
                    <th scope="col">Data</th>
                </tr>
            </thead>
            <tbody>
    <?php
    if ($result_orders && $result_orders->num_rows > 0) {
        while ($order_data = $result_orders->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $order_data['order_id'] . "</td>";
            echo "<td>" . $order_data['user_nome'] . "</td>"; // Nome do usuário
            echo "<td>" . $order_data['produto_nome'] . "</td>";
            echo "<td>" . $order_data['quantidade'] . "</td>";
            echo "<td>R$ " . $order_data['total'] . "</td>";
            echo "<td>" . $order_data['created_at'] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='7'>Nenhum pedido encontrado.</td></tr>";
    }
    ?>
            </tbody>
        </table>
    </div>
</body>
<script>
    var search = document.getElementById('pesquisar');

    search.addEventListener("keydown", function (event) {
        if (event.key === "Enter") {
            searchData();
        }
    });

    function searchData() {
        window.location = 'orders.php?search=' + search.value;
    }
</script>
</html>
