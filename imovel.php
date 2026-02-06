<?php
// imovel.php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$id = asInt($_GET['id'] ?? 0, 0);
if ($id <= 0) {
  http_response_code(400);
  die("ID inválido.");
}

$imovel = getPropertyById($id);
if (!$imovel) {
  http_response_code(404);
  die("Imóvel não encontrado.");
}

$pageTitle = ($imovel['description'] ?? 'Imóvel') . " • Detalhes";
require __DIR__ . '/partials/header.php';

$title = (string)($imovel['description'] ?? 'Imóvel');
$img = imageUrl((int)$imovel['id'], $imovel['primary_image'] ?? '');

$isRent = (($imovel['transaction_type'] ?? '') === 'Rent');
$priceValue = $isRent ? (float)($imovel['rental_value'] ?? $imovel['rent_price'] ?? 0) : (float)($imovel['rate'] ?? 0);
$priceLabel = $isRent ? 'Aluguel' : 'Venda';

$cityState = trim((string)($imovel['city'] ?? '') . (isset($imovel['state']) && $imovel['state'] !== '' ? ' • ' . $imovel['state'] : ''));
$zip = trim((string)($imovel['zip'] ?? ''));

$address = trim(
  (string)($imovel['street_name'] ?? '') . ' ' .
  (string)($imovel['street_number'] ?? '') . ' ' .
  (string)($imovel['unit_number'] ?? '')
);

$lat = trim((string)($imovel['latitude'] ?? ''));
$lng = trim((string)($imovel['longitude'] ?? ''));

$beds = (int)($imovel['beds'] ?? 0);
$full = (float)($imovel['full_baths'] ?? 0);
$half = (float)($imovel['half_baths'] ?? 0);
$baths = $full + ($half * 0.5);

$area = (float)($imovel['sqFt_total'] ?? 0);
$year = trim((string)($imovel['year_built'] ?? ''));
$status = trim((string)($imovel['status'] ?? ''));

// long_description pode vir em HTML
$descHtml = (string)($imovel['long_description'] ?? '');

$mapsLink = '';
if ($lat !== '' && $lng !== '' && $lat !== '0' && $lng !== '0') {
  $mapsLink = "https://www.google.com/maps?q=" . urlencode($lat . "," . $lng);
} else {
  $mapsLink = "https://www.google.com/maps/search/?api=1&query=" . urlencode(trim($address . ' ' . $cityState));
}
?>

<section class="mb-3">
  <a href="index.php" class="text-muted2 text-decoration-none"><i class="bi bi-arrow-left"></i> Voltar para a busca</a>
</section>

<section class="row g-4">
  <div class="col-lg-7">
    <div class="card-soft overflow-hidden">
      <img src="<?= e($img) ?>" alt="<?= e($title) ?>"
           class="w-100" style="height: 420px; object-fit: cover;"
           onerror="this.src='<?= e(PLACEHOLDER_IMAGE) ?>'">

      <div class="p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2">
          <div>
            <h1 class="h4 mb-1 fw-semibold"><?= e($title) ?></h1>
            <div class="text-muted2">
              <i class="bi bi-geo-alt"></i>
              <?= e($address ?: 'Endereço não informado') ?>
              <?php if ($cityState): ?> • <?= e($cityState) ?><?php endif; ?>
              <?php if ($zip): ?> • CEP <?= e($zip) ?><?php endif; ?>
            </div>
          </div>

          <div class="text-end">
            <span class="badge badge-soft mb-2"><?= e($priceLabel) ?></span>
            <div class="h4 mb-0 fw-semibold"><?= moneyBR($priceValue) ?></div>
            <div class="text-muted2 small">ID <?= (int)$imovel['id'] ?></div>
          </div>
        </div>

        <div class="mt-3 d-flex flex-wrap gap-2">
          <span class="pill"><i class="bi bi-door-open"></i> <?= $beds ?> quartos</span>
          <span class="pill"><i class="bi bi-droplet"></i> <?= rtrim(rtrim(number_format($baths, 1, '.', ''), '0'), '.') ?> banheiros</span>
          <?php if ($area > 0): ?>
            <span class="pill"><i class="bi bi-aspect-ratio"></i> <?= number_format($area, 0, ',', '.') ?> ft²</span>
          <?php endif; ?>
          <?php if ($year !== '' && $year !== '0'): ?>
            <span class="pill"><i class="bi bi-calendar3"></i> Ano <?= e($year) ?></span>
          <?php endif; ?>
          <?php if ($status !== ''): ?>
            <span class="pill"><i class="bi bi-check2-circle"></i> <?= e($status) ?></span>
          <?php endif; ?>
        </div>

        <hr class="my-4">

        <h2 class="h6 mb-2 fw-semibold">Descrição</h2>
        <div class="text-muted2" style="line-height:1.75;">
          <?php
            echo $descHtml !== '' ? $descHtml : "<p>Sem descrição detalhada.</p>";
          ?>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card-soft p-4 mb-4">
      <h2 class="h6 mb-3 fw-semibold"><i class="bi bi-info-circle"></i> Informações rápidas</h2>

      <div class="row g-3">
        <div class="col-6">
          <div class="text-muted2 small">Transação</div>
          <div class="fw-semibold"><?= e((string)($imovel['transaction_type'] ?? '—')) ?></div>
        </div>
        <div class="col-6">
          <div class="text-muted2 small">Estilo</div>
          <div class="fw-semibold"><?= e((string)($imovel['property_style'] ?? '—')) ?></div>
        </div>
        <div class="col-6">
          <div class="text-muted2 small">Cidade</div>
          <div class="fw-semibold"><?= e((string)($imovel['city'] ?? '—')) ?></div>
        </div>
        <div class="col-6">
          <div class="text-muted2 small">Estado</div>
          <div class="fw-semibold"><?= e((string)($imovel['state'] ?? '—')) ?></div>
        </div>
      </div>

      <hr class="my-4">

      <div class="d-grid gap-2">
        <a class="btn btn-outline-secondary" target="_blank" href="<?= e($mapsLink) ?>">
          <i class="bi bi-map"></i> Ver no mapa
        </a>

        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#contactModal">
          <i class="bi bi-whatsapp"></i> Quero este imóvel
        </button>
      </div>
    </div>

    <div class="card-soft p-4">
      <h2 class="h6 mb-3 fw-semibold"><i class="bi bi-list-check"></i> Características (principais)</h2>
      <ul class="list-unstyled mb-0 text-muted2">
        <?php
          $features = [
            'Ar-condicionado' => $imovel['air_conditioning'] ?? null,
            'Piscina' => $imovel['private_pool'] ?? null,
            'Varanda' => $imovel['balcony'] ?? null,
            'Elevador' => $imovel['lift'] ?? null,
            'Churrasqueira' => $imovel['grill'] ?? null,
            'Internet' => $imovel['internet'] ?? null,
            'Mobiliado' => $imovel['furnished'] ?? null,
            'Cozinha' => $imovel['kitchen'] ?? null,
          ];

          $printed = 0;
          foreach ($features as $label => $val) {
            $v = strtolower(trim((string)$val));
            $isYes = in_array($v, ['yes','1','true','sim'], true);
            if ($isYes) {
              echo '<li class="mb-2"><i class="bi bi-check2 text-success"></i> ' . e($label) . '</li>';
              $printed++;
            }
          }

          if ($printed === 0) {
            echo '<li class="text-muted2">Sem características marcadas nos dados sintéticos.</li>';
          }
        ?>
      </ul>
    </div>
  </div>
</section>

<!-- Modal contato -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:18px;">
      <div class="modal-header">
        <h5 class="modal-title fw-semibold">Mensagem rápida</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="text-muted2 mb-3">
          Copie e cole no WhatsApp (depois você pode ligar isso num formulário/CRM).
        </div>
        <textarea class="form-control" rows="5" id="msgText">Olá! Tenho interesse no imóvel ID <?= (int)$imovel['id'] ?> (<?= e($title) ?>). Pode me enviar mais informações?</textarea>

        <div class="d-grid mt-3">
          <button class="btn btn-primary" type="button" id="btnCopy">
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
        alert('Não foi possível copiar automaticamente. Selecione e copie manualmente.');
      }
    });
  })();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>