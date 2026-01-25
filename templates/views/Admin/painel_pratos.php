<?php
// Inicializa sessão, configurações e conexão com o banco
require_once __DIR__ . '/../../../Sistema/init.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Restringe acesso ao painel apenas para usuários administradores
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: painel_usuario.php");
    exit();
}

// Identifica o restaurante do administrador logado
$restaurante_id = $_SESSION['restaurante_id'] ?? 1;

// Gera e mantém token CSRF na sessão
function gerar_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Valida token CSRF recebido via formulário
function verificar_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Sanitização básica de entradas
function limpar($v) {
    return trim((string)$v);
}

$csrf = gerar_csrf_token();

// Processa requisições POST do painel
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Bloqueia ações sem token CSRF válido
    if (!verificar_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['mensagem'] = "Token inválido.";
        $_SESSION['tipo_mensagem'] = "danger";
        header("Location: painel_pratos.php");
        exit();
    }

    $acao = $_POST['acao'] ?? '';

    // Cadastro de novo prato
    if ($acao === "adicionar_prato") {

        $nome      = limpar($_POST['nome']);
        $descricao = limpar($_POST['descricao']);
        $preco     = limpar($_POST['preco']);
        $imagem    = null;

        // Processa upload de imagem, se enviada
        if (!empty($_FILES['imagem']['name'])) {

            $dir = __DIR__ . '/../../../uploads/pratos/';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }

            $nomeFinal = time() . "_" . basename($_FILES["imagem"]["name"]);
            move_uploaded_file($_FILES["imagem"]["tmp_name"], $dir . $nomeFinal);

            $imagem = $nomeFinal;
        }

        try {
            $stmt = $conexao->prepare(
                "INSERT INTO pratos (restaurante_id, nome, descricao, preco, imagem)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->bind_param("issds", $restaurante_id, $nome, $descricao, $preco, $imagem);
            $stmt->execute();

            $_SESSION['mensagem'] = "Prato cadastrado com sucesso!";
            $_SESSION['tipo_mensagem'] = "success";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao cadastrar prato.";
            $_SESSION['tipo_mensagem'] = "danger";
        }

        header("Location: painel_pratos.php");
        exit();
    }

    // Remoção de prato
    if ($acao === "remover_prato") {

        $id = (int) $_POST['id_prato'];

        try {
            $stmt = $conexao->prepare(
                "DELETE FROM pratos WHERE id_prato = ? AND restaurante_id = ?"
            );
            $stmt->bind_param("ii", $id, $restaurante_id);
            $stmt->execute();

            $_SESSION['mensagem'] = "Prato removido.";
            $_SESSION['tipo_mensagem'] = "success";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao remover prato.";
            $_SESSION['tipo_mensagem'] = "danger";
        }

        header("Location: painel_pratos.php");
        exit();
    }

    // Atualização de prato existente
    if ($acao === "editar_prato") {

        $id        = (int) $_POST['id_prato'];
        $nome      = limpar($_POST['nome']);
        $descricao = limpar($_POST['descricao']);
        $preco     = limpar($_POST['preco']);

        $novaImagem = null;

        // Upload de nova imagem, se fornecida
        if (!empty($_FILES['imagem']['name'])) {
            $nomeFinal = time() . "_" . basename($_FILES["imagem"]["name"]);
            move_uploaded_file(
                $_FILES["imagem"]["tmp_name"],
                __DIR__ . '/../../../uploads/pratos/' . $nomeFinal
            );
            $novaImagem = $nomeFinal;
        }

        try {
            if ($novaImagem) {
                $stmt = $conexao->prepare(
                    "UPDATE pratos
                     SET nome = ?, descricao = ?, preco = ?, imagem = ?
                     WHERE id_prato = ? AND restaurante_id = ?"
                );
                $stmt->bind_param("ssdssi", $nome, $descricao, $preco, $novaImagem, $id, $restaurante_id);

            } else {
                $stmt = $conexao->prepare(
                    "UPDATE pratos
                     SET nome = ?, descricao = ?, preco = ?
                     WHERE id_prato = ? AND restaurante_id = ?"
                );
                $stmt->bind_param("ssdii", $nome, $descricao, $preco, $id, $restaurante_id);
            }

            $stmt->execute();

            $_SESSION['mensagem'] = "Prato atualizado.";
            $_SESSION['tipo_mensagem'] = "success";

        } catch (Exception $e) {
            $_SESSION['mensagem'] = "Erro ao atualizar prato.";
            $_SESSION['tipo_mensagem'] = "danger";
        }

        header("Location: painel_pratos.php");
        exit();
    }
}

// Consulta pratos do restaurante
$stmt = $conexao->prepare(
    "SELECT * FROM pratos WHERE restaurante_id = ? ORDER BY id_prato DESC"
);
$stmt->bind_param("i", $restaurante_id);
$stmt->execute();
$pratos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Total de registros para indicadores
$totalPratos = count($pratos);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel - Pratos</title>

    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>

    <style>
        body { background: #f4f6f9; }
        .sidebar {
            width: 260px; min-height:100vh;
            position: fixed; top:0; left:0;
            background:#1a1a1d; padding-top:25px; color:white;
        }
        .sidebar .nav-link { color:#ccc; }
        .sidebar .nav-link:hover { background:#0d6efd; color:white; }
        main { margin-left:270px; padding:30px; }
        .stat-card { border-radius:10px; }
    </style>
</head>

<body>

<!-- MENU LATERAL -->
<aside class="sidebar">
    <h5 class="px-3">Painel Administrativo</h5>
    <nav class="nav flex-column">
        <a class="nav-link" href="painel_admin.php"><i class="fas fa-user-friends me-2"></i> Painel Administrativo</a>
        <a class="nav-link" href="tanques.php"><i class="fas fa-fish me-2"></i> Tanques</a>
        <a class="nav-link" href="painel_pratos.php"><i class="fas fa-utensils me-2"></i> Pratos</a>
         <a class="nav-link" href="painel_eventos.php"><i class="fas fa-calendar me-2"></i> Eventos</a>
         <a class="nav-link" href="/Zéfish/Sistema/logout.php"><i class="fas fa-home me-2"></i> Voltar ao site</a>
    </nav>
</aside>

<!-- CONTEÚDO -->
<main>

<div class="d-flex justify-content-between mb-4">
    <h3> Dashboard Administrativo - Pratos</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovo">
        <i class="fas fa-plus"></i> Novo Prato
    </button>
</div>

<?php if (isset($_SESSION['mensagem'])): ?>
<div class="alert alert-<?= $_SESSION['tipo_mensagem'] ?> alert-dismissible fade show">
    <?= $_SESSION['mensagem'] ?>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']); endif; ?>

<!-- CARDS -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow stat-card p-3 text-center">
            <h6 class="text-muted">Total de Pratos</h6>
            <h2><?= $totalPratos ?></h2>
        </div>
    </div>
</div>

<!-- ======= TABELA ======= -->
<div class="card shadow">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Lista de Pratos</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Imagem</th>
                <th>Ações</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($pratos as $p): ?>
            <tr>
                <td><?= $p['id_prato'] ?></td>
                <td><?= htmlspecialchars($p['nome']) ?></td>
                <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                <td>
                    <?php if ($p['imagem']): ?>
                        <img src="/Zéfish/uploads/pratos/<?= htmlspecialchars($p['imagem']) ?>"
                                width="70"
                                 class="rounded">                    
                                     <?php endif; ?>
                </td>
                <td>
                    <!-- EDITAR -->
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                            data-bs-target="#modalEditar<?= $p['id_prato'] ?>">
                        <i class="fas fa-edit"></i>
                    </button>

                    <!-- REMOVER -->
                    <form method="post" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="acao" value="remover_prato">
                        <input type="hidden" name="id_prato" value="<?= $p['id_prato'] ?>">
                        <button onclick="return confirm('Remover prato?')" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ========== TODOS OS MODAIS DE EDIÇÃO AQUI (FORA DA TABELA) ========== -->
<?php foreach ($pratos as $p): ?>
<div class="modal fade" id="modalEditar<?= $p['id_prato'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="acao" value="editar_prato">
            <input type="hidden" name="id_prato" value="<?= $p['id_prato'] ?>">

            <div class="modal-header">
                <h5 class="modal-title">Editar Prato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($p['nome']) ?>" required>

                <label class="form-label mt-3">Descrição</label>
                <textarea name="descricao" class="form-control"><?= htmlspecialchars($p['descricao']) ?></textarea>

                <label class="form-label mt-3">Preço</label>
                <input type="number" step="0.01" name="preco" class="form-control" value="<?= $p['preco'] ?>" required>

                <label class="form-label mt-3">Nova Imagem (opcional)</label>
                <input type="file" name="imagem" class="form-control">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
            </div>

        </form>
    </div>
</div>
<?php endforeach; ?>

<!-- MODAL NOVO PRATO -->
<div class="modal fade" id="modalNovo" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="post" enctype="multipart/form-data">

            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="acao" value="adicionar_prato">

            <div class="modal-header">
                <h5 class="modal-title">Cadastrar Novo Prato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>

                <label class="form-label mt-3">Descrição</label>
                <textarea name="descricao" class="form-control"></textarea>

                <label class="form-label mt-3">Preço</label>
                <input type="number" step="0.01" name="preco" class="form-control" required>

                <label class="form-label mt-3">Imagem</label>
                <input type="file" name="imagem" class="form-control">
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Salvar Prato</button>
            </div>
        </form>
    </div>
</div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
