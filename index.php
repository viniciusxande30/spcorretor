<?php
// index.php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$pageTitle = "Buscar imóveis";
$options = fetchFilterOptions();

// Filtros GET
$q = trim((string)($_GET['q'] ?? ''));
$city = trim((string)($_GET['city'] ?? ''));
$state = trim((string)($_GET['state'] ?? ''));
$transaction_type = trim((string)($_GET['transaction_type'] ?? ''));
$beds = $_GET['beds'] ?? '';
$baths = $_GET['baths'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

$page = max(1, asInt($_GET['page'] ?? 1, 1));

$filters = [
  'q' => $q !== '' ? $q : null,
  'city' => $city !== '' ? $city : null,
  'state' => $state !== '' ? $state : null,
  'transaction_type' => $transaction_type !== '' ? $transaction_type : null,
  'beds' => ($beds !== '' ? asInt($beds) : null),
  'baths' => ($baths !== '' ? asFloat($baths) : null),
  'min_price' => ($min_price !== '' ? asFloat($min_price) : null),
  'max_price' => ($max_price !== '' ? asFloat($max_price) : null),
];

$result = listProperties($filters, $page, PER_PAGE);

function keepQuery(array $override = []): string {
  $q = array_merge($_GET, $override);
  foreach ($q as $k => $v) {
    if ($v === '' || $v === null) unset($q[$k]);
  }
  return http_build_query($q);
}

require __DIR__ . '/partials/header.php';
?>

<section class="hero p-4 mb-4">
  <div class="row g-3 align-items-center">
    <div class="col-lg-7">
      <h1 class="h3 mb-2 fw-semibold">Encontre seu próximo imóvel com rapidez e clareza</h1>
      <div class="text-muted2">
        Filtros completos, cards profissionais e página de detalhes elegante.
      </div>

      <div class="mt-3 d-flex flex-wrap gap-2">
        <span class="pill"><i class="bi bi-shield-check"></i> Dados do banco</span>
        <span class="pill"><i class="bi bi-images"></i> Imagens automáticas</span>
        <span class="pill"><i class="bi bi-geo-alt"></i> Detalhes completos</span>
      </div>
    </div>

    <div class="col-lg-5 text-lg-end">
      <div class="card-soft p-3 d-inline-flex flex-column align-items-start align-items-lg-end">
        <div class="text-muted2 small">Imóveis encontrados</div>
        <div class="display-6 fw-semibold mb-0"><?= (int)$result['total'] ?></div>
      </div>
    </div>
  </div>

  <hr class="my-4">

  <form class="row g-3" method="get" action="index.php">
    <div class="col-lg-4">
      <label class="form-label text-muted2">Buscar</label>
      <input class="form-control" name="q" value="<?= e((string)($q ?? '')) ?>" placeholder="Ex: apartamento, suzano, rua..." />
    </div>

    <div class="col-lg-2 col-md-4">
      <label class="form-label text-muted2">Cidade</label>
      <select class="form-select" name="city">
        <option value="">Todas</option>
        <?php foreach ($options['cities'] as $c): ?>
          <option value="<?= e($c['city']) ?>" <?= ($city === $c['city'] ? 'selected' : '') ?>>
            <?= e($c['city']) ?> (<?= (int)$c['total'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-lg-2 col-md-4">
      <label class="form-label text-muted2">Estado</label>
      <select class="form-select" name="state">
        <option value="">Todos</option>
        <?php foreach ($options['states'] as $s): ?>
          <option value="<?= e($s['state']) ?>" <?= ($state === $s['state'] ? 'selected' : '') ?>>
            <?= e($s['state']) ?> (<?= (int)$s['total'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-lg-2 col-md-4">
      <label class="form-label text-muted2">Tipo</label>
      <select class="form-select" name="transaction_type">
        <option value="">Todos</option>
        <?php foreach ($options['transactionTypes'] as $t): ?>
          <option value="<?= e($t['transaction_type']) ?>" <?= ($transaction_type === $t['transaction_type'] ? 'selected' : '') ?>>
            <?= e($t['transaction_type']) ?> (<?= (int)$t['total'] ?>)
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-lg-1 col-md-4">
      <label class="form-label text-muted2">Quartos</label>
      <select class="form-select" name="beds">
        <option value="">—</option>
        <?php for ($i=0; $i<=6; $i++): ?>
          <option value="<?= $i ?>" <?= ((string)$beds === (string)$i ? 'selected' : '') ?>><?= $i ?>+</option>
        <?php endfor; ?>
      </select>
    </div>

    <div class="col-lg-1 col-md-4">
      <label class="form-label text-muted2">Banhos</label>
      <select class="form-select" name="baths">
        <option value="">—</option>
        <?php foreach ([1,1.5,2,2.5,3,3.5,4] as $b): ?>
          <option value="<?= $b ?>" <?= ((string)$baths === (string)$b ? 'selected' : '') ?>><?= $b ?>+</option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="col-lg-2 col-md-6">
      <label class="form-label text-muted2">Preço mínimo</label>
      <input class="form-control" name="min_price" value="<?= e((string)$min_price) ?>" placeholder="Ex: 200000" inputmode="decimal">
    </div>

    <div class="col-lg-2 col-md-6">
      <label class="form-label text-muted2">Preço máximo</label>
      <input class="form-control" name="max_price" value="<?= e((string)$max_price) ?>" placeholder="Ex: 700000" inputmode="decimal">
    </div>

    <div class="col-lg-8 d-flex align-items-end gap-2">
      <button class="btn btn-primary px-4" type="submit">
        <i class="bi bi-funnel"></i> Filtrar
      </button>
      <a class="btn btn-outline-secondary" href="index.php">
        <i class="bi bi-x-circle"></i> Limpar
      </a>
    </div>
  </form>
</section>

<section class="row g-4">
  <?php if (!$result['items']): ?>
    <div class="col-12">
      <div class="card-soft p-4">
        <h2 class="h5 mb-1">Nenhum imóvel encontrado</h2>
        <div class="text-muted2">Ajuste filtros, remova palavras ou amplie a faixa de preço.</div>
      </div>
    </div>
  <?php endif; ?>

  <?php foreach ($result['items'] as $p): 
    $title = $p['description'] ?: 'Imóvel';
    $img = imageUrl((int)$p['id'], $p['primary_image'] ?? '');

    $cityState = trim(($p['city'] ?? '') . (isset($p['state']) && $p['state'] !== '' ? ' • ' . $p['state'] : ''));
    $isRent = (($p['transaction_type'] ?? '') === 'Rent');

    $priceValue = $isRent ? (float)($p['rental_value'] ?? $p['rent_price'] ?? 0) : (float)($p['rate'] ?? 0);
    $priceLabel = $isRent ? 'Aluguel' : 'Venda';

    $bedsV = (int)($p['beds'] ?? 0);
    $bathsV = (float)((float)($p['full_baths'] ?? 0) + ((float)($p['half_baths'] ?? 0) * 0.5));
    $area = (float)($p['sqFt_total'] ?? 0);
  ?>
  <div class="col-lg-4 col-md-6">
    <a href="imovel.php?id=<?= (int)$p['id'] ?>" class="d-block text-decoration-none">
      <div class="prop-card">
        <img class="prop-cover"
             src="<?= e($img) ?>"
             alt="<?= e($title) ?>"
             onerror="this.src='<?= e(PLACEHOLDER_IMAGE) ?>'">

        <div class="p-3">
          <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
              <div class="fw-semibold fs-6"><?= e($title) ?></div>
              <div class="text-muted2 small">
                <i class="bi bi-geo-alt"></i> <?= e($cityState ?: 'Localização não informada') ?>
              </div>
            </div>
            <span class="badge badge-soft"><?= e($priceLabel) ?></span>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="fs-5 fw-semibold"><?= moneyBR($priceValue) ?></div>
            <div class="text-muted2 small">ID <?= (int)$p['id'] ?></div>
          </div>

          <div class="mt-3 d-flex flex-wrap gap-2">
            <span class="pill"><i class="bi bi-door-open"></i> <?= $bedsV ?> qtos</span>
            <span class="pill"><i class="bi bi-droplet"></i> <?= rtrim(rtrim(number_format($bathsV, 1, '.', ''), '0'), '.') ?> banh.</span>
            <?php if ($area > 0): ?>
              <span class="pill"><i class="bi bi-aspect-ratio"></i> <?= number_format($area, 0, ',', '.') ?> ft²</span>
            <?php endif; ?>
          </div>

          <div class="text-muted2 small mt-3" style="line-height:1.5;">
            <?= e(mb_strimwidth(strip_tags((string)($p['long_description'] ?? '')), 0, 110, '…', 'UTF-8')) ?>
          </div>

          <div class="mt-3 d-flex justify-content-end">
            <span class="btn btn-sm btn-primary">Ver detalhes <i class="bi bi-arrow-right"></i></span>
          </div>
        </div>
      </div>
    </a>
  </div>
  <?php endforeach; ?>
</section>

<?php if ($result['pages'] > 1): ?>
  <section class="mt-4">
    <div class="card-soft p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
      <div class="text-muted2 small">
        Página <?= (int)$result['page'] ?> de <?= (int)$result['pages'] ?>
      </div>
      <nav>
        <ul class="pagination pagination-sm mb-0">
          <?php
            $prev = max(1, $result['page'] - 1);
            $next = min($result['pages'], $result['page'] + 1);
          ?>
          <li class="page-item <?= ($result['page'] <= 1 ? 'disabled' : '') ?>">
            <a class="page-link" href="index.php?<?= e(keepQuery(['page' => $prev])) ?>">Anterior</a>
          </li>

          <?php
            $start = max(1, $result['page'] - 2);
            $end = min($result['pages'], $result['page'] + 2);
            for ($i=$start; $i<=$end; $i++):
          ?>
            <li class="page-item <?= ($i === $result['page'] ? 'active' : '') ?>">
              <a class="page-link" href="index.php?<?= e(keepQuery(['page' => $i])) ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>

          <li class="page-item <?= ($result['page'] >= $result['pages'] ? 'disabled' : '') ?>">
            <a class="page-link" href="index.php?<?= e(keepQuery(['page' => $next])) ?>">Próxima</a>
          </li>
        </ul>
      </nav>
    </div>
  </section>
<?php endif; ?>

<?php require __DIR__ . '/partials/footer.php'; ?>