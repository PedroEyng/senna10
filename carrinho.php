<?php
session_start();
if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    echo "Seu carrinho está vazio!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Carrinho de Compras</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Modelo</th>
                    <th>Tamanho</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $valor_total = 0;
                foreach ($_SESSION['carrinho'] as $item) {
                    $valor_total += $item['total'];
                    ?>
                    <tr>
                        <td><?php echo $item['nome']; ?></td>
                        <td><?php echo $item['modelo']; ?></td>
                        <td><?php echo $item['tamanho']; ?></td>
                        <td><?php echo $item['quantidade']; ?></td>
                        <td>R$ <?php echo number_format($item['preco'], 2, ',', '.'); ?></td>
                        <td>R$ <?php echo number_format($item['total'], 2, ',', '.'); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <h3>Valor Total: R$ <?php echo number_format($valor_total, 2, ',', '.'); ?></h3>

        <a href="checkout.php" class="btn btn-success">Ir para Checkout</a>
    </div>
</body>
</html>
