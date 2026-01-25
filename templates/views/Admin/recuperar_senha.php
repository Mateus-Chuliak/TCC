<?php
// Inicializa sessão, configurações globais e conexão com o banco
require_once __DIR__ . '/../../../Sistema/init.php';
require_once __DIR__ . '/../../../vendor/autoload.php';

use Sistema\Servicos\EmailService;

$msg = "";

// Processa apenas requisições POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // E-mail informado para recuperação
    $email = trim($_POST['email']);

    // Consulta usuário pelo e-mail
    $sql = "SELECT id_usuario FROM usuarios WHERE email = ? LIMIT 1";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Caso não exista usuário com o e-mail informado
    if ($result->num_rows === 0) {
        $msg = "Nenhuma conta encontrada com este e-mail.";
    } else {

        // Identificador do usuário encontrado
        $user = $result->fetch_assoc();
        $id = $user['id_usuario'];

        // Gera token seguro para redefinição de senha
        $token = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token);
        $expira = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Atualiza token e validade no banco
        $sql_update = "
            UPDATE usuarios 
            SET token_reset = ?, expiracao_reset = ?
            WHERE id_usuario = ?
        ";
        $stmt2 = $conexao->prepare($sql_update);
        $stmt2->bind_param("ssi", $token_hash, $expira, $id);
        $stmt2->execute();

        // Define URL base conforme ambiente
        $baseUrl = $isLocalhost ? URL_DESENVOLVIMENTO : URL_PRODUCAO;

        // Link completo de redefinição
        $link = $baseUrl . "/templates/views/Admin/redefinir.php?token=" . $token;

        // Envia e-mail com instruções de redefinição
        EmailService::enviar(
            $email,
            'Redefinição de senha - ZéFish',
            "
            <h2>Redefinição de Senha</h2>
            <p>Clique no botão abaixo para redefinir sua senha:</p>

            <p style='text-align:center;'>
                <a href='{$link}'
                   style='display:inline-block;
                          padding:12px 20px;
                          background:#2E82E3;
                          color:#fff;
                          text-decoration:none;
                          border-radius:6px;
                          font-weight:bold;'>
                   Redefinir senha
                </a>
            </p>

            <p>Ou copie e cole o link no navegador:</p>
            <p>{$link}</p>

            <p><small>Este link expira em 1 hora.</small></p>
            ",
            "Acesse o link para redefinir sua senha: {$link}"
        );

        // Libera recursos
        $stmt2->close();
        $stmt->close();

        // Mensagem de confirmação ao usuário
        $msg = "Enviamos um link de recuperação para seu e-mail.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>ZéFish - Redefinir Senha</title>

    <!-- Arquivos visuais -->
    <link rel="icon" href="/Zéfish/assets/img/fav_icon.png">
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
                            <a href="reserva.php" class="dropdown-item">Faça sua reserva</a>
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
                    <li class="nav-item d-flex align-items-center white header-user">
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

        <!-- Avatar ilustrativo -->
        <img src="/Zéfish/assets/img/perfil.png" alt="Perfil">

        <h3 class="mb-4 text-white">Recuperar Senha</h3>

        <!-- Mensagem de retorno -->
        <?php if (!empty($msg)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <!-- Formulário de solicitação -->
        <form method="POST">
            <input class="email" type="email" name="email" placeholder="Digite seu e-mail" required>
            <input class="submit" type="submit" value="Enviar Link">

            <div class="link-cadastro mt-3">
                <a href="login.php">Voltar ao Login</a>
            </div>
        </form>

    </div>
</main>

</body>
</html>
