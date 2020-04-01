<template>
    <div class="container-fluid">
        <div v-show="!viewer" class="row border">
            <div class="col-12 bg-white py-2 mx-0 f-20 d-flex d-md-block justify-content-around">
                <span
                        class="cursor fa fa-users px-1"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Validar Asistencia"
                        v-on:click="openUserModal"
                />
                <span
                        class="cursor fa fa-user px-1"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Elegir Presidente y Secretario(a)"
                        v-on:click="openRoleModal"
                />
                <span
                        class="cursor fa fa-wechat px-1"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Asunto del Acta"
                        v-on:click="openSubjectModal"
                />
                <span
                        class="cursor fa fa-list px-1"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Temas"
                        v-on:click="openTopicsModal"
                />
                <span
                        class="cursor fa fa-paragraph px-1"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Desarrollo de Temas"
                        v-on:click="openTopicDescriptionModal"
                />
                <span
                        class="cursor fa fa-tasks px-1"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Compromisos"
                        v-on:click="openTaskModal()"
                />
                <span class="border"></span>
                <span
                        class="cursor fa fa-share px-1"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Compartir"
                        v-on:click="openShareModal"
                />
                <span
                        class="cursor fa fa-question px-1"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Tomar Decisiones"
                        v-on:click="openQuestionModal"
                />
                <span
                        class="cursor fa fa-save px-1"
                        data-toggle="tooltip"
                        data-placement="bottom"
                        title="Guardar"
                        v-on:click="saveDocument"
                />
            </div>
        </div>
        <div class="row border p-2">
            <div v-bind:class="[ viewer ? '' : 'template_parent','col-12 col-md']">
                <div class="template p-4 p-md-5 bg-white">
                    <div class="row-fluid mb-3">
                        <div class="col-12 text-center py-3 px-0" id="header"></div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered">
                                <tr>
                                    <td class="bold">Acta N°</td>
                                    <td>
                                        {{ documentInformation.identificator }}
                                    </td>
                                    <td
                                            v-if="this.documentInformation.qrUrl"
                                            rowspan="4"
                                            id="qr"
                                            class="text-center align-middle"
                                    >
                                        <img
                                                :src="absoluteQrRoute"
                                                width="90"
                                        />
                                    </td>
                                </tr>
                                <tr>
                                    <td class="bold">Tema / Asunto</td>
                                    <td>
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
                                    <td class="text-center bold">
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
                                            <br/>
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
                                            <br/>
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
                                                <br/>
                                                <p v-html="item.description"></p>
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
                                    <td class="text-center bold" colspan="3">
                                        Decisiones
                                    </td>
                                </tr>
                                <tr
                                        v-if="
                                        documentInformation.questions.items
                                            .length
                                    "
                                >
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
                                    index) of documentInformation.questions
                                        .items"
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
                                                    Fecha límite
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
                                                <td>{{task.limitDate}}</td>
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
                        <div
                                class="col-12 text-center py-3 px-0"
                                id="footer"
                        ></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import {mapState} from "vuex";

    export default {
        name: "DocumentBuilder",
        props: ["viewer"],
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
            openShareModal() {
                top.window.actDocumentData = {
                    ...this.$store.state.documentInformation
                };
                top.topModal({
                    url: `views/modules/actas/src/entries/documentBuilder/modals/share.php`,
                    title: "Compartir",
                    onSuccess: data => {
                        this.$store.dispatch("shareRoute", data);
                        top.closeTopModal();
                    }
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
                            managers: data.managers,
                            limitDate: data.finalDate
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
            findHeaders() {
                let apiRoute = process.env.ABSOLUTE_ACTAS_API_ROUTE;
                $.ajax({
                    url: `${apiRoute}formato/obtener_ecabezados_estaticos.php`,
                    type: "POST",
                    dataType: "json",
                    data: {
                        key: localStorage.getItem("key"),
                        token: localStorage.getItem("token"),
                        format: "acta"
                    },
                    success: response => {
                        if (response.success) {
                            $("#header").html(response.data.header);
                            $("#footer").html(response.data.footer);
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
        mounted: function () {
            var url = new URL(window.location.href);
            var schedule = url.searchParams.get("schedule") || null;
            var documentId = url.searchParams.get("documentId") || null;

            this.$store.dispatch("refreshParams", {
                schedule,
                documentId
            });
            this.findHeaders();
        },
        computed: {
            ...mapState(["documentInformation", "userNames", "params", "apiRoute"]),
            absoluteQrRoute() {
                return (
                    process.env.ABSOLUTE_SAIA_ROUTE + this.documentInformation.qrUrl
                );
            }
        }
    };
</script>
<style>
    .template {
        border: 1px solid #cacaca;
        margin-bottom: 8px;
        box-shadow: 2px 2px 8px #c6c6c6;
    }

    .template_parent {
        height: 95vh;
        overflow-y: auto;
    }
</style>
