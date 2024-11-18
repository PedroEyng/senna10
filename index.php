<?php
// Inclua o arquivo de conexão
include('config.php');

 



$select_products = mysqli_query($conexao, "
    SELECT p.*, 
           i.caminho_imagem, 
           GROUP_CONCAT(DISTINCT t.tamanho SEPARATOR ', ') AS tamanhos
    FROM produtos p
    LEFT JOIN imagem_produtos i ON p.produto_id = i.produto_id
    LEFT JOIN tamanho_produto tp ON p.produto_id = tp.produto_id
    LEFT JOIN tamanhos t ON tp.tamanho_id = t.tamanho_id
    GROUP BY p.produto_id
");

?>


<!DOCTYPE html>
<html lang="pt-br">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
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

  <!-- Pre Header -->
  <div id="pre-header">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <span>Nós somos a Senna</span>
        </div>
      </div>
    </div>
  </div>

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
            <a class="nav-link" href="productsShow.php">Produtos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.html">Sobre nós</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.html">Contato</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="login.html">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="formulario.php">cadastrar</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>


  <!-- Banner? -->
  <div class="banner">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="caption">
            <h2>SENNA</h2>
            <div class="line-dec"></div>
            <p>SENNA SENNA SENNA <strong>Moda e luxo</strong> Tudo pela estética
              <br><br>Sé loko <a rel="nofollow" href="https://www.chess.com/member/rochedo77">Quem é Pedro Senna</a>
              Querem estilo e moda? <a rel="nofollow"
                href="https://www.google.com.br/maps/@-25.3631054,-51.4686936,3a,75y,246.24h,88.63t/data=!3m7!1e1!3m5!1sbmYjXiLNvFcgYGsBtsSi7g!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D1.37133628546313%26panoid%3DbmYjXiLNvFcgYGsBtsSi7g%26yaw%3D246.2423669312226!7i16384!8i8192?coh=205410&entry=ttu">Localização</a>.
            </p>
            <div class="main-button">
              <a href="#">Veja mais!</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  <!-- banner?/ -->
  <div class="featured-items">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="section-heading">
          <h1>Produtos</h1>
      </div>
      <div class="owl-carousel owl-theme">
        <?php
        while ($row = mysqli_fetch_assoc($select_products)) {
            $imagem = $row['caminho_imagem'] ?? 'assets/images/default.png'; // Imagem padrão
            echo "<div class='featured-item'>";
            echo "<img src='" . htmlspecialchars($imagem, ENT_QUOTES) . "' style='width:200px;' alt='Produto'>";
            echo "<h4>" . htmlspecialchars($row['produto_nome'], ENT_QUOTES) . "</h4>";
            echo "<h6>R$" . number_format($row['preco'], 2, ',', '.') . "</h6>";
            echo "<p> Tamanho:";
            echo "<p>" . htmlspecialchars($row['tamanhos'] ?: 'N/A', ENT_QUOTES) . "</p>";
            echo "<p>" . htmlspecialchars($row['descricao'], ENT_QUOTES) . "</p>";
            echo "<p>Modelo: " . htmlspecialchars($row['modelo'], ENT_QUOTES) . "</p>";
            echo "<a href='login.html' class='option-btn'>Logar para visualizar mais</a>";
            echo "</div>";
        }
        ?>
      </div>
    </div>
  </div>
    </div>
  </div>

  <!-- Featred Ends Here -->


  <!-- Subscribe Form Starts Here -->
  <div class="subscribe-form">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="section-heading">
            <div class="line-dec"></div>
            <h1>SENNA</h1>
          </div>
        </div>
        <div class="col-md-8 offset-md-2">
          <div class="main-content">
            <p>Descubra a essência da elegância intemporal com nossa marca de roupas femininas de grife, onde cada peça
              é um símbolo de sofisticação e exclusividade, desenhada para mulheres que não apenas seguem tendências,
              mas as definem.</p>
            <div class="container">
              <form id="subscribe" action="" method="get">
                <div class="row">
                  <div class="col-md-7">
                  </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Subscribe Form Ends Here -->



  <!-- Footer Starts Here -->
  <div class="footer">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="logo">
            <img src="assets/images/icones/header-logo.png" alt="">
          </div>
        </div>
        <div class="col-md-12">
          <div class="footer-menu">
            <ul>
              <li><a href="#">Home</a></li>
              <li><a href="#">Suporte</a></li>
              <li><a href="#">Politica Prividade</a></li>
            </ul>
          </div>
        </div>
        <div class="col-md-12">
          <div class="social-icons">
            <ul>
              <li><a href="#"><i class="fa fa-facebook"></i></a></li>
              <li><a href="#"><i class="fa fa-twitter"></i></a></li>
              <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
              <li><a href="#"><i class="fa fa-rss"></i></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Footer Ends Here -->


  <!-- Sub Footer Starts Here -->
  <div class="sub-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="copyright-text">
            <p>Senna &copy; 2024 tudo nosso.

              Nos encontre <a rel="nofollow"
                href="https://www.google.com.br/maps/@-25.3631054,-51.4686936,3a,48.9y,239.72h,88.94t/data=!3m7!1e1!3m5!1sbmYjXiLNvFcgYGsBtsSi7g!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D1.0629669091132712%26panoid%3DbmYjXiLNvFcgYGsBtsSi7g%26yaw%3D239.7179805764821!7i16384!8i8192?coh=205410&entry=ttu">Localização</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Sub Footer Ends Here -->


  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>


  <!-- Additional Scripts -->
  <script src="assets/js/custom.js"></script>
  <script src="assets/js/owl.js"></script>


  <script language="text/Javascript">
    cleared[0] = cleared[1] = cleared[2] = 0;
    function clearField(t) {
      if (!cleared[t.id]) {
        cleared[t.id] = 1;
        t.value = '';
        t.style.color = '#fff';
      }
    }
  </script>


</body>

</html>