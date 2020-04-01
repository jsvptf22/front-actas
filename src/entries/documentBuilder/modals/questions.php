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
            <button class="btn btn-complete" id="send_question">Publicar</button>
        </div>
    </div>
    <div class="row mx-0">
        <div class="col-12">
            <table id="question_table"></table>
        </div>
    </div>
</div>

<?= socketIoClient() ?>
<?= bootstrapTable() ?>
<?= icons() ?>
<script>
    $(function () {
        var remoteServer = "<?= ACTAS_NODE_SERVER ?>";
        var room = top.window.actDocumentData.questions.room;
        var questions = [];
        var socket = null;

        findRoom(room);

        $("#btn_success").on("click", function () {
            top.successModalEvent({
                questions: {
                    room,
                    items: questions
                }
            });
        });

        $("#send_question").on("click", function () {
            let question = $("[name='question']").val();

            if (!question.length) {
                top.notification({
                    type: "error",
                    message: "Debe indicar la pregunta a publicar"
                });

                return;
            }

            $.post(
                `${remoteServer}api/room/${room}/questions/${question}`,
                function (response) {
                    if (response.success) {
                        $("[name='question']").val('');
                        socket.emit("refreshQuestions", room);
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

        function findRoom(room) {
            $.get(`${remoteServer}api/room/${room}`, function (response) {
                if (response.success) {
                    $("#question_container").show();
                    openSocket(response.data._id);
                } else {
                    console.error(response.message);
                }
            }, 'json');

        }

        function openSocket(room) {
            socket = io(remoteServer + "room");

            socket.on("refreshQuestions", items => {
                questions = items;
                $('#question_table').bootstrapTable('refreshOptions', {
                    data: questions
                });
            });

            socket.emit("defineRoom", room);
            socket.emit("refreshQuestions", room);
        }
    });
</script>