const store = new Vuex.Store({
    state: {
        apiRoute: "",
        params: {},
        documentInformation: {
            modalTitle: "",
            documentId: 0,
            identificator: 0,
            initialDate: "",
            finalDate: "",
            subject: "",
            topicList: [],
            topicListDescription: [],
            userList: [],
            roles: {}
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
        },
        refreshDocumentInformation(context, information) {
            return new Promise((resolve, reject) => {
                context.commit("refreshDocumentInformation", information);
                context
                    .dispatch("saveDocument")
                    .then(() => {
                        console.log("resolve");
                    })
                    .catch(() => {
                        console.log("reject");
                    });
            });
        },
        saveDocument(context) {
            return new Promise((resolve, reject) => {
                if (
                    !context.state.documentInformation.subject.length &&
                    !context.state.documentInformation.documentId
                ) {
                    return reject();
                }

                $.post(
                    `${context.state.apiRoute}document/save.php`,
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

                            return reject();
                        }
                    },
                    "json"
                );
            });
        },
        updateAfterSave(context, data) {
            return new Promise((resolve, reject) => {
                let newData = {
                    documentId: data.document.id,
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

                console.log(
                    context.commit("refreshDocumentInformation", newData)
                );
                return resolve();
            });
        }
    }
});

export { store as default };
