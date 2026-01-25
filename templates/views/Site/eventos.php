<?php
// Inicializa o sistema (autoload, sessão, configurações e conexão)
require_once __DIR__ . '/../../../Sistema/init.php';

// Inicializa o sistema (autoload, sessão, configurações e conexão)
// Consulta todos os eventos ordenados pela data mais recente
$sql = "SELECT * FROM eventos ORDER BY data_evento DESC";
$res = $conexao->query($sql);

// Converte o resultado em array associativo
$eventos = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <!-- Meta tags essenciais -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Eventos - ZéFish</title>

    <!-- Ícone do site -->
    <link rel="icon" href="/Zéfish/assets/img/fav_icon.png">

    <!-- Bootstrap principal -->
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">

    <!-- Ícones FontAwesome -->
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>

    <!-- CSS global -->
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">

    <style>
        /* ====================== ESTILOS ESPECÍFICOS DA PÁGINA ====================== */

        /* Imagens do carrossel */
        .carousel-item img {
            height: 420px;
            object-fit: cover;
            border-radius: 10px;
        }

        /* CARD dos eventos */
        .event-card {
            height: 100%;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .event-card img {
            width: 100%;
            height: 230px;
            object-fit: cover;
        }

        .event-card .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        /* Mantém o botão na parte inferior */
        .event-card .btn {
            margin-top: auto;
        }

        /* Fundo da página */
        #evento-fundo {
            background: linear-gradient(to bottom, #001f3f 0%, #0074b7 50%, #7db7da 100%);
            min-height: 100vh;
            color: black;
        }

        /* Imagem do banner do carrossel */
        .carousel-img {
            width: 100%;
            height: 600px;
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-color: #000;
            border-radius: 10px;
        }
    </style>
</head>

<body id="evento-fundo">

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

<!-- ============================== CONTEÚDO PRINCIPAL ============================== -->
<div class="container" style="padding-top:150px;">

    <!-- Título principal -->
    <h1 class="text-white text-center mb-5">Eventos do ZéFish</h1>

    <!-- ========================= CARROSSEL DE EVENTOS ========================= -->
    <div id="eventos_carousel" class="carousel slide mb-5" data-ride="carousel">

        <div class="carousel-inner">

            <?php if (count($eventos) > 0): ?>

                <!-- Loop exibe todos os eventos no carrossel -->
                <?php foreach ($eventos as $index => $ev): ?>

                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <!-- Banner em background -->
                        <div class="carousel-img"
                             style="background-image: url('/Zéfish/uploads/eventos/<?= htmlspecialchars($ev['imagem']) ?>');">
                        </div>
                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <!-- Caso não haja eventos -->
                <div class="carousel-item active">
                    <img src="/Zéfish/assets/img/sem-foto.png" class="d-block w-100" alt="">
                    <div class="carousel-caption">
                        <h3>Nenhum evento disponível</h3>
                    </div>
                </div>

            <?php endif; ?>

        </div>

        <!-- Controles do carrossel -->
        <a class="carousel-control-prev" href="#eventos_carousel" data-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </a>

        <a class="carousel-control-next" href="#eventos_carousel" data-slide="next">
            <span class="carousel-control-next-icon"></span>
        </a>

    </div>

    <!-- ========================= LISTA DE EVENTOS ========================= -->
    <div class="row g-4">

        <?php foreach ($eventos as $ev): ?>
            <div class="col-md-4">

                <!-- Card do evento -->
                <div class="card event-card shadow">

                    <!-- Imagem do evento -->
                    <img src="/Zéfish/uploads/eventos/<?= htmlspecialchars($ev['imagem']) ?>"
                         class="card-img-top">

                    <div class="card-body">

                        <!-- Título -->
                        <h5 class="card-title"><?= htmlspecialchars($ev['titulo']) ?></h5>

                        <!-- Mini-descrição com limite de 100 caracteres -->
                        <p class="card-text">
                            <?= nl2br(htmlspecialchars(substr($ev['descricao'], 0, 100))) ?>...
                        </p>

                        <!-- Botão que abre modal -->
                        <button class="btn btn-primary w-100"
                                data-bs-toggle="modal"
                                data-bs-target="#modalEvento<?= $ev['id_evento'] ?>">
                            Ver detalhes
                        </button>

                    </div>

                </div>
            </div>
        <?php endforeach; ?>

    </div>

    <!-- ========================= MODAIS DOS EVENTOS ========================= -->
    <?php foreach ($eventos as $ev): ?>
        <div class="modal fade" id="modalEvento<?= $ev['id_evento'] ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <!-- Cabeçalho -->
                    <div class="modal-header d-flex justify-content-center">
                        <h5 class="modal-title"><?= htmlspecialchars($ev['titulo']) ?></h5>
                    </div>

                    <!-- Corpo -->
                    <div class="modal-body">
                        <div class="row">

                            <!-- Imagem -->
                            <div class="col-md-4 text-center mb-3">
                                <?php if ($ev['imagem']): ?>
                                    <img src="/Zéfish/uploads/eventos/<?= htmlspecialchars($ev['imagem']) ?>"
                                         class="img-fluid rounded">
                                <?php endif; ?>
                            </div>

                            <!-- Texto -->
                            <div class="col-md-8">
                                <p><?= nl2br(htmlspecialchars($ev['descricao'])) ?></p>

                                <p><strong>Data:</strong>
                                    <?= date("d/m/Y", strtotime($ev['data_evento'])) ?>
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Rodapé modal -->
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>

                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<!-- ============================== RODAPÉ ============================== -->
<footer id="footer">
    <div class="container">

        <div class="row align-items-center mb-3">

            <!-- Logo -->
            <div class="col-md-4 text-center text-md-left mb-3">
                <a href="../../../index.php">
                    <img src="/Zéfish/assets/img/logo.png" height="60" alt="ZéFish">
                </a>
            </div>

            <!-- Texto -->
            <div class="col-md-4 text-center">
                <p>O lugar ideal para quem ama pescar, relaxar e viver momentos inesquecíveis em família.</p>
            </div>

            <!-- Redes sociais -->
            <div class="col-md-4 d-flex justify-content-center justify-content-md-end">
                <ul class="socials d-flex list-unstyled m-0 p-0">
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/instagram.png" height="28"></a></li>
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/facebook.png" height="28"></a></li>
                    <li class="mx-2"><a href="#"><img src="/Zéfish/assets/img/twitter.png" height="28"></a></li>
                </ul>
            </div>

        </div>

        <!-- Links -->
        <div class="row">
            <div class="col text-center">
                <a href="../../../index.php">Início</a> |
                <a href="/Zéfish/templates/views/Site/regras.php">Regras</a> |
                <a href="#">Gastronomia</a> |
                <a href="../../../index.php">Localização</a>
            </div>
        </div>

        <!-- Direitos autorais -->
        <div class="row mt-4">
            <div class="col text-center">
                <small>&copy; 2025 ZéFish. Todos os direitos reservados.</small>
            </div>
        </div>

    </div>
</footer>

<!-- Scripts Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts Bootstrap 4 (compatibilidade com carrossel antigo) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
<script src="/Zéfish/assets/js/bootstrap.min.js"></script>

</body>
</html>
