<?php
require_once __DIR__ . '/../../../Sistema/init.php'; 
// Importa o arquivo de inicialização do sistema (conexão, sessões, configs)

// Busca os pratos cadastrados para o restaurante específico
$restaurante_id = 1;

$stmt = $conexao->prepare("SELECT * FROM pratos WHERE restaurante_id = ? ORDER BY nome ASC");
$stmt->bind_param("i", $restaurante_id);
$stmt->execute();

// Obtém todos os pratos em formato de array associativo
$pratos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cardápio | ZéFish</title>

    <!-- Bootstrap + CSS Customizado -->
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">

    <!-- Ícones Font Awesome -->
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>
    
<style>
/* Card principal de cada prato */
.prato-card {
    color: black;
    border-radius: 15px;
    transition: all .3s ease;
    overflow: hidden;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    z-index: 1;
}

/* Aumenta levemente o card no hover */
.prato-card:hover {
    transform: scale(1.05);
    z-index: 10;
}

/* Imagem do prato com corte proporcional */
.prato-img {
    height: 180px;
    object-fit: cover;
    width: 100%;
}

/* Corpo do card alinhado verticalmente */
.card-body {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

/* Descrição com limite de 4 linhas + efeito fade */
.descricao-prato {
    display: -webkit-box;
    -webkit-line-clamp: 4;
    -webkit-box-orient: vertical;
    overflow: hidden;
    font-size: .95rem;
    line-height: 1.4em;
    max-height: calc(1.4em * 4);
    position: relative;
    transition: max-height .4s ease;
}

/* Gradiente no final da descrição */
.descricao-prato::after {
    content: "";
    position: absolute;
    left: 0; right: 0; bottom: 0;
    height: 4.2em;
    background: linear-gradient(to bottom, rgba(255,255,255,0), #ffffff);
}

/* Remove limite de linhas ao passar o mouse */
.prato-card:hover .descricao-prato {
    max-height: 500px;
    -webkit-line-clamp: unset;
}

.prato-card:hover .descricao-prato::after {
    opacity: 0;
}

/* Preço sempre fixo ao final do card */
.price-tag {
    margin-top: auto;
    font-weight: 600;
    font-size: 1rem;
}

/* Banner do cardápio */
.hero-cardapio { 
    background: url('/Zéfish/assets/img/cardapio-bg.jpg') center/cover no-repeat;
    max-height: 650px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Container centralizado dos cards */
.cardapio-wrapper {
    background: #ffffff;
    border-radius: 18px;
    padding: 20px;
    margin: 0 auto;
    margin-top: -10px;
    margin-bottom: 40px;
    max-width: 1000px;
    box-shadow: 0 6px 25px rgba(0,0,0,0.12);
}

/* Fundo com degradê azul */
#restaurante-fundo {
    background: linear-gradient(to bottom, #001f3f, #0074b7 50%, #7db7da 100%);
    min-height: 100vh;
}
</style>
</head>

<<body id="restaurante-fundo">
<header>
    <nav id="navbarHeader" class="navbar navbar-expand-xl fixed-top navbar-light navbar-transparente">
        <div class="container">

            <!-- Logo -->
            <a href="../../../index.php" class="navbar-brand">
                <img src="/Zéfish/assets/img/logo.png" width="115">
            </a>

            <!-- Botão mobile -->
            <button class="navbar-toggler" data-toggle="collapse" data-target="#nav-principal">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navegação -->
            <div class="collapse navbar-collapse" id="nav-principal">
                <ul class="navbar-nav">

                    <!-- Menus principais -->
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

                    <li class="nav-item"><a href="cardapio.php" class="nav-link">
                        <i class="fa fa-cutlery pr-4"></i>Gastronomia</a>
                    </li>

                    <li class="nav-item"><a href="eventos.php" class="nav-link">
                        <i class="fa fa-calendar pr-4"></i>Eventos</a>
                    </li>

                    <li class="nav-item"><a href="comunidade.php" class="nav-link">
                        <i class="fa fa-users pr-4"></i>Comunidade</a>
                    </li>

                    <li class="nav-item"><a href="regras.php" class="nav-link">
                        <i class="fa fa-exclamation-triangle pr-4"></i>Regras</a>
                    </li>

                    <!-- Ícone do usuário -->
                    <li class="nav-item">
                        <a href="../Admin/login.php" class="nav-link">
                            <i class="fa fa-user-circle fa-2x"></i>
                        </a>
                    </li>

                </ul>
            </div>

        </div>
    </nav>
</header>

 <!-- Main -->
 <main style="padding-top:120px">
    <div class="cardapio-wrapper p-5">

        <!-- Banner -->
        <section class="hero-cardapio mb-5">
            <h1 class="display-4 fw-bold ">Nosso Cardápio</h1>
        </section>

        <!-- Lista dos pratos -->
        <div class="container pb-5">
            <div class="row gy-4 gx-4">

                <?php foreach ($pratos as $p): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card prato-card shadow-sm">

                        <!-- Imagem -->
                        <?php if ($p['imagem']): ?>
                            <img src="/Zéfish/uploads/pratos/<?= $p['imagem'] ?>" 
                                 class="prato-img">
                        <?php else: ?>
                            <img src="/Zéfish/assets/img/sem-foto.png" 
                                 class="prato-img">
                        <?php endif; ?>

                        <!-- Conteúdo -->
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($p['nome']) ?></h5>

                            <p class="mt-2 text-muted small descricao-prato">
                                <?= nl2br(htmlspecialchars($p['descricao'])) ?>
                            </p>

                            <!-- Preço -->
                            <p class="price-tag">R$ <?= number_format($p['preco'], 2, ',', '.') ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Caso não existam pratos -->
                <?php if (count($pratos) === 0): ?>
                    <p class="text-center mt-5">Nenhum prato encontrado.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</main>

<!-- Rodapé -->
<footer id="footer">
    <div class="container">

        <!-- Logo, texto e redes sociais -->
        <div class="row align-items-center mb-3">
            <div class="col-md-4 text-center text-md-left mb-3">
                <a href="../../../index.php">
                    <img src="/Zéfish/assets/img/logo.png" height="60">
                </a>
            </div>

            <div class="col-md-4 text-center">
                <p>O lugar ideal para quem ama pescar e relaxar.</p>
            </div>

            <div class="col-md-4 d-flex justify-content-center justify-content-md-end">
                <ul class="socials d-flex list-unstyled">
                    <li class="mx-2"><img src="/Zéfish/assets/img/instagram.png" height="28"></li>
                    <li class="mx-2"><img src="/Zéfish/assets/img/facebook.png" height="28"></li>
                    <li class="mx-2"><img src="/Zéfish/assets/img/twitter.png" height="28"></li>
                </ul>
            </div>
        </div>

        <!-- Links -->
        <div class="text-center">
            <a href="../../../index.php">Início</a> |
            <a href="/Zéfish/templates/views/Site/regras.php">Regras</a> |
            <a href="#">Gastronomia</a> |
            <a href="../../../index.php">Localização</a>
        </div>

        <!-- Direitos autorais -->
        <div class="text-center mt-4">
            <small>&copy; 2025 ZéFish. Todos os direitos reservados.</small>
        </div>

    </div>
</footer>



    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="/Zéfish/assets/js/bootstrap.min.js"></script>
</body>
</html>