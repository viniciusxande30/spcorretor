<?php
// partials/footer.php
declare(strict_types=1);
?>
<section id="contato" class="mt-5" style="background:#0b0f16;">
  <div class="container container-wide py-5 text-white">
    <div class="row g-4">
      <div class="col-lg-5">
        <div class="fw-semibold" style="letter-spacing:.4px;">
          SP<span style="color:#c9a14a;">CORRETOR</span>
        </div>
        <div class="text-white-50 mt-2">
          Feito pela ID Solutions
        </div>
        <div class="text-white-50 small mt-3">
          <div><i class="bi bi-geo-alt"></i> São Paulo - SP</div>
          <div><i class="bi bi-telephone"></i> (11) 0000-0000</div>
          <div><i class="bi bi-envelope"></i> contato@seudominio.com.br</div>
        </div>
      </div>

      <div class="col-lg-3">
        <div class="fw-semibold mb-2">Navegação rápida</div>
        <div class="d-flex flex-column gap-2">
          <a class="text-white-50" href="index.php">Home</a>
          <a class="text-white-50" href="#buscar">Buscar imóvel</a>
          <a class="text-white-50" href="#destaques">Destaques</a>
          <a class="text-white-50" href="#contato">Contato</a>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="fw-semibold mb-2">Suporte</div>
        <div class="text-white-50">
          Precisa de integração com WhatsApp, CRM ou anúncios? Dá pra evoluir isso em etapas.
        </div>

        <a class="btn btn-sm mt-3"
           style="border-radius:999px;background:#c9a14a;border:1px solid #c9a14a;color:#111;"
           href="#buscar">
          <i class="bi bi-lightning-charge"></i> Quero buscar agora
        </a>
      </div>
    </div>

    <hr style="border-color: rgba(255,255,255,.10)" class="my-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 text-white-50 small">
      <div>© <?= date('Y') ?> • IMOB PREMIUM</div>
      <div>PHP • Bootstrap • JS</div>
    </div>
  </div>
</section>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
  // Controles dos “rails” (carrossel horizontal igual do print)
  function railScroll(id, dir){
    const el = document.getElementById(id);
    if(!el) return;
    const step = Math.min(700, el.clientWidth * 0.9);
    el.scrollBy({ left: dir * step, behavior: 'smooth' });
  }
</script>

</body>
</html>