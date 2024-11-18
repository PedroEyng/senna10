<?php
session_start(); // Inicia a sessão, deve ser a primeira linha do arquivo
@include 'config.php';

// Verifica se o formulário foi enviado
if (isset($_POST['order_btn'])) {
    // Verifica se o usuário está logado
    if (!isset($_SESSION['user_id'])) {
        die('Usuário não autenticado!');
    }
    
    // Obtém o ID do usuário logado
    $user_id = $_SESSION['user_id'];

    // Dados do pedido
    $email = $_POST['email'];
    $pagamento = $_POST['pagamento']; // Método de pagamento
    $endereco = $_POST['endereco'];

    // Exibe os dados do formulário para depuração (remover depois de testar)
    var_dump($_POST);  // Verifique se o 'pagamento' está sendo passado corretamente

    // Verifica se o método de pagamento foi selecionado
    if (empty($pagamento)) {
        die('Método de pagamento não selecionado.');
    }

    // Selecione os produtos no carrinho
    $select_cart = mysqli_query($conexao, 
    "SELECT c.produto_id, c.quantidade, c.preco, c.tamanho, p.produto_nome AS produto_nome 
     FROM carrinho AS c
     JOIN produtos AS p
     ON c.produto_id = p.produto_id"
    ) or die('Query failed: ' . mysqli_error($conexao));

    // Calcular o total
    $total = 0;

    // Prepara o statement para inserir os dados no banco
    $insert_order = $conexao->prepare("INSERT INTO orders (user_id, produto_id, total, quantidade, tamanho, email, endereco, pagamento, created_at) 
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Verifica se o prepared statement foi criado corretamente
    if (!$insert_order) {
        die('Erro ao preparar a consulta: ' . $conexao->error);
    }

    // Loop através dos produtos no carrinho
    if (mysqli_num_rows($select_cart) > 0) {
        $created_at = date('Y-m-d H:i:s'); // Data e hora atual
        while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
            $produto_id = $fetch_cart['produto_id'];
            $subtotal = $fetch_cart['preco'] * $fetch_cart['quantidade'];
            $total += $subtotal;
            $quantidade = $fetch_cart['quantidade'];

            // Se o tamanho estiver vazio, define um valor padrão
            $tamanho = !empty($fetch_cart['tamanho']) ? $fetch_cart['tamanho'] : 'Padrão';  // Valor padrão para tamanho

            // Bind parameters para evitar injeção de SQL
            $insert_order->bind_param("iiidsssss", $user_id, $produto_id, $subtotal, $quantidade, $tamanho, $email, $endereco, $pagamento, $created_at);

            // Executa a inserção no banco de dados
            if ($insert_order->execute()) {
                // A execução foi bem-sucedida
                continue;
            } else {
                // Caso haja erro, exibe mensagem
                die('Erro ao salvar o pedido: ' . $insert_order->error);
            }
        }

        // Limpar o carrinho após a finalização do pedido
        $clear_cart = mysqli_query($conexao, "DELETE FROM carrinho WHERE user_id = '$user_id'");

        // Mensagem de sucesso ou redirecionamento
        echo "<div class='success-message'>Seu pedido foi realizado com sucesso!</div>";
        header('Location: perfil.php');
    } else {
        echo "<div class='error-message'>Seu carrinho está vazio!</div>";
    }
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Checkout</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="assets/css/checkout.css">
</head>
<body>

<div class="container mt-5">
<section class="checkout-form">
   <h1 class="heading">Complete seu pedido</h1>

   <form action="" method="post">
      <div class="display-order mb-4">
         <?php
         // Seleciona os produtos no carrinho
         $select_cart = mysqli_query($conexao, 
         "SELECT c.produto_id, c.quantidade, c.preco, c.tamanho, p.produto_nome AS produto_nome 
          FROM carrinho AS c
          JOIN produtos AS p
          ON c.produto_id = p.produto_id"
         ) or die('Query failed: ' . mysqli_error($conexao));  

         $total = 0;
         if (mysqli_num_rows($select_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
               $produto_id = $fetch_cart['produto_id'];
               $subtotal = $fetch_cart['preco'] * $fetch_cart['quantidade'];
               $total += $subtotal;
         ?>
   <div class="order-item mb-3">
      <div class="d-flex align-items-center">
         <div class="ms-3">
            <span><strong><?= $fetch_cart['produto_nome']; ?></strong> (<?= $fetch_cart['quantidade']; ?>) - Tamanho: <?= $fetch_cart['tamanho']; ?></span>
         </div>
      </div>
   </div>

         <?php
            }
         } else {
            echo "<div class='display-order'><span>Seu carrinho está vazio!</span></div>";
         }
         ?>
         <div class="total-amount">
            <span>Total: </span><span>R$ <?= number_format($total, 2, ',', '.'); ?></span>
         </div>
      </div>

      <div class="form-group">
         <label for="email">Endereço de E-mail</label>
         <input type="email" id="email" name="email" class="form-control" required>
      </div>

      <div class="form-group">
         <label for="endereco">Endereço de Envio</label>
         <textarea name="endereco" id="endereco" class="form-control" required></textarea>
      </div>

      <div class="form-group">
         <label for="pagamento">Método de Pagamento</label>
         <select name="pagamento" id="pagamento" class="form-control" required>
            <option value="cartao_credito">Cartão de Crédito</option>
            <option value="paypal">PayPal</option>
            <option value="transferencia_bancaria">Transferência Bancária</option>
         </select>
      </div>

      <button type="submit" name="order_btn" class="btn btn-primary mt-3">Finalizar Pedido</button>
   </form>
</section>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

</body>
</html>
