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

// Buscar o nome do usuário do banco de dados e obter o user_id
$sql = "SELECT * FROM user WHERE email = ? LIMIT 1";
$stmtUser = $conexao->prepare($sql);
$stmtUser->bind_param("s", $logado);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows > 0) {
    $usuario = $resultUser->fetch_assoc();
    $_SESSION['user_id'] = $usuario['user_id']; // Armazena o ID do usuário na sessão
} else {
    // Se o usuário não for encontrado, redireciona para o login
    header('Location: login.html');
    exit;
}

// Obtendo o ID do produto selecionado via GET
if (isset($_GET['produto_id'])) {
    $produto_id = $_GET['produto_id'];

    // Consulta para obter os detalhes do produto e suas imagens
    $sql = "SELECT p.*, GROUP_CONCAT(i.caminho_imagem SEPARATOR ',') AS imagem_produtos 
            FROM produtos p 
            LEFT JOIN imagem_produtos i ON p.produto_id = i.produto_id 
            WHERE p.produto_id = ? 
            GROUP BY p.produto_id";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
        $imagens = !empty($produto['imagem_produtos']) ? explode(',', $produto['imagem_produtos']) : [];
    } else {
        echo "Produto não encontrado!";
        exit;
    }

    // Consulta para obter tamanhos disponíveis para o produto
    $sql_tamanhos = "SELECT t.tamanho 
                     FROM tamanhos t 
                     INNER JOIN tamanho_produto tp ON t.tamanho_id = tp.tamanho_id 
                     WHERE tp.produto_id = ?";
    $stmt_tamanhos = $conexao->prepare($sql_tamanhos);
    $stmt_tamanhos->bind_param("i", $produto_id);
    $stmt_tamanhos->execute();
    $result_tamanhos = $stmt_tamanhos->get_result();

} else {
    echo "Nenhum produto selecionado!";
    exit;
}

// Inicializa as variáveis
$imagens = $imagens ?? []; // Inicializa como array vazio se não estiver definido
$produto = $produto ?? []; // Inicializa como array vazio se não estiver definido

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se o usuário está autenticado
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }

    // Captura as informações do formulário
    $produto_id = $_POST['produto_id'];
    $tamanho = $_POST['tamanho'];
    $quantidade = $_POST['quantidade'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $pagamento = $_POST['pagamento'];

    // Obtém o ID do usuário da sessão
    $user_id = $_SESSION['user_id'];

    $preco = $produto['preco']; // Supondo que você já tenha recuperado o preço do produto
    $total = $quantidade * $preco;

$sql = "INSERT INTO orders (user_id, produto_id, tamanho, quantidade, total, email, endereco, created_at, pagamento) 
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), ?)";

// Aqui a string de tipos é atualizada. Supondo que 'quantidade' e 'total' sejam inteiros e double.
$stmt = $conexao->prepare($sql);
$stmt->bind_param("iisidsds", $user_id, $produto_id, $tamanho, $quantidade, $total, $email, $endereco, $pagamento);

    if ($stmt->execute()) {
        // Exibir mensagem de sucesso
        echo '<div class="alert alert-success" role="alert" style="text-align: center;">
                <h4 class="alert-heading">Compra Realizada!</h4>
                <p>A sua compra foi realizada com sucesso.</p>
                <hr>
                <p class="mb-0">Você será redirecionado para o seu perfil em breve.</p>
              </div>';
        
        // Redireciona após 3 segundos
        header("refresh:3;url=perfil.php");
    } else {
        echo "Erro ao realizar o pedido: " . $stmt->error;
    }

    $stmt->close();
    $conexao->close();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Senna | Checkout</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <script>
        function setMainImage(src) {
            document.getElementById("mainImage").src = src;
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Checkout</h1>

        <!-- Exibir imagem principal do produto -->
        <?php if (!empty($imagens)) { ?>
            <img id="mainImage" src="<?php echo htmlspecialchars($imagens[0]); ?>" alt="Imagem do Produto" class="main-image">
        <?php } ?>

        <!-- Grid de imagens para seleção -->
        <div class="grid-container">
            <?php
            foreach ($imagens as $imagem) { ?>
                <div class="grid-item">
                    <img src="<?php echo htmlspecialchars($imagem); ?>" alt="Imagem do Produto" onclick="setMainImage(this.src)">
                </div>
            <?php } ?>
        </div>

        <p>Descrição: <?php echo isset($produto['descricao']) ? htmlspecialchars($produto['descricao']) : 'Descrição não disponível.'; ?></p>
        <p>Preço: R$ <?php echo isset($produto['preco']) ? number_format($produto['preco'], 2, ',', '.') : '0,00'; ?></p>

        <!-- Selecionar Tamanho -->
        <form method="POST">
            <input type="hidden" name="produto_id" value="<?php echo htmlspecialchars($produto['produto_id']); ?>">

            <div class="form-group">
                <label for="tamanho">Tamanho:</label>
                <select name="tamanho" id="tamanho" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php while ($tamanho = $result_tamanhos->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($tamanho['tamanho']); ?>">
                            <?php echo htmlspecialchars($tamanho['tamanho']); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="form-group">
                <label for="quantidade">Quantidade:</label>
                <input type="number" name="quantidade" id="quantidade" class="form-control" value="1" min="1" required>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="endereco">Endereço:</label>
                <input type="text" name="endereco" id="endereco" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="pagamento">Método de Pagamento:</label>
                <select name="pagamento" id="pagamento" class="form-control" required>
                    <option value="pix">PIX</option>
                    <option value="cartao_credito">Cartão de Crédito</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Checkout</button>
        </form>
    </div>
</body>
</html>
