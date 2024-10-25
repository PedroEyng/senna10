<?php
include('config.php');
session_start();

// Verifica se o email e a senha estão definidos na sessão
if (!isset($_SESSION['email']) || !isset($_SESSION['senha'])) {
    unset($_SESSION['email'], $_SESSION['senha']);
    header('Location: login.html');
    exit;
}

// Armazena o email do usuário logado
$logado = $_SESSION['email'];

// Verifica se o usuário é administrador
if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
    header('Location: perfil.php');
    exit;
} elseif (isset($_SESSION['admin']) && $_SESSION['admin'] != 0) {
    header('Location: estoque.php');
    exit;
}

// Adiciona um novo produto
if (isset($_POST['add_product'])) {
    $p_nome = mysqli_real_escape_string($conexao, $_POST['p_nome']);
    $p_preco = mysqli_real_escape_string($conexao, $_POST['p_preco']);
    $p_imagem = $_FILES['p_imagem']['name'];
    $p_imagem_tmp_name = $_FILES['p_imagem']['tmp_name'];
    $p_imagem_folder = 'assets/images/produtos/' . $p_imagem;
    $p_modelo = mysqli_real_escape_string($conexao, $_POST['p_modelo']);
    $p_descricao = mysqli_real_escape_string($conexao, $_POST['p_descricao']);

    if (isset($_FILES['p_imagem']) && $_FILES['p_imagem']['error'] == 0) {
        $p_imagem = $_FILES['p_imagem']['name'];
        $p_imagem_tmp_name = $_FILES['p_imagem']['tmp_name'];
        $p_imagem_folder = 'assets/images/produtos/' . $p_imagem;
    } else {
        $message[] = 'Erro no upload da imagem ou imagem não enviada.';
    }
    
    // Insere o produto no banco
    $insert_query = mysqli_query($conexao, "INSERT INTO `produtos`(nome_produto, preco, modelo, descricao) VALUES('$p_nome', '$p_preco', '$p_modelo', '$p_descricao')");

    if ($insert_query) {
        // Obtém o ID do produto recém-inserido
        $produto_id = mysqli_insert_id($conexao);

        // Move a imagem para a pasta de produtos, se o upload foi bem-sucedido
        if (move_uploaded_file($p_imagem_tmp_name, $p_imagem_folder)) {
            // Insere o caminho da imagem na tabela `imagem_produtos`
            $insert_image_query = mysqli_query($conexao, "INSERT INTO `imagem_produtos`(produto_id, caminho_imagem) VALUES('$produto_id', '$p_imagem_folder')");
            $message[] = $insert_image_query ? 'Produto e imagem adicionados com sucesso' : 'Produto adicionado, mas falha ao registrar a imagem';
        } else {
            $message[] = 'Produto adicionado, mas falha ao mover a imagem';
        }

        // Insere as cores selecionadas
        if (isset($_POST['p_cores'])) {
            foreach ($_POST['p_cores'] as $cor_id) {
                mysqli_query($conexao, "INSERT INTO produto_cores (produto_id, cor_id) VALUES ('$produto_id', '$cor_id')");
            }
        }

        // Insere os tamanhos selecionados
        if (isset($_POST['p_tamanhos'])) {
            foreach ($_POST['p_tamanhos'] as $tamanho_id) {
                mysqli_query($conexao, "INSERT INTO produto_tamanhos (produto_id, tamanho_id) VALUES ('$produto_id', '$tamanho_id')");
            }
        }
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
if (isset($_POST['update_product'])) {
    $update_p_produto_id = mysqli_real_escape_string($conexao, $_POST['update_p_produto_id']);
    $update_p_nome = mysqli_real_escape_string($conexao, $_POST['update_p_nome']);
    $update_p_preco = mysqli_real_escape_string($conexao, $_POST['update_p_preco']);
    $update_p_modelo = mysqli_real_escape_string($conexao, $_POST['update_p_modelo']);
    $update_p_descricao = mysqli_real_escape_string($conexao, $_POST['update_p_descricao']);
    $update_p_imagem = $_FILES['update_p_imagem']['name'];
    $update_p_imagem_tmp_name = $_FILES['update_p_imagem']['tmp_name'];
    $update_p_imagem_folder = 'assets/images/produtos/' . $update_p_imagem;

    $update_query = mysqli_query($conexao, "UPDATE `produtos` SET produto_nome = '$update_p_nome', preco = '$update_p_preco', modelo = '$update_p_modelo', descricao = '$update_p_descricao'". ($update_p_imagem ? ", imagem = '$update_p_imagem'" : "") . " WHERE produto_id = '$update_p_produto_id'");

    if ($update_query) {
        if ($update_p_imagem && move_uploaded_file($update_p_imagem_tmp_name, $update_p_imagem_folder)) {
            $message[] = 'Produto atualizado com sucesso';
        } else {
            $message[] = 'Produto atualizado, mas falha ao mover a imagem';
        }
    } else {
        $message[] = 'O produto não pôde ser atualizado';
    }
    header('location:estoque.php');
    exit;
}
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
    <title>SENNA</title>
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
            echo '<div class="message"><span>' . $msg . '</span> <i class="fas fa-times" onclick="this.parentElement.style.display = `none`;"></i> </div>';
        }
    }
    ?>

    <div class="container">
        <section class="sessao">
            <form action="" method="post" class="add-product-form" enctype="multipart/form-data">
                <h3>Adicionar um novo produto</h3>
                <input type="text" name="p_nome" placeholder="Nome do produto" class="box" required>
                <input type="number" name="p_preco" min="0" placeholder="Preço do produto" class="box" required>
                <input type="file" name="p_imagem" accept="image/png, image/jpg, image/jpeg" class="box" required>

                <label for="tamanhos">Selecione os tamanhos:</label>
                <select name="p_tamanhos[]" multiple class="box">
                    <?php
                    $tamanhos_query = mysqli_query($conexao, "SELECT * FROM tamanhos");
                    while ($row = mysqli_fetch_assoc($tamanhos_query)) {
                        echo "<option value='" . $row['tamanho_id'] . "'>" . $row['nome_tamanho'] . "</option>";
                    }
                    ?>
                </select>

                <input type="text" name="p_modelo" placeholder="Modelo do produto" class="box" required>
                <input type="text" name="p_descricao" placeholder="Breve descrição sobre o produto" class="box" required>

                <input type="submit" value="Adicionar produto" name="add_product" class="btn">
            </form>
        </section>

        <section class="display-product-table">
            <table>
                <thead>
                    <th>Imagem do produto</th>
                    <th>Nome do produto</th>
                    <th>Tamanho</th>
                    <th>Modelo</th>
                    <th>Descrição</th>
                    <th>Preço</th>
                    <th>Ação</th>
                </thead>
                <tbody>
                    <?php
              $select_products = mysqli_query($conexao, "SELECT p.*, i.caminho_imagem FROM produtos p LEFT JOIN imagem_produtos i ON p.produto_id = i.produto_id");
              while ($row = mysqli_fetch_assoc($select_products)) {
                $produto_id = $row['produto_id'];
                $imagem = $row['caminho_imagem']; // A variável $imagem agora recebe o caminho da imagem
                echo "<tr>
                    <td><img style='width:200px;' src='" . $imagem . "' alt=''></td> <!-- Certifique-se de que o caminho está correto -->
                    <td>" . $row['nome_produto'] . "</td>
                    <td>" . $row['tamanho_id'] . "</td>
                    <td>" . $row['modelo'] . "</td>
                    <td>" . $row['descricao'] . "</td>
                    <td>R$" . number_format($row['preco'], 2, ',', '.') . "</td>
                    <td>
                        <a href='estoque.php?edit=$produto_id' class='delete-btn'><i class='fas fa-trash'></i>Deletar</a>
                        <a href='estoque.php?delete=$produto_id' class='option-btn' onclick='return confirm(`Tem certeza que deseja excluir este produto?`);'><i class='fas fa-edit'></i> Editar</a>
                    </td>
                </tr>";
            }
            
                    ?>
                </tbody>
            </table>
        </section>
    </div>
      <section class="edit-form-container">

         <?php

         if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];

            

            $edit_query = mysqli_query($conexao, "SELECT * FROM `produtos` WHERE produto_id = $edit_id");
            if (mysqli_num_rows($edit_query) > 0) {
               while ($fetch_edit = mysqli_fetch_assoc($edit_query)) {

                
                  ?>

                  <form action="" method="post" enctype="multipart/form-data">
                  <img src="assets/images/produtos/<?php echo $fetch_edit['caminho_imagem']; ?>" height="200" alt="">

                     <input type="hidden" name="update_p_produto_id" value="<?php echo $fetch_edit['produto_id']; ?>">

                     <input type="text" class="box" required name="update_p_nome"
                        value="<?php echo $fetch_edit['produto_nome']; ?>">
                     <input type="text" class="box" name="update_p_tamanho" value="<?php echo $fetch_edit['tamanho']; ?>">
                     <input type="text" class="box" name="update_p_modelo" value="<?php echo $fetch_edit['modelo']; ?>">
                     <input type="text" class="box" name="update_p_descricao" value="<?php echo $fetch_edit['descricao']; ?>">
                     <input type="number" min="0" class="box" required name="update_p_preco"
                        value="<?php echo $fetch_edit['preco']; ?>">
                     <input type="file" class="box" required name="update_p_imagem" accept="image/png, image/jpg, image/jpeg">

                     <input type="submit" value="update the prodcut" name="update_product" class="option-btn">

                     <input type="reset" value="cancel" id="close-edit" class="option-btn">

                  </form>

                  <?php
               }
               ;
            }
            ;
            echo "<script>document.querySelector('.edit-form-container').style.display = 'flex';</script>";
         }
         ;
         ?>

      </section>

   </div>


   <!-- custom js file link  -->
   <script src="assets/js/script.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>