<?php
// Inicializa configurações globais, sessão e conexão com o banco
require_once __DIR__ . '/../../../Sistema/init.php';

// Carrega dependências externas via Composer
require_once __DIR__ . '/../../../vendor/autoload.php';

/* ================= DEBUG CONTROLADO ================= */

use Sistema\Servicos\EmailService;

// Variáveis de feedback ao usuário
$erro = '';
$sucesso = '';

// Valida se o fluxo 2FA foi iniciado corretamente
if (!isset($_SESSION['email_temp'])) {
    header("Location: login.php");
    exit;
}

// E-mail armazenado temporariamente no processo de autenticação
$emailTemp = $_SESSION['email_temp'];

// Processa solicitação de reenvio do código 2FA
if (isset($_GET['reenviar'])) {

    // Gera um novo código e define validade
    $codigo = random_int(100000, 999999);
    $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT);
    $expira = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Atualiza código e expiração no banco
    $upd = $conexao->prepare(
        "UPDATE usuarios
         SET codigo_2fa = ?, expiracao_2fa = ?
         WHERE email = ?"
    );
    $upd->bind_param("sss", $codigo_hash, $expira, $emailTemp);
    $upd->execute();

    // Envia o novo código para o e-mail do usuário
    EmailService::enviar(
        $emailTemp,
        "Novo código – ZéFish",
        "
        <h2>Novo código de confirmação</h2>
        <p>Use apenas este código:</p>
        <h1 style='letter-spacing:5px;'>$codigo</h1>
        <p>Válido por 5 minutos.</p>
        ",
        "Seu novo código é: $codigo"
    );

    // Mensagem de confirmação do reenvio
    $sucesso = "Um novo código foi enviado.";
}

// Processa a validação do código informado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Código digitado pelo usuário
    $codigo_postado = trim($_POST['codigo']);

    // Recupera dados do usuário para validação
    $stmt = $conexao->prepare(
        "SELECT id_usuario, nome, email, tipo, codigo_2fa, expiracao_2fa
         FROM usuarios WHERE email = ? LIMIT 1"
    );
    $stmt->bind_param("s", $emailTemp);
    $stmt->execute();
    $res = $stmt->get_result();

    // Verifica se o usuário existe
    if ($res->num_rows === 0) {

        $erro = "Erro ao validar usuário.";
        unset($_SESSION['email_temp']);

    } else {

        $usuario = $res->fetch_assoc();

        // Valida código informado e prazo de expiração
        if (
            password_verify($codigo_postado, $usuario['codigo_2fa']) &&
            strtotime($usuario['expiracao_2fa']) > time()
        ) {

            // Regenera o ID da sessão após autenticação
            session_regenerate_id(true);

            // Define dados da sessão autenticada
            $_SESSION['logado']     = true;
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nome']       = $usuario['nome'];
            $_SESSION['email']      = $usuario['email'];
            $_SESSION['tipo']       = $usuario['tipo'];

            // Remove marcador temporário do 2FA
            unset($_SESSION['email_temp']);

            // Limpa dados de autenticação em duas etapas
            $clear = $conexao->prepare(
                "UPDATE usuarios
                 SET codigo_2fa = NULL,
                     expiracao_2fa = NULL,
                     tentativas = 0
                 WHERE id_usuario = ?"
            );
            $clear->bind_param("i", $usuario['id_usuario']);
            $clear->execute();

            // Define destino conforme perfil do usuário
            $destino = ($usuario['tipo'] === 'administrador')
                ? "../Admin/painel_admin.php"
                : "../Site/comunidade.php";

            header("Location: $destino");
            exit;

        } else {

            // Incrementa contador de tentativas inválidas
            $up = $conexao->prepare(
                "UPDATE usuarios
                 SET tentativas = tentativas + 1
                 WHERE id_usuario = ?"
            );
            $up->bind_param("i", $usuario['id_usuario']);
            $up->execute();

            $erro = "Código inválido ou expirado.";
        }
    }

    // Registros básicos para auditoria
    error_log("2FA tentativa de confirmação processada.");
}
$codigo_debug = $_SESSION['codigo_debug'] ?? null;

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <title>ZéFish - Confirmação</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="../../../assets/img/fav_icon.png">
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">

    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>
</head>

<body class="login">

<!-- HEADER -->
    <header>
        <nav id="navbarHeader" class="navbar navbar-expand-xl fixed-top navbar-light navbar-transparente">
            <div class="container">

                <!-- Logo -->
                <a href="../../../index.php" class="navbar-brand">
                    <img src="../../../assets/img/logo.png" width="115" class="img-fluid" alt="Zéfish Logo">
                </a>

                <!-- Toggle mobile -->
                <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-principal">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Menu -->
                <div class="collapse navbar-collapse" id="nav-principal">
                    <ul class="navbar-nav">

                        <!-- Pescaria -->
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

                        <!-- Gastronomia -->
                        <li class="nav-item">
                            <a href="../Site/cardapio.php" class="nav-link">
                                <i class="fa fa-cutlery pr-4"></i>Gastronomia
                            </a>
                        </li>

                        <!-- Eventos -->
                       <li class="nav-item"> 
                            <a href="../Site/eventos.php" class="nav-link"  > 
                                 <i class="fa fa-calendar pr-4" aria-hidden="true"></i>Eventos
                            </a>
                        </li>


                        <!-- Comunidade -->
                        <li class="nav-item "> 
                            <a href="../Site/comunidade.php" class="nav-link"> 
                                <i class="fa fa-users pr-4" aria-hidden="true"></i>Comunidade
                            </a>
                        </li>

                        <!-- Regras -->
                        <li class="nav-item">
                            <a href="../Site/regras.php" class="nav-link">
                                <i class="fa fa-exclamation-triangle pr-4"></i>Regras
                            </a>
                        </li>
                            
                        <li class="divisor"></li>
                        
                        <!-- Login -->
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

        <img src="/Zéfish/assets/img/perfil.png" alt="Perfil">

        <h3 class="text-white mb-3">Confirmação de Segurança</h3>
        <p class="text-white">Digite o código enviado ao seu e-mail.</p>
        <?php if (isset($codigo_debug)): ?>
            <p style="color:yellow; font-size:22px;">
                DEBUG CÓDIGO: <?= $codigo_debug ?>
            </p>
        <?php endif; ?>
        <?php if ($erro): ?>
            <p class="erro-login ativo"><?= $erro ?></p>
        <?php elseif ($sucesso): ?>
            <div class="alert alert-success"><?= $sucesso ?></div>
        <?php endif; ?>

        <form method="POST">
            <input
                type="text"
                name="codigo"
                maxlength="6"
                class="email"
                placeholder="Código de 6 dígitos"
                style="text-align:center;"
                required
            >

            <input type="submit" value="Confirmar" class="submit">
        </form>

        <div class="text-center mt-3">
            <a href="?reenviar=1" class="text-white">
                Reenviar código
            </a>
        </div>

    </div>
</main>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="../../../assets/js/bootstrap.min.js"></script>

</body>
</html>
