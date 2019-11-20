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

include_once $rootPath . 'assets/librerias.php';

$params = json_encode([
    'baseUrl' => $rootPath,
]);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link rel="icon" href="<%= BASE_URL %>favicon.ico" />
    <title>front</title>
    <?= jquery() ?>
    <?= bootstrap() ?>
    <?= theme() ?>
    <?= icons() ?>
    <?= moment() ?>
</head>

<body>
    <div id="app"></div>
    <script src="<?= $rootPath ?>views/modules/actas/node_modules/vue/dist/vue.min.js"></script>
    <script src="<?= $rootPath ?>views/modules/actas/node_modules/vuex/dist/vuex.min.js"></script>
    <script src="<?= $rootPath ?>views/modules/actas/node_modules/vue-router/dist/vue-router.min.js"></script>
    <script src="<?= $rootPath ?>views/modules/actas/node_modules/axios/dist/axios.min.js"></script>
    <script id="document_script" data-params='<?= $params ?>' type="module" src="<?= $rootPath ?>views/modules/actas/views/home/home.js"></script>
</body>

</html>