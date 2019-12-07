const store = new Vuex.Store({
    state: {
        apiRoute: "",
        params: {},
        userNames: [],
        documentInformation: {
            documentId: 0,
            identificator: 0,
            initialDate: moment().format("YYYY-MM-DD HH:mm:ss"),
            finalDate: "",
            subject: "",
            topicList: [],
            topicListDescription: [],
            userList: [],
            roles: {},
            tasks: []
        }
    },
    mutations: {
        refreshParams(state, params) {
            state.params = params;
        },
        generateApiRoute(state, baseUrl) {
            state.apiRoute = baseUrl + "app/modules/actas/app/";
        },
        refreshDocumentInformation(state, information) {
            state.documentInformation = {
                ...state.documentInformation,
                ...information
            };
        }
    },
    actions: {
        refreshParams(context, data) {
            context.commit("refreshParams", data);
            context.commit("generateApiRoute", data.baseUrl);

            if (!data.documentId) {
                context.dispatch("refreshDocumentInformation", {});
            } else {
                context.dispatch("findDocumentInformation");
            }
        },
        refreshDocumentInformation(context, information) {
            return new Promise((resolve, reject) => {
                context.commit("refreshDocumentInformation", information);
            });
        },
        findDocumentInformation(context) {
            $.post(
                `${context.state.apiRoute}documento/consulta_editor.php`,
                {
                    key: localStorage.getItem("key"),
                    token: localStorage.getItem("token"),
                    documentId: context.state.params.documentId
                },
                function(response) {
                    if (response.success) {
                        context.dispatch(
                            "updateDocumentInformation",
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
        updateDocumentInformation(context, data) {
            return new Promise((resolve, reject) => {
                data.topicList = [];
                data.topicListDescription = [];

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

                context.commit("refreshDocumentInformation", data);

                resolve();
            });
        }
    }
});

store.dispatch("refreshParams", $("#base_script").data("params"));
top.window.actDocumentData = { ...store.state.documentInformation };

export { store as default };
