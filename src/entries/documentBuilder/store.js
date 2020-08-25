import Vue from 'vue';
import Vuex from 'vuex';
import moment from 'moment';
import io from 'socket.io-client';

Vue.prototype.moment = moment;
Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        socket: null,
        apiRoute: '',
        params: {},
        documentInformation: {
            id: 0,
            documentId: 0,
            identificator: 0,
            initialDate: moment().format('YYYY-MM-DD HH:mm:ss'),
            finalDate: '',
            subject: '',
            topics: [],
            userList: [],
            roles: {},
            tasks: [],
            qrUrl: '',
            headers: {},
            questions: [],
        },
    },
    mutations: {
        generateApiRoute(state, baseUrl) {
            state.apiRoute = process.env.ABSOLUTE_ACTAS_API_ROUTE;
        },
        refreshParams(state, data) {
            state.params = data;
        },
        refreshDocumentInformation(state, data) {
            top.window.actDocumentData = {...data};
            state.documentInformation = data;

            if (data.documentId) {
                state.socket.emit('defineRoom', data.documentId + '-Manager');
                state.socket.emit('updateClients', {
                    room: data.documentId + '-DocumentViewer',
                    data: data,
                });
                state.socket.emit('updateClients', {
                    room: data.documentId + '-QuestionViewer',
                    data: data.questions,
                });
            }
        },
        refreshSocket(state, socket) {
            state.socket = socket;
        },
    },
    actions: {
        checkRequiredData(context) {
            return new Promise((resolve, reject) => {
                let i = context.state.documentInformation;

                try {
                    if (!i.subject.length) {
                        throw 'Debe indicar el asunto';
                    }

                    if (!i.topics.length) {
                        throw 'Debe indicar los temas a tratar';
                    }

                    if (!i.userList.length) {
                        throw 'Debe indicar los asistentes';
                    }

                    if (!Object.keys(i.roles).length) {
                        throw 'Debe asignar los roles';
                    }

                    return resolve();
                } catch (error) {
                    return reject(error);
                }
            });
        },
        setRequest(context, data) {
            context.commit('generateApiRoute');
            context.commit('refreshParams', data);
            context.commit(
                'refreshDocumentInformation',
                context.state.documentInformation
            );

            if (data.documentId || data.schedule) {
                context.dispatch('findDocumentInformation');
            }
        },
        findDocumentInformation(context) {
            $.post(
                `${context.state.apiRoute}documento/consulta_editor.php`,
                {
                    key: localStorage.getItem('key'),
                    token: localStorage.getItem('token'),
                    documentId: context.state.params.documentId,
                    schedule: context.state.params.schedule,
                },
                function (response) {
                    if (response.success) {
                        context.dispatch('updateSocket', response.data);
                    } else {
                        top.notification({
                            type: 'error',
                            message: response.message,
                        });
                    }
                },
                'json'
            );
        },
        syncData(context, data) {
            return new Promise((resolve, reject) => {
                let newData = {
                    ...context.state.documentInformation,
                    ...data,
                };

                $.post(
                    `${context.state.apiRoute}documento/guardar.php`,
                    {
                        key: localStorage.getItem('key'),
                        token: localStorage.getItem('token'),
                        documentInformation: JSON.stringify(newData),
                    },
                    (response) => {
                        context.dispatch('updateSocket', response.data);
                        return response.success
                            ? resolve(response)
                            : reject(response);
                    },
                    'json'
                );
            });
        },
        shareRoute(context, data) {
            return new Promise((resolve, reject) => {
                $.post(
                    `${context.state.apiRoute}documento/enviar_enlace.php`,
                    {
                        key: localStorage.getItem('key'),
                        token: localStorage.getItem('token'),
                        documentId:
                        context.state.documentInformation.documentId,
                        data,
                    },
                    (response) => {
                        return response.success
                            ? resolve(response)
                            : reject(response);
                    },
                    'json'
                );
            });
        },
        updateSocket(context, data) {
            if (!context.state.socket && data.documentId) {
                context.dispatch('openSocket').then(() => {
                    if (data.documentId || data.schedule) {
                        context.commit('refreshDocumentInformation', data);
                    }
                });
            } else {
                context.commit('refreshDocumentInformation', data);
            }
        },
        openSocket(context) {
            let socket = io(process.env.ACTAS_NODE_SERVER + 'meeting');

            socket.on('getData', () => {
                context.commit(
                    'refreshDocumentInformation',
                    context.state.documentInformation
                );
            });

            socket.on('addVote', (data) => {
                let questions = context.state.documentInformation.questions;
                let index = questions.findIndex(
                    (q) => +q.id === +data.question
                );
                let question = questions[index];

                question.options = question.options.map(o => {
                    if (+o.id === +data.action) {
                        o.votes = +o.votes + 1;
                    }

                    return o;
                });

                questions[index] = question;
                context.dispatch('syncData', {questions});
            });

            context.commit('refreshSocket', socket);
        },
    },
});

export {store as default};
