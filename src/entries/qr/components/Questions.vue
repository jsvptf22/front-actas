<template>
    <div class="container" id="app">
        <div class="row">
            <div class="col-12 table-responsive px-0">
                <table class="table table-bordered my-0">
                    <tbody>
                        <tr>
                            <th class="text-center bold">Pregunta</th>
                            <th class="text-center bold">Aprobación</th>
                            <th class="text-center bold">Rechazo</th>
                            <th class="text-center bold">Opciones</th>
                        </tr>
                        <tr v-for="question in questions" :key="question.idact_question">
                            <td class="text-center">{{ question.label }}</td>
                            <td class="text-center">{{ question.approve }}</td>
                            <td class="text-center">{{ question.reject }}</td>
                            <td class="text-center">
                                <button
                                    v-if="canShow(question.idact_question)"
                                    v-on:click="vote(1, question.idact_question)"
                                    class="btn btn-sm btn-block btn-complete"
                                >Aprobar</button>
                                <button
                                    v-if="canShow(question.idact_question)"
                                    v-on:click="vote(0, question.idact_question)"
                                    class="btn btn-sm btn-block btn-danger"
                                >Rechazar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
    import io from "socket.io-client";

    export default {
    name: "Questions",
    props: ["documentId"],
    data: function() {
        return {
            socket: null,
            questions: []
        };
    },
    methods: {
        canShow(question) {
            let actions = localStorage.getItem("actions") || "{}";
            actions = JSON.parse(actions);

            let keys = actions[this.documentId]
                ? Object.keys(actions[this.documentId])
                : [];

            return keys.indexOf(question) == -1;
        },
        vote(action, questionId) {
            this.socket.emit("vote", {
                room: this.documentId + "-Manager",
                question: questionId,
                action: action
            });
            this.storeLocalAction(this.documentId, questionId, action);
        },
        storeLocalAction(room, question, action) {
            let actions = localStorage.getItem("actions") || "{}";
            actions = JSON.parse(actions);

            if (!actions[room]) {
                actions[room] = {};
            }

            actions[room][question] = action;
            actions = JSON.stringify(actions);
            localStorage.setItem("actions", actions);
        },
        createSocket() {
            this.socket = io(process.env.ACTAS_NODE_SERVER + "meeting");

            this.socket.on("refreshClient", data => {
                this.questions = data;
            });
        },
        syncData(documentId) {
            this.socket.emit("defineRoom", documentId + "-QuestionViewer");
            this.socket.emit("getData", documentId + "-Manager");
        }
    },
    mounted: function() {
        this.createSocket();
        this.syncData(this.documentId);
    }
};
</script>
