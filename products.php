<?php 
session_start();
include_once('config.php');

// Consulta para juntar produtos e suas imagens
$sql = "SELECT p.*, GROUP_CONCAT(i.caminho_imagem SEPARATOR ',') AS imagens_produto 
        FROM produtos p 
        LEFT JOIN imagem_produtos i ON p.produto_id = i.produto_id
        GROUP BY p.produto_id";

$result = $conexao->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700" rel="stylesheet">

    <title>Senna | Produtos</title>

    <!-- Bootstrap core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Additional CSS Files -->
    <link rel="stylesheet" href="assets/css/fontawesome.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/owl.css">
</head>

<body>
    <!-- Pre Header -->
    <div id="pre-header">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <span>A Senna é pra quem pode, não pra quem quer.</span>
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
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="indexLogado.html">Home</a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="products.php">Produtos <span class="sr-only">Atuais</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.html">Sobre nós</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contato</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sair.php">logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Items Starts Here -->
    <div class="featured-page">
        <div class="container">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="section-heading">
                        <div class="line-dec"></div>
                        <h1>Produtos Disponíveis</h1>
                    </div>
                </div>
                <div class="col-md-8 col-sm-12">
                    <div id="filters" class="button-group">
                        <button class="btn btn-primary" data-filter="*">Todos</button>
                        <button class="btn btn-primary" data-filter=".new">Novos</button>
                        <button class="btn btn-primary" data-filter=".low">Menos Preço</button>
                        <button class="btn btn-primary" data-filter=".high">Maior Preço</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="featured container no-gutter">
    <div id="productCarousel" class="carousel slide" data-ride="carousel">
    <div class="carousel-inner d-flex justify-content-center">
        <?php 
        if ($result->num_rows > 0) {
            $firstItem = true;
            while ($row = $result->fetch_assoc()) {
                $imagens = explode(',', $row['imagens_produto']); 
                $primeiraImagem = isset($imagens[0]) ? $imagens[0] : null; // Seleciona apenas a primeira imagem
        ?>
            <div class="carousel-item <?php echo $firstItem ? 'active' : ''; ?> text-center">
                <div class="product-item mx-auto" style="max-width: 450px;">
                    <?php if ($primeiraImagem) { ?>
                        <img src="<?php echo htmlspecialchars(trim($primeiraImagem), ENT_QUOTES); ?>" 
                             alt="<?php echo htmlspecialchars($row['produto_nome'], ENT_QUOTES); ?>" 
                             class="img-fluid" 
                             style="width: 450px; height: 300px; object-fit: cover;"> <!-- Definindo tamanho fixo -->
                    <?php } else { ?>
                        <p>Imagem não disponível.</p>
                    <?php } ?>
                    <div class="down-content">
                        <a href="product-details.php?produto_id=<?php echo $row['produto_id']; ?>">
                            <h4><?php echo htmlspecialchars($row['produto_nome'], ENT_QUOTES); ?></h4>
                        </a>
                        <h6>R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></h6>
                        <p><?php echo htmlspecialchars($row['descricao'], ENT_QUOTES); ?></p>
                    </div>
                </div>
            </div>
        <?php 
                $firstItem = false;
            }
        } else {
            echo "<p>Nenhum produto encontrado.</p>";
        }
        ?>
    </div>
    <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev" style="color: black;">
        <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: black;"></span>
        <span class="sr-only">Anterior</span>
    </a>
    <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next" style="color: black;">
        <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: black;"></span>
        <span class="sr-only">Próximo</span>
    </a>
</div>

</div>

    <div class="page-navigation">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <ul>
                        <li class="current-page"><a href="#">1</a></li>
                        <li><a href="#">2</a></li>
                        <li><a href="#">3</a></li>
                        <li><a href="#"><i class="fa fa-angle-right"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="subscribe-form">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-heading">
                        <div class="line-dec"></div>
                        <h1>Senna</h1>
                    </div>
                </div>
                <div class="col-md-8 offset-md-2">
                    <div class="main-content">
                        <p>Balançamos, mas não caímos</p>
                        <div class="container">
                            <form id="subscribe" action="" method="get">
                                <div class="row">
                                    <div class="col-md-7">
                                        <fieldset>
                                            <input name="email" type="text" class="form-control" id="email" 
                                            onfocus="if(this.value == 'Your Email...') { this.value = ''; }" 
                                            onBlur="if(this.value == '') { this.value = 'Your Email...';}"
                                            value="Your Email..." required="">
                                        </fieldset>
                                    </div>
                                    <div class="col-md-5">
                                        <fieldset>
                                            <button type="submit" id="form-submit" class="button">Compre Agora!</button>
                                        </fieldset>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Starts Here -->
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="logo">
                        <img src="assets/images/header-logo.png" alt="">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="footer-menu">
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Help</a></li>
                            <li><a href="#">Privacy Policy</a></li>
                            <li><a href="#">How It Works ?</a></li>
                            <li><a href="#">Contact Us</a></li>
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
                        <p>Somos Senna, eu sou Pedro Senna&copy; 2024 tudo nosso
                        LOCALIZE <a rel="nofollow" href="https://www.google.com/maps/@-25.3631054,-51.4686936,3a,75y,259.6h,78.6t/data=!3m7!1e1!3m5!1sbmYjXiLNvFcgYGsBtsSi7g!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D11.400780367291091%26panoid%3DbmYjXiLNvFcgYGsBtsSi7g%26yaw%3D259.60087638106074!7i16384!8i8192?coh=205410&entry=ttu">Senna</a></p>
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
    <script src="assets/js/isotope.js"></script>

    <script language="text/Javascript"> 
        cleared[0] = cleared[1] = cleared[2] = 0; 
        function clearField(t) {                   
            if (!cleared[t.id]) {                      
                cleared[t.id] = 1;  
                t.value='';         
                t.style.color='#fff';
            }
        }
    </script>
</body>
</html>
