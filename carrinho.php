<?php
@include 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];  // Use sempre o user_id da sessão

// Atualizando a quantidade do produto no carrinho
if (isset($_POST['update_update_btn'])) {
    $update_value = $_POST['update_quantity'];
    $update_id = $_POST['update_quantity_id'];

    // Garantir que a atualização afete somente o carrinho do usuário logado
    $update_quantity_query = mysqli_query($conexao, "UPDATE carrinho SET quantidade = '$update_value' WHERE produto_id = '$update_id' AND user_id = '$user_id'");

    if ($update_quantity_query) {
        header('location:carrinho.php'); // Redireciona após a atualização
        exit();
    } else {
        echo "Erro ao atualizar a quantidade!";
    }
}

// Removendo um produto do carrinho do usuário logado
if (isset($_GET['remove'])) {
    $remove_id = $_GET['remove'];
    
    // Verificar e garantir que o produto removido é do carrinho do usuário logado
    $remove_query = mysqli_query($conexao, "DELETE FROM carrinho WHERE produto_id = '$remove_id' AND user_id = '$user_id'");

    if ($remove_query) {
        header('location:carrinho.php'); // Redireciona após remover o produto
        exit();
    } else {
        echo "Erro ao remover o item!";
    }
}

// Deletando todos os produtos do carrinho do usuário logado
if (isset($_GET['delete_all'])) {
    $delete_query = mysqli_query($conexao, "DELETE FROM carrinho WHERE user_id = '$user_id'");

    if ($delete_query) {
        header('location:carrinho.php'); // Redireciona após deletar todos os itens
        exit();
    } else {
        echo "Erro ao excluir todos os itens!";
    }
}

// Consultando os itens do carrinho para o usuário logado
$query = "SELECT * FROM carrinho WHERE user_id = '$user_id'";

// Executando a consulta
$select_cart = mysqli_query($conexao, "
    SELECT 
        carrinho.quantidade, 
        produtos.produto_id, 
        produtos.produto_nome, 
        produtos.preco, 
        (SELECT caminho_imagem FROM imagem_produtos WHERE produto_id = produtos.produto_id LIMIT 1) AS imagem
    FROM carrinho
    JOIN produtos ON carrinho.produto_id = produtos.produto_id
    WHERE carrinho.user_id = '$user_id'
");

if (!$select_cart) {
    die("Erro na consulta: " . mysqli_error($conexao));
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>SENNA | Carrinho</title>

   <!-- Bootstrap CSS link -->
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>
<body>

<div class="container my-5">

<section class="shopping-cart">

   <h1 class="text-center mb-4">Shopping Cart</h1>

   <table class="table table-bordered text-center">
    <thead class="table-dark">
        <tr>
            <th>Imagem</th>
            <th>Nome</th>
            <th>Preço</th>
            <th>Quantidade</th>
            <th>Preço Total</th>
            <th>Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grand_total = 0;

        if (mysqli_num_rows($select_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                // Verifica se existe uma imagem, caso contrário, usa a imagem padrão
                $imagem = $fetch_cart['imagem'] ? $fetch_cart['imagem'] : 'assets/images/default.png';

                ?>
                <tr>
                <td>
                 <!-- Exibe a imagem com o caminho completo -->
        <?php
        // Consultando mais imagens associadas ao produto
        $select_images = mysqli_query($conexao, "SELECT caminho_imagem FROM imagem_produtos WHERE produto_id = '{$fetch_cart['produto_id']}'");
        $is_active = true;
        $images_found = false; // Flag para verificar se encontramos imagens no banco

        while ($image = mysqli_fetch_assoc($select_images)) {
            $image_path = 'assets/images/produtos/' . $image['caminho_imagem'];
            $images_found = true;
            ?>
            <div class="carousel-item <?php echo ($is_active) ? 'active' : ''; ?>">
            <img src="<?php echo htmlspecialchars($imagem, ENT_QUOTES); ?>" class="img-thumbnail" style="height: auto;   width: 100%;
    height: 300px; /* Ajuste a altura conforme necessário */
    object-fit: cover; " alt="Produto">

            </div>
            <?php
            $is_active = false; // Após a primeira imagem, desativa a classe "active"
        }

        // Se nenhuma imagem foi encontrada, mostrar a imagem padrão
        if (!$images_found) {
            $default_image_path = 'assets/images/default.png'; 
            ?>
            <div class="carousel-item active">
                <img src="<?php echo htmlspecialchars($default_image_path, ENT_QUOTES); ?>" class="d-block w-100" alt="Imagem Padrão">
            </div>
            <?php
        }
        ?>
    </div>

                </td>
                <td><?php echo htmlspecialchars($fetch_cart['produto_nome'], ENT_QUOTES); ?></td>
                <td>R$ <?php echo number_format($fetch_cart['preco'], 2, ',', '.'); ?></td>
                <td>
                    <form action="" method="post" class="d-flex justify-content-center align-items-center">
                        <input type="hidden" name="update_quantity_id" value="<?php echo $fetch_cart['produto_id']; ?>">
                        <input type="number" name="update_quantity" min="1" class="form-control w-50 me-2" value="<?php echo $fetch_cart['quantidade']; ?>">
                        <button type="submit" class="btn btn-primary btn-sm" name="update_update_btn">Atualizar</button>
                    </form>
                </td>
                <td>R$ <?php echo number_format($fetch_cart['preco'] * $fetch_cart['quantidade'], 2, ',', '.'); ?></td>
                <td>
                    <a href="carrinho.php?remove=<?php echo $fetch_cart['produto_id']; ?>" onclick="return confirm('Remover item do carrinho?')" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i> Remover
                    </a>
                </td>
                </tr>
                <?php
                $grand_total += $fetch_cart['preco'] * $fetch_cart['quantidade'];
            }
        }
        ?>
        <tr class="table-light">
            <td colspan="3"><a href="products.php" class="btn btn-secondary btn-sm">Continuar Comprando</a></td>
            <td><strong>Total Geral</strong></td>
            <td><strong>R$ <?php echo number_format($grand_total, 2, ',', '.'); ?></strong></td>
            <td>
                <a href="carrinho.php?delete_all" onclick="return confirm('Tem certeza de que deseja excluir tudo?');" class="btn btn-danger btn-sm">
                    <i class="fas fa-trash"></i> Excluir Tudo
                </a>
            </td>
        </tr>
    </tbody>
</table>
<a href="checkout.php" class="btn btn-success">
    Ir para o Checkout
</a>


</section>

</div>
   
<!-- Bootstrap JS bundle link -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>