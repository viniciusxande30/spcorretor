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
      --bg:#ffffff;
      --muted:#6b7280;
      --border:#e5e7eb;
      --black:#0b0f16;
      --gold:#c9a14a;
      --radius:18px;
      --shadow:0 14px 40px rgba(15, 23, 42, .10);
      --shadow-sm:0 10px 25px rgba(15, 23, 42, .08);
    }

    body{ background:var(--bg); color:#0f172a; }
    .container-wide{ max-width:1180px; }

    /* NAVBAR */
    .topbar{
      background: #0b0f16;
      border-bottom: 1px solid rgba(255,255,255,.08);
    }
    .topbar .nav-link, .topbar .navbar-brand{ color: rgba(255,255,255,.90) !important; }
    .topbar .nav-link:hover{ color:#fff !important; }
    .brand-mark{
      display:inline-flex; align-items:center; gap:10px;
      letter-spacing:.5px;
    }
    .brand-badge{
      width:34px; height:34px; border-radius:999px;
      display:inline-grid; place-items:center;
      background: rgba(201,161,74,.15);
      border:1px solid rgba(201,161,74,.35);
      color: var(--gold);
    }

    /* HERO */
    .hero{
      position:relative;
      min-height: 420px;
      border-radius: 0 0 34px 34px;
      overflow:hidden;
      background: #111;
    }
    .hero::before{
      content:"";
      position:absolute; inset:0;
      background-image: var(--hero);
      background-size: cover;
      background-position: center;
      filter: saturate(1.05);
      transform: scale(1.02);
    }
    .hero::after{
      content:"";
      position:absolute; inset:0;
      background: linear-gradient(90deg, rgba(0,0,0,.62), rgba(0,0,0,.18), rgba(0,0,0,.45));
    }
    .hero-content{
      position:relative;
      padding: 48px 0 120px;
      color:#fff;
    }
    .hero h1{ font-weight:700; letter-spacing:.2px; }
    .hero p{ color: rgba(255,255,255,.85); max-width: 680px; }

    /* SEARCH BAR flutuante */
    .search-float{
      position:relative;
      margin-top: -60px;
      z-index:5;
    }
    .search-card{
      background:#fff;
      border:1px solid var(--border);
      border-radius: 999px;
      box-shadow: var(--shadow);
      padding: 10px;
    }
    .search-card .form-control, .search-card .form-select{
      border:none;
      box-shadow:none !important;
      border-radius: 999px;
      height: 46px;
      background: #fff;
    }
    .search-sep{
      width:1px; background: var(--border);
      align-self: stretch;
      margin: 6px 0;
    }

    /* SECTIONS */
    .section-title{
      display:flex; align-items:flex-end; justify-content:space-between;
      gap:14px;
      margin: 26px 0 12px;
    }
    .section-title h2{ font-size: 1.25rem; margin:0; font-weight:700; }
    .section-title .sub{ color: var(--muted); font-size:.95rem; margin-top:4px; }

    /* FEATURED big card */
    .featured{
      background:#fff;
      border:1px solid var(--border);
      border-radius: 22px;
      box-shadow: var(--shadow-sm);
      overflow:hidden;
    }
    .featured-img{
      height: 360px;
      width:100%;
      object-fit: cover;
      background:#f3f4f6;
    }
    .featured-side{ padding: 18px; }
    .price{ font-weight:800; font-size: 1.25rem; }
    .chip{
      display:inline-flex; gap:8px; align-items:center;
      padding: 6px 10px;
      border-radius: 999px;
      border: 1px solid var(--border);
      color:#111827;
      background:#fff;
      font-size:.9rem;
    }
    .chip i{ color: #111827; opacity:.75; }
    .btn-darksoft{
      background:#111827;
      border:1px solid #111827;
      color:#fff;
      border-radius: 999px;
      padding: 10px 16px;
    }

    /* HORIZONTAL RAIL */
    .rail-wrap{ position:relative; }
    .rail{
      display:flex;
      gap: 14px;
      overflow:auto;
      scroll-behavior:smooth;
      padding: 6px 2px 14px;
    }
    .rail::-webkit-scrollbar{ height: 10px; }
    .rail::-webkit-scrollbar-thumb{ background:#e5e7eb; border-radius:99px; }
    .rail-card{
      min-width: 265px;
      max-width: 265px;
      background:#fff;
      border:1px solid var(--border);
      border-radius: 18px;
      box-shadow: var(--shadow-sm);
      overflow:hidden;
      position:relative;
    }
    .rail-img{ height: 150px; width:100%; object-fit: cover; background:#f3f4f6; }
    .rail-body{ padding: 12px; }
    .rail-actions{
      position:absolute; top:10px; right:10px;
      display:flex; gap:8px;
    }
    .icon-btn{
      width:34px; height:34px; border-radius:999px;
      display:grid; place-items:center;
      background: rgba(255,255,255,.92);
      border:1px solid rgba(0,0,0,.08);
      color:#111827;
    }
    .icon-btn:hover{ filter: brightness(.98); }

    .rail-nav{
      position:absolute;
      top: 56px;
      width: 42px; height: 42px;
      border-radius: 999px;
      background:#fff;
      border:1px solid var(--border);
      box-shadow: var(--shadow-sm);
      display:grid; place-items:center;
      color:#111827;
      cursor:pointer;
      user-select:none;
    }
    .rail-prev{ left: -10px; }
    .rail-next{ right: -10px; }

    /* Neighborhood cards */
    .nb-grid{
      display:grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 14px;
    }
    .nb-card{
      position:relative;
      border-radius: 18px;
      overflow:hidden;
      border:1px solid var(--border);
      box-shadow: var(--shadow-sm);
      background:#fff;
      min-height: 160px;
    }
    .nb-card img{ width:100%; height: 190px; object-fit:cover; }
    .nb-overlay{
      position:absolute; inset:auto 0 0 0;
      padding: 12px;
      background: linear-gradient(180deg, rgba(0,0,0,0), rgba(0,0,0,.72));
      color:#fff;
      font-weight:700;
    }

    @media (max-width: 992px){
      .search-card{ border-radius: 18px; }
      .search-sep{ display:none; }
      .nb-grid{ grid-template-columns: 1fr; }
      .hero-content{ padding: 34px 0 120px; }
    }
  </style>
</head>

<body>
<nav class="navbar navbar-expand-lg topbar">
  <div class="container container-wide py-2">
    <a class="navbar-brand brand-mark fw-semibold" href="index.php">
      <span class="brand-badge"><i class="bi bi-compass"></i></span>
      <span>SP <span style="color:var(--gold);">CORRETOR</span></span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav">
      <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
    </button>

    <div class="collapse navbar-collapse" id="topnav">
      <ul class="navbar-nav mx-auto gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#buscar">Buscar imóvel</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#destaques">Destaques</a></li>
        <li class="nav-item"><a class="nav-link" href="index.php#contato">Contato</a></li>
      </ul>

      <div class="d-flex align-items-center gap-3">
        <span class="text-white-50 small d-none d-lg-inline"><i class="bi bi-telephone"></i> (11) 00000-0000</span>
        <a class="btn btn-sm" style="border-radius:999px;border:1px solid rgba(255,255,255,.18);color:#fff;" href="#buscar">
          <i class="bi bi-search"></i> Buscar
        </a>
      </div>
    </div>
  </div>
</nav>

<main>