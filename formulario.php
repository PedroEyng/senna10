<?php
include_once('config.php');
$erro_email = '';

if (isset($_POST['submit'])) {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verifica se o email já existe no banco de dados
    $email_check_query = "SELECT * FROM user WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conexao, $email_check_query);
    $user = mysqli_fetch_assoc($result);

    if ($user) { // Se o email já existir
        $erro_email = 'O email já está registrado! Por favor, registre outro!';
    } else {
        // Se o email não existir, insere os dados no banco de dados
        $result = mysqli_query($conexao, "INSERT INTO user(user_nome,senha,email) 
        VALUES ('$nome','$senha','$email')");

        header('Location: login.html');
    }
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
    <title>SENNA | cadastro</title>
    <link rel="shortcut icon" href="assets/images/icones/icone.png" type="image/x-icon">
</head>

<body>
    <header class="cabecalho">
        <a href="index.html">
            <img class="logo" src="assets/images/icones/header-logo.png" alt="">
        </a>
    </header>
    <div class="box">
        <form method="POST">
            <fieldset>
                <legend><b>Cadastre-se no SENNA</b></legend>
                <br>
                <div class="inputBox">
                    <input type="text" name="nome" id="nome" class="inputUser" required>
                    <label for="nome" class="labelInput">Nome completo</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="text" name="email" id="email" class="inputUser" required>
                    <label for="email" class="labelInput">Email</label>
                </div>
                <br><br>
                <div class="inputBox">
                    <input type="password" name="senha" id="senha" class="inputUser" required>
                    <label for="senha" class="labelInput">Senha</label>
                </div>
             <a href="login.html" class="esqueci">Logar</a>
                <br><br>
                <input type="submit" name="submit" id="submit">
            </fieldset>
        </form>
    </div>

    <!-- Modal personalizada -->
    <div class="modal-overlay" id="modal-overlay">
        <div class="modal">
            <p><?php echo $erro_email; ?></p>
            <button class="close-btn" id="close-btn">Fechar</button>
        </div>
    </div>

    <script>
        // Verifica se existe mensagem de erro para o email
        var erroEmail = "<?php echo $erro_email; ?>";

        if (erroEmail !== '') {
            // Exibe a modal
            document.getElementById('modal-overlay').style.display = 'flex';
        }

        // Fecha a modal ao clicar no botão "Fechar"
        document.getElementById('close-btn').addEventListener('click', function () {
            document.getElementById('modal-overlay').style.display = 'none';
        });
    </script>

</body>

</html>