<?php
// index.php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

$pageTitle = "Home";

$options  = fetchFilterOptions();
$featured = fetchFeaturedToday();

$launch = fetchShowcase('launch', 12);
$sale   = fetchShowcase('sale', 12);
$rent   = fetchShowcase('rent', 12);

$neigh  = fetchTopNeighborhoods(6);

// filtros GET (na barra do hero)
$q     = trim((string)($_GET['q'] ?? ''));
$city  = trim((string)($_GET['city'] ?? ''));
$nb    = trim((string)($_GET['neighborhood'] ?? ''));
$style = trim((string)($_GET['property_style'] ?? ''));
$tt    = trim((string)($_GET['transaction_type'] ?? ''));

$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$beds      = $_GET['beds'] ?? '';

$page = max(1, asInt($_GET['page'] ?? 1, 1));

$filters = [
  'q' => $q !== '' ? $q : null,
  'city' => $city !== '' ? $city : null,
  'neighborhood' => $nb !== '' ? $nb : null,
  'property_style' => $style !== '' ? $style : null,
  'transaction_type' => $tt !== '' ? $tt : null,
  'min_price' => ($min_price !== '' ? asFloat($min_price) : null),
  'max_price' => ($max_price !== '' ? asFloat($max_price) : null),
  'beds' => ($beds !== '' ? asInt($beds) : null),
  'baths' => null,
];

/**
 * ✅ FIX 1: "O que deseja?" não funcionava porque às vezes o site continuava no modo destaques.
 * Agora: se tiver QUALQUER parâmetro em $_GET (q, city, etc), mostramos resultados.
 */
$hasSearch = !empty($_GET);

/**
 * Se tem busca, carrega lista paginada
 */
$list = $hasSearch ? listProperties($filters, $page, PER_PAGE) : null;

require __DIR__ . '/partials/header.php';
?>

<style>
  /* ✅ FIX 3: escurecer mais a imagem do hero e deixar título branco legível */
  .hero::after{
    background: linear-gradient(90deg, rgba(0,0,0,.78), rgba(0,0,0,.30), rgba(0,0,0,.72)) !important;
  }
  .hero h1{
    color:#fff !important;
    text-shadow: 0 10px 30px rgba(0,0,0,.55);
  }
  .hero p{
    color: rgba(255,255,255,.92) !important;
    text-shadow: 0 6px 18px rgba(0,0,0,.45);
  }

  /* ✅ FIX 4: filtros mais “retangulares premium” + alinhados */
  .search-card{
    border-radius: 18px !important;
    padding: 14px !important;
  }
  .search-card .form-control,
  .search-card .form-select{
    height: 48px !important;
    border-radius: 12px !important;
    border: 1px solid #e5e7eb !important;
    background: #fff !important;
    padding-left: 14px !important;
  }
  .search-card .form-control:focus,
  .search-card .form-select:focus{
    border-color: rgba(201,161,74,.55) !important;
    box-shadow: 0 0 0 .2rem rgba(201,161,74,.15) !important;
  }
  .btn-darksoft{
    height: 48px !important;
    border-radius: 12px !important;
    display:flex !important;
    align-items:center !important;
    justify-content:center !important;
    gap:8px !important;
  }
  .btn-outline-secondary{
    height: 48px !important;
    border-radius: 12px !important;
  }
  /* placeholders mais “clean” */
  .search-card ::placeholder{ color:#9ca3af; }

  /* ✅ FIX 2: setas dos slides clicáveis (z-index) */
  .rail-nav{
    z-index: 20 !important;
  }
</style>

<!-- HERO -->
<section class="hero" style="--hero:url('<?= e(HERO_BG) ?>');">
  <div class="container container-wide hero-content">
    <div class="row">
      <div class="col-lg-8">
        <h1 class="display-6 mb-2">Imóveis com curadoria e experiência premium</h1>
        <p class="mb-0">
          Encontre oportunidades de compra e locação com filtros práticos e apresentação elegante.
        </p>
      </div>
    </div>
  </div>
</section>

<!-- SEARCH FLOAT -->
<section id="buscar" class="search-float">
  <div class="container container-wide">

    <form class="search-card" method="get" action="index.php">
      <!-- linha 1 -->
      <div class="row g-2 align-items-center">
        <div class="col-lg-3">
          <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="O que deseja? (ex: apartamento, suzano)">
        </div>

        <div class="col-lg-2">
          <select class="form-select" name="city">
            <option value="">Cidade</option>
            <?php foreach($options['cities'] as $c): ?>
              <option value="<?= e($c['city']) ?>" <?= $city===$c['city']?'selected':'' ?>>
                <?= e($c['city']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-lg-2">
          <input class="form-control" name="neighborhood" value="<?= e($nb) ?>" placeholder="Bairro (ex: Rua...)">
        </div>

        <div class="col-lg-2">
          <select class="form-select" name="property_style">
            <option value="">Tipos de imóvel</option>
            <?php foreach($options['styles'] as $s): ?>
              <option value="<?= e($s['property_style']) ?>" <?= $style===$s['property_style']?'selected':'' ?>>
                <?= e($s['property_style']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-lg-2">
          <select class="form-select" name="transaction_type">
            <option value="">Venda/locação</option>
            <option value="Sale" <?= $tt==='Sale'?'selected':'' ?>>Venda</option>
            <option value="Rent" <?= $tt==='Rent'?'selected':'' ?>>Locação</option>
          </select>
        </div>

        <div class="col-lg-1 d-grid">
          <button class="btn-darksoft" type="submit">
            <i class="bi bi-search"></i> Buscar
          </button>
        </div>

        <!-- linha 2 (extras) -->
        <div class="col-12 mt-1">
          <div class="row g-2">
            <div class="col-md-3">
              <input class="form-control" name="min_price" value="<?= e((string)$min_price) ?>" placeholder="Preço mín. (ex: 200000)" inputmode="decimal">
            </div>
            <div class="col-md-3">
              <input class="form-control" name="max_price" value="<?= e((string)$max_price) ?>" placeholder="Preço máx. (ex: 800000)" inputmode="decimal">
            </div>
            <div class="col-md-3">
              <select class="form-select" name="beds">
                <option value="">Quartos</option>
                <?php for($i=0;$i<=6;$i++): ?>
                  <option value="<?= $i ?>" <?= (string)$beds===(string)$i?'selected':'' ?>><?= $i ?>+</option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-md-3 d-grid">
              <a class="btn btn-outline-secondary" href="index.php">Limpar</a>
            </div>
          </div>
        </div>

      </div>
    </form>

  </div>
</section>

<div class="container container-wide">

  <?php if ($hasSearch && $list): ?>
    <!-- LISTAGEM (quando usa filtros) -->
    <div class="section-title">
      <div>
        <h2>Resultado da busca</h2>
        <div class="sub"><?= (int)$list['total'] ?> imóveis encontrados</div>
      </div>
      <a href="index.php" class="text-decoration-none" style="color:#0b0f16;">Ver destaques</a>
    </div>

    <div class="row g-3">
      <?php if (!$list['items']): ?>
        <div class="col-12">
          <div class="featured p-4">
            <div class="fw-bold">Nenhum imóvel encontrado</div>
            <div class="text-muted mt-1">Tente remover termos, trocar cidade ou ampliar a faixa de preço.</div>
          </div>
        </div>
      <?php endif; ?>

      <?php foreach($list['items'] as $p):
        $img = imageUrl((int)$p['id'], $p['primary_image'] ?? '');
        $title = $p['description'] ?: 'Imóvel';
        $loc = trim(($p['city'] ?? '') . ' - ' . ($p['state'] ?? ''));
        $pv = priceValue($p);
        $pl = priceLabel($p);
        $bedsV = (int)($p['beds'] ?? 0);
        $bathsV = (float)((float)($p['full_baths'] ?? 0) + ((float)($p['half_baths'] ?? 0) * 0.5));
        $area = (float)($p['sqFt_total'] ?? 0);
      ?>
      <div class="col-lg-3 col-md-4 col-sm-6">
        <a class="text-decoration-none" href="imovel.php?id=<?= (int)$p['id'] ?>">
          <div class="rail-card" style="min-width:auto;max-width:none;">
            <img class="rail-img" src="<?= e($img) ?>" onerror="this.src='<?= e(PLACEHOLDER_IMAGE) ?>'">
            <div class="rail-body">
              <div class="d-flex justify-content-between">
                <div class="fw-semibold"><?= e($title) ?></div>
                <span class="badge" style="background:rgba(201,161,74,.18);border:1px solid rgba(201,161,74,.35);color:#7c5c18;"><?= e($pl) ?></span>
              </div>
              <div class="text-muted small"><i class="bi bi-geo-alt"></i> <?= e($loc) ?></div>
              <div class="mt-2 fw-bold"><?= moneyBR($pv) ?></div>
              <div class="mt-2 d-flex flex-wrap gap-2">
                <span class="chip"><i class="bi bi-door-open"></i> <?= $bedsV ?></span>
                <span class="chip"><i class="bi bi-droplet"></i> <?= rtrim(rtrim(number_format($bathsV,1,'.',''),'0'),'.') ?></span>
                <?php if($area>0): ?><span class="chip"><i class="bi bi-aspect-ratio"></i> <?= number_format($area,0,',','.') ?></span><?php endif; ?>
              </div>
            </div>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if ($list['pages'] > 1): ?>
      <div class="d-flex justify-content-center mt-4">
        <nav>
          <ul class="pagination">
            <?php
              $prev = max(1, $list['page'] - 1);
              $next = min($list['pages'], $list['page'] + 1);
              $qs = $_GET;
              $make = function($p) use ($qs){
                $q = $qs; $q['page'] = $p;
                return 'index.php?' . http_build_query($q);
              };
            ?>
            <li class="page-item <?= $list['page']<=1?'disabled':'' ?>"><a class="page-link" href="<?= e($make($prev)) ?>">Anterior</a></li>
            <?php for($i=max(1,$list['page']-2); $i<=min($list['pages'],$list['page']+2); $i++): ?>
              <li class="page-item <?= $i===$list['page']?'active':'' ?>"><a class="page-link" href="<?= e($make($i)) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $list['page']>=$list['pages']?'disabled':'' ?>"><a class="page-link" href="<?= e($make($next)) ?>">Próxima</a></li>
          </ul>
        </nav>
      </div>
    <?php endif; ?>

  <?php else: ?>

    <!-- OPORTUNIDADES DE HOJE (destaque grande) -->
    <div class="section-title">
      <div>
        <h2>Oportunidades de hoje</h2>
        <div class="sub">Imóveis em destaque</div>
      </div>
      <a href="#destaques" class="text-decoration-none" style="color:#0b0f16;">Veja mais</a>
    </div>

    <?php if ($featured):
      $imgF = imageUrl((int)$featured['id'], $featured['primary_image'] ?? '');
      $pv = priceValue($featured);
      $pl = priceLabel($featured);
      $loc = trim(($featured['city'] ?? '') . ' - ' . ($featured['state'] ?? ''));
      $bedsV = (int)($featured['beds'] ?? 0);
      $bathsV = (float)((float)($featured['full_baths'] ?? 0) + ((float)($featured['half_baths'] ?? 0) * 0.5));
      $area = (float)($featured['sqFt_total'] ?? 0);
    ?>
    <div class="featured">
      <div class="row g-0">
        <div class="col-lg-7">
          <img class="featured-img" src="<?= e($imgF) ?>" onerror="this.src='<?= e(PLACEHOLDER_IMAGE) ?>'">
        </div>
        <div class="col-lg-5">
          <div class="featured-side">
            <div class="d-flex justify-content-between align-items-start gap-2">
              <div>
                <div class="text-muted small">Cód: ID <?= (int)$featured['id'] ?></div>
                <div class="fw-bold fs-5"><?= e((string)$featured['description']) ?></div>
                <div class="text-muted"><i class="bi bi-geo-alt"></i> <?= e($loc ?: 'Localização não informada') ?></div>
              </div>
              <span class="badge" style="background:rgba(201,161,74,.18);border:1px solid rgba(201,161,74,.35);color:#7c5c18;">
                <?= e($pl) ?>
              </span>
            </div>

            <div class="mt-3 price"><?= moneyBR($pv) ?></div>

            <div class="mt-3 d-flex flex-wrap gap-2">
              <span class="chip"><i class="bi bi-door-open"></i> <?= $bedsV ?> quartos</span>
              <span class="chip"><i class="bi bi-droplet"></i> <?= rtrim(rtrim(number_format($bathsV,1,'.',''),'0'),'.') ?> banh.</span>
              <?php if($area>0): ?><span class="chip"><i class="bi bi-aspect-ratio"></i> <?= number_format($area,0,',','.') ?> ft²</span><?php endif; ?>
            </div>

            <div class="mt-3 text-muted" style="line-height:1.55;">
              <?= e(mb_strimwidth(strip_tags((string)($featured['long_description'] ?? '')), 0, 160, '…', 'UTF-8')) ?>
            </div>

            <div class="mt-4 d-flex gap-2">
              <a class="btn-darksoft text-decoration-none" href="imovel.php?id=<?= (int)$featured['id'] ?>">
                Ver detalhes <i class="bi bi-arrow-right"></i>
              </a>
              <a class="btn btn-outline-secondary" href="#buscar">
                Refazer busca
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- DESTAQUES -->
    <div id="destaques" class="mt-4">

      <!-- LANÇAMENTO -->
      <div class="section-title">
        <div>
          <h2>Destaques de lançamento</h2>
          <div class="sub">Os melhores imóveis para investir</div>
        </div>
        <a href="#buscar" class="text-decoration-none" style="color:#0b0f16;">Veja mais</a>
      </div>

      <div class="rail-wrap">
        <div class="rail-nav rail-prev" data-rail="rail-launch" data-dir="-1"><i class="bi bi-chevron-left"></i></div>
        <div class="rail-nav rail-next" data-rail="rail-launch" data-dir="1"><i class="bi bi-chevron-right"></i></div>

        <div class="rail" id="rail-launch">
          <?php foreach($launch as $p):
            $img = imageUrl((int)$p['id'], $p['primary_image'] ?? '');
            $pv = priceValue($p);
            $loc = trim(($p['city'] ?? '') . ' - ' . ($p['state'] ?? ''));
          ?>
          <a class="text-decoration-none" href="imovel.php?id=<?= (int)$p['id'] ?>">
            <div class="rail-card">
              <div class="rail-actions">
                <div class="icon-btn" title="Compartilhar"><i class="bi bi-share"></i></div>
                <div class="icon-btn" title="Favoritar"><i class="bi bi-heart"></i></div>
              </div>
              <img class="rail-img" src="<?= e($img) ?>" onerror="this.src='<?= e(PLACEHOLDER_IMAGE) ?>'">
              <div class="rail-body">
                <div class="text-muted small">Cód: ID <?= (int)$p['id'] ?></div>
                <div class="fw-semibold"><?= e((string)($p['description'] ?? 'Imóvel')) ?></div>
                <div class="text-muted small"><i class="bi bi-geo-alt"></i> <?= e($loc) ?></div>
                <div class="mt-2 fw-bold"><?= moneyBR($pv) ?></div>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- VENDA -->
      <div class="section-title">
        <div>
          <h2>Destaques de venda</h2>
          <div class="sub">As melhores opções para quem quer comprar</div>
        </div>
        <a href="#buscar" class="text-decoration-none" style="color:#0b0f16;">Veja mais</a>
      </div>

      <div class="rail-wrap">
        <div class="rail-nav rail-prev" data-rail="rail-sale" data-dir="-1"><i class="bi bi-chevron-left"></i></div>
        <div class="rail-nav rail-next" data-rail="rail-sale" data-dir="1"><i class="bi bi-chevron-right"></i></div>

        <div class="rail" id="rail-sale">
          <?php foreach($sale as $p):
            $img = imageUrl((int)$p['id'], $p['primary_image'] ?? '');
            $pv = priceValue($p);
            $loc = trim(($p['city'] ?? '') . ' - ' . ($p['state'] ?? ''));
          ?>
          <a class="text-decoration-none" href="imovel.php?id=<?= (int)$p['id'] ?>">
            <div class="rail-card">
              <div class="rail-actions">
                <div class="icon-btn" title="Compartilhar"><i class="bi bi-share"></i></div>
                <div class="icon-btn" title="Favoritar"><i class="bi bi-heart"></i></div>
              </div>
              <img class="rail-img" src="<?= e($img) ?>" onerror="this.src='<?= e(PLACEHOLDER_IMAGE) ?>'">
              <div class="rail-body">
                <div class="text-muted small">Cód: ID <?= (int)$p['id'] ?></div>
                <div class="fw-semibold"><?= e((string)($p['description'] ?? 'Imóvel')) ?></div>
                <div class="text-muted small"><i class="bi bi-geo-alt"></i> <?= e($loc) ?></div>
                <div class="mt-2 fw-bold"><?= moneyBR($pv) ?></div>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- LOCAÇÃO -->
      <div class="section-title">
        <div>
          <h2>Destaques de locação</h2>
          <div class="sub">Sua nova casa está aqui</div>
        </div>
        <a href="#buscar" class="text-decoration-none" style="color:#0b0f16;">Veja mais</a>
      </div>

      <div class="rail-wrap">
        <div class="rail-nav rail-prev" data-rail="rail-rent" data-dir="-1"><i class="bi bi-chevron-left"></i></div>
        <div class="rail-nav rail-next" data-rail="rail-rent" data-dir="1"><i class="bi bi-chevron-right"></i></div>

        <div class="rail" id="rail-rent">
          <?php foreach($rent as $p):
            $img = imageUrl((int)$p['id'], $p['primary_image'] ?? '');
            $pv = priceValue($p);
            $loc = trim(($p['city'] ?? '') . ' - ' . ($p['state'] ?? ''));
          ?>
          <a class="text-decoration-none" href="imovel.php?id=<?= (int)$p['id'] ?>">
            <div class="rail-card">
              <div class="rail-actions">
                <div class="icon-btn" title="Compartilhar"><i class="bi bi-share"></i></div>
                <div class="icon-btn" title="Favoritar"><i class="bi bi-heart"></i></div>
              </div>
              <img class="rail-img" src="<?= e($img) ?>" onerror="this.src='<?= e(PLACEHOLDER_IMAGE) ?>'">
              <div class="rail-body">
                <div class="text-muted small">Cód: ID <?= (int)$p['id'] ?></div>
                <div class="fw-semibold"><?= e((string)($p['description'] ?? 'Imóvel')) ?></div>
                <div class="text-muted small"><i class="bi bi-geo-alt"></i> <?= e($loc) ?></div>
                <div class="mt-2 fw-bold"><?= moneyBR($pv) ?></div>
              </div>
            </div>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- BAIRROS EM DESTAQUE -->
    <div class="section-title">
      <div>
        <h2>Bairros em destaque</h2>
        <div class="sub">Confira os mais procurados</div>
      </div>
      <a href="#buscar" class="text-decoration-none" style="color:#0b0f16;">Buscar</a>
    </div>

    <div class="nb-grid">
      <?php foreach($neigh as $n):
        $nid = (int)($n['sample_id'] ?? 0);
        $img = imageUrl($nid, $n['sample_image'] ?? '');
        $label = (string)($n['neighborhood'] ?? 'Bairro');
      ?>
      <a class="text-decoration-none" href="index.php?neighborhood=<?= urlencode($label) ?>">
        <div class="nb-card">
          <img src="<?= e($img) ?>" onerror="this.src='<?= e(PLACEHOLDER_IMAGE) ?>'">
          <div class="nb-overlay">
            <?= e($label) ?>
            <span class="small" style="opacity:.85;"> • <?= (int)$n['total'] ?> imóveis</span>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</div>

<script>
  /**
   * ✅ FIX 2: Setas dos slides
   * - Agora não depende do onclick inline
   * - Usa data-rail e data-dir, e faz scroll do container certo
   */
  (function () {
    function scrollRail(railId, dir){
      const el = document.getElementById(railId);
      if(!el) return;
      const step = Math.min(700, el.clientWidth * 0.9);
      el.scrollBy({ left: dir * step, behavior: 'smooth' });
    }

    document.querySelectorAll('.rail-nav').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const rail = btn.getAttribute('data-rail');
        const dir = parseInt(btn.getAttribute('data-dir') || '1', 10);
        scrollRail(rail, dir);
      });
    });
  })();
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>