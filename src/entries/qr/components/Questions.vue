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
                        <tr v-for="question in questions" :key="question._id">
                            <td class="text-center">{{ question.label }}</td>
                            <td class="text-center">{{ question.approve }}</td>
                            <td class="text-center">{{ question.reject }}</td>
                            <td class="text-center">
                                <button
                                    v-if="canShow(question._id)"
                                    v-on:click="vote(1, question._id)"
                                    class="btn btn-sm btn-block btn-complete"
                                >
                                    Aprobar
                                </button>
                                <button
                                    v-if="canShow(question._id)"
                                    v-on:click="vote(0, question._id)"
                                    class="btn btn-sm btn-block btn-danger"
                                >
                                    Rechazar
                                </button>
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
import Room from "./Room";

export default {
    name: "Questions",
    props: ["roomId"],
    data: function() {
        return {
            identificator: "",
            Room: null,
            socket: null,
            question: "",
            questions: []
        };
    },
    methods: {
        canShow(question) {
            let actions = localStorage.getItem("actions") || "{}";
            actions = JSON.parse(actions);

            let keys = actions[this.Room._id]
                ? Object.keys(actions[this.Room._id])
                : [];

            return keys.indexOf(question) == -1;
        },
        vote(action, questionId) {
            fetch(
                `${process.env.ACTAS_NODE_SERVER}api/room/${this.Room._id}/questions/${questionId}/vote/${action}`,
                {
                    method: "POST"
                }
            )
                .then(function(response) {
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.storeLocalAction(
                            this.Room._id,
                            questionId,
                            action
                        );
                        this.refreshQuestions();
                        top.notification({
                            type: "success",
                            message: data.message
                        });
                    } else {
                        top.notification({
                            type: "error",
                            message: data.message
                        });
                    }
                });
        },
        refreshQuestions() {
            this.socket.emit("refreshQuestions", this.Room._id);
        },
        defineRoom() {
            fetch(`${process.env.ACTAS_NODE_SERVER}api/room/${this.roomId}`, {
                method: "GET"
            })
                .then(function(response) {
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        this.Room = new Room(data.data);
                        this.socket.emit("defineRoom", this.Room._id);
                        this.refreshQuestions();
                    } else {
                        top.notification({
                            type: "error",
                            message: data.message
                        });
                    }
                });
        },
        defineSocket() {
            this.socket = io(process.env.ACTAS_NODE_SERVER + "room");

            this.socket.on("refreshQuestions", questions => {
                this.questions = questions;
            });

            this.defineRoom();
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
        }
    },
    created: function() {
        this.defineSocket();
    }
};
</script>
