<?php
// partials/header.php
declare(strict_types=1);
require_once __DIR__ . '/../functions.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' • ' : '' ?>Imobiliária</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    :root{
      --radius: 18px;
      --border: #e9ecef;
      --muted: #6c757d;
      --bg: #f6f7f9;
      --shadow: 0 16px 40px rgba(15, 23, 42, .08);
      --shadow-sm: 0 10px 25px rgba(15, 23, 42, .06);
    }

    body{ background: var(--bg); color:#0f172a; }
    .container-narrow{ max-width: 1180px; }

    .navbar{ background:#fff; border-bottom: 1px solid var(--border); }
    .brand-dot{ width:10px; height:10px; border-radius:99px; display:inline-block; background:#0d6efd; margin-right:10px; }

    .card-soft{
      background:#fff;
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow-sm);
    }

    .hero{
      background: linear-gradient(180deg, #ffffff, #f9fafb);
      border: 1px solid var(--border);
      border-radius: calc(var(--radius) + 6px);
      box-shadow: var(--shadow);
      overflow:hidden;
    }

    .form-control, .form-select{
      border-radius: 14px;
      border: 1px solid var(--border);
      background:#fff;
    }
    .btn{ border-radius: 14px; }

    .badge-soft{
      background:#eef2ff;
      color:#3730a3;
      border: 1px solid #e0e7ff;
      font-weight: 600;
    }

    .prop-card{
      border-radius: var(--radius);
      overflow:hidden;
      border: 1px solid var(--border);
      background:#fff;
      box-shadow: var(--shadow-sm);
      transition: transform .15s ease, box-shadow .15s ease;
      height: 100%;
    }
    .prop-card:hover{ transform: translateY(-2px); box-shadow: var(--shadow); }

    .prop-cover{
      height: 220px;
      width: 100%;
      object-fit: cover;
      background: #f1f5f9;
    }

    .text-muted2{ color: var(--muted); }
    .pill{
      display:inline-flex;
      gap:8px;
      align-items:center;
      border: 1px solid var(--border);
      background:#fff;
      padding: 8px 10px;
      border-radius: 999px;
      font-size: .9rem;
      color:#0f172a;
    }

    a{ color: inherit; }
    a:hover{ color: inherit; }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container container-narrow py-2">
      <a class="navbar-brand fw-semibold" href="index.php">
        <span class="brand-dot"></span> Imobiliária <span class="text-primary">Clean</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="topnav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php"><i class="bi bi-search"></i> Buscar</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <main class="container container-narrow py-4">