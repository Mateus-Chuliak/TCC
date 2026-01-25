<?php
// Inicializa configurações globais, sessão e conexão
require_once __DIR__ . '/../../../Sistema/init.php';

// Carrega dependências externas
require_once __DIR__ . '/../../../vendor/autoload.php';


use Sistema\Servicos\EmailService;


// Redireciona usuários já autenticados conforme perfil
if (isset($_SESSION['logado']) && $_SESSION['logado'] === true) {

    if ($_SESSION['tipo'] === 'administrador') {
        header("Location: ../Admin/painel_admin.php");
        exit;
    }

    header("Location: ../Site/painel_usuario.php");
    exit;
}

// Mensagem de erro exibida na interface
$erro = "";

// Processa tentativa de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Dados informados pelo usuário
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // Busca usuário pelo e-mail
    $stmt = $conexao->prepare(
        "SELECT * FROM usuarios WHERE email = ? LIMIT 1"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Valida existência do usuário
    if ($result->num_rows === 0) {
        $erro = "E-mail ou senha inválidos.";
    } else {

        $usuario = $result->fetch_assoc();

        // Bloqueia conta após múltiplas tentativas falhas
        if ($usuario['tentativas'] >= 3) {
            $erro = "Conta bloqueada. Utilize a recuperação de senha.";
        }
        // Valida senha informada
        elseif (password_verify($senha, $usuario['senha'])) {

            // Reinicia contador de tentativas
            $reset = $conexao->prepare(
                "UPDATE usuarios SET tentativas = 0 WHERE email = ?"
            );
            $reset->bind_param("s", $email);
            $reset->execute();

            // Gera código temporário para autenticação em duas etapas
            $codigo = random_int(100000, 999999);
            $codigo_hash = password_hash($codigo, PASSWORD_DEFAULT);
            $expira = date("Y-m-d H:i:s", strtotime("+10 minutes"));
            
            $_SESSION['codigo_debug'] = $codigo;


            // Salva código e validade no banco
            $upd = $conexao->prepare(
                "UPDATE usuarios
                 SET codigo_2fa = ?, expiracao_2fa = ?
                 WHERE email = ?"
            );
            $upd->bind_param("sss", $codigo_hash, $expira, $email);
            $upd->execute();

            // Envia código de confirmação por e-mail
            EmailService::enviar(
                $email,
                "Código de segurança – ZéFish",
                "
                <h2>Confirmação de Segurança</h2>
                <p>Seu código é:</p>
                <h1 style='letter-spacing:5px;'>$codigo</h1>
                <p>Válido por 10 minutos.</p>
                ",
                "Seu código é: $codigo"
            );

            // Define sessão temporária para validação 2FA
            $_SESSION['email_temp'] = $email;
            session_regenerate_id(true);

            // Redireciona para a etapa de confirmação
            header("Location: confirmacao.php");
            exit;

        } else {

            // Incrementa tentativas em caso de falha
            $up = $conexao->prepare(
                "UPDATE usuarios
                 SET tentativas = tentativas + 1
                 WHERE email = ?"
            );
            $up->bind_param("s", $email);
            $up->execute();

            $erro = "E-mail ou senha inválidos.";
        }
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <title>ZéFish - Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicon -->
    <link rel="icon" href="/Zéfish/assets/img/fav_icon.png">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>

    <!-- CSS customizado -->
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">
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
    <div id="container" class="login-container">
        <img src="/Zéfish/assets/img/perfil.png" alt="Ícone de perfil">

        <form method="POST" action="">
            <div>
                <input class="email" type="email" name="email" placeholder="Digite seu e-mail" aria-label="E-mail" required>
            </div>

            <div>
                <input class="senha" type="password" name="senha" placeholder="Digite sua senha" aria-label="Senha" required>
            </div>

            <div class="link-recuperacao">
                <p><a href="recuperar_senha.php">Esqueci minha senha</a></p>
            </div>

            <div class="d-flex justify-content-center align-items-center" > 
                <input class="submit" type="submit" value="Entrar">
            </div>
            
            <div class="link-cadastro">
                <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
            </div>
            
            <!-- Mensagem de erro -->
            <?php if (!empty($erro)): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <?= $erro ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Fechar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            
        </form>
    </div>
</main>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="/Zéfish/assets/js/bootstrap.min.js"></script>

</body>
</html>
