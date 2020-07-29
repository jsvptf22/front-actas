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
    <div class="row">
        <div class="col-12">
            <div class='form-group form-group-default'>
                <label>Pregunta</label>
                <textarea v-model="question.label" class="form-control" name="question"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12" v-if="question.options.length">
            <h6>Opciones</h6>
            <div v-for="option of question.options" v-bind:key="option.id" class="card mb-1">
                <div class="card-body">
                    <div class="row">
                        <div class="col-auto f-20 d-flex align-items-center pr-1 mr-1">
                            <i v-on:click="removeOption(option.id)" class="text-danger cursor fa fa-times"
                               title="eliminar"></i>
                        </div>
                        <div class="col">
							<span>
								{{option.label}}
							</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="input-group mb-3">
                <input v-model="option.label" type="text" class="form-control" placeholder="Nueva opción"
                       aria-describedby="button-addon2"/>
                <div class="input-group-append">
                    <button class="btn btn-secondary" type="button" v-on:click="saveOption">Crear Opción</button>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <button class="btn btn-complete btn-block" v-on:click="saveQuestion">Crear Decisión</button>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table id="question_table"></table>
        </div>
    </div>
</div>

<?= bootstrapTable() ?>
<?= icons() ?>
<?= vue() ?>
<script>
    $(function () {
        let app = new Vue({
            el: '#question_container',
            data: function () {
                return {
                    questions: [],
                    question: {
                        id: 0,
                        label: "",
                        options: []
                    },
                    option: {
                        id: 0,
                        label: ""
                    }
                };
            },
            created() {
                this.questions = top.window.actDocumentData.questions.slice();
                this.createSyncQuestionsEvent();
            },
            methods: {
                deleteQuestion(id) {
                    this.questions = this.questions.filter(q => q.id != id);
                },
                editQuestion(id) {
                    this.question = this.questions.find(q => q.id == id);
                },
                saveQuestion() {
                    if (!this.question.label.length) {
                        top.notification({
                            type: "error",
                            message: "Debe indicar la pregunta a publicar"
                        });

                        return;
                    }

                    if (!this.question.options.length) {
                        top.notification({
                            type: "error",
                            message: "Debe indicar opciones"
                        });

                        return;
                    }

                    if (!this.question.id) {
                        this.question.id = new Date().getTime() + "-" + this.questions.length;
                        this.question.new = 1;
                    } else {
                        this.deleteQuestion(this.question.id);
                    }

                    this.questions.push(this.question);
                    this.question = {
                        id: 0,
                        label: "",
                        options: []
                    };
                },
                saveOption() {
                    if (!this.option.label.length) {
                        top.notification({
                            type: "error",
                            message: "Debe indicar la opción"
                        });

                        return;
                    }

                    if (!this.option.id) {
                        this.option.id = new Date().getTime() + "-" + this.questions.length;
                        this.option.new = 1;
                        this.option.votes = 0;
                    }

                    this.question.options.push(this.option);
                    this.option = {
                        id: 0,
                        label: ""
                    };
                },
                removeOption(optionId) {
                    this.question.options = this.question.options.filter(o => o.id !== optionId);
                },
                createSyncQuestionsEvent() {
                    $("#btn_success").on("click", () => {
                        top.successModalEvent({
                            questions: this.questions
                        });
                    });
                }
            },
            watch: {
                questions: function (value) {
                    $('#question_table').bootstrapTable('refreshOptions', {
                        data: value
                    });
                }
            }
        });

        $('#question_table').bootstrapTable({
            classes: 'table table-hover mt-0',
            theadClasses: 'thead-light',
            columns: [
                {
                    field: 'label',
                    title: 'Pregunta'
                },
                {
                    field: 'other',
                    title: 'Opciones',
                    align: 'center',
                    formatter: function (value, row, index, field) {
                        return `<div class="dropdown">
                            <button class="btn" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-left bg-white" role="menu">
                                <a href="#" class="dropdown-item new_action" data-type="delete" data-id="${row.id}">
                                    <i class="fa fa-trash"></i> Eliminar
                                </a>
                                <a href="#" class="dropdown-item new_action" data-type="edit" data-id="${row.id}">
                                    <i class="fa fa-edit"></i> Editar
                                </a>
                            </div>
                        </div>`;
                    }
                }
            ],
        });

        $(document)
            .off('click', '.new_action')
            .on('click', '.new_action', function () {
                let id = $(this).data('id');

                if ($(this).data('type') === 'delete') {
                    app.deleteQuestion(id);
                } else {
                    app.editQuestion(id);
                }
            })
    })
</script>