const store = new Vuex.Store({
    state: {
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
        refreshDocumentInformation(state, information) {
            state.documentInformation = {
                ...state.documentInformation,
                ...information
            };
        }
    },
    actions: {
        refreshDocumentInformation(context, information) {
            return new Promise((resolve, reject) => {
                context.commit("refreshDocumentInformation", information);
                context
                    .dispatch("saveDocument")
                    .then(() => resolve())
                    .catch(() => reject());
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

                Axios.request({
                    url: `${process.env.VUE_APP_MODULE_API_ROUTE}document/save`,
                    method: "post",
                    responseType: "json",
                    data: context.state.documentInformation,
                    headers: {
                        Authorization: this._vm.$session.get("apiToken")
                    }
                })
                    .then(response => {
                        if (response.data.success) {
                            let newData = {
                                documentId: response.data.data.document.id,
                                identificator:
                                    response.data.data.document.identificator,
                                initialDate:
                                    response.data.data.document.initialDate,
                                finalDate:
                                    response.data.data.document.finalDate,
                                topicList: [],
                                topicListDescription: []
                            };

                            response.data.data.topics.forEach(t => {
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

                            context.commit(
                                "refreshDocumentInformation",
                                newData
                            );
                            return resolve();
                        } else {
                            alert("Error al guardar");
                        }
                    })
                    .catch(response => {
                        alert(response.message);
                        return reject();
                    });
            });
        }
    }
});

export { store as default };
