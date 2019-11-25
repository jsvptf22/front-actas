const store = new Vuex.Store({
    state: {
        apiRoute: "",
        params: {},
        userNames: [],
        documentInformation: {
            id: 0,
            identificator: 0,
            initialDate: "",
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
                console.log("Pendiente por desarrollar el editar");
            }
        },
        refreshDocumentInformation(context, information) {
            return new Promise((resolve, reject) => {
                context.commit("refreshDocumentInformation", information);
            });
        },
        checkRequiredData(context) {
            return new Promise((resolve, reject) => {
                let i = context.state.documentInformation;
                console.log(
                    i.topicList,
                    i.topicList.length,
                    i.userList,
                    i.userList.length
                );

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
        }
        /*saveDocument(context) {
            return new Promise((resolve, reject) => {
                if (
                    !context.state.documentInformation.subject.length &&
                    !context.state.documentInformation.id
                ) {
                    return reject();
                }

                $.post(
                    `${context.state.apiRoute}documento/guardar.php`,
                    {
                        ...context.state.documentInformation,
                        key: localStorage.getItem("key"),
                        token: localStorage.getItem("token")
                    },
                    response => {
                        if (response.success) {
                            context
                                .dispatch("updateAfterSave", response.data)
                                .then(() => {
                                    resolve();
                                })
                                .catch(() => {
                                    top.notification({
                                        type: "error",
                                        message: "Error al actualizar los datos"
                                    });
                                    reject();
                                });
                        } else {
                            top.notification({
                                type: "error",
                                message: response.message
                            });

                            reject();
                        }
                    },
                    "json"
                );
            });
        },
        updateAfterSave(context, data) {
            return new Promise((resolve, reject) => {
                let newData = {
                    id: data.document.id,
                    identificator: data.document.identificator,
                    initialDate: data.document.initialDate,
                    finalDate: data.document.finalDate,
                    topicList: [],
                    topicListDescription: []
                };

                data.topics.forEach(t => {
                    newData.topicList.push({
                        id: t.idact_document_topic,
                        label: t.name
                    });

                    if (t.description) {
                        newData.topicListDescription.push({
                            topic: t.idact_document_topic,
                            description: t.description
                        });
                    }
                });

                console.log(newData, context.state.documentInformation);
                context.commit("refreshDocumentInformation", newData);

                resolve();
            });
        }*/
    }
});

store.dispatch("refreshParams", $("#base_script").data("params"));
top.window.actDocumentData = { ...store.state.documentInformation };

export { store as default };
