<?php
session_start();
include_once('config.php');

// Obtendo o ID do produto selecionado via GET
if (isset($_GET['produto_id'])) {
    $produto_id = $_GET['produto_id'];

    // Consulta para obter os detalhes do produto
    $sql = "SELECT * FROM produtos WHERE produto_id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();
    } else {
        echo "Produto não encontrado!";
        exit;
    }

    // Consulta para obter os tamanhos disponíveis para o produto
    $sql_tamanhos = "SELECT * FROM detalhe WHERE produto_id = ?";
    $stmt_tamanhos = $conexao->prepare($sql_tamanhos);
    $stmt_tamanhos->bind_param("i", $produto_id);
    $stmt_tamanhos->execute();
    $result_tamanhos = $stmt_tamanhos->get_result();

    // Consulta para obter os modelos disponíveis para o produto
    $sql_modelos = "SELECT * FROM modelos WHERE produto_id = ?";
    $stmt_modelos = $conexao->prepare($sql_modelos);
    $stmt_modelos->bind_param("i", $produto_id);
    $stmt_modelos->execute();
    $result_modelos = $stmt_modelos->get_result();

    // Consulta para obter as cores disponíveis para o produto
    $sql_cores = "SELECT * FROM cores WHERE produto_id = ?";
    $stmt_cores = $conexao->prepare($sql_cores);
    $stmt_cores->bind_param("i", $produto_id);
    $stmt_cores->execute();
    $result_cores = $stmt_cores->get_result();

} else {
    echo "Nenhum produto selecionado!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Produto</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1><?php echo $produto['produto_nome']; ?></h1>
        <img src="assets/images/produtos/<?php echo $produto['imagem']; ?>" alt="Imagem do Produto" class="img-fluid">
        <p>Descrição: <?php echo $produto['descricao']; ?></p>
        <p>Preço: R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>

        <!-- Selecionar Tamanho -->
        <form action="adicionar_carrinho.php" method="POST">
            <input type="hidden" name="produto_id" value="<?php echo $produto['produto_id']; ?>">

            <div class="form-group">
                <label for="tamanho">Tamanho:</label>
                <select name="tamanho" id="tamanho" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php while ($tamanho = $result_tamanhos->fetch_assoc()) { ?>
                        <option value="<?php echo $tamanho['tamanho']; ?>"><?php echo $tamanho['tamanho']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Selecionar Modelo -->
            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <select name="modelo" id="modelo" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php while ($modelo = $result_modelos->fetch_assoc()) { ?>
                        <option value="<?php echo $modelo['modelo']; ?>"><?php echo $modelo['modelo']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <!-- Selecionar Cor -->
            <div class="form-group">
                <label for="cor">Cor:</label>
                <select name="cor" id="cor" class="form-control" required>
                    <option value="">Selecione</option>
                    <?php while ($cor = $result_cores->fetch_assoc()) { ?>
                        <option value="<?php echo $cor['cor']; ?>"><?php echo $cor['cor']; ?></option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Adicionar ao Carrinho</button>
        </form>
    </div>
</body>
</html>
