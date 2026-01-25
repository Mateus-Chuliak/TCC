<?php
require_once __DIR__ . '/init.php';

// Limpa todas as variáveis de sessão
$_SESSION = [];

// Destroi a sessão
session_destroy();

// Apaga o cookie da sessão (mais seguro)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redireciona para a home pública
header("Location: /Zéfish/index.php");
exit;