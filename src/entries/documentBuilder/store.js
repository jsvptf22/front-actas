import Vue from "vue";
import Vuex from "vuex";
import moment from "moment";
import io from "socket.io-client";

Vue.prototype.moment = moment;
Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        socket: null,
        apiRoute: "",
        params: {},
        userNames: [],
        documentInformation: {
            id: 0,
            documentId: 0,
            identificator: 0,
            fk_agendamiento_act: 0,
            initialDate: moment().format("YYYY-MM-DD HH:mm:ss"),
            finalDate: "",
            subject: "",
            topicList: [],
            topicListDescription: [],
            userList: [],
            roles: {},
            tasks: [],
            questions: {
                room: "",
                items: []
            },
            qrUrl: ""
        }
    },
    mutations: {
        generateApiRoute(state, baseUrl) {
            state.apiRoute = process.env.ABSOLUTE_ACTAS_API_ROUTE;
        },
        refreshParams(state, data) {
            state.params = data;
        },
        refreshDocumentInformation(state, data) {
            var url = new URL(window.location.href);
            var finded =
                url.searchParams.get("schedule") ||
                url.searchParams.get("documentId");

            if (!finded && data.documentId) {
                window.location.href += "&documentId=" + data.documentId;
            }

            state.documentInformation = data;
        },
        refreshSocket(state, socket) {
            state.socket = socket;
        }
    },
    actions: {
        refreshParams(context, data) {
            context.commit("generateApiRoute");
            context.commit("refreshParams", data);
            context.dispatch("openSocket").then(() => {
                if (data.documentId || data.schedule) {
                    context.dispatch("findDocumentInformation");
                }
            });
        },
        refreshDocumentInformation(context, data) {
            return new Promise((resolve, reject) => {
                let newData = {
                    ...context.state.documentInformation,
                    ...data
                };

                context
                    .dispatch("syncData", newData)
                    .then(response => {
                        context.state.socket.emit(
                            "defineRoom",
                            response.data.documentId
                        );

                        context.state.socket.emit(
                            "refreshEditor",
                            response.data
                        );
                    })
                    .catch(r => console.error(r.message));
            });
        },
        findDocumentInformation(context) {
            $.post(
                `${context.state.apiRoute}documento/consulta_editor.php`,
                {
                    key: localStorage.getItem("key"),
                    token: localStorage.getItem("token"),
                    documentId: context.state.params.documentId,
                    schedule: context.state.params.schedule
                },
                function(response) {
                    if (response.success) {
                        context.state.socket.emit(
                            "defineRoom",
                            response.data.documentId
                        );

                        context.state.socket.emit(
                            "refreshEditor",
                            response.data
                        );
                    } else {
                        top.notification({
                            type: "error",
                            message: response.message
                        });
                    }
                },
                "json"
            );
        },
        convertDocumentInformation(context, data) {
            return new Promise((resolve, reject) => {
                data.topicList = [];
                data.topicListDescription = [];

                if (data.topics) {
                    data.topics.forEach(t => {
                        data.topicList.push({
                            id: t.id,
                            label: t.name
                        });

                        if (t.description) {
                            data.topicListDescription.push({
                                topic: t.id,
                                description: t.description
                            });
                        }
                    });
                }

                context.commit("refreshDocumentInformation", data);
                top.window.actDocumentData = { ...data };
                resolve();
            });
        },
        checkRequiredData(context) {
            return new Promise((resolve, reject) => {
                let i = context.state.documentInformation;

                try {
                    if (!i.subject.length) {
                        throw "Debe indicar el asunto";
                    }

                    if (!i.topicList.length) {
                        throw "Debe indicar los temas a tratar";
                    }

                    if (!i.userList.length) {
                        throw "Debe indicar los asistentes";
                    }

                    if (!Object.keys(i.roles).length) {
                        throw "Debe asignar los roles";
                    }

                    return resolve();
                } catch (error) {
                    return reject(error);
                }
            });
        },
        syncData(context, data) {
            return new Promise((resolve, reject) => {
                $.post(
                    `${context.state.apiRoute}documento/guardar.php`,
                    {
                        key: localStorage.getItem("key"),
                        token: localStorage.getItem("token"),
                        documentInformation: JSON.stringify(data)
                    },
                    response => {
                        return response.success
                            ? resolve(response)
                            : reject(response);
                    },
                    "json"
                );
            });
        },
        openSocket(context) {
            let socket = io(process.env.ACTAS_NODE_SERVER + "documentBuilder");

            socket.on("refreshEditor", data => {
                context.dispatch("convertDocumentInformation", data);
            });

            context.commit("refreshSocket", socket);
        }
    }
});

export { store as default };
