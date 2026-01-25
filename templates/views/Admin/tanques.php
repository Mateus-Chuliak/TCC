<?php
require_once __DIR__ . '/../../../Sistema/init.php';

// ===================== CONFIGURAÇÕES =====================
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


// Proteção de acesso (⚠️ reativado)
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'administrador') {
    header("Location: painel_usuario.php");
    exit();
}

// ===================== FUNÇÕES =====================
function gerar_csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verificar_csrf_token($token)
{
    return isset($_SESSION['csrf_token']) &&
        hash_equals($_SESSION['csrf_token'], (string)$token);
}

function limpa($v)
{
    return trim((string)$v);
}

function validar_int($v, $min = null)
{
    if ($v === null || $v === '') return null;
    $v = filter_var($v, FILTER_VALIDATE_INT);
    if ($v === false || ($min !== null && $v < $min)) return null;
    return $v;
}

function validar_float($v, $min = null)
{
    if ($v === null || $v === '') return null;
    $v = filter_var($v, FILTER_VALIDATE_FLOAT);
    if ($v === false || ($min !== null && $v < $min)) return null;
    return $v;
}

$csrf = gerar_csrf_token();

$restaurante_id = 1;

// ===================== PROCESSAMENTO POST =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verificar_csrf_token($_POST['csrf_token'] ?? '')) {
        $_SESSION['mensagem'] = 'Token inválido.';
        $_SESSION['tipo_mensagem'] = 'danger';
        header('Location: tanques.php');
        exit();
    }

    $acao = $_POST['acao'] ?? '';

    // REMOVER TANQUE
    if ($acao === 'remover_tanque') {

        $id_tanque = validar_int($_POST['id_tanque'] ?? null, 1);

        if (!$id_tanque) {
            $_SESSION['mensagem'] = 'ID inválido.';
            $_SESSION['tipo_mensagem'] = 'danger';
            header('Location: tanques.php');
            exit();
        }

        try {
            $conexao->begin_transaction();

            $stmt = $conexao->prepare("DELETE FROM tanques WHERE id_tanque = ?");
            $stmt->bind_param("i", $id_tanque);
            $stmt->execute();
            $stmt->close();

            $conexao->commit();
            $_SESSION['mensagem'] = 'Tanque removido com sucesso.';
            $_SESSION['tipo_mensagem'] = 'success';

        } catch (Exception $e) {
            $conexao->rollback();
            // Em produção pode deixar genérico; aqui deixei levemente mais útil
            $_SESSION['mensagem'] = 'Erro ao remover tanque.';
            $_SESSION['tipo_mensagem'] = 'danger';
        }

        header('Location: tanques.php');
        exit();
    }

    
    // Adicionar Tanque
if ($acao === 'adicionar_tanque') {

    $nome = limpa($_POST['nome_tanque'] ?? '');
    if ($nome === '') {
        $nome = null;
    }

    $medida     = validar_float($_POST['medida_tanque'] ?? null, 0);
    $capacidade = validar_int($_POST['capacidade'] ?? null, 0);

    $medida     = $medida     ?? 0.0;
    $capacidade = $capacidade ?? 0;

    try {
        $conexao->begin_transaction();

        // INSERE O TANQUE (UMA ÚNICA VEZ)
        $stmt = $conexao->prepare("
            INSERT INTO tanques (restaurante_id, nome, medida_tanque, capacidade)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "isdi",
            $restaurante_id,
            $nome,
            $medida,
            $capacidade
        );
        $stmt->execute();
        $tanque_id = $conexao->insert_id;
        $stmt->close();

        // ✅ 2. PROCESSA AS ESPÉCIES
        $especies    = $_POST['especies_novo'] ?? [];
        $quantidades = $_POST['quantidades_novo'] ?? [];

        for ($i = 0; $i < count($especies); $i++) {

            $esp = limpa($especies[$i]);
            $qtd = validar_int($quantidades[$i] ?? 0, 0);

            if ($esp === '' || $qtd === null) {
                continue;
            }

            // busca peixe
            $stmt = $conexao->prepare("SELECT id_peixe FROM peixes WHERE nome = ?");
            $stmt->bind_param("s", $esp);
            $stmt->execute();
            $res = $stmt->get_result();

            if ($row = $res->fetch_assoc()) {
                $id_peixe = (int)$row['id_peixe'];
            } else {
                $stmt2 = $conexao->prepare("INSERT INTO peixes (nome) VALUES (?)");
                $stmt2->bind_param("s", $esp);
                $stmt2->execute();
                $id_peixe = $conexao->insert_id;
                $stmt2->close();
            }
            $stmt->close();

            //VINCULA PEIXE AO TANQUE
            $stmt = $conexao->prepare("
                INSERT INTO tanque_peixes (id_tanque, id_peixe, quantidade)
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("iii", $tanque_id, $id_peixe, $qtd);
            $stmt->execute();
            $stmt->close();
        }

        $conexao->commit();
        $_SESSION['mensagem'] = 'Tanque cadastrado com sucesso.';
        $_SESSION['tipo_mensagem'] = 'success';

    } catch (Exception $e) {
        $conexao->rollback();
        $_SESSION['mensagem'] = 'Erro ao cadastrar tanque.';
        $_SESSION['tipo_mensagem'] = 'danger';
    }

    header('Location: tanques.php');
    exit();
}
}
// ===================== BUSCA DE TANQUES =====================
$tanques = [];
$totalTanques = 0;
$tanquesComPeixe = 0;
$totalPeixes = 0;

// ⚠️ Assumindo que as colunas existem no banco exatamente como aqui
$sql = "
SELECT 
    t.id_tanque,
    t.nome,
    t.medida_tanque,
    t.capacidade,
    t.criado_em,
    p.id_peixe,
    p.nome AS especie,
    tp.quantidade
FROM tanques t
LEFT JOIN tanque_peixes tp ON tp.id_tanque = t.id_tanque
LEFT JOIN peixes p ON p.id_peixe = tp.id_peixe
WHERE t.restaurante_id = 1
ORDER BY t.id_tanque DESC
";

$result = $conexao->query($sql);
$result = $conexao->query($sql);

while ($row = $result->fetch_assoc()) {

    $id = (int)$row['id_tanque'];

    if (!isset($tanques[$id])) {
        $tanques[$id] = [
            'id_tanque'     => $id,
            'nome'          => $row['nome'],
            'medida_tanque' => $row['medida_tanque'],
            'capacidade'    => $row['capacidade'],
            'criado_em'     => $row['criado_em'],
            'especies'      => []
        ];
    }

    if (!empty($row['id_peixe'])) {
        $tanques[$id]['especies'][] = [
            'id_peixe'   => (int)$row['id_peixe'],
            'especie'    => $row['especie'],
            'quantidade' => (int)$row['quantidade']
        ];
    }
}

// Reindexa array de tanques
$tanques = array_values($tanques);

// ✅ Agora calculamos os contadores de forma correta
$totalTanques = count($tanques);
$tanquesComPeixe = 0;
$totalPeixes = 0;

foreach ($tanques as $t) {
    $qTotal = 0;
    if (!empty($t['especies'])) {
        foreach ($t['especies'] as $s) {
            $qTotal += (int)($s['quantidade'] ?? 0);
        }
        if ($qTotal > 0) {
            $tanquesComPeixe++;   // ✅ conta 1 por tanque, não por espécie
            $totalPeixes += $qTotal;
        }
    }
}
// ---------- HTML ----------
?><!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <title>ZéFish - Painel Tanques</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="/Zéfish/assets/img/fav_icon.png">
    <link rel="stylesheet" href="/Zéfish/assets/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/Zéfish/assets/css/style.css">
    <style>
        body { background-color: #f5f6fa; }
        .sidebar { width: 260px; min-height:100vh; position:fixed; left:0; top:0; background:#1a1a1d; color:white; padding-top:30px; box-shadow:2px 0 10px rgba(0,0,0,.15); }
        .sidebar h5 { padding-left:15px; font-weight:600; margin-bottom:10px; }
        .sidebar .nav-link { color:#ccc !important; margin:6px 0; }
        .sidebar .nav-link:hover { background:#0d6efd; color:white !important; border-radius:6px; }
        main { margin-left:270px; padding:30px; }
        .card { border-radius:10px; }
        .especies-badge { display:inline-block; background:#e9ecef; padding:3px 8px; margin:2px; border-radius:12px; font-size:13px; }
        .img-thumb { width:70px; height:auto; border-radius:6px; }
    </style>
</head>
<body>
<aside class="sidebar">
    <h5 class="px-3">Painel Administrativo</h5>
    <nav class="nav flex-column px-2">
        <a class="nav-link" href="painel_admin.php"><i class="fas fa-user-friends me-2"></i> Painel Administrativo</a>
        <a class="nav-link active" href="tanques.php"><i class="fas fa-fish me-2"></i> Tanques</a>
        <a class="nav-link" href="painel_pratos.php"><i class="fas fa-utensils me-2"></i> Pratos</a>
        <a class="nav-link" href="painel_eventos.php"><i class="fas fa-calendar me-2"></i> Eventos</a>
        <a class="nav-link" href="/Zéfish/Sistema/logout.php"><i class="fas fa-home me-2"></i> Voltar ao site</a>
    </nav>
</aside>

<main>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Dashboard Administrativo - Tanques</h3>
        <div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovo">
                <i class="fas fa-plus me-1"></i> Novo Tanque
            </button>
        </div>
    </div>

    <?php if (isset($_SESSION['mensagem'])): ?>
        <div class="alert alert-<?= htmlspecialchars($_SESSION['tipo_mensagem'] ?? 'info') ?> alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['mensagem']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['mensagem'], $_SESSION['tipo_mensagem']); endif; ?>

    <div class="row g-4 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm stat-card"><div class="card-body text-center"><i class="fas fa-fish fa-3x text-primary mb-2"></i><h6 class="text-muted">Total Tanques</h6><h2 class="fw-bold"><?= $totalTanques ?></h2></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm stat-card"><div class="card-body text-center"><i class="fas fa-check-circle fa-3x text-success mb-2"></i><h6 class="text-muted">Com Peixes</h6><h2 class="fw-bold text-success"><?= $tanquesComPeixe ?></h2></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm stat-card"><div class="card-body text-center"><i class="fas fa-times-circle fa-3x text-danger mb-2"></i><h6 class="text-muted">Vazios</h6><h2 class="fw-bold text-danger"><?= ($totalTanques - $tanquesComPeixe) ?></h2></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm stat-card"><div class="card-body text-center"><i class="fas fa-water fa-3x text-warning mb-2"></i><h6 class="text-muted">Total Peixes</h6><h2 class="fw-bold text-warning"><?= $totalPeixes ?></h2></div></div></div>
    </div>

    <!-- Tabela de tanques -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white"><h5 class="mb-0"><i class="fas fa-table me-2"></i> Lista de Tanques</h5></div>
        <div class="card-body table-responsive">
            <?php if (!empty($tanques)): ?>
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Espécies de Peixes</th>
                        <th>Quantidade Total</th>
                        <th>Medida (m³)</th>
                        <th>Capacidade</th>
                        <th>Ocupação</th>
                        <th>Data</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tanques as $t): 
                        $qTotal = 0;
                        if (!empty($t['especies'])) {
                            foreach ($t['especies'] as $s) $qTotal += ($s['quantidade'] ?? 0);
                        }
                        $ocupacao = ($t['capacidade'] > 0) ? ($qTotal / $t['capacidade']) * 100 : 0;
                        $corOcup = $ocupacao >= 80 ? 'danger' : ($ocupacao >= 50 ? 'warning' : 'success');
                        ?>
                        <tr>
                            <td><strong>#<?= (int)$t['id_tanque'] ?></strong></td>
                            //
                            <td>
                                <?php if (!empty($t['especies'])): foreach ($t['especies'] as $esp): ?>
                                    <span class="especies-badge"><?= htmlspecialchars($esp['especie']) ?>: <?= (int)$esp['quantidade'] ?></span>
                                <?php endforeach; else: ?>
                                    <span class="text-muted">Sem espécies</span>
                                <?php endif; ?>
                            </td>
                            <td><span class="badge bg-<?= ($qTotal > 0 ? 'success' : 'secondary') ?>"><?= $qTotal ?></span></td>
                            <td><?= $t['medida_tanque'] ? number_format((float)$t['medida_tanque'], 2) . ' m³' : '-' ?></td>
                            <td><?= $t['capacidade'] ?? '-' ?></td>
                            <td>
                                <?php if ($t['capacidade']): ?>
                                    <div class="progress" style="height:20px;"><div class="progress-bar bg-<?= $corOcup ?>" style="width: <?= $ocupacao ?>%"><?= number_format($ocupacao, 0) ?>%</div></div>
                                <?php else: ?> - <?php endif; ?>
                            </td>
                            <td><?= isset($t['criado_em']) ? date('d/m/Y', strtotime($t['criado_em'])) : '-' ?></td>
                            <td>
                                <button 
        class="btn btn-warning btn-sm me-1"
        title="Editar"
        data-bs-toggle="modal"
        data-bs-target="#modalEditar<?= (int)$t['id_tanque'] ?>">
        <i class="fas fa-edit"></i>
    </button>


                                <form method="POST" style="display:inline-block" onsubmit="return confirm('Confirmar remoção do tanque?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                                    <input type="hidden" name="acao" value="remover_tanque">
                                    <input type="hidden" name="id_tanque" value="<?= (int)$t['id_tanque'] ?>">
                                    <button class="btn btn-danger btn-sm" title="Remover">
            <i class="fas fa-trash"></i>
        </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="text-center py-5"><i class="fas fa-inbox fa-4x text-muted mb-3"></i><p>Nenhum tanque cadastrado</p></div>
            <?php endif; ?>
        </div>
    </div>
<!-- Power BI -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-gradient text-white"
         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i> Dashboard Analítico - Power BI</h5>
    </div>

    <div class="card-body p-0">
        <div class="powerbi-container">
            <iframe title="zefish_db"
                    width="1140"
                    height="541.25"
                    src="https://app.powerbi.com/view?r=eyJrIjoiNTg2NjQ2YjYtNjhiMC00NTZkLThhNjYtNTZmYzNjNWRjMDgwIiwidCI6ImNmNzJlMmJkLTdhMmItNDc4My1iZGViLTM5ZDU3YjA3Zjc2ZiIsImMiOjR9"
                    frameborder="0"
                    allowFullScreen="true">
            </iframe>
        </div>
    </div>
</div>
    <!-- MODAL NOVO TANQUE -->
    <div class="modal fade" id="modalNovo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <input type="hidden" name="acao" value="adicionar_tanque">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Novo Tanque</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6"><label class="form-label">Nome do Tanque (opcional)</label><input type="text" name="nome_tanque" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label">Medida (m³)</label><input type="number" step="0.01" name="medida_tanque" class="form-control"></div>
                        <div class="col-md-3"><label class="form-label">Capacidade Total</label><input type="number" name="capacidade" class="form-control" min="0"></div>
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0">Espécies de Peixes</h6>
                        <button type="button" class="btn btn-sm btn-success" onclick="adicionarLinhaNovo()">Adicionar Espécie</button>
                    </div>

                    <div id="especiesContainerNovo">
                        <!-- linha padrão -->
                        <div class="row especie-row mb-2">
                            <div class="col-md-5">
                                <label class="form-label">Espécie</label>
                                <input type="text" name="especies_novo[]" class="form-control" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Quantidade</label>
                                <input type="number" name="quantidades_novo[]" class="form-control" min="0" value="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Imagem (opcional)</label>
                                <input type="file" name="imagens_novo[]" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removerLinha(this)" disabled><i class="fas fa-trash"></i></button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Tanque</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAIS EDITAR (UM POR TANQUE) -->
    <?php foreach ($tanques as $t): 
        $tid = (int)$t['id_tanque'];
        $esps = $t['especies'] ?? [];
        ?>
        <div class="modal fade" id="modalEditar<?= $tid ?>" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form class="modal-content" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                    <input type="hidden" name="acao" value="editar_tanque">
                    <input type="hidden" name="id_tanque" value="<?= $tid ?>">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Editar Tanque #<?= $tid ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label">Nome do Tanque (opcional)</label><input type="text" name="nome_tanque" class="form-control" value="<?= htmlspecialchars($t['nome'] ?? '') ?>"></div>
                            <div class="col-md-3"><label class="form-label">Medida (m³)</label><input type="number" step="0.01" name="medida_tanque" class="form-control" value="<?= htmlspecialchars($t['medida_tanque'] ?? '') ?>"></div>
                            <div class="col-md-3"><label class="form-label">Capacidade Total</label><input type="number" name="capacidade" class="form-control" min="0" value="<?= htmlspecialchars($t['capacidade'] ?? '') ?>"></div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0">Espécies</h6>
                            <button type="button" class="btn btn-sm btn-success" onclick="adicionarLinhaEditar(<?= $tid ?>)">Adicionar Espécie</button>
                        </div>

                        <div id="especiesContainerEditar_<?= $tid ?>">
                            <?php if (!empty($esps)): foreach ($esps as $i => $esp): ?>
                                <div class="row especie-row mb-2">
                                    <input type="hidden" name="id_peixe_existing[]" value="<?= (int)$esp['id_peixe'] ?>">
                                    <div class="col-md-5">
                                        <label class="form-label">Espécie</label>
                                        <input type="text" name="especies_edit[]" class="form-control" value="<?= htmlspecialchars($esp['especie']) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantidade</label>
                                        <input type="number" name="quantidades_edit[]" class="form-control" min="0" value="<?= (int)$esp['quantidade'] ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Imagem (substituir)</label>
                                        <input type="file" name="imagens_edit[]" class="form-control" accept="image/*">
                                        <?php if (!empty($esp['imagem'])): ?>
                                            <div class="mt-2"><img src="<?= htmlspecialchars($esp['imagem']) ?>" class="img-thumb" alt=""></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removerLinha(this)"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            <?php endforeach; else: ?>
                                <!-- linha vazia para adicionar -->
                                <div class="row especie-row mb-2">
                                    <input type="hidden" name="id_peixe_existing[]" value="">
                                    <div class="col-md-5">
                                        <label class="form-label">Espécie</label>
                                        <input type="text" name="especies_edit[]" class="form-control" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantidade</label>
                                        <input type="number" name="quantidades_edit[]" class="form-control" min="0" value="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Imagem (opcional)</label>
                                        <input type="file" name="imagens_edit[]" class="form-control" accept="image/*">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removerLinha(this)"><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Funções mínimas para adicionar/remover linhas em modais
    function criarLinha(especie = '', qtd = 0, showImagemHtml = '', isNovo = true) {
        const row = document.createElement('div');
        row.className = 'row especie-row mb-2';
        // if novo, we don't have existing id_peixe hidden (or set to empty)
        let hiddenId = isNovo ? '<input type="hidden" name="id_peixe_existing[]" value="">' : '';
        row.innerHTML = `
            ${hiddenId}
            <div class="col-md-5">
                <label class="form-label">Espécie</label>
                <input type="text" name="${isNovo ? 'especies_novo[]' : 'especies_edit[]'}" class="form-control" value="${escapeHtml(especie)}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Quantidade</label>
                <input type="number" name="${isNovo ? 'quantidades_novo[]' : 'quantidades_edit[]'}" class="form-control" min="0" value="${qtd}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Imagem (opcional)</label>
                <input type="file" name="${isNovo ? 'imagens_novo[]' : 'imagens_edit[]'}" class="form-control" accept="image/*">
                ${showImagemHtml ? '<div class="mt-2">' + showImagemHtml + '</div>' : ''}
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removerLinha(this)"><i class="fas fa-trash"></i></button>
            </div>
        `;
        return row;
    }

    function escapeHtml(s) {
        return String(s).replaceAll('&', '&amp;').replaceAll('<', '&lt;').replaceAll('>', '&gt;').replaceAll('"', '&quot;').replaceAll("'", '&#39;');
    }

    function adicionarLinhaNovo() {
        const container = document.getElementById('especiesContainerNovo');
        container.appendChild(criarLinha('', 0, '', true));
        atualizarBotoesRemover(container);
    }

    function adicionarLinhaEditar(tid) {
        const container = document.getElementById('especiesContainerEditar_' + tid);
        if (!container) return;
        container.appendChild(criarLinha('', 0, '', false));
        atualizarBotoesRemover(container);
    }

    function removerLinha(btn) {
        const row = btn.closest('.especie-row');
        if (!row) return;
        const container = row.parentElement;
        row.remove();
        // se container for o novo e ficar vazio, adicionar uma linha vazia para evitar forms sem inputs
        if (container && container.id === 'especiesContainerNovo' && container.children.length === 0) {
            adicionarLinhaNovo();
        }
        // se container de editar ficar vazio, adicionar uma linha vazia
        if (container && container.id.startsWith('especiesContainerEditar_') && container.children.length === 0) {
            const tid = container.id.split('_')[1];
            adicionarLinhaEditar(tid);
        }
        atualizarBotoesRemover(container);
    }

    function atualizarBotoesRemover(container) {
        if (!container) {
            // atualizar ambos
            container = document.getElementById('especiesContainerNovo');
            if (container) {
                const rows = container.querySelectorAll('.especie-row');
                rows.forEach((r, idx) => {
                    const btn = r.querySelector('button[onclick^="removerLinha"]');
                    if (btn) btn.disabled = (rows.length === 1);
                });
            }
            // editar containers
            document.querySelectorAll('[id^="especiesContainerEditar_"]').forEach(cont => {
                cont.querySelectorAll('.especie-row').forEach(r => {
                    const btn = r.querySelector('button[onclick^="removerLinha"]');
                    if (btn) btn.disabled = false;
                });
            });
            return;
        }
        const rows = container.querySelectorAll('.especie-row');
        rows.forEach((r, idx) => {
            const btn = r.querySelector('button[onclick^="removerLinha"]');
            if (btn) btn.disabled = (container.id === 'especiesContainerNovo' && rows.length === 1);
        });
    }

    // Inicializa containers ao carregar
    document.addEventListener('DOMContentLoaded', function () {
        // garantir que no modal novo exista pelo menos uma linha
        const novo = document.getElementById('especiesContainerNovo');
        if (novo && novo.children.length === 0) adicionarLinhaNovo();

        // garantir que cada editar container tenha ao menos uma linha
        document.querySelectorAll('[id^="especiesContainerEditar_"]').forEach(cont => {
            if (cont.children.length === 0) {
                const tid = cont.id.split('_')[1];
                adicionarLinhaEditar(tid);
            }
        });

        atualizarBotoesRemover();
    });

    // Ao fechar modal (opcional) podemos limpar inputs file para evitar comportamento estranho
    document.querySelectorAll('.modal').forEach(m => {
        m.addEventListener('hidden.bs.modal', function () {
            // limpar campos file do modal fechado
            this.querySelectorAll('input[type="file"]').forEach(f => f.value = '');
        });
    });
</script>
</body>
</html>
