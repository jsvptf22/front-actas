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
<div id="question_container">
    <div class="row mx-0">
        <div class="col">
            <div class='form-group form-group-default'>
                <label>Pregunta</label>
                <textarea class="form-control" name="question"></textarea>
            </div>
        </div>
        <div class="col-auto">
            <button class="btn btn-complete" id="send_question">Crear</button>
        </div>
    </div>
    <div class="row mx-0">
        <div class="col-12">
            <table id="question_table"></table>
        </div>
    </div>
</div>

<?= bootstrapTable() ?>
<?= icons() ?>
<script>
    $(function() {
        var questions = top.window.actDocumentData.questions.slice();

        $("#btn_success").on("click", function() {
            top.successModalEvent({
                questions: questions
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
            } else {
                $("[name='question']").val('');
            }

            questions.push({
                label: question,
                approve: 0,
                reject: 0
            });

            $('#question_table').bootstrapTable('refreshOptions', {
                data: questions,
            })
        });

        $('#question_table').bootstrapTable({
            classes: 'table table-hover mt-0',
            theadClasses: 'thead-light',
            columns: [{
                    field: 'label',
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
            data: questions
        });
    });
</script>