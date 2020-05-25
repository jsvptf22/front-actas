<template>
    <div class="template p-4 p-md-5 bg-white">
        <div class="row-fluid mb-3">
            <div class="col-12 text-center py-3 px-0" id="header"></div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <tr>
                        <td class="bold">Acta N°</td>
                        <td>{{ documentInformation.identificator }}</td>
                        <td
                            v-if="this.documentInformation.qrUrl"
                            rowspan="4"
                            id="qr"
                            class="text-center align-middle"
                        >
                            <img :src="absoluteQrRoute" width="90" />
                        </td>
                    </tr>
                    <tr>
                        <td class="bold">Tema / Asunto</td>
                        <td>{{ documentInformation.subject }}</td>
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
                        <td class="text-center bold">Participantes</td>
                    </tr>
                    <tr>
                        <td>
                            <span class="bold">Asistentes:</span>
                            <div v-for="user of getAssistants()" v-bind:key="user.id">
                                <span class="ml-5">
                                    {{
                                    user.name
                                    }}
                                </span>
                                <br />
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="bold">Invitados:</span>
                            <div v-for="user of getInvited()" v-bind:key="user.id">
                                <span class="ml-5">
                                    {{
                                    user.name
                                    }}
                                </span>
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
                        <td class="text-center bold">Puntos a Tratar / Orden del día</td>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li
                                    v-for="topic of documentInformation.topicList"
                                    v-bind:key="topic.id"
                                >{{ topic.label }}</li>
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
                        <td class="text-center bold">Puntos Tratados / Desarrollo</td>
                    </tr>
                    <tr>
                        <td>
                            <ul>
                                <li
                                    v-for="item of documentInformation.topicListDescription"
                                    v-bind:key="item.id"
                                >
                                    <span>
                                        {{
                                        getTopicLabel(item.topic)
                                        }}
                                    </span>
                                    <br />
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
                    <thead>
                        <tr>
                            <td class="text-center bold" colspan="3">Decisiones</td>
                        </tr>
                    </thead>
                    <tbody
                        v-if="
                            documentInformation.questions &&
                            documentInformation.questions.items.length
                        "
                    >
                        <tr>
                            <th class="text-center bold">Pregunta</th>
                            <th class="text-center bold">Aprobación</th>
                            <th class="text-center bold">Rechazo</th>
                        </tr>
                        <tr
                            v-for="(question,index) of documentInformation.questions.items"
                            v-bind:key="index"
                        >
                            <td>{{ question.label }}</td>
                            <td>{{ question.approve }}</td>
                            <td>{{ question.reject }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <tr>
                        <td class="text-center bold">Compromisos</td>
                    </tr>
                    <tr>
                        <td>
                            <table
                                v-if="
                                    documentInformation.tasks &&
                                    documentInformation.tasks.length
                                "
                                class="table"
                            >
                                <tr>
                                    <th class="text-center bold">Tarea</th>
                                    <th class="text-center bold">Responsable</th>
                                    <th class="text-center bold">Fecha límite</th>
                                    <th class="text-center bold">Ver</th>
                                </tr>
                                <tr
                                    v-for="(task,index) of documentInformation.tasks"
                                    v-bind:key="index"
                                >
                                    <td>{{ task.name }}</td>
                                    <td>{{getTasksUsers(task.managers)}}</td>
                                    <td>{{task.limitDate}}</td>
                                    <td>
                                        <button class="btn" v-on:click="openTaskModal(task.id)">
                                            <span class="fa fa-eye"></span>
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
                                    documentInformation.roles && 
                                    documentInformation.roles.secretary
                                "
                            >{{documentInformation.roles.secretary.name}}</span>
                        </td>
                        <td>
                            <span class="bold">PRESIDENTE:</span>
                            <span
                                v-if="
                                    documentInformation.roles &&
                                    documentInformation.roles.president
                                "
                            >{{documentInformation.roles.president.name}}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row-fluid mt-3">
            <div class="col-12 text-center py-3 px-0" id="footer"></div>
        </div>
    </div>
</template>
<script>
import io from "socket.io-client";

export default {
    name: "Viewer",
    data: function() {
        return {
            documentInformation: {},
            socket: null
        };
    },
    props: ["documentId"],
    methods: {
        getAssistants() {
            if (this.documentInformation.userList) {
                var response = this.documentInformation.userList.filter(
                    u => +u.external == 0
                );
            } else {
                var response = {};
            }

            return response;
        },
        getInvited() {
            if (this.documentInformation.userList) {
                var response = this.documentInformation.userList.filter(
                    u => +u.external == 1
                );
            } else {
                var response = {};
            }

            return response;
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
        },
        createSocket() {
            this.socket = io(process.env.ACTAS_NODE_SERVER + "meeting");

            this.socket.on("refreshClient", data => {
                this.documentInformation = data;
            });
        },
        syncData(documentId) {
            this.socket.emit("defineRoom", documentId);
            this.socket.emit("getData", documentId + "-Manager");
        }
    },
    mounted: function() {
        this.findHeaders();
        this.createSocket();
        this.syncData(this.documentId);
    },
    watch: {
        documentId: function(value) {
            this.syncData(value);
        }
    },
    computed: {
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
