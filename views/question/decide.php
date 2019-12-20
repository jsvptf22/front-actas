<?php
$max_salida = 10;
$rootPath = $ruta = '';

while ($max_salida > 0) {
    if (is_file($ruta . 'sw.js')) {
        $rootPath = $ruta;
    }

    $ruta .= '../';
    $max_salida--;
}

include_once $rootPath . 'views/assets/librerias.php';

$decrypt = CriptoController::decrypt_blowfish($_REQUEST['q']);
$data = json_decode($decrypt);

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
</head>

<body>
    <div class="container h-100">
        <div class="row d-flex justify-content-center d-flex align-items-center h-100">
            <div class="col-12 col-md-auto">
                <div class="card">
                    <div class="card-body">
                        <p><?= $data->question ?></p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-complete send" data-type="1">Aprobar</button>
                        <button class="btn btn-danger send" data-type="0">Rechazar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= jquery() ?>
    <?= bootstrap() ?>
    <?= theme() ?>
    <?= icons() ?>
    <script data-baseurl="<?= $rootPath ?>" src="<?= $rootPath ?>views/assets/theme/assets/js/cerok_libraries/notifications/topNotification.js" type="text/javascript"></script>
    <script id="vote_script" data-params='<?= $decrypt ?>'>
        $(function() {
            let params = $('#vote_script').data('params');
            let baseUrl = '<?= $rootPath ?>';

            $(".send").on('click', function() {
                $.post(
                    `${baseUrl}app/modules/back_actas/app/preguntas/votar.php`, {
                        key: localStorage.getItem('key'),
                        token: localStorage.getItem('token'),
                        question: params.id,
                        key: params.user,
                        action: $(this).data('type')
                    },
                    function(response) {
                        if (response.success) {
                            top.notification({
                                type: 'success',
                                message: response.message
                            });
                        } else {
                            top.notification({
                                type: 'error',
                                message: response.message
                            });
                        }
                    },
                    'json'
                );
            })
        });
    </script>
</body>

</html>