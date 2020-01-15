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

$Formato = Saia\models\formatos\Formato::findByAttributes(['nombre' => 'acta']);
$CamposFormato = Saia\models\formatos\CamposFormato::findByAttributes([
    'nombre' => 'asistentes_externos'
]);

$params = json_encode([
    'baseUrl' => $rootPath,
    'fieldId' => $CamposFormato->getPK()
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
</head>

<body>
    <div class="container" style="height: 100vh; overflow-y:auto">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Crear evento</h5>
                    </div>
                    <div class="card-body">
                        <form id="planning_form">
                            <div class="form-group form-group-default input-group required date">
                                <div class="form-input-group">
                                    <label>Fecha:</label>
                                    <input name="initialDate" type="text" class="form-control" placeholder="Seleccione.." id="initialDate">
                                </div>
                                <div class="input-group-append ">
                                    <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>
                            <div class="form-group form-group-default required">
                                <label for="subject">Asunto</label>
                                <input name="subject" id="subject" type="text" class="form-control">
                            </div>
                            <div class='form-group form-group-default form-group-default-select2' id="assistants_container">
                                <label>Asistentes</label>
                                <select class="full-width" id='user_select' multiple="multiple"></select>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-complete" id="saveData">Guardar</button>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="row">
                    <div class="col-12">
                        <h6>Próximos eventos</h6>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12" id="list"></div>
                </div>
            </div>
        </div>
    </div>

    <?= jquery() ?>
    <?= bootstrap() ?>
    <?= theme() ?>
    <?= icons() ?>
    <?= moment() ?>
    <?= select2() ?>
    <?= dateTimePicker() ?>
    <?= validate() ?>
    <script id="base_script" data-params='<?= $params ?>' src="<?= $rootPath ?>views/modules/actas/views/planning/js/index.js"></script>
</body>

</html>