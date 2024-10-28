<?php
include('config.php');
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['email'])) {
    // Se não estiver logado, redireciona para a página de login
    header('Location: login.html');
    exit;
}

// Atribui o e-mail do usuário logado a uma variável
$logado = $_SESSION['email'];

// Busca o nome do usuário e a permissão no banco de dados
$sql = "SELECT * FROM user WHERE email = ? LIMIT 1";
$stmtUser = $conexao->prepare($sql);
$stmtUser->bind_param("s", $logado);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();

if ($resultUser->num_rows > 0) {
    $row = $resultUser->fetch_assoc();
    $user_Id = $row['user_id']; // ID do usuário
    $nomeUsuario = htmlspecialchars($row['user_nome']);
    $isAdmin = $row['is_admin']; // Supondo que este campo exista
} else {
    // Se não encontrar o usuário, encerra a sessão e redireciona
    session_destroy();
    header('Location: login.html');
    exit;
}

// Verifica se o usuário é administrador
if ($isAdmin != 1) {
    // Se não for administrador, redireciona para a página inicial ou outra página
    header('Location: perfil.php'); // Altere para a página desejada
    exit;
}
// Adiciona um novo produto
// Adiciona um novo produto
if (isset($_POST['add_product'])) {
    $p_nome = mysqli_real_escape_string($conexao, $_POST['p_nome']);
    $p_preco = mysqli_real_escape_string($conexao, $_POST['p_preco']);
    $p_modelo = mysqli_real_escape_string($conexao, $_POST['p_modelo']);
    $p_descricao = mysqli_real_escape_string($conexao, $_POST['p_descricao']);

    // Insere o produto no banco
    $insert_query = mysqli_query($conexao, "INSERT INTO `produtos`(produto_nome, preco, modelo, descricao) VALUES('$p_nome', '$p_preco', '$p_modelo', '$p_descricao')");

    if ($insert_query) {
        $produto_id = mysqli_insert_id($conexao);

        // Processa até quatro imagens, se enviadas
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($_FILES["p_imagem$i"]["name"])) {
                $image_name = $_FILES["p_imagem$i"]["name"];
                $image_tmp_name = $_FILES["p_imagem$i"]["tmp_name"];
                $image_folder = 'assets/images/produtos/' . $image_name;

                if (move_uploaded_file($image_tmp_name, $image_folder)) {
                    $insert_image_query = mysqli_query($conexao, "INSERT INTO `imagem_produtos`(produto_id, caminho_imagem) VALUES('$produto_id', '$image_folder')");
                    if (!$insert_image_query) {
                        $message[] = 'Produto adicionado, mas falha ao registrar uma das imagens';
                    }
                } else {
                    $message[] = 'Erro no upload de uma das imagens';
                }
            }
        }

        // Insere os tamanhos selecionados
        if (isset($_POST['p_tamanhos'])) {
            foreach ($_POST['p_tamanhos'] as $tamanho_id) {
                mysqli_query($conexao, "INSERT INTO tamanho_produto (produto_id, tamanho_id) VALUES ('$produto_id', '$tamanho_id')");
            }
        }

        $message[] = 'Produto e imagens adicionados com sucesso';
    } else {
        $message[] = 'Não foi possível adicionar o produto';
    }
}


// Exclui um produto
if (isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($conexao, $_GET['delete']);
    $delete_query = mysqli_query($conexao, "DELETE FROM `produtos` WHERE produto_id = $delete_id");
    $message[] = $delete_query ? 'O produto foi excluído' : 'O produto não pôde ser excluído';
    header('location:estoque.php');
    exit;
}

// Atualiza um produto

if (isset($_GET['edit'])) {
    $edit_id = mysqli_real_escape_string($conexao, $_GET['edit']);
    $product_query = mysqli_query($conexao, "SELECT * FROM produtos WHERE produto_id = '$edit_id'");
    $product_data = mysqli_fetch_assoc($product_query);

    if ($product_data) {
        // Renderiza o formulário de edição
        ?>
        <div class="edit-form-container" style="display: block; max-width: 600px; margin: 10% auto; padding: 50px; border: 1px solid #ccc; border-radius: 5px;">
            <h3>Editar Produto</h3>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="update_p_produto_id" value="<?php echo htmlspecialchars($edit_id, ENT_QUOTES); ?>">
                <input type="text" name="update_p_nome" value="<?php echo htmlspecialchars($product_data['produto_nome'], ENT_QUOTES); ?>" placeholder="Nome do produto" class="box" required>
                <input type="number" name="update_p_preco" min="0" value="<?php echo $product_data['preco']; ?>" placeholder="Preço do produto" class="box" required>
                <textarea name="update_p_descricao" placeholder="Descrição do produto" class="box" required><?php echo htmlspecialchars($product_data['descricao'], ENT_QUOTES); ?></textarea>
                <input type="text" name="update_p_modelo" value="<?php echo htmlspecialchars($product_data['modelo'], ENT_QUOTES); ?>" placeholder="Modelo do produto" class="box" required>
                
                <input type="file" name="update_p_imagem" accept="image/png, image/jpg, image/jpeg" class="box">
                
                <input type="submit" name="update_product" value="Atualizar Produto" class="btn">
            </form>
            <button id="close-edit" class="btn">Fechar</button> <!-- Botão para fechar o formulário -->
        </div>
        <?php
    } else {
        echo "<p>Produto não encontrado para edição.</p>";
    }
}



$select_products = mysqli_query($conexao, "
    SELECT p.*, 
           i.caminho_imagem, 
           GROUP_CONCAT(t.tamanho SEPARATOR ', ') AS tamanhos
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-admin.css">
    <title>SENNA | estoque</title>
    <link rel="shortcut icon" href="assets/images/icones/icone.png" type="image/x-icon">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark static-top">
            <a class="navbar-brand" href="painel.php">
                <img class="logo" src="assets/images/icones/header-logo.png" alt="">
            </a>
        </nav>
    </header>

    <?php
    if (isset($message)) {
        foreach ($message as $msg) {
            echo '<div class="message"><span>' . htmlspecialchars($msg, ENT_QUOTES) . '</span> <i class="fas fa-times" onclick="this.parentElement.style.display = `none`;"></i> </div>';
        }
    }
    ?>

    <div class="container">
        <section class="sessao">
            <form action="" method="post" class="add-product-form" enctype="multipart/form-data">
                <h3>Adicionar um novo produto</h3>
                <input type="text" name="p_nome" placeholder="Nome do produto" class="box" required>
                <input type="number" name="p_preco" min="0" placeholder="Preço do produto" class="box" required>

                <input type="file" name="p_imagem1" accept="image/png, image/jpg, image/jpeg" class="box" required>
                <input type="file" name="p_imagem2" accept="image/png, image/jpg, image/jpeg" class="box">
                <input type="file" name="p_imagem3" accept="image/png, image/jpg, image/jpeg" class="box">
                <input type="file" name="p_imagem4" accept="image/png, image/jpg, image/jpeg" class="box">

                <label for="tamanhos">Selecione os tamanhos:</label>
                <select name="p_tamanhos[]" multiple class="box">
                    <?php
                    $tamanhos_query = mysqli_query($conexao, "SELECT * FROM tamanhos");
                    while ($row = mysqli_fetch_assoc($tamanhos_query)) {
                        echo "<option value='" . htmlspecialchars($row['tamanho_id'], ENT_QUOTES) . "'>" . htmlspecialchars($row['tamanho'], ENT_QUOTES) . "</option>";
                    }
                    ?>
                </select>
                <textarea name="p_descricao" placeholder="Descrição do produto" class="box" required></textarea>
                <input type="text" name="p_modelo" placeholder="Modelo do produto" class="box" required>
                <input type="submit" name="add_product" value="Adicionar Produto" class="btn">
            </form>

            <section class="display-product-table">
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Imagem</th>
                        <th>Preço</th>
                        <th>Modelo</th>
                        <th>Descrição</th>
                        <th>Tamanhos</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
           while ($row = mysqli_fetch_assoc($select_products)) {
            $produto_id = $row['produto_id'];
            $imagem = isset($row['caminho_imagem']) ? $row['caminho_imagem'] : 'assets/images/default.png'; // Imagem padrão
           echo "<tr>
                <td><img style='width:200px;' src='" . htmlspecialchars($imagem, ENT_QUOTES) . "' alt='Produto'></td>
                <td>" . htmlspecialchars($row['produto_nome'], ENT_QUOTES) . "</td>
                <td>" . htmlspecialchars($row['tamanhos'] ?: 'N/A', ENT_QUOTES) . "</td>
                <td>" . htmlspecialchars($row['modelo'], ENT_QUOTES) . "</td>
                <td>" . htmlspecialchars($row['descricao'], ENT_QUOTES) . "</td>
                <td>R$" . number_format($row['preco'], 2, ',', '.') . "</td>
                <td>
                    <a href='estoque.php?edit=" . htmlspecialchars($produto_id, ENT_QUOTES) . "' class='option-btn'><i class='fas fa-edit'></i>Editar</a>
                    <br>
                    <a href='estoque.php?delete=" . htmlspecialchars($produto_id, ENT_QUOTES) . "' class='delete-btn' onclick='return confirm(`Tem certeza que deseja excluir este produto?`);'><i class='fas fa-trash'></i> Deletar</a>
                </td>
            </tr>";
        }
        
                    ?>
                </tbody>
            </table>
        </section>
    </div>
    <script src="assets/js/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
