<?php
session_start();
include_once('config.php');

if (!isset($_SESSION['carrinho']) || empty($_SESSION['carrinho'])) {
    echo "Seu carrinho está vazio!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario_id = $_SESSION['user_id']; // Assumindo que o usuário está logado
    $cpf = $_POST['cpf'];
    $forma_pagamento = $_POST['forma_pagamento'];
    $valor_total = $_POST['valor_total'];

    // Registrar no banco de dados
    $sql = "INSERT INTO checkout (usuario_id, cpf, forma_pagamento, valor_total) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issd", $usuario_id, $cpf, $forma_pagamento, $valor_total);
    $stmt->execute();

    // Limpa o carrinho
    unset($_SESSION['carrinho']);

    echo "Compra realizada com sucesso!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Finalizar Compra</h1>
        <form action="checkout.php" method="POST">
            <div class="form-group">
                <label for="cpf">CPF:</label>
                <input type="text" name="cpf" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="forma_pagamento">Forma de Pagamento:</label>
                <select name="forma_pagamento" class="form-control" required>
                    <option value="Cartão">Cartão</option>
                    <option value="Boleto">Boleto</option>
                </select>
            </div>

            <input type="hidden" name="valor_total" value="<?php echo $valor_total; ?>">

            <button type="submit" class="btn btn-primary">Finalizar Compra</button>
        </form>
    </div>
</body>
</html>
