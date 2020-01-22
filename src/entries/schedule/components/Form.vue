<template>
    <div class="card">
        <div class="card-header">
            <h5>Crear evento</h5>
        </div>
        <div class="card-body">
            <form id="planning_form">
                <div
                    class="form-group form-group-default input-group required date"
                >
                    <div class="form-input-group">
                        <label>Fecha:</label>
                        <input
                            name="initialDate"
                            type="text"
                            class="form-control"
                            placeholder="Seleccione.."
                            id="initialDate"
                        />
                    </div>
                    <div class="input-group-append ">
                        <span class="input-group-text"
                            ><i class="fa fa-calendar"></i
                        ></span>
                    </div>
                </div>
                <div class="form-group form-group-default required">
                    <label for="subject">Asunto</label>
                    <input
                        name="subject"
                        id="subject"
                        type="text"
                        class="form-control"
                    />
                </div>
                <div
                    class="form-group form-group-default form-group-default-select2"
                    id="assistants_container"
                >
                    <label>Asistentes</label>
                    <select
                        class="full-width"
                        id="user_select"
                        multiple="multiple"
                    ></select>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <button class="btn btn-complete" id="saveData">
                Guardar
            </button>
            <div class="progress-circle-indeterminate d-none" id="spiner"></div>
        </div>
    </div>
</template>
<script>
import "moment";
import "GlobalAssets/theme/assets/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css";
import "GlobalAssets/theme/assets/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js";
import "select2";
import "select2/dist/js/i18n/es.js";
import "select2/dist/css/select2.min.css";
import "jquery-validation";

export default {
    name: "Form",
    data: function() {
        return {
            fieldId: 0
        };
    },
    methods: {
        findFieldData() {
            $.post(
                `${process.env.ABSOLUTE_ACTAS_API_ROUTE}formato/busca_campo.php`,
                {
                    key: localStorage.getItem("key"),
                    token: localStorage.getItem("token"),
                    field: "asistentes_externos",
                    formatName: "acta"
                },
                response => {
                    if (response.success) {
                        this.fieldId = response.data.idcampos_formato;
                    } else {
                        top.notification({
                            type: "error",
                            message: response.message
                        });
                    }
                },
                "json"
            );
        }
    },
    mounted: function() {
        let vm = this;

        this.findFieldData();

        $("#initialDate").datetimepicker({
            locale: "es",
            format: "YYYY-MM-DD HH:mm:ss"
        });

        $("#user_select")
            .select2({
                minimumInputLength: 3,
                language: "es",
                ajax: {
                    url: `${process.env.ABSOLUTE_ACTAS_API_ROUTE}funcionario/asistentes.php`,
                    dataType: "json",
                    data: function(e) {
                        return {
                            term: e.term,
                            key: localStorage.getItem("key"),
                            token: localStorage.getItem("token")
                        };
                    },
                    processResults: function(response) {
                        if (!response.data.length) {
                            response.data = [
                                {
                                    id: 9999,
                                    name: "Crear tercero",
                                    showModal: true
                                }
                            ];
                        }

                        return {
                            results: response.data.map(u => {
                                u.text = u.name;
                                return u;
                            })
                        };
                    }
                }
            })
            .on("select2:selecting", e => {
                let data = e.params.args.data;

                if (data.showModal) {
                    e.preventDefault();

                    openModal();
                }
            });

        $("#assistants_container")
            .off("click", ".select2-selection__choice")
            .on("click", ".select2-selection__choice", function(e) {
                if ($(e.target).hasClass("select2-selection__choice__remove")) {
                    return;
                }
                let title = $(this).attr("title");
                let item = $("#user_select")
                    .select2("data")
                    .find(i => i.text == title);
                openModal(item, $(this));
            });

        $("#planning_form").validate({
            rules: {
                initialDate: {
                    required: true
                },
                subject: {
                    required: true
                }
            },
            messages: {
                initialDate: {
                    required: "Campo requerido"
                },
                subject: {
                    required: "Campo requerido"
                }
            },
            submitHandler: function(form) {
                $("#saveData,#spiner").toggleClass("d-none");
                let data = $("#planning_form").serialize();
                data =
                    data +
                    "&" +
                    $.param({
                        key: localStorage.getItem("key"),
                        token: localStorage.getItem("token"),
                        users: JSON.stringify($("#user_select").select2("data"))
                    });

                $.post(
                    `${process.env.ABSOLUTE_ACTAS_API_ROUTE}agendamiento/guardar.php`,
                    data,
                    response => {
                        if (response.success) {
                            top.notification({
                                message: response.message,
                                type: "success"
                            });

                            $("#saveData,#spiner").toggleClass("d-none");
                            $("#initialDate")
                                .data("DateTimePicker")
                                .clear();

                            $("#subject").val("");
                            $("#user_select")
                                .val(null)
                                .trigger("change");

                            vm.$emit("newRecord");
                        } else {
                            top.notification({
                                message: response.message,
                                type: "error",
                                title: "Error!"
                            });
                        }
                    },
                    "json"
                );
            }
        });

        $("#saveData").on("click", function() {
            $("#planning_form").trigger("submit");
        });

        function openModal(item = null, selectedNode = null) {
            $("#user_select").select2("close");

            let user = $("#user_select")
                .select2("data")
                .find(u => u.id == item.id);

            if (user && !+user.external) {
                return;
            }

            top.topModal({
                url: "views/tercero/formularioDinamico.php",
                params: {
                    fieldId: vm.fieldId,
                    id: item ? item.id : 0
                },
                title: "Tercero",
                buttons: {
                    success: {
                        label: "Continuar",
                        class: "btn btn-complete"
                    },
                    cancel: {
                        label: "Cerrar",
                        class: "btn btn-danger"
                    }
                },
                onSuccess: function(data) {
                    if (selectedNode) {
                        selectedNode.find("span").trigger("click");
                    }

                    $("#user_select").select2("close");
                    var option = new Option(data.text, data.id, true, true);
                    $("#user_select")
                        .append(option)
                        .trigger("change");
                    top.closeTopModal();
                }
            });
        }
    }
};
</script>
