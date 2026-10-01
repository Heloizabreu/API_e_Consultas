<?php 

$host = "192.168.10.25";
$usuario = "postgres";
$banco = "almoxarifado";
$senha = "------";

$pdo = new PDO (
    "pgsql:host=$host;port=5432;dbname=$banco",
    $usuario,
    $senha
);