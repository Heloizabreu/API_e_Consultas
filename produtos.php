<?php

header("Content-Type: application/json");

require "conexao.php";

$metodo = $_SERVER["REQUEST_METHOD"];

// CADASTRAR PEÇA
if ($metodo == "POST") {
    $json = file_get_contents("php://input");
    $dados = json_decode($json, true);

    // Validação de campos obrigatórios
    if (empty($dados["nome"]) || empty($dados["categoria"]) || empty($dados["fornecedor"]) || !isset($dados["quantidade"]) || !isset($dados["preco_unitario"])) {
        echo json_encode(["Mensagem" => "Todos os campos obrigatórios devem ser preenchidos!"]);
        exit;
    }

    // Validação das regras de negócio
    $categorias_validas = ["eletrica", "mecanica", "hidraulica"];
    if (!in_array($dados["categoria"], $categorias_validas)) {
        echo json_encode(["Mensagem" => "Categoria inválida. Use: eletrica, mecanica ou hidraulica."]);
        exit;
    }

    if ($dados["quantidade"] < 0) {
        echo json_encode(["Mensagem" => "A quantidade não pode ser negativa."]);
        exit;
    }

    if ($dados["preco_unitario"] <= 0) {
        echo json_encode(["Mensagem" => "O preço unitário deve ser maior que zero."]);
        exit;
    }

    $sql = "INSERT INTO pecas (nome, categoria, fornecedor, quantidade, preco_unitario) VALUES (?, ?, ?, ?, ?)";
    $comando = $pdo->prepare($sql);
    $comando->execute([
        $dados["nome"],
        $dados["categoria"],
        $dados["fornecedor"],
        $dados["quantidade"],
        $dados["preco_unitario"]
    ]);

    echo json_encode(["Mensagem" => "Peça cadastrada com sucesso!"]);
}

// LISTAR PEÇAS
if ($metodo == "GET") {
    $sql = "SELECT * FROM pecas ORDER BY id";
    $comando = $pdo->query($sql);
    $pecas = $comando->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($pecas);
}

// ATUALIZAR PEÇA
if ($metodo == "PUT") {
    $id = $_GET["id"] ?? null;

    if (!$id) {
        echo json_encode(["Mensagem" => "Informe o ID na URL. Ex: pecas.php?id=1"]);
        exit;
    }

    $json = file_get_contents("php://input");
    $dados = json_decode($json, true);

    if (empty($dados["nome"]) || empty($dados["categoria"]) || empty($dados["fornecedor"]) || !isset($dados["quantidade"]) || !isset($dados["preco_unitario"])) {
        echo json_encode(["Mensagem" => "Todos os campos são obrigatórios."]);
        exit;
    }

    $categorias_validas = ["eletrica", "mecanica", "hidraulica"];
    if (!in_array($dados["categoria"], $categorias_validas)) {
        echo json_encode(["Mensagem" => "Categoria inválida."]);
        exit;
    }

    if ($dados["quantidade"] < 0 || $dados["preco_unitario"] <= 0) {
        echo json_encode(["Mensagem" => "Quantidade ou preço inválidos."]);
        exit;
    }

    $sql = "UPDATE pecas SET nome = ?, categoria = ?, fornecedor = ?, quantidade = ?, preco_unitario = ? WHERE id = ?";
    $comando = $pdo->prepare($sql);
    $comando->execute([
        $dados["nome"],
        $dados["categoria"],
        $dados["fornecedor"],
        $dados["quantidade"],
        $dados["preco_unitario"],
        $id
    ]);

    echo json_encode(["Mensagem" => "Peça atualizada com sucesso!"]);
}

// EXCLUIR PEÇA
if ($metodo == "DELETE") {
    $id = $_GET["id"] ?? null;

    if (!$id) {
        echo json_encode(["Mensagem" => "Informe o ID na URL. Ex: pecas.php?id=1"]);
        exit;
    }

    $sql = "DELETE FROM pecas WHERE id = ?";
    $comando = $pdo->prepare($sql);
    $comando->execute([$id]);

    echo json_encode(["Mensagem" => "Peça excluída com sucesso!"]);
}