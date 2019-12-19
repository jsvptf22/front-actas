<?php
$max_salida = 10;
$rootPath = $ruta = '';

while ($max_salida > 0) {
    if (is_file($ruta . 'sw.js')) {
        $rootPath = $ruta;
        break;
    }

    $ruta .= '../';
    $max_salida--;
}

include_once $rootPath . 'views/assets/librerias.php';

$params = json_encode([
    'baseUrl' => $rootPath,
] + $_REQUEST);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" href="<%= BASE_URL %>favicon.ico" />
    <title>front</title>

    <style>
        .template {
            border: 1px solid #cacaca;
            margin-bottom: 8px;
            box-shadow: 2px 2px 8px #c6c6c6;
        }

        #template_parent {
            height: 100vh;
            overflow-y: auto;
        }

        .firm_square {
            height: 150px;
        }
    </style>
</head>

<body>
    <div id="app"></div>
    <?= vue() ?>
    <?= vuex() ?>
    <?= vueRouter() ?>
    <?= jquery() ?>
    <?= bootstrap() ?>
    <?= theme() ?>
    <?= icons() ?>
    <?= moment() ?>
    <?= fab() ?>
    <script id="base_script" data-params='<?= $params ?>' type="module" src="<?= $rootPath ?>views/modules/actas/views/document/js/entrie.js"></script>
</body>

</html>