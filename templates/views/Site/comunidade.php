<?php
require_once __DIR__ . '/../../../Sistema/init.php';
// Inicializa sessão, autoload e conexão com o banco

$logado  = $_SESSION['logado'] ?? false;
$usuario = $logado ? $_SESSION['nome'] : 'Visitante';
// Define estado de autenticação do usuário

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($usuario === 'Visitante') {
        // Impede publicação sem autenticação
        header('Location: comunidade.php');
        exit;
    }

    $titulo   = $usuario;
    $mensagem = $_POST['mensagem'] ?? '';
    $imagem   = null;
    // Dados da nova publicação

    if (!empty($_FILES['imagem']['name'])) {

        $dirWeb  = '/Zéfish/uploads/comunidade/';
        $dirFis  = $_SERVER['DOCUMENT_ROOT'] . $dirWeb;
        // Diretório público e físico de upload

        if (!is_dir($dirFis)) {
            mkdir($dirFis, 0777, true);
        }

        $nomeArquivo = time() . '_' . basename($_FILES['imagem']['name']);
        $destino = $dirFis . $nomeArquivo;

        if (move_uploaded_file($_FILES['imagem']['tmp_name'], $destino)) {
            $imagem = $dirWeb . $nomeArquivo;
        }
    }

    $stmt = $conexao->prepare(
        'INSERT INTO comunidade (usuario, titulo, mensagem, imagem)
         VALUES (?, ?, ?, ?)'
    );
    $stmt->bind_param('ssss', $usuario, $titulo, $mensagem, $imagem);
    $stmt->execute();
    // Persiste a publicação

    header('Location: comunidade.php');
    exit;
}

$sql = 'SELECT * FROM comunidade ORDER BY data_post DESC';
$res = $conexao->query($sql);
$posts = $res->fetch_all(MYSQLI_ASSOC);
// Carrega publicações do mural
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>ZéFish - Comunidade</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" href="/Zéfish/assets/img/fav_icon.png">
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>
</head>

<body style="padding-top:120px; background:linear-gradient(135deg,#0CA6E9,#3CD3C1);">

<!-- ============================== HEADER ============================== -->
<header>
    <nav id="navbarHeader" class="navbar navbar-expand-xl fixed-top navbar-light navbar-transparente">
        <div class="container">

            <!-- Logo -->
            <a href="../../../index.php" class="navbar-brand">
                <img src="/Zéfish/assets/img/logo.png" width="115" class="img-fluid">
            </a>

            <!-- Botão mobile -->
            <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-principal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse" id="nav-principal">
                <ul class="navbar-nav">

                    <!-- Pescaria -->
                    <li class="nav-item dropdown">
                        <a href="pescaria.php" class="nav-link dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-anchor pr-4"></i>Pescaria
                        </a>
                        <div class="dropdown-menu">
                            <a href="reserva.php" class="dropdown-item">Faça sua reserva</a>
                            <a href="pescaria.php" class="dropdown-item">Tipos de pesca</a>
                            <a href="descricao.php" class="dropdown-item">Nossos Peixes</a>
                        </div>
                    </li>

                    <!-- Gastronomia -->
                    <li class="nav-item">
                        <a href="cardapio.php" class="nav-link">
                            <i class="fa fa-cutlery pr-4"></i>Gastronomia
                        </a>
                    </li>

                    <!-- Eventos -->
                    <li class="nav-item">
                        <a href="eventos.php" class="nav-link">
                            <i class="fa fa-calendar pr-4"></i>Eventos
                        </a>
                    </li>

                    <!-- Comunidade -->
                    <li class="nav-item">
                        <a href="comunidade.php" class="nav-link">
                            <i class="fa fa-users pr-4"></i>Comunidade
                        </a>
                    </li>

                    <!-- Regras -->
                    <li class="nav-item">
                        <a href="regras.php" class="nav-link">
                            <i class="fa fa-exclamation-triangle pr-4"></i>Regras
                        </a>
                    </li>

                    <!-- Divisor -->
                    <li class="divisor"></li>

                    <!-- Login Admin -->
                    <li class="nav-item d-flex align-items-center white header-user">
                        <a href="../Admin/login.php" class="nav-link">
                            <i class="fa fa-user-circle fa-2x"></i>
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </nav>
</header>

<section class="container mt-5">
    <h2 class="text-center text-white fw-bold mb-4">Comunidade ZéFish</h2>

    <div class="text-center mb-4">
        <?php if ($usuario === 'Visitante'): ?>
            <button class="btn btn-primary" onclick="alert('Você precisa estar logado para postar!');">
                Criar Publicação
            </button>
        <?php else: ?>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPost">
                Criar Publicação
            </button>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="modalPost">
        <div class="modal-dialog">
            <form class="modal-content" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Postagem</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <textarea name="mensagem" class="form-control mb-3" rows="4" required></textarea>
                    <input type="file" name="imagem" class="form-control">
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Publicar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mt-4">
        <?php foreach ($posts as $p): ?>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <?php if ($p['imagem']): ?>
                        <img src="<?= htmlspecialchars($p['imagem']) ?>" class="card-img-top" style="height:210px;object-fit:cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5><?= htmlspecialchars($p['titulo']) ?></h5>
                        <p><?= nl2br(htmlspecialchars($p['mensagem'])) ?></p>
                        <small class="text-muted">
                            <?= date('d/m/Y H:i', strtotime($p['data_post'])) ?>
                        </small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (empty($posts)): ?>
            <p class="text-center text-white mt-5">Nenhuma postagem ainda.</p>
        <?php endif; ?>
    </div>
</section>

<footer class="text-center text-white mt-5 mb-4">
    <small>&copy; 2025 ZéFish. Todos os direitos reservados.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
