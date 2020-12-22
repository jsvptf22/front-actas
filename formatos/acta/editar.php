<?php
$max_salida = 10;
$rootPath = $ruta = "";

while ($max_salida > 0) {
    if (is_file($ruta . "index.php")) {
        $rootPath = $ruta;
        break;
    }

    $ruta .= "../";
    $max_salida --;
}

include_once $rootPath . 'app/vendor/autoload.php';
include_once $rootPath . 'views/assets/librerias.php';

use Saia\controllers\SessionController;
use Saia\controllers\generator\component\ComponentBuilder;
use Saia\controllers\AccionController;
use Saia\models\formatos\Formato;
use App\Bundles\actas\formatos\acta\FtActa;

SessionController::goUp($_REQUEST['token'], $_REQUEST['key']);

$Formato = new Formato(15);
$documentId = $_REQUEST['documentId'] ?? 0;
    $FtActa = FtActa::findByDocumentId($documentId);?><!DOCTYPE html>
<html>

<head>
    <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="utf-8" />
    <title>SGDA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=10.0, shrink-to-fit=no" />
    <meta name="apple-mobile-web-app-capable" content="yes">

            <?= jquery() ?>        <?= bootstrap() ?>        <?= cssTheme() ?>    </head>

<body>

                <div class='container-fluid container-fixed-lg col-lg-8' style="overflow: auto;height:100vh">
                        <div class='card card-default'>
                <div class='card-body'>
                    <h5 class='text-black w-100 text-center'>
                        Acta                    </h5>
                    <form name='formulario_formatos' id='formulario_formatos' role='form' autocomplete='off'>
                        <input type='hidden' name='documento_iddocumento' value='<?= Saia\controllers\generator\component\ComponentBuilder::callShowValue(
                'documento_iddocumento',
                $FtActa
            ) ?>'>
<input type='hidden' name='encabezado' value='<?= Saia\controllers\generator\component\ComponentBuilder::callShowValue(
                'encabezado',
                $FtActa
            ) ?>'>
<input type='hidden' name='firma' value='<?= Saia\controllers\generator\component\ComponentBuilder::callShowValue(
                'firma',
                $FtActa
            ) ?>'>
<input type='hidden' name='idft_acta' value='<?= Saia\controllers\generator\component\ComponentBuilder::callShowValue(
                'idft_acta',
                $FtActa
            ) ?>'>

        <?php
        $selected = $FtActa->dependencia ?? '';
        $query = Saia\core\DatabaseConnection::getDefaultConnection()->createQueryBuilder();
        $roles = $query
            ->select("dependencia as nombre, iddependencia_cargo, cargo")
            ->from("vfuncionario_dc")
            ->where("estado_dc = 1 and login = :login")
            ->andWhere(
                $query->expr()->lte('fecha_inicial', ':initialDate'),
                $query->expr()->gte('fecha_final', ':finalDate')
            )->setParameter(":login", Saia\controllers\SessionController::getLogin())
            ->setParameter(':initialDate', new DateTime(), \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)
            ->setParameter(':finalDate', new DateTime(), \Doctrine\DBAL\Types\Types::DATETIME_MUTABLE)
            ->execute()->fetchAll();
    
        $total = count($roles);

        if ($total > 1) {

            echo "<div class='form-group form-group-default form-group-default-select2 required' id='group_dependencie'>
            <label>PERTENECE A</label>
            <select class='full-width select2-hidden-accessible' name='dependencia' id='dependencia' required>";
            foreach ($roles as $row) {
                echo "<option value='{$row["iddependencia_cargo"]}'>
                    {$row["nombre"]} - ({$row["cargo"]})
                </option>";
            }
    
            echo "</select>
                <script>
                $(function (){
                    $('#dependencia').select2();
                    $('#dependencia').val({$selected});
                    $('#dependencia').trigger('change');
                });  
                </script>
                </div>";
        } else if ($total == 1) {
            echo "<input class='required' type='hidden' value='{$roles[0]['iddependencia_cargo']}' id='dependencia' name='dependencia'>";
        } else {
            throw new Exception("Error al buscar la dependencia", 1);
        }
        ?>
            <div class="form-group form-group-default required" id="group_asunto">
                <label title="">
                    ASUNTO
                </label>
                <textarea 
                    name="asunto"
                    id="asunto" 
                    rows="3" 
                    class="form-control required"
                ></textarea>
                
                <input type="hidden" id="asunto_unique_id" name="asunto_unique_id" class="ckeditorImageTemporal" />
            </div><script>
    $(function (){
        var baseUrl = 'http://localhost/';
        
        $.post(
            baseUrl + 'app/documento/consulta_campo_documento.php',
            {
                token: localStorage.getItem('token'),
                key: localStorage.getItem('key'),
                fieldName: 'asunto',
                documentId: $("[name=documentId]").val()
            },
            function (response){
                if (response.success){
                    setTimeout(() => {
                        var editor = CKEDITOR.instances['asunto'];
                        editor.setData(response.data);
                        $('#asunto').val(response.data);
                    }, 1000);
                }else{
                    top.notification({
                        type: 'error',
                        message: response.message
                    });
                }
            },
            'json'
        );
    });
</script>
            <div class="form-group form-group-default input-group required date" id="group_fecha_inicial">
                <div class="form-input-group">
                    <label for='fecha_inicial' title=''>
                        FECHA INICIAL
                    </label>
                    <input type="text" class="form-control required" id="fecha_inicial" name="fecha_inicial">
                </div>
                <div class='input-group-append'>
                    <span class='input-group-text'>
                        <i class='fa fa-calendar'></i>
                    </span>
                </div>
            </div><?php
                $defaultDate = \Saia\controllers\generator\component\ComponentBuilder::callShowValue(
                    'fecha_inicial',
                    $FtActa
                );
                
                if($defaultDate){
                    $defaultDate = Saia\controllers\DateController::convertDate(
                        $defaultDate,
                        'Y-m-d H:i:s',
                        Saia\controllers\DateController::PUBLIC_DATETIME_FORMAT
                    );
                }
            ?>        <script type='text/javascript'>
            $(function () {
                let defaultDate = '<?= $defaultDate ?>';
                let options = {
                    locale: 'es',
                    format: 'YYYY-MM-DD HH:mm:ss',
                    defaultDate: moment().format('YYYY-MM-DD HH:mm:ss')
                };

                switch ('') {
                    case "lt":
                        options.maxDate = moment().subtract(1, 'd').format('YYYY-MM-DD HH:mm:ss');
                        options.defaultDate = moment().subtract(1, 'd').format('YYYY-MM-DD HH:mm:ss');
                        break;
                    case "lte":
                        options.maxDate = moment().format('YYYY-MM-DD HH:mm:ss');
                        break;
                    case "gt":
                        options.minDate = moment().add(1, 'd').format('YYYY-MM-DD HH:mm:ss');
                        options.defaultDate = moment().add(1, 'd').format('YYYY-MM-DD HH:mm:ss');
                        break;
                    case "gte":
                        options.minDate = moment().format('YYYY-MM-DD HH:mm:ss');
                        break;
                }
                $('#fecha_inicial').datetimepicker(options);

                if(!defaultDate.length){
                    $('#fecha_inicial').data('DateTimePicker').clear();
                }
            });
        </script>
            <div class="form-group form-group-default input-group  date" id="group_fecha_final">
                <div class="form-input-group">
                    <label for='fecha_final' title=''>
                        FECHA FINAL
                    </label>
                    <input type="text" class="form-control " id="fecha_final" name="fecha_final">
                </div>
                <div class='input-group-append'>
                    <span class='input-group-text'>
                        <i class='fa fa-calendar'></i>
                    </span>
                </div>
            </div><?php
                $defaultDate = \Saia\controllers\generator\component\ComponentBuilder::callShowValue(
                    'fecha_final',
                    $FtActa
                );
                
                if($defaultDate){
                    $defaultDate = Saia\controllers\DateController::convertDate(
                        $defaultDate,
                        'Y-m-d H:i:s',
                        Saia\controllers\DateController::PUBLIC_DATETIME_FORMAT
                    );
                }
            ?>        <script type='text/javascript'>
            $(function () {
                let defaultDate = '<?= $defaultDate ?>';
                let options = {
                    locale: 'es',
                    format: 'YYYY-MM-DD HH:mm:ss',
                    defaultDate: moment().format('YYYY-MM-DD HH:mm:ss')
                };

                switch ('') {
                    case "lt":
                        options.maxDate = moment().subtract(1, 'd').format('YYYY-MM-DD HH:mm:ss');
                        options.defaultDate = moment().subtract(1, 'd').format('YYYY-MM-DD HH:mm:ss');
                        break;
                    case "lte":
                        options.maxDate = moment().format('YYYY-MM-DD HH:mm:ss');
                        break;
                    case "gt":
                        options.minDate = moment().add(1, 'd').format('YYYY-MM-DD HH:mm:ss');
                        options.defaultDate = moment().add(1, 'd').format('YYYY-MM-DD HH:mm:ss');
                        break;
                    case "gte":
                        options.minDate = moment().format('YYYY-MM-DD HH:mm:ss');
                        break;
                }
                $('#fecha_final').datetimepicker(options);

                if(!defaultDate.length){
                    $('#fecha_final').data('DateTimePicker').clear();
                }
            });
        </script>
<input type='hidden' name='estado' value='<?= Saia\controllers\generator\component\ComponentBuilder::callShowValue(
                'estado',
                $FtActa
            ) ?>'>
            <div class='form-group form-group-default form-group-default-select2 required' id='group_asistentes_externos'>
                <label title='' class='autocomplete'>ASISTENTES EXTERNOS</label>
                <select class="full-width" id='asistentes_externos' multiple="multiple" required ></select>
                <input type="hidden" class="select2 required" name="asistentes_externos">
            </div>
            <script>
                $(function(){
                    let baseUrl = "<?= ABSOLUTE_SAIA_ROUTE ?>";
                    let select = $("#asistentes_externos");
                    let onceClick = 0;
                    select.select2({
                        minimumInputLength: 0,
                        language: 'es',
                        ajax: {
                            url: baseUrl+'app/tercero/autocompletar.php',
                            dataType: 'json',
                            data: function(params) {
                                return {
                                    term: params.term,
                                    key: localStorage.getItem('key'),
                                    token: localStorage.getItem('token')
                                };
                            },
                            processResults: function(response) {
                                let crearNuevo = {id: 9999, text: 'Crear tercero', showModal: true};
                                let importar = {id: 9999, text: 'Importar terceros', showModalImport: true};
                                response.data.push(crearNuevo,importar);
                                return { results: response.data}
                            }
                        }                        
                    }).on('select2:selecting', function (e) {
                        
                        let data = e.params.args.data;

                        if(data.showModal){
                            e.preventDefault();
                            openModal();
                        }
                        if(data.showModalImport){
                            e.preventDefault();
                            openModalImport(e.target.id);
                        }
                    }).on('change', function(){
                        let value = $(this).val().join(',');
                        $("[name='asistentes_externos']").val(value);
                    });

                    $('#group_asistentes_externos')
                        .off('click', '.select2-selection__choice')
                        .on('click', '.select2-selection__choice', function (e){
                            if($(e.target).hasClass('select2-selection__choice__remove')){
                                return;
                            }
                            let title = $(this).attr('title');
                            let item = $("#asistentes_externos").select2('data').find(i => i.text == title);
                            openModal(item, $(this));
                        });

                    function openModal(item = 0, selectedNode = null){
                        if (!onceClick){
                            onceClick++;
                             top.topModal({
                                url: 'views/tercero/formularioDinamico.php',
                                params: {
                                    fieldId : 10125,
                                    id: item.id
                                },
                                title: 'Tercero',
                                buttons: {
                                    success: {
                                        label: 'Continuar',
                                        class: 'btn btn-complete'
                                    },
                                    cancel: {
                                        label: 'Cerrar',
                                        class: 'btn btn-danger'
                                    }
                                },
                                onSuccess: function(data) {                                
                                    if(selectedNode){
                                        selectedNode.find('span').trigger('click');
                                    }
    
                                    select.select2('close');
                                    var option = new Option(data.text, data.id, true, true);
                                    select.append(option).trigger('change');
                                    top.closeTopModal();
                                },
                                afterHide: function() {
                                  onceClick = 0;
                                }
                            });
                        }
                    }

                    function openModalImport(idCampo){
                          if (!onceClick){
                              onceClick++;
                               let options = {
                                    url: `views/tercero/importarTerceros.php`,
                                    params: {idCampo:idCampo},
                                    centerAlign: false,
                                    size: 'modal-lg',
                                    title: 'Importar terceros',
                                    buttons: {
                                        success: {
                                            label: 'Continuar',
                                            class: 'btn btn-complete'
                                        },
                                        cancel: {
                                            label: 'Cerrar',
                                            class: 'btn btn-danger'
                                        }
                                    },
                                    onSuccess: function (response){successImport(response);},
                                    afterHide: function() {
                                        onceClick = 0;
                                    }
                                };
                                top.topModal(options);
                          }
                    }

                    function successImport(response){
                        let tercero = JSON.parse(response.data);
                            tercero.forEach(datos => {
                                var option = new Option(
                                    datos.nombre,
                                    datos.id,
                                    true,
                                    true
                                );
                                $('#' + response.campo)
                                    .append(option)
                                    .trigger('change');
                            });
                    }
                });
            </script>            <script>
                $(function(){
                    var baseUrl = "<?= ABSOLUTE_SAIA_ROUTE ?>";
                    var select = $("#asistentes_externos");
                    var selected = "<?= $FtActa->asistentes_externos ?>".split(',');
                    selected.forEach(id => {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: baseUrl+'app/tercero/autocompletar.php',
                            data: {
                                defaultUser: id,
                                key: localStorage.getItem('key'),
                                token: localStorage.getItem('token')
                            },
                            success: function(response) {
                                response.data.forEach(u => {
                                    var option = new Option(u.text, u.id, true, true);
                                    select
                                        .append(option)
                                        .trigger('change');
                                });
                            }
                        });
                    })
                });
            </script>
        <div class='form-group form-group-default required' id='group_asistentes_internos'>
            <label title=''>ASISTENTES INTERNOS</label>
            <div class="col pl-0 pr-1" id="asistentes_internos_ua"></div>

            <input class='required' type='hidden' id='asistentes_internos' name='asistentes_internos'>
        </div>
        <script>
            $(function () {
                let users = null;

                if (typeof Users == 'undefined') {
                    $.getScript('<?= ABSOLUTE_SAIA_ROUTE ?>views/assets/theme/assets/js/cerok_libraries/users/users.js', r => {
                        showUserComponent_asistentes_internos();
                    });
                } else {
                    showUserComponent_asistentes_internos();
                }

                let data = '<?= $FtActa->asistentes_internos ?>';
                function showUserComponent_asistentes_internos() {
                    users = new Users({
                        selector: '#asistentes_internos_ua',
                        baseUrl: '<?= ABSOLUTE_SAIA_ROUTE ?>',
                        identificator: 'asistentes_internos',
                        seleccionUnica: 0,
                        tipoDependencia: 0,
                        editar: 1,
                        default: data.split(','),
                        change: (type) => {fillHidden(type)}
                    });
                }

                function fillHidden(type=1){
                            $('#asistentes_internos').val(users.getList(type).join(','));
                }
            });
        </script>
<input type='hidden' name='duracion' value='<?= Saia\controllers\generator\component\ComponentBuilder::callShowValue(
                'duracion',
                $FtActa
            ) ?>'>
<input type='hidden' name='documentId' value='<?= $documentId ?>'>
					<input type='hidden' id='tipo_radicado' name='tipo_radicado' value='apoyo'>
					<input type='hidden' name='formatId' value='15'>
					<div class='form-group px-0 pt-3' id='form_buttons'><button class='btn btn-complete' id='save_document' type='button'>Continuar</button><div class='progress-circle-indeterminate d-none' id='spiner'></div></div>                    </form>
                </div>
            </div>
            </div>

            <?= jsTheme() ?>            <?= icons() ?>            <?= moment() ?>            <?= select2() ?>            <?= validate() ?>            <?= ckeditor() ?>            <?= jqueryUi() ?>            <?= fancyTree(true) ?>            <?= dateTimePicker() ?>            <?= dropzone() ?>
                <?php
        if ($documentId) {
            $additionalParameters = $FtActa->getRouteParams(FtActa::SCOPE_ROUTE_PARAMS_EDIT);
        } else {
            $additionalParameters = $FtActa->getRouteParams(FtActa::SCOPE_ROUTE_PARAMS_ADD);
        }
        $params = json_encode(array_merge(
            $_REQUEST,
            $additionalParameters,
            ['baseUrl' => ABSOLUTE_SAIA_ROUTE]
        ));
        
        $isRadFormat = 0;
        $isDistributionFormat = 0;
        $destinationFields = 0;
    ?>            <script>
                $(function() {
                    let baseUrl = 'http://localhost/';
                    let file = 'views/modules/actas/formatos/acta/funciones.js';
                    let isRadFormat = <?= $isRadFormat ?>;
                    let isDistributionFormat = <?= $isDistributionFormat ?>;
                    let destinationFields = <?= $destinationFields ?>;
                    let params = <?= $params ?>;
                    let packageView = 0;
                    let documentId = '<?= $documentId ?>';
                    let isEdit = documentId > 0;

                    $.getScript(baseUrl + file, () => {
                        window.routeParams = <?= $params ?>;
                        if (+documentId) {
                            edit(<?= $params ?>)
                        } else {
                            add(<?= $params ?>);
                            if (window.routeParams.padre){
                                $.post(
                                `${baseUrl}app/formato/consulta_ft_padre.php`,
                                {
                                    key: localStorage.getItem('key'),
                                    token: localStorage.getItem('token'),
                                    padre : window.routeParams.padre
                                },
                                function (response){
                                    let data = response.data;
                                    if(response.success){
                                        $(`input[name="${data.parentFormatTable}"]`).val(data.parentFtId);
                                    }
                                },
                                'json'
                            );
                            }
                        }
                    });

                    $("#add_item").click(function() {
                        checkForm((data) => {
                            let options = top.window.modalOptions;
                            top.window.modalOptions = null;
                            top.topModal(options)
                        })
                    });

                    $("#save_item").click(function() {
                        checkForm((data) => {
                            top.successModalEvent(data);
                        })
                    });
                    $("#save_document").click(function() {
                        checkForm((data) => {
                            let finalRoute = destinationFields ?
                                `views/buzones/grilla.php?idbusqueda_componente=${packageView}&` :
                                `views/documento/index_acordeon.php?`;

                            let route = baseUrl + finalRoute;
                            route += $.param(data);

                            if (isRadFormat) {
                                let documentId = data.documentId;
                                let digitalizacion = $('input[name="digitalizacion"]:checked').val();
                                let orientacionSello = $('input[name="colilla"]:checked').data('key') || 0;
                                let digitalizationRoute = `views/documento/digitalizar_paginas.php?documentId=${documentId}&librerias=1`;

                                if (isEdit){
                                    if (+digitalizacion) {
                                        route = baseUrl + digitalizationRoute;
                                    }
                                }else{
                                    let dataUrl = {
                                        target: 'self',
                                        colilla_vertical: orientacionSello,
                                        documentId: documentId
                                    };

                                    let rutaOrientacion = +orientacionSello ?
                                        'views/colilla/colillaVertical.php?' :
                                        'views/colilla/colillaHorizontal.php?';

                                    if (+digitalizacion) {
                                        dataUrl.enlace = digitalizationRoute;
                                    }

                                    let paramsUrl = $.param(dataUrl);
                                    route = baseUrl + rutaOrientacion + paramsUrl;
                                }
                            }

                            window.location.href = route;
                        })
                    });

                    function checkForm(callback) {
                        $("#formulario_formatos").validate({
                            ignore: [],
                            submitHandler: function(form) {
                                $("#form_buttons").find('button,#spiner').toggleClass('d-none');

                                executeEvents(callback);
                            },
                            invalidHandler: function() {
                                $("#save_document").show();
                                $("#boton_enviando").remove();
                            }
                        });
                        $("#formulario_formatos").trigger('submit');
                    }

                    function executeEvents(callback) {
                        let documentId = $("[name='documentId']").val();

                        (+documentId ? beforeSendEdit(<?= $params ?>) : beforeSendAdd(<?= $params ?>))
                        .then(r => {
                            sendData()
                                .then(requestResponse => {
                                    (+documentId ? afterSendEdit(requestResponse) : afterSendAdd(requestResponse))
                                    .then(r => {
                                            callback(requestResponse.data);
                                        })
                                        .catch(message => {
                                            fail(message);
                                        })
                                }).catch(message => {
                                    fail(message);
                                });
                        }).catch(message => {
                            fail(message);
                        });
                    }

                    let saveRoute = destinationFields ?
                        'app/documento/guardar_paquete.php':
                        'app/documento/guardar_ft.php';

                    function sendData() {
                        return new Promise((resolve, reject) => {
                            let data = $('#formulario_formatos').serialize() + '&' +
                                $.param({
                                    key: localStorage.getItem('key'),
                                    token: localStorage.getItem('token')
                                });

                            $.post(baseUrl + saveRoute,
                                data,
                                function(response) {
                                    if (response.success) {
                                        // Si es adicionar valido imagenes temporales de ckeditor
                                        if (!+$('input[name=documentId]').val()){
                                            processCkeditor(response.data.documentId)
                                                .then(() => {
                                                    resolve(response);
                                                });
                                        }else{
                                            resolve(response);
                                        }
                                    } else {
                                        reject(response.message);
                                    }
                                },
                                'json'
                            );
                        });
                    }

                    function fail(message) {
                        $("#form_buttons").find('button,#spiner').toggleClass('d-none');
                        top.notification({
                            message: message,
                            type: 'error',
                            title: 'Error!'
                        });
                    }

                    if (isRadFormat) {
                        if (isEdit){
                            $('#group_colilla').hide();
                        }
                        if (typeof params.fk_rcmail_data != 'undefined') {
                            $.ajax({
                                url: `${baseUrl}app/modules/back_roundcube/app/request.php`,
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    token: localStorage.getItem('token'),
                                    key: localStorage.getItem('key'),
                                    method: 'getInfoRcmail',
                                    data: {
                                        id: params.fk_rcmail_data
                                    }
                                },
                                success: function (response) {
                                    $("#descripcion").val(response.asunto);
                                    loadAnexos(response.anexos_digitales);

                                }
                            });
                        }
                    }

                    if (isDistributionFormat){
                        //Campos
                        let $origen_interno = $('#origen_interno');
                        let $origen_externo = $('#origen_externo');
                        let $destino_interno = $('#destino_interno');
                        let $destino_externo = $('#destino_externo');

                        // Grupos
                        let $origen_interno_group = $('#group_origen_interno');
                        let $origen_externo_group = $('#group_origen_externo');
                        let $destino_interno_group = $('#group_destino_interno');
                        let $destino_externo_group = $('#group_destino_externo');

                        $destino_interno_group.addClass('required');
                        $origen_interno_group.addClass('required');
                        $destino_externo_group.hide();
                        $destino_interno_group.hide();

                        // si es el editar no ocultar el campo del origen
                        if (+documentId) {
                            var tipoOrigenKey = $(`input[name="tipo_origen"][value="${params.tipo_origen}"]`).data('key');
                            if (tipoOrigenKey == 1) {
                                seleccionarOrigenExterno();
                                seleccionarDestinoInterno();
                            } else {
                                seleccionarOrigenInterno();
                            }
                        } else {
                            $origen_externo_group.hide();
                            $origen_interno_group.hide();
                        }

                        /*
                            Selecciona tipo origen
                            1. Externo
                            2. Interno
                        */
                        $('input[name="tipo_origen"]').on('change', function () {
                            if ($(this).data('key') == 1){
                                seleccionarOrigenExterno();
                            }else{
                                seleccionarOrigenInterno();
                            }
                        });

                        /*
                            Selecciona tipo destino
                            1. Externo
                            2. Interno
                        */
                        $('input[name="tipo_destino"]').on('change', function () {
                            if ($(this).data('key') == 1){
                                seleccionarDestinoExterno();
                            } else {
                                seleccionarDestinoInterno();
                            }
                        });

                        function seleccionarOrigenExterno() {
                            if ($('#tipo_origen0:checked')) {
                                $origen_interno_group.hide();
                                $origen_externo_group.show();
                                $origen_interno.removeAttr('required');
                                $origen_externo.attr('required', 'required');
                                $destino_interno.show();
                                $origen_interno.val('').trigger('change');
                                $('#tipo_destino0').attr('disabled', 'disabled');
                                $('#tipo_destino1').trigger('click');
                                $('#tipo_origen0').trigger('click');
                                $('#tipo_radicado').val('radicacion_entrada');
                                seleccionarDestinoInterno();
                            }
                        }
                        function seleccionarOrigenInterno() {
                            if ($('#tipo_origen1:checked')) {
                                $origen_externo_group.hide();
                                $origen_interno_group.show();
                                $origen_externo.removeAttr('required');
                                $origen_interno.attr('required', 'required');
                                $origen_externo.val('').trigger('change');
                                $('#tipo_destino0').removeAttr('disabled');
                            }
                        }
                        function seleccionarDestinoExterno() {
                            if ($('#tipo_destino0:checked')) {
                                $destino_interno_group.hide();
                                $destino_externo_group.show();
                                $destino_interno.removeAttr('required');
                                $destino_interno.val('').trigger('change');
                                $destino_externo.attr('required', 'required');
                            }
                        }
                        function seleccionarDestinoInterno() {
                            if ($('#tipo_destino1:checked')) {
                                $destino_externo_group.hide();
                                $destino_interno_group.show();
                                $destino_externo.removeAttr('required');
                                $destino_interno.attr('required', 'required');
                                $destino_externo.val('').trigger('change');
                            }
                        }
                    }

                    function loadAnexos(anexos) {
                        let baseUrl = localStorage.getItem('baseUrl');

                        var myDropzone = Dropzone.forElement("#dropzone_anexos_digitales");
                        anexos.forEach(mockFile => {
                            var thumbnail = mockFile.thumbnail || mockFile.route;
                            var stringify = JSON.stringify({
                                success: 1,
                                data: [mockFile.route]
                            });
                            myDropzone.removeAllFiles();
                            myDropzone.emit('addedfile', mockFile);
                            myDropzone.emit('thumbnail', mockFile, baseUrl + thumbnail);
                            myDropzone.emit('complete', mockFile);
                            myDropzone.emit('success', mockFile, stringify);
                        });
                    }

                    function processCkeditor(documentId){
                        return new Promise((resolve, reject) => {
                            temporalList= [];

                            $('.ckeditorImageTemporal').each(function(){
                                if ($(this).val()){
                                    temporalList.push($(this).val());
                                    console.log($(this).val());
                                }
                            });

                            console.log(temporalList);

                            if (temporalList.length){
                                $.post(
                                    `${baseUrl}app/ckeditor_image/asignar.php`,
                                    {
                                        token: localStorage.getItem('token'),
                                        key: localStorage.getItem('key'),
                                        temporalList,
                                        documentId,
                                    },
                                    function (response){
                                        if (response.success){
                                            resolve();
                                        }
                                    },
                                    'json'
                                );
                            }else{
                                resolve();
                            }
                        });
                    }

                    (function getPackageView(){
                        if (destinationFields){
                            $.post(
                                `${baseUrl}app/busquedas/consulta_componente.php`,
                                {
                                    token: localStorage.getItem('token'),
                                    key: localStorage.getItem('key'),
                                    name: 'radicacion_masiva'
                                },
                                function (response){
                                    if (response.success){
                                        packageView = response.data.idbusqueda_componente;
                                    }else{
                                        top.notification({
                                            message:'Error consultando el componente Radicacion Masiva',
                                            type: 'error',
                                        });
                                    }
                                },
                                'json'
                            );
                        }
                    })();
                });
            </script>
                <?php AccionController::execute(
        AccionController::ACTION_EDIT,
        AccionController::BEFORE_MOMENT,
        $FtActa ?? null,
        $Formato
    ) ?>
</body>

</html>