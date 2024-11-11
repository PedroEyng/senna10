<?php
include("config.php");
session_start();
// Verificação e redirecionamento
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email']);
    unset($_SESSION['senha']);
    header('Location: login.html');
    exit;
}

// Autenticação do usuário
$email = $_SESSION['email'];
$senha = $_SESSION['senha'];
$stmt = $conexao->prepare("SELECT * FROM user WHERE email = ? AND senha = ?");
$stmt->bind_param("ss", $email, $senha);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['admin'] == 1) {
        if (basename($_SERVER['PHP_SELF']) !== 'estoque.php') {
            header('Location: estoque.php');
            exit;
        }
    } else {
        if (basename($_SERVER['PHP_SELF']) !== 'perfil.php') {
            header('Location: perfil.php');
            exit;
        }
    }
} else {
    header('Location: login.html');
    exit;
}

// Processamento do add_product
if (isset($_POST['add_product'])) {
    $p_nome = $_POST['p_nome'];
    $p_preco = $_POST['p_preco'];
    $p_descricao = $_POST['p_descricao'];
    $p_modelo = $_POST['p_modelo'];
    $p_tamanhos = $_POST['p_tamanhos'];


    // Verifica se a pasta uploads existe, se não, cria a pasta
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);  // Cria a pasta, se não existir, com permissões adequadas
}

// Upload das imagens
$imagens = [];
for ($i = 1; $i <= 4; $i++) {
    if (!empty($_FILES["p_imagem$i"]['name'])) {
        $imagem_nome = uniqid() . '_' . basename($_FILES["p_imagem$i"]['name']);
        $imagem_caminho = $upload_dir . $imagem_nome;
        if (move_uploaded_file($_FILES["p_imagem$i"]['tmp_name'], $imagem_caminho)) {
            $imagens[] = $imagem_caminho;
        } else {
            $message[] = 'Erro ao mover a imagem para o diretório de uploads.';
        }
    }
}


    // Inserir o produto no banco de dados
    $insert_product_query = $conexao->prepare("INSERT INTO produtos (produto_nome, preco, descricao, modelo) VALUES (?, ?, ?, ?)");
    $insert_product_query->bind_param("sdss", $p_nome, $p_preco, $p_descricao, $p_modelo);
    
    if ($insert_product_query->execute()) {
        $produto_id = $conexao->insert_id;

        // Inserir tamanhos associados ao produto
        foreach ($p_tamanhos as $tamanho_id) {
            $insert_size_query = $conexao->prepare("INSERT INTO tamanho_produto (produto_id, tamanho_id) VALUES (?, ?)");
            $insert_size_query->bind_param("ii", $produto_id, $tamanho_id);
            $insert_size_query->execute();
        }

        // Inserir imagens na tabela de imagens do produto
        foreach ($imagens as $imagem_caminho) {
            $insert_image_query = $conexao->prepare("INSERT INTO imagem_produtos (produto_id, caminho_imagem) VALUES (?, ?)");
            $insert_image_query->bind_param("is", $produto_id, $imagem_caminho);
            $insert_image_query->execute();
        }

        $message[] = 'Produto adicionado com sucesso';
    } else {
        $message[] = 'Não foi possível adicionar o produto';
    }
}




// Processamento do update_product
// Processamento do update_product
if (isset($_POST['update_product'])) {
    $update_p_produto_id = $_POST['update_p_produto_id'];
    $update_p_nome_produto = $_POST['update_p_nome'];
    $update_p_preco = $_POST['update_p_preco'];
    $update_p_modelo = $_POST['update_p_modelo'];
    $update_p_descricao = $_POST['update_p_descricao'];
    $update_p_tamanhos = $_POST['update_p_tamanhos'];

    // Atualizar o produto no banco de dados
    $update_query = $conexao->prepare("UPDATE `produtos` SET produto_nome = ?, preco = ?, modelo = ?, descricao = ? WHERE produto_id = ?");
    $update_query->bind_param("sdssi", $update_p_nome_produto, $update_p_preco, $update_p_modelo, $update_p_descricao, $update_p_produto_id);
    if ($update_query->execute()) {
        // Atualizar tamanhos do produto
        $delete_sizes_query = $conexao->prepare("DELETE FROM tamanho_produto WHERE produto_id = ?");
        $delete_sizes_query->bind_param("i", $update_p_produto_id);
        $delete_sizes_query->execute();

        foreach ($update_p_tamanhos as $tamanho_id) {
            $insert_size_query = $conexao->prepare("INSERT INTO tamanho_produto (produto_id, tamanho_id) VALUES (?, ?)");
            $insert_size_query->bind_param("ii", $update_p_produto_id, $tamanho_id);
            $insert_size_query->execute();
        }

        $message[] = 'Produto e tamanhos atualizados com sucesso';
    } else {
        $message[] = 'Não foi possível atualizar o produto';
    }

    // Redireciona para a página padrão
    header('Location: estoque.php');
    exit;
}



// Carregar dados do produto ao editar
$product_data = null;
$tamanhos_atual = [];
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    
    // Carregar os dados do produto
    $product_query = $conexao->prepare("SELECT * FROM produtos WHERE produto_id = ?");
    $product_query->bind_param("i", $edit_id);
    $product_query->execute();
    $product_data = $product_query->get_result()->fetch_assoc();
    
    // Carregar tamanhos atribuídos ao produto
    $tamanhos_query = $conexao->prepare("SELECT t.tamanho_id FROM tamanho_produto tp JOIN tamanhos t ON tp.tamanho_id = t.tamanho_id WHERE tp.produto_id = ?");
    $tamanhos_query->bind_param("i", $edit_id);
    $tamanhos_query->execute();
    $tamanhos_result = $tamanhos_query->get_result();
    
    // Armazenar tamanhos atribuídos ao produto
    while ($row = $tamanhos_result->fetch_assoc()) {
        $tamanhos_atual[] = $row['tamanho_id'];
    }
}
?>
<!-- HTML - Formulário de edição -->
<?php if ($product_data): ?>
    <div class="overlay" onclick="document.querySelector('.overlay').style.display='none'; document.querySelector('.edit-form-container').style.display='none'"></div>
    <div class="edit-form-container">
        <h3>Editar Produto</h3>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" name="update_p_produto_id" value="<?php echo htmlspecialchars($edit_id); ?>">
            <input type="text" name="update_p_nome" value="<?php echo htmlspecialchars($product_data['produto_nome']); ?>" placeholder="Nome do produto" required>
            <br>
            <input type="number" name="update_p_preco" value="<?php echo htmlspecialchars($product_data['preco']); ?>" placeholder="Preço do produto" required>
            <br>
            <input type="text" name="update_p_modelo" value="<?php echo htmlspecialchars($product_data['modelo']); ?>" placeholder="Modelo do produto" required>
            <br>
            <textarea name="update_p_descricao" placeholder="Descrição do produto"><?php echo htmlspecialchars($product_data['descricao']); ?></textarea>
            <br>
           
            <label for="tamanhos">Selecione os tamanhos:</label><br>
            <select name="update_p_tamanhos[]" multiple class="box">
                <?php
                // Carregar tamanhos disponíveis
                $tamanhos_query = mysqli_query($conexao, "SELECT * FROM tamanhos");
                while ($row = mysqli_fetch_assoc($tamanhos_query)) {
                    $selected = in_array($row['tamanho_id'], $tamanhos_atual) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($row['tamanho_id'], ENT_QUOTES) . "' $selected>" . htmlspecialchars($row['tamanho'], ENT_QUOTES) . "</option>";
                }
                ?>
            </select>
            <br>

            <button type="submit" name="update_product">Atualizar Produto</button>
            <button type="button" onclick="window.location.href='estoque.php'">Cancelar</button>
            <br><br>
        </form>
    </div>
<?php endif; ?>



<?php

// Exclui um produto
if (isset($_GET['delete'])) {
    $delete_id = mysqli_real_escape_string($conexao, $_GET['delete']);
    $delete_query = mysqli_query($conexao, "DELETE FROM `produtos` WHERE produto_id = $delete_id");
    $message[] = $delete_query ? 'O produto foi excluído' : 'O produto não pôde ser excluído';
    header('location:estoque.php');
    exit;
}
 



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
                        <th>Tamanhos</th>
                        <th>Modelo</th>
                        <th>Descrição</th>
                        <th>Preço</th>
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
