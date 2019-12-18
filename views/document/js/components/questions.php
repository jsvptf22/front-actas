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

?>
<div class="row mx-0">
    <div class="col">
        <div class='form-group form-group-default'>
            <label>Pregunta</label>
            <textarea class="form-control" name="question"></textarea>
        </div>
    </div>
    <div class="col-auto">
        <button class="btn btn-complete" id="send_question">Publicar</button>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table id="question_table"></table>
    </div>
</div>

<?= bootstrapTable() ?>
<?= icons() ?>
<script>
    $(function() {
        var realBaseUrl = Session.getBaseUrl();
        var selectedUsers = top.window.actDocumentData.userList;

        $("#btn_success").on("click", function() {
            top.successModalEvent({
                questions: []
            });
        });

        $("#send_question").on("click", function() {
            let question = $("[name='question']").val();

            if (!question.length) {
                top.notification({
                    type: "error",
                    message: "Debe indicar la pregunta a publicar"
                });

                return;
            }

            $.post(
                `${realBaseUrl}app/modules/back_actas/app/preguntas/crear.php`, {
                    key: localStorage.getItem('key'),
                    token: localStorage.getItem('token'),
                    question: question,
                    id: top.window.actDocumentData.id
                },
                function(response) {
                    if (response.success) {
                        $("[name='question']").val('');
                        $('#question_table').bootstrapTable('refresh');
                    } else {
                        top.notification({
                            type: 'error',
                            message: response.message
                        });
                    }
                },
                'json'
            );
        });

        $('#question_table').bootstrapTable({
            url: `${realBaseUrl}app/modules/back_actas/app/preguntas/listado.php`,
            classes: 'table table-hover mt-0',
            theadClasses: 'thead-light',
            showRefresh: true,
            queryParams: function(queryParams) {
                return $.extend({}, queryParams, {
                    key: localStorage.getItem('key'),
                    token: localStorage.getItem('token'),
                    id: top.window.actDocumentData.id
                });
            },
            columns: [{
                    field: 'question',
                    title: 'Pregunta'
                },
                {
                    field: 'approve',
                    title: 'Aprobaciones'
                },
                {
                    field: 'reject',
                    title: 'Rechazos'
                }
            ],
            onPostBody: function() {
                $('.fa-sync').addClass('fa-refresh');
            },
        });
    });
</script>