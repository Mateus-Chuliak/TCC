<?php
// Inicializa sessão, configurações globais e conexão com o banco
require_once __DIR__ . '/../../../Sistema/init.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

$mensagem = "";

// Valida a presença do token na URL
if (!isset($_GET['token'])) {
    die("Token inválido.");
}

// Token recebido e sua representação hash
$token = $_GET['token'];
$token_hash = hash('sha256', $token);

// Consulta usuário com token válido e não expirado
$sql = "
    SELECT id_usuario
    FROM usuarios
    WHERE token_reset = ?
      AND expiracao_reset > NOW()
    LIMIT 1
";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();

// Interrompe se o token for inválido ou expirado
if ($result->num_rows === 0) {
    die("Token inválido ou expirado.");
}

$user = $result->fetch_assoc();

// Processa redefinição apenas via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Gera hash seguro da nova senha
    $nova_senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    // Atualiza senha e invalida o token de redefinição
    $sql_up = "
        UPDATE usuarios
        SET senha = ?,
            token_reset = NULL,
            expiracao_reset = NULL,
            tentativas = 0
        WHERE id_usuario = ?
    ";
    $stmt2 = $conexao->prepare($sql_up);
    $stmt2->bind_param("si", $nova_senha, $user['id_usuario']);

    // Define mensagem conforme o resultado da operação
    if ($stmt2->execute()) {
        $mensagem = "Senha redefinida com sucesso.";
    } else {
        $mensagem = "Erro ao redefinir a senha.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>

    <!-- Estilos e ícones -->
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>
</head>

<body class="login">

<header>
    <nav id="navbarHeader" class="navbar navbar-expand-xl fixed-top navbar-light navbar-transparente">
        <div class="container">

            <!-- Identidade visual -->
            <a href="../../../index.php" class="navbar-brand">
                <img src="../../../assets/img/logo.png" width="115" class="img-fluid" alt="Zéfish Logo">
            </a>

            <!-- Navegação responsiva -->
            <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-principal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu principal -->
            <div class="collapse navbar-collapse" id="nav-principal">
                <ul class="navbar-nav">

                    <li class="nav-item dropdown">
                        <a href="../Site/pescaria.php" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-anchor pr-4"></i>Pescaria
                        </a>
                        <div class="dropdown-menu">
                            <a href="../Site/reserva.php" class="dropdown-item">Faça sua reserva</a>
                            <a href="../Site/pescaria.php" class="dropdown-item">Tipos de pesca</a>
                            <a href="../Site/pescaria.php" class="dropdown-item">Nossos Peixes</a>
                        </div>
                    </li>

                    <li class="nav-item">
                        <a href="../Site/cardapio.php" class="nav-link">
                            <i class="fa fa-cutlery pr-4"></i>Gastronomia
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="../Site/eventos.php" class="nav-link">
                            <i class="fa fa-calendar pr-4"></i>Eventos
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="../Site/comunidade.php" class="nav-link">
                            <i class="fa fa-users pr-4"></i>Comunidade
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="../Site/regras.php" class="nav-link">
                            <i class="fa fa-exclamation-triangle pr-4"></i>Regras
                        </a>
                    </li>

                    <li class="divisor"></li>

                    <!-- Acesso ao login -->
                    <li class="nav-item d-flex align-items-center header-user">
                        <a href="login.php" class="nav-link">
                            <i class="fa fa-user-circle fa-2x"></i>
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </nav>
</header>

<main>
    <div class="login-container">

        <!-- Ícone ilustrativo -->
        <img src="/Zéfish/assets/img/perfil.png" alt="Perfil">

        <h3 class="text-white mb-3">Redefinir Senha</h3>

        <!-- Mensagem de retorno -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <!-- Formulário de redefinição -->
        <form method="POST">
            <input
                class="senha1"
                type="password"
                name="senha"
                placeholder="Nova senha"
                required
            >

            <div class="d-flex justify-content-center">
                <input class="submit" type="submit" value="Redefinir">
            </div>

            <div class="link-cadastro mt-3">
                <a href="login.php">Voltar ao Login</a>
            </div>
        </form>

    </div>
</main>

</body>
</html>
