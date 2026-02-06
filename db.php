<?php
// db.php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function db(): PDO {
  static $pdo = null;
  if ($pdo instanceof PDO) return $pdo;

  $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

  try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
  } catch (PDOException $e) {
    http_response_code(500);
    echo "<h2>Falha ao conectar no banco local</h2>";
    echo "<p><b>Host:</b> " . htmlspecialchars(DB_HOST) . "</p>";
    echo "<p><b>DB:</b> " . htmlspecialchars(DB_NAME) . "</p>";
    echo "<p><b>User:</b> " . htmlspecialchars(DB_USER) . "</p>";
    echo "<p><b>Erro:</b> " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
  }
}
