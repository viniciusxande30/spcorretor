<?php
// config.php
declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_NAME', 'idso3685_imobiliaria');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

define('DB_TABLE', 'tblitems');

define('PER_PAGE', 12);

// Imagens remotas: .../main_images/{id}/{primary_image}
define('REMOTE_IMAGE_BASE', 'https://idimob.spcorretor.com.br/modules/realestate/uploads/main_images/');
define('PLACEHOLDER_IMAGE', 'assets/img/placeholder.jpg');

// Imagem do HERO (pode trocar por outra)
define('HERO_BG', 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1800&q=60');
