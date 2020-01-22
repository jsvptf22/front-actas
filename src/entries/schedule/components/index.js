$(function() {
    let params = $("#base_script").data("params");

    (function init() {
        findList();
        createPicker();
        createAutocomplete();
        createEvent();
    })();

    function createPicker() {
        $("#initialDate").datetimepicker({
            locale: "es",
            format: "YYYY-MM-DD HH:mm:ss"
        });
    }

    function createAutocomplete() {
        $("#user_select")
            .select2({
                minimumInputLength: 3,
                language: "es",
                ajax: {
                    url: `${params.baseUrl}app/modules/back_actas/app/funcionario/asistentes.php`,
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
    }

    function createEvent() {
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
                let params = $("#base_script").data("params");
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
                    `${params.baseUrl}app/modules/back_actas/app/planeacion/guardar.php`,
                    data,
                    function(response) {
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
                            findList();
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

        $(document).on("click", ".init", function() {
            let id = $(this).data("id");
            openForm(id);
        });
    }

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
                fieldId: params.fieldId,
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

    function findList() {
        $.post(
            `${params.baseUrl}app/modules/back_actas/app/planeacion/listar.php`,
            {
                key: localStorage.getItem("key"),
                token: localStorage.getItem("token")
            },
            function(response) {
                if (response.success) {
                    createList(response.data.list);
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

    function createList(data) {
        $("#list").empty();
        data.forEach(i => {
            let template = generateTemplate(i);
            $("#list").append(template);
        });
    }

    function generateTemplate(item) {
        return `<div class="card">
            <div class="card-body">
                ${item.label}
                <span class="float-right text-master">${item.date}</span>
            </div>
            <div class="card-footer">
                <button class="btn btn-complete float-right init" data-id="${item.id}">
                    Iniciar
                </button>
            </div>
        </div>`;
    }

    function openForm(id) {
        $.post(
            `${params.baseUrl}app/formato/consulta_rutas.php`,
            {
                key: localStorage.getItem("key"),
                token: localStorage.getItem("token"),
                formatName: "acta",
                planning: id
            },
            function(response) {
                if (response.success) {
                    window.location.href =
                        params.baseUrl + response.data.ruta_adicionar;
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
});
