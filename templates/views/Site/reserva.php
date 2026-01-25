<?php
require_once __DIR__ . '/../../../Sistema/init.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>ZéFish - Reserva</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/Zéfish/assets/css/style.css">

<script src="https://kit.fontawesome.com/488a0cd27b.js" crossorigin="anonymous"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<style>
#reserva-fundo {
    background: linear-gradient(to bottom, #001f3f, #0074b7, #7db7da);
    min-height: 100vh;
}
#pixKey { color:#001f3f;font-size:18px }
.card-shadow { box-shadow:0 10px 38px rgba(0,0,0,.16) }
</style>
</head>

<body id="reserva-fundo">

<!-- MODAL PIX -->
<div class="modal fade" id="pixModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <h4 class="text-center mb-3">Pagamento via PIX</h4>
      <p class="text-center">Copie a chave abaixo:</p>
      <div class="bg-light p-3 text-center rounded">
        <strong id="pixKey">Carregando...</strong>
      </div>
      <div id="qrcode" class="d-flex justify-content-center mt-3"></div>
      <button class="btn btn-secondary w-100 mt-3" data-bs-dismiss="modal">Fechar</button>
    </div>
  </div>
</div>

<main class="max-w-4xl mx-auto py-12 px-4">

<form id="formReserva" method="POST" class="bg-white rounded-2xl card-shadow p-8 text-black">
    
    <a href="/Zéfish/index.php"
   class="btn btn-outline-secondary d-inline-flex align-items-center gap-2 mb-4">
    <i class="fa-solid fa-arrow-left"></i>
    Voltar para Home
    </a>
    <h2 class="text-center text-3xl font-bold mb-6">Reserva de Pesca</h2>

    <!-- PACOTES -->
    <h5 class="font-bold">1️⃣ Escolha o Pacote</h5>
    <div class="grid md:grid-cols-3 gap-4 mt-3">
        <?php
        $pacotes = [
            ['basico','🎣 Básico','80,00','8000'],
            ['familia','👨‍👩‍👧‍👦 Família','350,00','35000'],
            ['completo','🏕️ Completo','450,00','45000']
        ];
        foreach ($pacotes as $p):
        ?>
        <label class="cursor-pointer">
            <input type="radio" name="pacote" value="<?= $p[0] ?>" data-price="<?= $p[3] ?>" class="peer sr-only" required>
            <div class="border rounded-xl p-4 peer-checked:border-teal-600 peer-checked:bg-teal-50">
                <h5 class="font-bold"><?= $p[1] ?></h5>
                <p class="text-teal-700 font-bold">R$ <?= $p[2] ?></p>
            </div>
        </label>
        <?php endforeach; ?>
    </div>

    <!-- DATA / PESSOAS -->
    <div class="grid md:grid-cols-2 gap-4 mt-6">
        <div>
            <label>Data</label>
            <input type="date" name="data_reserva" class="form-control" required>
        </div>
        <div>
            <label>Pessoas</label>
            <input type="number" name="num_pessoas" min="1" max="20" value="1" class="form-control" required>
        </div>
    </div>

    <!-- DADOS -->
    <div class="grid md:grid-cols-2 gap-4 mt-6">
        <input name="nome"      class="form-control" placeholder="Nome completo" required>
        <input name="cpf"       class="form-control" placeholder="CPF">
        <input name="email"     type="email" class="form-control" placeholder="Email" required>
        <input name="telefone"  class="form-control" placeholder="Telefone" required>
    </div>

    <button type="submit" class="btn btn-success w-100 mt-6 py-3">
        <i class="fa fa-qrcode"></i> Finalizar e Pagar PIX
    </button>

</form>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById("formReserva").addEventListener("submit", function(e){
    e.preventDefault();

    if (!this.checkValidity()) {
        this.reportValidity();
        return;
    }

    const pacote = document.querySelector('input[name="pacote"]:checked');
    if (!pacote) {
        alert("Selecione um pacote.");
        return;
    }

    const fd = new FormData(this);
    fd.append("valor", pacote.dataset.price);

    fetch("/Zéfish/templates/views/Admin/salvar_reserva.php", {
        method: "POST",
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            alert(data.error || "Erro ao salvar reserva.");
            return;
        }

        // Atualiza chave PIX e QRCode
        document.getElementById("pixKey").textContent = data.pix_key;
        document.getElementById("qrcode").innerHTML = "";
        new QRCode(document.getElementById("qrcode"), {
            text: data.pix_key,
            width: 200,
            height: 200
        });

        new bootstrap.Modal(document.getElementById("pixModal")).show();
    })
    .catch(err => {
        alert("Erro inesperado: " + err);
    });
});
</script>

</body>
</html>
