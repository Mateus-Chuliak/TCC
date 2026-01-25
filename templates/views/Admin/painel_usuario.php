<?php
// Inicializa sessão, variáveis globais e conexão com o banco
require_once __DIR__ . '/../../../Sistema/init.php';

// Impede acesso direto sem autenticação
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

// Dados básicos do usuário autenticado
$usuario    = $_SESSION['nome'];
$id_usuario = $_SESSION['id_usuario'];
$email      = $_SESSION['email'];

// Consulta os últimos agendamentos do usuário
$sql = "SELECT data_reserva AS data, horario, status
        FROM reservas
        WHERE usuario_id = ?
        ORDER BY data_reserva DESC
        LIMIT 5";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();

// Resultado convertido em array associativo
$agendamentos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>ZéFish - Painel do Usuário</title>

    <!-- Arquivos visuais e dependências -->
    <link rel="icon" href="/ZéFish/assets/img/fav_icon.png">
    <link rel="stylesheet" href="/ZéFish/assets/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/ZéFish/assets/css/style.css">

    <style>
        /* Estilo específico do painel do usuário */
        body { padding-top: 90px; background-color: #f8f9fa; }
        .painel { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 0 10px rgba(0,0,0,.1); }
        .perfil img { width: 100px; border-radius: 50%; }
        .info, .agendamentos { margin-top: 30px; }
        .btn-sair { background: #c0392b; color: white; border-radius: 5px; }
        .btn-sair:hover { background: #e74c3c; }
    </style>
</head>

<body class="granulado">

<header>
    <nav id="navbarHeader" class="navbar navbar-expand-xl fixed-top navbar-light navbar-transparente">
        <div class="container">

            <!-- Identidade visual -->
            <a href="../../../index.php" class="navbar-brand">
                <img src="/ZéFish/assets/img/logo.png" width="115" class="img-fluid">
            </a>

            <!-- Controle de navegação responsiva -->
            <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-principal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu principal -->
            <div class="collapse navbar-collapse" id="nav-principal">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a href="pescaria.php" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-anchor pr-4"></i>Pescaria
                        </a>
                        <div class="dropdown-menu">
                            <a href="../Site/reserva.php" class="dropdown-item">Faça sua reserva</a>
                            <a href="../Site/pescaria.php" class="dropdown-item">Tipos de pesca</a>
                            <a href="../Site/descricao.php" class="dropdown-item">Nossos Peixes</a>
                        </div>
                    </li>

                    <li class="nav-item"><a href="../Site/cardapio.php" class="nav-link"><i class="fa fa-cutlery pr-4"></i>Gastronomia</a></li>
                    <li class="nav-item"><a href="../Site/eventos.php" class="nav-link"><i class="fa fa-calendar pr-4"></i>Eventos</a></li>
                    <li class="nav-item"><a href="../Site/comunidade.php" class="nav-link"><i class="fa fa-users pr-4"></i>Comunidade</a></li>
                    <li class="nav-item"><a href="../Site/regras.php" class="nav-link"><i class="fa fa-exclamation-triangle pr-4"></i>Regras</a></li>

                    <!-- Acesso ao perfil -->
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

<div class="container">
    <div class="painel">

        <!-- Dados visuais do usuário -->
        <div class="perfil text-center">
            <img src="/ZéFish/assets/img/perfil.png" alt="Perfil">
            <h2><?= htmlspecialchars($usuario) ?></h2>
            <p><i class="fa fa-envelope"></i> <strong><?= htmlspecialchars($email) ?></strong></p>
        </div>

        <!-- Informações estáticas da conta -->
        <div class="info">
            <h4>Informações da conta</h4>
            <p><strong>Status:</strong> Usuário ativo</p>
            <p><strong>Tipo:</strong> Padrão</p>
            <p><strong>Último login:</strong> <?= date("d/m/Y H:i") ?></p>
        </div>

        <!-- Histórico recente de reservas -->
        <div class="agendamentos">
            <h4>Últimos Agendamentos</h4>

            <?php if ($agendamentos): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Horário</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($agendamentos as $a): ?>
                            <tr>
                                <td><?= date("d/m/Y", strtotime($a['data'])) ?></td>
                                <td><?= htmlspecialchars($a['horario']) ?></td>
                                <td><?= htmlspecialchars($a['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Você ainda não possui agendamentos.</p>
            <?php endif; ?>
        </div>

        <!-- Encerramento de sessão -->
        <div class="text-center mt-4">
            <button class="btn-sair" id="sair">Sair da conta</button>
        </div>

    </div>
</div>

<script>
// Confirmação simples para logout voluntário
document.getElementById("sair").addEventListener("click", () => {
    if (confirm("Deseja realmente sair da sua conta?")) {
        window.location.href = "../../../index.php";
    }
});
</script>

</body>
</html>
