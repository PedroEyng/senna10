<?php
session_start();
include('config.php');

// Verifica se o usuário está logado
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email'], $_SESSION['senha']);
    header('Location: login.html');
    exit();
}

// Recupera informações do usuário logado
$email = $_SESSION['email'];
$user_id = $_SESSION['user_id'] ?? null;

// Obtém o `user_id` caso não esteja na sessão
if (!$user_id) {
    $sql = "SELECT user_id FROM user WHERE email = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $user_id = $user['user_id'];
    } else {
        echo "Erro: Usuário não encontrado. Faça login novamente.";
        header('Location: login.html');
        exit();
    }
}

// Adiciona produto ao carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $produto_id = intval($_POST['produto_id']);
    $produto_nome = htmlspecialchars($_POST['produto_nome'], ENT_QUOTES);
    $preco = floatval($_POST['preco']);
    $quantidade = 1;

    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    // Verifica se o produto já existe no carrinho
    $produtoEncontrado = false;
    foreach ($_SESSION['carrinho'] as &$produto) {
        if ($produto['produto_id'] == $produto_id) {
            // Se o produto já estiver no carrinho, aumenta a quantidade
            $produto['quantidade']++;
            $produtoEncontrado = true;
            break;
        }
    }

    if (!$produtoEncontrado) {
        // Se o produto não estiver no carrinho, adiciona um novo item
        $_SESSION['carrinho'][] = [
            'produto_id' => $produto_id,
            'produto_nome' => $produto_nome,
            'preco' => $preco,
            'quantidade' => $quantidade
        ];
    }

    // Verifica se o produto já existe no banco de dados para o usuário antes de inserir
    $stmt = $conexao->prepare("SELECT * FROM carrinho WHERE user_id = ? AND produto_id = ?");
    $stmt->bind_param("ii", $user_id, $produto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Se o produto já existe no carrinho do banco, atualiza a quantidade
        $stmt = $conexao->prepare("UPDATE carrinho SET quantidade = quantidade + 1 WHERE user_id = ? AND produto_id = ?");
        $stmt->bind_param("ii", $user_id, $produto_id);
    } else {
        // Se o produto não existe no carrinho do banco, insere um novo
        $stmt = $conexao->prepare("INSERT INTO carrinho (user_id, produto_id, preco, quantidade) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iidi", $user_id, $produto_id, $preco, $quantidade);
    }

    if (!$stmt->execute()) {
        $_SESSION['produto_erro'] = 'Erro ao adicionar o produto no carrinho!';
    } else {
        $_SESSION['produto_adicionado'] = 'Produto adicionado ao carrinho com sucesso!';
    }
    header("Location: products.php");
    exit();
}

// Consulta para obter os produtos
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
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
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
                    <li class="nav-item active">
                        <a class="nav-link"  href="carrinho.php"> carrinho</a>
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
                                        alt="<?php echo htmlspecialchars($row['produto_nome'], ENT_QUOTES); ?>" class="img-fluid"
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

                            <form method="POST">
                                <input type="hidden" name="produto_id" value="<?php echo $row['produto_id']; ?>">
                                <input type="hidden" name="produto_nome"
                                    value="<?php echo htmlspecialchars($row['produto_nome'], ENT_QUOTES); ?>">
                                <input type="hidden" name="preco" value="<?php echo $row['preco']; ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-success">Adicionar ao Carrinho</button>
                            </form>
                            
                            <br>
                        </div>
                        <?php
                        $firstItem = false;
                    }
                } else {
                    echo "<p>Nenhum produto encontrado.</p>";
                }
                ?>
            </div>
            <a class="carousel-control-prev" href="#productCarousel" role="button" data-slide="prev"
                style="color: black;">
                <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: black;"></span>
                <span class="sr-only">Anterior</span>
            </a>
            <a class="carousel-control-next" href="#productCarousel" role="button" data-slide="next"
                style="color: black;">
                <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: black;"></span>
                <span class="sr-only">Próximo</span>
            </a>
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
                            LOCALIZE <a rel="nofollow"
                                href="https://www.google.com/maps/@-25.3631054,-51.4686936,3a,75y,259.6h,78.6t/data=!3m7!1e1!3m5!1sbmYjXiLNvFcgYGsBtsSi7g!2e0!6shttps:%2F%2Fstreetviewpixels-pa.googleapis.com%2Fv1%2Fthumbnail%3Fcb_client%3Dmaps_sv.tactile%26w%3D900%26h%3D600%26pitch%3D11.400780367291091%26panoid%3DbmYjXiLNvFcgYGsBtsSi7g%26yaw%3D259.60087638106074!7i16384!8i8192?coh=205410&entry=ttu">Senna</a>
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
    <script src="assets/js/isotope.js"></script>

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