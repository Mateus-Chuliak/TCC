<?php
// Parâmetros de conexão com o banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'zefish');
define('DB_USER', 'root');
define('DB_PASS', '');

// Retorna uma conexão PDO ativa ou null em caso de falha
function getConnection() {
    try {
        // Inicializa conexão PDO com suporte a UTF-8 completo
        $conn = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                // Ativa exceções para tratamento adequado de erros
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                // Define retorno de dados como array associativo
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

                // Desativa emulação de prepares para maior segurança
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );

        // Retorna a instância ativa de conexão
        return $conn;

    } catch (PDOException $e) {
        // Registra erro no log sem expor detalhes ao cliente
        error_log("Erro de conexão: " . $e->getMessage());

        // Indica falha na criação da conexão
        return null;
    }
}
?>
