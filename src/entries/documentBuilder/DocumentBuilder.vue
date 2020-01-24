<template>
    <div class="container-fluid">
        <div id="fab" class="pr-3 pb-3" style="z-index:1"></div>
        <div class="row p-2">
            <div class="col-12 col-md" id="template_parent">
                <div class="template p-4 p-md-5">
                    <div class="row-fluid mb-3">
                        <div class="col-12 text-center p-3" id="header"></div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="bold">Acta N°</td>
                                    <td>
                                        {{ documentInformation.identificator }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bold">Tema / Asunto</td>
                                    <td colspan="3">
                                        {{ documentInformation.subject }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bold">Inicio</td>
                                    <td>
                                        {{ getInitialDate() }}
                                        {{ getInitialTime() }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bold">Fin</td>
                                    <td>{{ getFinaltime() }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="text-center">
                                        Participantes
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="bold">Asistentes:</span>
                                        <div
                                            v-for="user of getAssistants()"
                                            v-bind:key="user.id"
                                        >
                                            <span class="ml-5">{{
                                                user.name
                                            }}</span>
                                            <br />
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span class="bold">Invitados:</span>
                                        <div
                                            v-for="user of getInvited()"
                                            v-bind:key="user.id"
                                        >
                                            <span class="ml-5">{{
                                                user.name
                                            }}</span>
                                            <br />
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="text-center bold">
                                        Puntos a Tratar / Orden del día
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul>
                                            <li
                                                v-for="topic of documentInformation.topicList"
                                                v-bind:key="topic.id"
                                            >
                                                {{ topic.label }}
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="text-center bold">
                                        Puntos Tratados / Desarrollo
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul>
                                            <li
                                                v-for="item of documentInformation.topicListDescription"
                                                v-bind:key="item.id"
                                            >
                                                <span>{{
                                                    getTopicLabel(item.topic)
                                                }}</span>
                                                <br />
                                                <p>
                                                    {{ item.description }}
                                                </p>
                                            </li>
                                        </ul>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="text-center bold">
                                        Decisiones
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table
                                            v-if="
                                                documentInformation.questions
                                                    .items.length
                                            "
                                            class="table"
                                        >
                                            <tr>
                                                <th class="text-center bold">
                                                    Pregunta
                                                </th>
                                                <th class="text-center bold">
                                                    Aprobación
                                                </th>
                                                <th class="text-center bold">
                                                    Rechazo
                                                </th>
                                            </tr>
                                            <tr
                                                v-for="(question,
                                                index) of documentInformation
                                                    .questions.items"
                                                v-bind:key="index"
                                            >
                                                <td>{{ question.label }}</td>
                                                <td>
                                                    {{ question.approve }}
                                                </td>
                                                <td>
                                                    {{ question.reject }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="text-center bold">
                                        Compromisos
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table
                                            v-if="
                                                documentInformation.tasks.length
                                            "
                                            class="table"
                                        >
                                            <tr>
                                                <th class="text-center bold">
                                                    Tarea
                                                </th>
                                                <th class="text-center bold">
                                                    Responsable
                                                </th>
                                                <th class="text-center bold">
                                                    Ver
                                                </th>
                                            </tr>
                                            <tr
                                                v-for="(task,
                                                index) of documentInformation.tasks"
                                                v-bind:key="index"
                                            >
                                                <td>{{ task.name }}</td>
                                                <td>
                                                    {{
                                                        getTasksUsers(
                                                            task.managers
                                                        )
                                                    }}
                                                </td>
                                                <td>
                                                    <button
                                                        class="btn"
                                                        v-on:click="
                                                            openTaskModal(
                                                                task.id
                                                            )
                                                        "
                                                    >
                                                        <span
                                                            class="fa fa-eye"
                                                        ></span>
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td>
                                        <span class="bold">SECRETARIO:</span>
                                        <span
                                            v-if="
                                                documentInformation.roles
                                                    .secretary
                                            "
                                            >{{
                                                documentInformation.roles
                                                    .secretary.name
                                            }}</span
                                        >
                                    </td>
                                    <td>
                                        <span class="bold">PRESIDENTE:</span>
                                        <span
                                            v-if="
                                                documentInformation.roles
                                                    .president
                                            "
                                            >{{
                                                documentInformation.roles
                                                    .president.name
                                            }}</span
                                        >
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row-fluid mt-3">
                        <div class="col-12 text-center p-3" id="footer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import { mapState } from "vuex";
import "GlobalAssets/theme/assets/plugins/fabjs/fab.css";
import Fab from "GlobalAssets/theme/assets/plugins/fabjs/fab.js";

export default {
    name: "DocumentBuilder",
    data: function() {
        return {
            buttons: {
                openUserModal: {
                    button: {
                        data: {
                            function: "openUserModal"
                        },
                        tooltip: "Validar Asistencia",
                        visible: 1,
                        id: "openUserModal",
                        class: "small yellow action",
                        html: ""
                    },
                    icon: {
                        class: "fa fa-users",
                        html: ""
                    }
                },
                openRoleModal: {
                    button: {
                        data: {
                            function: "openRoleModal"
                        },
                        tooltip: "Elegir Presidente y Secretario(a)",
                        visible: 1,
                        id: "openRoleModal",
                        class: "small yellow action",
                        html: ""
                    },
                    icon: {
                        class: "fa fa-user",
                        html: ""
                    }
                },
                openSubjectModal: {
                    button: {
                        data: {
                            function: "openSubjectModal"
                        },
                        tooltip: "Asunto del Acta",
                        visible: 1,
                        id: "openSubjectModal",
                        class: "small yellow action",
                        html: ""
                    },
                    icon: {
                        class: "fa fa-wechat",
                        html: ""
                    }
                },
                openTopicsModal: {
                    button: {
                        data: {
                            function: "openTopicsModal"
                        },
                        tooltip: "Temas",
                        visible: 1,
                        id: "openTopicsModal",
                        class: "small yellow action",
                        html: ""
                    },
                    icon: {
                        class: "fa fa-list",
                        html: ""
                    }
                },
                openTopicDescriptionModal: {
                    button: {
                        data: {
                            function: "openTopicDescriptionModal"
                        },
                        tooltip: "Desarrollo de Temas",
                        visible: 1,
                        id: "openTopicDescriptionModal",
                        class: "small yellow action",
                        html: ""
                    },
                    icon: {
                        class: "fa fa-paragraph",
                        html: ""
                    }
                },
                openTaskModal: {
                    button: {
                        data: {
                            function: "openTaskModal"
                        },
                        tooltip: "Compromisos",
                        visible: 1,
                        id: "openTaskModal",
                        class: "small yellow action",
                        html: ""
                    },
                    icon: {
                        class: "fa fa-tasks",
                        html: ""
                    }
                },
                openQuestionModal: {
                    button: {
                        data: {
                            function: "openQuestionModal"
                        },
                        tooltip: "Tomar Decisiones",
                        visible: 1,
                        id: "openQuestionModal",
                        class: "small yellow action",
                        html: ""
                    },
                    icon: {
                        class: "fa fa-question",
                        html: ""
                    }
                },
                saveDocument: {
                    button: {
                        data: {
                            function: "saveDocument"
                        },
                        tooltip: "Guardar",
                        visible: 1,
                        id: "saveDocument",
                        class: "small yellow action",
                        html: ""
                    },
                    icon: {
                        class: "fa fa-save",
                        html: ""
                    }
                }
            }
        };
    },
    methods: {
        openUserModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/users.php`,
                title: "Validación de asistencia"
            });
        },
        openSubjectModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/subject.php`,
                title: "Asunto del Acta"
            });
        },
        openTopicsModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/topics.php`,
                title: "Creación de temas"
            });
        },
        openTopicDescriptionModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/topic.php`,
                title: "Descripción del tema"
            });
        },
        openRoleModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/roles.php`,
                title: "Asignación de roles"
            });
        },
        openQuestionModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/questions.php`,
                title: "Decisiones"
            });
        },
        showModal(options) {
            top.window.actDocumentData = {
                ...this.$store.state.documentInformation
            };
            top.topModal({
                ...options,
                onSuccess: data => {
                    this.$store.dispatch("refreshDocumentInformation", data);
                    top.closeTopModal();
                }
            });
        },
        openTaskModal(taskId = null) {
            top.topModal({
                url: `views/tareas/crear.php`,
                params: {
                    id: taskId
                },
                centerAlign: false,
                size: "modal-lg",
                title: "Tarea o Recordatorio",
                buttons: {},
                onSuccess: data => {
                    data = {
                        id: data.id,
                        name: data.name,
                        managers: data.managers
                    };
                    let tasks = this.documentInformation.tasks;

                    if (taskId) {
                        let index = tasks.findIndex(t => t.id == data.id);
                        tasks[index] = data;
                    } else {
                        tasks.push(data);
                    }

                    this.$store.dispatch("refreshDocumentInformation", {
                        tasks: tasks
                    });
                }
            });
        },
        saveDocument() {
            this.$store
                .dispatch("checkRequiredData")
                .then(() => {
                    this.$store
                        .dispatch("syncData", this.documentInformation)
                        .then(response => {
                            let route = `${process.env.ABSOLUTE_SAIA_ROUTE}views/documento/index_acordeon.php?`;
                            route += $.param({
                                documentId: this.documentInformation.documentId
                            });
                            window.location.href = route;
                        })
                        .catch(response => {
                            top.notification({
                                type: "error",
                                message: response.message
                            });
                        });
                })
                .catch(message => {
                    top.notification({
                        type: "error",
                        message: message
                    });
                });
        },
        getTopicLabel(topicId) {
            return this.documentInformation.topicList.find(i => i.id == topicId)
                .label;
        },
        getAssistants() {
            return this.documentInformation.userList.filter(
                u => +u.external == 0
            );
        },
        getInvited() {
            return this.documentInformation.userList.filter(
                u => +u.external == 1
            );
        },
        getInitialDate() {
            if (!this.documentInformation.initialDate) {
                return "";
            }

            let m = this.moment(
                this.documentInformation.initialDate,
                "YYYY-MM-DD HH:mm:ss"
            );
            return m.format("YYYY-MM-DD");
        },
        getInitialTime() {
            if (!this.documentInformation.initialDate) {
                return "";
            }

            let m = this.moment(
                this.documentInformation.initialDate,
                "YYYY-MM-DD HH:mm:ss"
            );
            return m.format("HH:mm:ss");
        },
        getFinaltime() {
            if (!this.documentInformation.finalDate) {
                return "";
            }

            let m = this.moment(
                this.documentInformation.finalDate,
                "YYYY-MM-DD HH:mm:ss"
            );
            return m.format("HH:mm:ss");
        },
        getUserName(userId) {
            var index = this.userNames.findIndex(u => u.iduser == userId);

            if (index == -1) {
                let baseUrl = process.env.ABSOLUTE_SAIA_ROUTE;
                $.ajax({
                    url: `${baseUrl}app/funcionario/consulta_funcionario.php`,
                    type: "POST",
                    dataType: "json",
                    data: {
                        key: localStorage.getItem("key"),
                        token: localStorage.getItem("token"),
                        type: "userInformation",
                        userId: userId
                    },
                    async: false,
                    success: response => {
                        if (response.success) {
                            this.userNames.push(response.data);
                        } else {
                            top.notification({
                                type: "error",
                                message: response.message
                            });
                        }
                    }
                });
            }

            index = this.userNames.findIndex(u => u.iduser == userId);
            return this.userNames[index].name;
        },
        getTasksUsers(users) {
            let names = [];

            users.forEach(userId => {
                names.push(this.getUserName(userId));
            });

            return names.join(", ", name);
        },
        createFab() {
            let instance = this;
            new Fab({
                selector: "#fab",
                button: {
                    style: "blue",
                    html: ""
                },
                icon: {
                    style: "fa fa-arrow-up",
                    html: ""
                },
                // "top-left" || "top-right" || "bottom-left" || "bottom-right"
                position: "bottom-right",
                // "horizontal" || "vertical"
                direction: "vertical",
                buttons: this.buttons
            });

            $(document).on("click", ".action", function() {
                let action = $(this).data("info").function;
                instance[action]();
            });
        },
        findFormat() {
            let baseUrl = process.env.ABSOLUTE_SAIA_ROUTE;
            $.ajax({
                url: `${baseUrl}app/formato/consulta.php`,
                type: "POST",
                dataType: "json",
                data: {
                    key: localStorage.getItem("key"),
                    token: localStorage.getItem("token"),
                    name: "acta"
                },
                success: response => {
                    if (response.success) {
                        this.findHeader(response.data.encabezado);
                        this.findHeader(response.data.pie_pagina, true);
                    } else {
                        top.notification({
                            type: "error",
                            message: response.message
                        });
                    }
                }
            });
        },
        findHeader(id, footer=false){
            let baseUrl = process.env.ABSOLUTE_SAIA_ROUTE;
            $.ajax({
                url: `${baseUrl}app/generador/obtener_contenido_encabezado.php`,
                type: "POST",
                dataType: "json",
                data: {
                    key: localStorage.getItem("key"),
                    token: localStorage.getItem("token"),
                    identificator: id
                },
                success: response => {
                    if (response.success) {
                        let selector = footer ? '#footer' : '#header';
                        $(selector).html(response.data.content);
                    } else {
                        top.notification({
                            type: "error",
                            message: response.message
                        });
                    }
                }
            });
        }
    },
    mounted: function() {
        var url = new URL(window.location.href);
        var schedule = url.searchParams.get("schedule") || null;
        var documentId = url.searchParams.get("documentId") || null;

        this.$store.dispatch("refreshParams", {
            schedule,
            documentId
        });
        this.createFab();
        this.findFormat();
    },
    computed: mapState([
        "documentInformation",
        "userNames",
        "params",
        "apiRoute"
    ])
};
</script>
<style>
.template {
    border: 1px solid #cacaca;
    margin-bottom: 8px;
    box-shadow: 2px 2px 8px #c6c6c6;
}

#template_parent {
    height: 100vh;
    overflow-y: auto;
}
</style>
