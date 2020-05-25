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
            fk_agendamiento_act: 0,
            initialDate: moment().format('YYYY-MM-DD HH:mm:ss'),
            finalDate: '',
            subject: '',
            topics: [],
            userList: [],
            roles: {},
            tasks: [],
            questions: {
                room: '',
                items: [],
            },
            qrUrl: '',
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
            top.window.actDocumentData = { ...data };
            state.documentInformation = data;

            let socketRoom = data.documentId;
            state.socket.emit('defineRoom', socketRoom + '-Manager');
            state.socket.emit('updateClients', {
                room: socketRoom,
                data: data,
            });
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

                    if (!i.topicList.length) {
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
            context.dispatch('openSocket').then(() => {
                if (data.documentId || data.schedule) {
                    context.dispatch('findDocumentInformation');
                }
            });
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
                function(response) {
                    if (response.success) {
                        context.commit(
                            'refreshDocumentInformation',
                            response.data
                        );
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
                        context.commit(
                            'refreshDocumentInformation',
                            response.data
                        );
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
        openSocket(context) {
            let socket = io(process.env.ACTAS_NODE_SERVER + 'meeting');

            socket.on('getData', () => {
                context.commit(
                    'refreshDocumentInformation',
                    context.state.documentInformation
                );
            });

            context.commit('refreshSocket', socket);
        },
    },
});

export { store as default };
