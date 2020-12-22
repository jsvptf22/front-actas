<?php
$max_salida = 10;
$rootPath = $ruta = '';

while ($max_salida > 0) {
    if (is_file($ruta . 'index.php')) {
        $rootPath = $ruta;
        break;
    }

    $ruta .= '../';
    $max_salida--;
}

include_once $rootPath . 'app/vendor/autoload.php';

use App\Bundles\actas\formatos\acta\FtActa;
use Saia\controllers\SessionController;
use Saia\models\documento\Documento;

try {
    SessionController::goUp($_REQUEST['token'], $_REQUEST['key']);
    
    $documentId = $_REQUEST["documentId"];
    $Documento = new Documento($documentId);
    $FtActa = $Documento->getFt();
    $Formato = $FtActa->getFormat();

    $Documento->addRead(SessionController::getValue('idfuncionario'));     
    ?>


<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=10.0, shrink-to-fit=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta content="" name="description"/>
    <meta content="" name="Cero K"/>
    <link href="<?= ABSOLUTE_SAIA_ROUTE ?>views/formato/css/mostrar.css" rel="stylesheet" type="text/css">
<style>
    body #documento{
        font-size: 11px
    }
</style>
</head>

<body>
<?php if(
    !$_REQUEST['mostrar_pdf'] &&
    !$_REQUEST['actualizar_pdf'] &&
    0 == 0
): ?>

<div class="container bg-white mx-0 px-1 px-md-1 mw-100">
    <div id="documento" class="row p-0 m-0">
        <div id="pag-0" class="col-12 page_border bg-white">
            <div class="page_margin_top mb-0" id="doc_header">
                <?= Saia\controllers\functions\Header::crearEncabezadoPiePagina(
                    '<table align="center" border="1" cellspacing="0" style="border-collapse:collapse; width:100%">
                        <tbody>
                            <tr>
                                <td style="border-color:#b6b8b7; text-align:center; width:30%">{*nombre_empresa*}</td>
                                <td style="border-color:#b6b8b7; text-align:center; vertical-align:middle; width:40%"><strong>{*nombre_formato*}</strong></td>
                                <td style="border-color:#b6b8b7; text-align:center; vertical-align:middle; width:30%">{*logo_empresa*}</td>
                            </tr>
                        </tbody>
                    </table>
                    ',
                    $Documento
                ) ?>
            </div>
            <div id="pag_content-0" class="page_content">
                <div id="page_overflow">
                    <?= Saia\controllers\functions\Header::crearEncabezadoPiePagina(
                    '<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <td class="bold">Acta N&deg;</td>
                    <td>{*formato_numero*}</td>
                    <td
                        rowspan="4"
                        class="text-center align-middle"
                    >
                        {*qrCodeHtml*}
                    </td>
                </tr>
                <tr>
                    <td class="bold">Tema / Asunto</td>
                    <td>{*asunto*}</td>
                </tr>
                <tr>
                    <td class="bold">Inicio</td>
                    <td>{*fecha_inicial*}</td>
                </tr>
                <tr>
                    <td class="bold">Fin</td>
                    <td>{*fecha_final*}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="row">
<div class="col-12">
<table class="table table-bordered">
	<tbody>
		<tr>
			<td class="text-center">Participantes</td>
		</tr>
		<tr>
			<td><span class="bold">Asistentes:</span> {*listInternalAssistants*}</td>
		</tr>
		<tr>
			<td><span class="bold">Invitados:</span> {*listExternalAssistants*}</td>
		</tr>
	</tbody>
</table>
</div>
</div>

<div class="row">
<div class="col-12">
<table class="table table-bordered">
	<tbody>
		<tr>
			<td class="bold text-center">Puntos a Tratar / Orden del d&iacute;a</td>
		</tr>
		<tr>
			<td>
				{*listTopics*}
			</td>
		</tr>
	</tbody>
</table>
</div>
</div>

<div class="row">
<div class="col-12">
<table class="table table-bordered">
	<tbody>
		<tr>
			<td class="bold text-center">Puntos Tratados / Desarrollo</td>
		</tr>
		<tr>
			<td>{*listTopicDescriptions*}</td>
		</tr>
	</tbody>
</table>
</div>
</div>

<div class="row">
<div class="col-12">
{*listQuestions*}
</div>
</div>

<div class="row">
<div class="col-12">
{*listTasks*}
</div>
</div>

<div class="row">
<div class="col-12">
<table class="table table-bordered">
	<tbody>
		<tr>
			<td>{*mostrar_estado_proceso*}</td>
		</tr>
	</tbody>
</table>
</div>
</div>',
                    $Documento
                ) ?>
                </div>
            </div>
            <div class="page_margin_bottom" id="doc_footer">
                <?= Saia\controllers\functions\Header::crearEncabezadoPiePagina(
                    '',
                    $Documento
                ) ?>
            </div>
        </div> <!-- end page-n -->
    </div> <!-- end #documento-->
</div> <!-- end .container -->
<?php else:

    $params = [
        "type" => "TIPO_DOCUMENTO",
        "typeId" => $documentId,
    ];

    $reresh =  
        $Documento->isActive() ||
        $_REQUEST["actualizar_pdf"] ||
        (
            !$Documento->pdf && (
                $Formato->mostrar_pdf == 1 ||
                $_REQUEST['mostrar_pdf']
            )
        );
    $json = $Documento->getPdfJson($reresh);
    $FileJson = new Saia\controllers\anexos\FileJson($json);
    $FileTemporal = $FileJson->convertToFileTemporal();
    $url = ABSOLUTE_SAIA_ROUTE . $FileTemporal->getRouteFromRoot();

    echo "<iframe width='100%' frameborder='0' onload='this.height = window.innerHeight - 20' src='{$url}'></iframe>";
endif;
    
$scope = FtActa::SCOPE_ROUTE_PARAMS_SHOW;
$additionalParameters = $FtActa->getRouteParams($scope);
$params = json_encode(array_merge($_REQUEST,$additionalParameters));
?>
    <script>
        $(function () {
            $.getScript("http://localhost/views/modules/actas/formatos/acta/funciones.js", () => {
                window.routeParams = <?= $params ?>;
                show(<?= $params ?>)
            });
        });
    </script>
    </body>
</html>
<?php
} catch (\Throwable $th) {
    die($th->getMessage());
}