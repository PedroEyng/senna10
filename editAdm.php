<?php
include_once('config.php');

if (!empty($_GET['user_id'])) {
    $id = $_GET['user_id'];
    $sqlSelect = "SELECT * FROM user WHERE user_id=$id";
    $result = $conexao->query($sqlSelect);

    if ($result->num_rows > 0) {
        while ($user_data = mysqli_fetch_assoc($result)) {
            $nome = $user_data['user_nome'];
            $senha = $user_data['senha'];
            $email = $user_data['email'];
            $admin = $user_data['admin'];
        
        
        }
    } else {
        header('Location: usuarios.php');
        exit;
    }
} else {
    header('Location: usuarios.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/form.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400..900&family=Pacifico&display=swap"
        rel="stylesheet">
    <title>SENNA | Editar Cadastro</title>
    <link rel="shortcut icon" href="assets/images/icones/icone.png" type="image/x-icon">
</head>

<body>
    <a href="painel.php">Voltar</a>
    <div class="box">
        <form action="saveEditAdm.php" method="POST">
            <fieldset>
                <legend><b>Editar Usuário</b></legend>
                <br>
                <div class="inputBox">
                    <input type="text" name="user_nome" id="nome" class="inputUser" value="<?php echo $nome; ?>"
                        required>
                    <label for="nome" class="labelInput">Nome completo</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="senha" id="senha" class="inputUser" value="<?php echo $senha; ?>" required>
                    <label for="senha" class="labelInput">Senha</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="email" id="email" class="inputUser" value=<?php echo $email; ?> required>
                    <label for="email" class="labelInput">email</label>
                </div>
                <br><br>

                <div class="container mt-5">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="admin" id="admin" value="1" <?php echo ($admin == '1') ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="admin">
                            Admin
                        </label>
                    </div>
                        <br><br>
                        <input type="hidden" name="user_id" value="<?php echo $id; ?>">
                        <input type="submit" name="update" id="submit">
            </fieldset>
        </form>
    </div>
</body>

</html>