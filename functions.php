<?php
// functions.php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function moneyBR(?float $value): string {
  if ($value === null) return '—';
  return 'R$ ' . number_format((float)$value, 2, ',', '.');
}

function asInt($v, int $default = 0): int {
  if ($v === null || $v === '') return $default;
  return (int)$v;
}

function asFloat($v, float $default = 0.0): float {
  if ($v === null || $v === '') return $default;
  return (float)$v;
}

function imageUrl(int $id, ?string $filename): string {
  $filename = trim((string)$filename);
  if ($filename === '') return PLACEHOLDER_IMAGE;

  // evita path traversal básico
  $filename = basename($filename);

  // padrão: .../main_images/{id}/{filename}
  return rtrim(REMOTE_IMAGE_BASE, '/') . '/' . $id . '/' . $filename;
}

function normalizeState(?string $state): ?string {
  $state = trim((string)$state);
  if ($state === '') return null;
  // deixa "SP", "São Paulo", etc
  return $state;
}

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

  if (!empty($filters['state'])) {
    $where[] = "state = :state";
    $params[':state'] = $filters['state'];
  }

  if (!empty($filters['transaction_type'])) {
    $where[] = "transaction_type = :tt";
    $params[':tt'] = $filters['transaction_type'];
  }

  // Preço: se tiver rent_price preenchido e transaction = Rent, usa rent_price.
  // Caso contrário, usa rate (venda). Para filtro, fazemos lógica simples:
  // - min_price / max_price aplica em (CASE WHEN transaction_type='Rent' THEN rental_value OR rent_price ELSE rate END)
  if ($filters['min_price'] !== null) {
    $where[] = "(CASE 
      WHEN transaction_type = 'Rent' THEN COALESCE(rental_value, rent_price, 0)
      ELSE COALESCE(rate, 0)
    END) >= :minp";
    $params[':minp'] = $filters['min_price'];
  }

  if ($filters['max_price'] !== null) {
    $where[] = "(CASE 
      WHEN transaction_type = 'Rent' THEN COALESCE(rental_value, rent_price, 0)
      ELSE COALESCE(rate, 0)
    END) <= :maxp";
    $params[':maxp'] = $filters['max_price'];
  }

  if ($filters['beds'] !== null) {
    $where[] = "beds >= :beds";
    $params[':beds'] = $filters['beds'];
  }

  // Banhos = full_baths + (half_baths * 0.5) — no mínimo, full_baths
  if ($filters['baths'] !== null) {
    $where[] = "(COALESCE(full_baths,0) + (COALESCE(half_baths,0) * 0.5)) >= :baths";
    $params[':baths'] = $filters['baths'];
  }

  return $where ? ("WHERE " . implode(" AND ", $where)) : "";
}

function fetchFilterOptions(): array {
  $pdo = db();
  $table = DB_TABLE;

  $cities = $pdo->query("SELECT city, COUNT(*) as total 
                         FROM {$table}
                         WHERE city IS NOT NULL AND city <> ''
                         GROUP BY city
                         ORDER BY city ASC")->fetchAll();

  $states = $pdo->query("SELECT state, COUNT(*) as total 
                         FROM {$table}
                         WHERE state IS NOT NULL AND state <> ''
                         GROUP BY state
                         ORDER BY state ASC")->fetchAll();

  $transactionTypes = $pdo->query("SELECT transaction_type, COUNT(*) as total
                                   FROM {$table}
                                   WHERE transaction_type IS NOT NULL AND transaction_type <> ''
                                   GROUP BY transaction_type
                                   ORDER BY transaction_type ASC")->fetchAll();

  return [
    'cities' => $cities,
    'states' => $states,
    'transactionTypes' => $transactionTypes,
  ];
}

function listProperties(array $filters, int $page, int $perPage): array {
  $pdo = db();
  $table = DB_TABLE;

  $params = [];
  $where = buildWhere($filters, $params);

  $offset = max(0, ($page - 1) * $perPage);

  // Total
  $sqlCount = "SELECT COUNT(*) as total FROM {$table} {$where}";
  $stmt = $pdo->prepare($sqlCount);
  $stmt->execute($params);
  $total = (int)$stmt->fetchColumn();

  // Lista (campos principais)
  $sql = "SELECT 
            id, description, long_description, transaction_type,
            rate, rent_price, rental_value,
            beds, full_baths, half_baths, sqFt_total,
            street_name, street_type, street_number, unit_number,
            city, state, zip,
            primary_image, latitude, longitude, status, date_created
          FROM {$table}
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
  $table = DB_TABLE;

  $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
  $stmt->execute([':id' => $id]);
  $row = $stmt->fetch();
  return $row ?: null;
}