<?php
// functions.php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function asInt($v, int $d = 0): int { return ($v === null || $v === '') ? $d : (int)$v; }
function asFloat($v, float $d = 0.0): float { return ($v === null || $v === '') ? $d : (float)$v; }

function moneyBR(?float $value): string {
  if ($value === null) return '—';
  return 'R$ ' . number_format((float)$value, 2, ',', '.');
}

function imageUrl(int $id, ?string $filename): string {
  $filename = trim((string)$filename);
  if ($filename === '') return PLACEHOLDER_IMAGE;
  $filename = basename($filename);
  return rtrim(REMOTE_IMAGE_BASE, '/') . '/' . $id . '/' . $filename;
}

function priceValue(array $p): float {
  $isRent = (($p['transaction_type'] ?? '') === 'Rent');
  if ($isRent) return (float)($p['rental_value'] ?? $p['rent_price'] ?? 0);
  return (float)($p['rate'] ?? 0);
}

function priceLabel(array $p): string {
  return (($p['transaction_type'] ?? '') === 'Rent') ? 'Locação' : 'Venda';
}

/**
 * “bairro” não existe no seu schema.
 * Aqui usamos o campo "street_name" como fallback de bairro (só para filtro).
 * Se depois você criar coluna bairro, só ajustar aqui.
 */
function buildWhere(array $filters, array &$params): string {
  $where = [];

  if (!empty($filters['q'])) {
    $where[] = "(description LIKE :q OR long_description LIKE :q OR street_name LIKE :q OR city LIKE :q OR state LIKE :q)";
    $params[':q'] = '%' . $filters['q'] . '%';
  }
  if (!empty($filters['city'])) {
    $where[] = "city = :city";
    $params[':city'] = $filters['city'];
  }
  if (!empty($filters['neighborhood'])) {
    // usando street_name como “bairro” por enquanto
    $where[] = "street_name LIKE :nb";
    $params[':nb'] = '%' . $filters['neighborhood'] . '%';
  }
  if (!empty($filters['property_style'])) {
    $where[] = "property_style = :ps";
    $params[':ps'] = $filters['property_style'];
  }
  if (!empty($filters['transaction_type'])) {
    $where[] = "transaction_type = :tt";
    $params[':tt'] = $filters['transaction_type'];
  }
  if ($filters['min_price'] !== null) {
    $where[] = "(CASE WHEN transaction_type='Rent' THEN COALESCE(rental_value, rent_price, 0) ELSE COALESCE(rate,0) END) >= :minp";
    $params[':minp'] = $filters['min_price'];
  }
  if ($filters['max_price'] !== null) {
    $where[] = "(CASE WHEN transaction_type='Rent' THEN COALESCE(rental_value, rent_price, 0) ELSE COALESCE(rate,0) END) <= :maxp";
    $params[':maxp'] = $filters['max_price'];
  }
  if ($filters['beds'] !== null) {
    $where[] = "COALESCE(beds,0) >= :beds";
    $params[':beds'] = $filters['beds'];
  }

  return $where ? ("WHERE " . implode(" AND ", $where)) : "";
}

function fetchFilterOptions(): array {
  $pdo = db();
  $t = DB_TABLE;

  $cities = $pdo->query("SELECT city, COUNT(*) total FROM {$t} WHERE city<>'' AND city IS NOT NULL GROUP BY city ORDER BY city")->fetchAll();
  $styles = $pdo->query("SELECT property_style, COUNT(*) total FROM {$t} WHERE property_style<>'' AND property_style IS NOT NULL GROUP BY property_style ORDER BY property_style")->fetchAll();

  return ['cities' => $cities, 'styles' => $styles];
}

function listProperties(array $filters, int $page, int $perPage): array {
  $pdo = db();
  $t = DB_TABLE;

  $params = [];
  $where = buildWhere($filters, $params);

  $offset = max(0, ($page - 1) * $perPage);

  $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$t} {$where}");
  $stmt->execute($params);
  $total = (int)$stmt->fetchColumn();

  $sql = "SELECT id, description, long_description, transaction_type,
                 rate, rent_price, rental_value,
                 beds, full_baths, half_baths, sqFt_total,
                 street_name, street_number, unit_number,
                 city, state, zip,
                 primary_image, status, date_created, new_construction
          FROM {$t}
          {$where}
          ORDER BY date_created DESC, id DESC
          LIMIT :limit OFFSET :offset";

  $stmt = $pdo->prepare($sql);
  foreach ($params as $k => $v) $stmt->bindValue($k, $v);
  $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
  $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
  $stmt->execute();

  $items = $stmt->fetchAll();

  return [
    'items' => $items,
    'total' => $total,
    'page' => $page,
    'perPage' => $perPage,
    'pages' => (int)ceil($total / max(1, $perPage)),
  ];
}

function getPropertyById(int $id): ?array {
  $pdo = db();
  $t = DB_TABLE;
  $stmt = $pdo->prepare("SELECT * FROM {$t} WHERE id=:id LIMIT 1");
  $stmt->execute([':id' => $id]);
  $row = $stmt->fetch();
  return $row ?: null;
}

/**
 * Seções estilo print
 */
function fetchFeaturedToday(): ?array {
  $pdo = db();
  $t = DB_TABLE;

  // Preferir "new" / "Completed" / mais recente — adaptável
  $sql = "SELECT id, description, long_description, transaction_type,
                 rate, rent_price, rental_value, beds, full_baths, half_baths,
                 sqFt_total, city, state, primary_image, date_created
          FROM {$t}
          ORDER BY date_created DESC, id DESC
          LIMIT 1";
  $row = $pdo->query($sql)->fetch();
  return $row ?: null;
}

function fetchShowcase(string $kind, int $limit = 12): array {
  $pdo = db();
  $t = DB_TABLE;

  // lançamento = new_construction yes/1/true OR status 'new'
  // venda = Sale
  // locação = Rent
  if ($kind === 'launch') {
    $sql = "SELECT id, description, long_description, transaction_type, rate, rent_price, rental_value,
                   beds, full_baths, half_baths, sqFt_total, city, state, primary_image, status, new_construction, date_created
            FROM {$t}
            WHERE (LOWER(COALESCE(new_construction,'')) IN ('yes','1','true','sim') OR LOWER(COALESCE(status,''))='new')
            ORDER BY date_created DESC, id DESC
            LIMIT :lim";
  } elseif ($kind === 'sale') {
    $sql = "SELECT id, description, long_description, transaction_type, rate, rent_price, rental_value,
                   beds, full_baths, half_baths, sqFt_total, city, state, primary_image, date_created
            FROM {$t}
            WHERE transaction_type='Sale'
            ORDER BY date_created DESC, id DESC
            LIMIT :lim";
  } else { // rent
    $sql = "SELECT id, description, long_description, transaction_type, rate, rent_price, rental_value,
                   beds, full_baths, half_baths, sqFt_total, city, state, primary_image, date_created
            FROM {$t}
            WHERE transaction_type='Rent'
            ORDER BY date_created DESC, id DESC
            LIMIT :lim";
  }

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll();
}

function fetchTopNeighborhoods(int $limit = 6): array {
  $pdo = db();
  $t = DB_TABLE;

  // Como não tem bairro, agrupamos por street_name (só pra criar o bloco “Bairros em destaque”)
  $sql = "SELECT street_name as neighborhood, COUNT(*) total,
                 MAX(id) as sample_id,
                 MAX(primary_image) as sample_image
          FROM {$t}
          WHERE street_name IS NOT NULL AND street_name <> ''
          GROUP BY street_name
          ORDER BY total DESC
          LIMIT :lim";

  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
  $stmt->execute();
  return $stmt->fetchAll();
}