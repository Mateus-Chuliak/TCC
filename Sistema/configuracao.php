<?php

// Define o fuso horário padrão da aplicação
date_default_timezone_set('America/Sao_Paulo');

// Informações institucionais do sistema
define('SITE_NOME', 'Zéfish');
define('SITE_DESCRICAO', 'Zéfish - Tecnologia em Sistemas');

// URLs base para diferentes ambientes
define('URL_PRODUCAO', 'https://zefish.com.br');
define('URL_DESENVOLVIMENTO', 'http://localhost/Zéfish');

// Obtém o host atual da requisição
$host = $_SERVER['HTTP_HOST'] ?? '';

// Detecta se a aplicação está rodando em ambiente local
$isLocalhost =
    stripos($host, 'localhost') !== false  ||
    stripos($host, '127.0.0.1') !== false ||
    stripos($host, '::1') !== false;

// Configurações específicas para ambiente local
if ($isLocalhost) {

    // Parâmetros de acesso ao banco em desenvolvimento
    define('DB_HOST', 'localhost');
    define('DB_PORTA', 3306);
    define('DB_NOME', 'zefish');
    define('DB_USUARIO', 'root');
    define('DB_SENHA', '');

    // URLs internas utilizadas no ambiente local
    define('URL_SITE', '/Zéfish/');
    define('URL_ADMIN', '/Zéfish/templates/views/Admin/');

} else {

    // Parâmetros de acesso ao banco em produção
    define('DB_HOST', 'localhost');
    define('DB_PORTA', 3306);
    define('DB_NOME', 'zefish');
    define('DB_USUARIO', 'seu_usuario');
    define('DB_SENHA', 'sua_senha');

    // URLs utilizadas no ambiente de produção
    define('URL_SITE', '/');
    define('URL_ADMIN', '/admin/');
}
