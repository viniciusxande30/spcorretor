<?php
// config.php
declare(strict_types=1);

define('DB_HOST', 'localhost');
define('DB_NAME', 'idso3685_imobiliaria');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Ajuste se sua tabela tiver outro nome
define('DB_TABLE', 'tblitems');

define('PER_PAGE', 9);

// Base das imagens no seu site (remotas)
define('REMOTE_IMAGE_BASE', 'https://idimob.spcorretor.com.br/modules/realestate/uploads/main_images/');
define('PLACEHOLDER_IMAGE', 'assets/img/placeholder.jpg');
