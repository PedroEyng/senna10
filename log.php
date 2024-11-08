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
$stmt = $conexao->prepare("SELECT * FROM user WHERE email = ? AND senha = ?"); // Inclui 'nome'
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
   $row = $result->fetch_assoc();
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

if (!empty($_GET['search'])) {
    $data = $_GET['search'];
    $sql = "SELECT o.*, p.nome AS produto_nome 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            WHERE o.order_id LIKE '%$data%' OR p.nome LIKE '%$data%' 
            ORDER BY o.order_id DESC";
} else {
    $sql = "SELECT o.*, p.nome AS produto_nome 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            ORDER BY o.order_id DESC";
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
    <link rel="stylesheet" href="assets/css/style-admin.css"> <link rel="stylesheet" href="assets/css/orders.css">
    <title>SISTEMA | GN - Orders</title>
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
    echo "<h1>Bem vindo <u>$usernome</u></h1>"; // Agora deve funcionar sem erro
    ?>
    <br>
    <div class="box-search">
        <input type="search" class="form-control w-25" placeholder="Pesquisar" id="pesquisar">
        <button onclick="searchData()" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search"
                viewBox="0 0 16 16">
                <path
                    d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
            </svg>
        </button>
    </div>
    <div class="m-5">
        <table class="table text-white table-bg">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Produto</th>
                    <th scope="col">Quantidade</th>
                    <th scope="col">Preço Total</th>
                    <th scope="col">Data</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
    <?php
    while ($order_data = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $order_data['order_id'] . "</td>";
        echo "<td>" . $order_data['produto_nome'] . "</td>"; // Esta linha já foi ajustada
        echo "<td>" . $order_data['quantidade'] . "</td>";
        echo "<td>R$ " . $order_data['total'] . "</td>";
        echo "<td>" . $order_data['data_order'] . "</td>";
        echo "<td>" . $order_data['status'] . "</td>";
        echo "</tr>";
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
