<?php

// Configurações básicas para sessões mais seguras
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_path', '/');

// Inicializa a sessão apenas se ainda não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Flag usada para indicar que o bootstrap da aplicação foi executado
if (!defined('APP_INIT')) {
    define('APP_INIT', true);
}

// Carrega o autoload do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Inicializa o carregamento de variáveis de ambiente (.env)
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Carrega configurações globais e conexão com o banco
require_once __DIR__ . '/configuracao.php';
require_once __DIR__ . '/conexao.php';
