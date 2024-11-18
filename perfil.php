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
<html lang="pt-br">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style-perfil.css">
  <title>SENNA</title>
  <link rel="shortcut icon" href="assets/images/icones/icone.png" type="image/x-icon">
  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">


  <!-- Adicional do CSS Files -->
  <link rel="stylesheet" href="assets/css/fontawesome.css">
  <link rel="stylesheet" href="assets/css/main.css">
  <link rel="stylesheet" href="assets/css/owl.css">
  <!--
começou a brincadeira
-->
</head>

<body>
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img class="logo" src="assets/images/icones/header-logo.png" alt="">
      </a>

      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
        aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item active">
            <a class="nav-link" href="index.html">Home
              <span class="sr-only">SENNA</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="products.php">Produtos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="about.html">Sobre nós</a>
          </li>
          <li class="nav-item">
          <a class="nav-link" href="mailto:pedro.senna.fernandes@escola.pr.gov.br">Contato</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="sair.php">Logout</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  </header>

  <main class="home">
    <div class="painel">
      <form class="perfil">
        <div class="info">
          <h1 class="bem-vindo">Seja Bem Vindo(a)</h1>
          <h2 class='nome'>
            <?php echo htmlspecialchars($nomeUsuario); ?>
          </h2>
          <ul class="nav nav-pills">
            <li class="nav-item">
              <a class="btn btn-outline-light" href="edit.php?user_id=<?= htmlspecialchars($user_Id) ?>"
                title="Editar Perfil">
                Editar perfil
              </a>
            </li>

          </ul>
      </form>
    </div>
  </main>

  <footer class="roda-pe">
    <!--substituir-->
  </footer>
</body>

</html>