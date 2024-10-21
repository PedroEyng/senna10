<?php
session_start();
include_once('config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produto_id = $_POST['produto_id'];
    $tamanho = $_POST['tamanho'];
    $modelo = $_POST['modelo'];
    $quantidade = 1; // Por padrão, 1 unidade.

    // Consulta o produto no banco
    $sql = "SELECT * FROM produtos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $produto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $produto = $result->fetch_assoc();

        // Prepara os dados para adicionar ao carrinho
        $carrinho_item = [
            'produto_id' => $produto_id,
            'nome' => $produto['nome'],
            'preco' => $produto['preco'],
            'tamanho' => $tamanho,
            'modelo' => $modelo,
            'quantidade' => $quantidade,
            'total' => $produto['preco'] * $quantidade
        ];

        // Adiciona o produto ao carrinho (sessão)
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = [];
        }

        $_SESSION['carrinho'][] = $carrinho_item;

        // Redireciona para o carrinho
        header("Location: carrinho.php");
        exit;
    } else {
        echo "Produto não encontrado!";
    }
}
?>
