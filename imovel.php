<?php
// imovel.php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$id = asInt($_GET['id'] ?? 0, 0);
if ($id <= 0) { http_response_code(400); die("ID inválido."); }

$imovel = getPropertyById($id);
if (!$imovel) { http_response_code(404); die("Imóvel não encontrado."); }

$pageTitle = ($imovel['description'] ?? 'Imóvel') . " • Detalhes";
require __DIR__ . '/partials/header.php';

$title = (string)($imovel['description'] ?? 'Imóvel');
$img   = imageUrl((int)$imovel['id'], $imovel['primary_image'] ?? '');

$pv = priceValue($imovel);
$pl = priceLabel($imovel);

$cityState = trim((string)($imovel['city'] ?? '') . (isset($imovel['state']) && $imovel['state'] !== '' ? ' - ' . $imovel['state'] : ''));
$zip = trim((string)($imovel['zip'] ?? ''));

$address = trim(
  (string)($imovel['street_name'] ?? '') . ' ' .
  (string)($imovel['street_number'] ?? '') . ' ' .
  (string)($imovel['unit_number'] ?? '')
);

$lat = trim((string)($imovel['latitude'] ?? ''));
$lng = trim((string)($imovel['longitude'] ?? ''));

$mapsLink = '';
if ($lat !== '' && $lng !== '' && $lat !== '0' && $lng !== '0') {
  $mapsLink = "https://www.google.com/maps?q=" . urlencode($lat . "," . $lng);
} else {
  $mapsLink = "https://www.google.com/maps/search/?api=1&query=" . urlencode(trim($address . ' ' . $cityState));
}

$beds = (int)($imovel['beds'] ?? 0);
$full = (float)($imovel['full_baths'] ?? 0);
$half = (float)($imovel['half_baths'] ?? 0);
$baths = $full + ($half * 0.5);
$area = (float)($imovel['sqFt_total'] ?? 0);

$descHtml = (string)($imovel['long_description'] ?? '');
?>

<div class="container container-wide" style="padding-top:22px; padding-bottom:22px;">
  <a href="index.php" class="text-decoration-none" style="color:#0b0f16;">
    <i class="bi bi-arrow-left"></i> Voltar
  </a>

  <div class="row g-4 mt-2">
    <div class="col-lg-8">
      <div class="featured">
        <img class="featured-img" src="<?= e($img) ?>" onerror="this.src='<?= e(PLACEHOLDER_IMAGE) ?>'">
        <div class="p-4">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <div class="text-muted small">Cód: ID <?= (int)$imovel['id'] ?></div>
              <h1 class="h4 fw-bold mb-1"><?= e($title) ?></h1>
              <div class="text-muted">
                <i class="bi bi-geo-alt"></i> <?= e($address ?: 'Endereço não informado') ?>
                <?php if($cityState): ?> • <?= e($cityState) ?><?php endif; ?>
                <?php if($zip): ?> • CEP <?= e($zip) ?><?php endif; ?>
              </div>
            </div>
            <div class="text-end">
              <span class="badge" style="background:rgba(201,161,74,.18);border:1px solid rgba(201,161,74,.35);color:#7c5c18;">
                <?= e($pl) ?>
              </span>
              <div class="mt-2" style="font-weight:900;font-size:1.35rem;"><?= moneyBR($pv) ?></div>
            </div>
          </div>

          <div class="mt-3 d-flex flex-wrap gap-2">
            <span class="chip"><i class="bi bi-door-open"></i> <?= $beds ?> quartos</span>
            <span class="chip"><i class="bi bi-droplet"></i> <?= rtrim(rtrim(number_format($baths,1,'.',''),'0'),'.') ?> banh.</span>
            <?php if($area>0): ?><span class="chip"><i class="bi bi-aspect-ratio"></i> <?= number_format($area,0,',','.') ?> ft²</span><?php endif; ?>
          </div>

          <hr class="my-4">

          <h2 class="h6 fw-bold mb-2">Descrição</h2>
          <div class="text-muted" style="line-height:1.75;">
            <?= $descHtml !== '' ? $descHtml : "<p>Sem descrição detalhada.</p>"; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="featured p-3">
        <div class="d-flex align-items-center justify-content-between">
          <div class="fw-bold">Ações</div>
          <div class="text-muted small">ID <?= (int)$imovel['id'] ?></div>
        </div>

        <div class="d-grid gap-2 mt-3">
          <a class="btn btn-outline-secondary" style="border-radius:999px;" target="_blank" href="<?= e($mapsLink) ?>">
            <i class="bi bi-map"></i> Ver no mapa
          </a>
          <button class="btn-darksoft" type="button" data-bs-toggle="modal" data-bs-target="#contactModal">
            <i class="bi bi-whatsapp"></i> Quero este imóvel
          </button>
        </div>

        <hr class="my-3">

        <div class="fw-bold mb-2">Informações</div>
        <div class="text-muted small d-flex flex-column gap-2">
          <div><i class="bi bi-house"></i> Tipo: <?= e((string)($imovel['property_style'] ?? '—')) ?></div>
          <div><i class="bi bi-tag"></i> Transação: <?= e((string)($imovel['transaction_type'] ?? '—')) ?></div>
          <div><i class="bi bi-geo"></i> Cidade: <?= e((string)($imovel['city'] ?? '—')) ?></div>
          <div><i class="bi bi-geo-alt"></i> Estado: <?= e((string)($imovel['state'] ?? '—')) ?></div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Modal contato -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:18px;">
      <div class="modal-header">
        <h5 class="modal-title fw-semibold">Mensagem rápida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="text-muted mb-3">
          Copie e cole no WhatsApp.
        </div>
        <textarea class="form-control" rows="5" id="msgText">Olá! Tenho interesse no imóvel ID <?= (int)$imovel['id'] ?> (<?= e($title) ?>). Pode me enviar mais informações?</textarea>

        <div class="d-grid mt-3">
          <button class="btn btn-dark" type="button" id="btnCopy">
            <i class="bi bi-clipboard"></i> Copiar mensagem
          </button>
        </div>
        <div class="small text-success mt-2" id="copyStatus" style="display:none;">Copiado ✅</div>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    const btn = document.getElementById('btnCopy');
    const txt = document.getElementById('msgText');
    const status = document.getElementById('copyStatus');
    btn?.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(txt.value);
        status.style.display = 'block';
        setTimeout(() => status.style.display = 'none', 1500);
      } catch (e) {
        alert('Não foi possível copiar automaticamente. Copie manualmente.');
      }
    });
  })();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>
