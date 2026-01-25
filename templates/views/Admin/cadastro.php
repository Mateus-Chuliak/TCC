<?php
// Inicializa ambiente, configurações e conexão
require_once __DIR__ . '/../../../Sistema/init.php';

// Carrega dependências do Composer
require_once __DIR__ . '/../../../vendor/autoload.php';

use Sistema\Servicos\EmailService;

// Mensagem de feedback ao usuário
$mensagem = '';

// Processa envio do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitiza e prepara os dados recebidos
    $nome  = trim($_POST['usuario']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $cpf   = trim($_POST['cpf']);

    // Insere o usuário no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, senha, cpf)
            VALUES (?, ?, ?, ?)";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("ssss", $nome, $email, $senha, $cpf);

    if ($stmt->execute()) {

        // Envia e-mail de boas-vindas após cadastro
        EmailService::enviar(
            $email,
            'Bem-vindo ao ZéFish',
            "<h2>Conta criada com sucesso</h2><p>Olá <b>{$nome}</b>, seja bem-vindo ao ZéFish!</p>"
        );

        // Define mensagem de sucesso
        $mensagem = "Cadastro realizado com sucesso.";

    } else {
        // Define mensagem de erro
        $mensagem = "Erro ao cadastrar usuário.";
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
    <!-- Metadados básicos da página -->
    <title>ZéFish - Cadastro</title>
    <link rel="icon" href="../../../assets/img/fav_icon.png">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Estilos e ícones -->
    <link rel="stylesheet" href="../../../assets/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>

<body class="cadastro">

<!-- Cabeçalho com navegação principal -->
<header>
    <nav id="navbarHeader" class="navbar navbar-expand-xl fixed-top navbar-light navbar-transparente">
        <div class="container">

            <!-- Logotipo e link para a home -->
            <a href="../../../index.php" class="navbar-brand">
                <img src="../../../assets/img/logo.png" width="115" class="img-fluid" alt="Zéfish Logo">
            </a>

            <!-- Botão do menu mobile -->
            <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-principal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu principal -->
            <div class="collapse navbar-collapse" id="nav-principal">
                <ul class="navbar-nav">

                    <!-- Menu Pescaria -->
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

                    <!-- Links institucionais -->
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

<!-- Seção do formulário de cadastro -->
<section class="mt-5 pt-5">
    <div class="cadastro-container">

        <!-- Imagem de perfil ilustrativa -->
        <img src="../../../assets/img/perfil.png" alt="Perfil">

        <!-- Exibe mensagens de retorno -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info mt-3"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <!-- Formulário de cadastro -->
        <form method="POST">

            <input class="nome" type="text" name="usuario"
                   placeholder="Digite seu nome completo" required>

            <input class="email" type="email" name="email"
                   placeholder="Digite seu e-mail" required>

            <input class="senha" type="password" name="senha"
                   placeholder="Crie uma senha" required>

            <input class="cpf" type="text" name="cpf"
                   placeholder="Digite seu CPF" required>

            <input type="submit" value="Cadastrar"
                   class="submit btn btn-primary mt-3">

            <p class="mt-3">
                Já tem uma conta? <a href="login.php">Entrar</a>
            </p>

        </form>
    </div>
</section>

<!-- Scripts necessários para Bootstrap -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="../../../assets/js/bootstrap.min.js"></script>

</body>
</html>
