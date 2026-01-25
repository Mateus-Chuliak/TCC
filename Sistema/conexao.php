<?php

// Variáveis de ambiente obrigatórias para conexão com o banco
$varsObrigatorias = [
    'DB_HOST',
    'DB_USUARIO',
    'DB_SENHA',
    'DB_NOME',
    'DB_PORTA',
];

// Verifica se todas as configurações necessárias estão definidas
foreach ($varsObrigatorias as $var) {
    if (!defined($var)) {
        die("Configuração obrigatória não definida: {$var}");
    }
}

// Cria conexão MySQLi com parâmetros configurados
$conexao = new mysqli(
    DB_HOST,
    DB_USUARIO,
    DB_SENHA,
    DB_NOME,
    (int) DB_PORTA
);

// Interrompe a execução em caso de erro de conexão
if ($conexao->connect_error) {
    die("Erro ao conectar ao MySQL");
}

// Define charset padrão para suporte a UTF-8 completo
$conexao->set_charset("utf8mb4");
