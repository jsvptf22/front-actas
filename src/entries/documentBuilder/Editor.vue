<template>
    <div class="container-fluid">
        <div class="row border">
            <div class="col-12 bg-white py-2 mx-0 f-20 d-flex d-md-block justify-content-around">
                <span
                    class="cursor fa fa-users px-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Validar Asistencia"
                    v-on:click="openUserModal"
                />
                <span
                    class="cursor fa fa-user px-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Elegir Presidente y Secretario(a)"
                    v-on:click="openRoleModal"
                />
                <span
                    class="cursor fa fa-wechat px-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Asunto del Acta"
                    v-on:click="openSubjectModal"
                />
                <span
                    class="cursor fa fa-list px-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Temas"
                    v-on:click="openTopicsModal"
                />
                <span
                    class="cursor fa fa-paragraph px-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Desarrollo de Temas"
                    v-on:click="openTopicDescriptionModal"
                />
                <span
                    class="cursor fa fa-tasks px-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Compromisos"
                    v-on:click="openTaskModal()"
                />
                <span class="border"></span>
                <span
                    class="cursor fa fa-share px-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Compartir"
                    v-on:click="openShareModal"
                />
                <span
                    class="cursor fa fa-question px-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Tomar Decisiones"
                    v-on:click="openQuestionModal"
                />
                <span
                    class="cursor fa fa-save px-1"
                    data-toggle="tooltip"
                    data-placement="bottom"
                    title="Guardar"
                    v-on:click="saveDocument"
                />
            </div>
        </div>
        <div class="row border p-2">
            <div class="col-12 col-md template_parent">
                <Viewer v-bind:documentId="documentInformation.documentId"></Viewer>
            </div>
        </div>
    </div>
</template>
<script>
import io from "socket.io-client";
import { mapState } from "vuex";
import Viewer from "./../qr/components/Viewer.vue";

export default {
    name: "Editor",
    components: {
        Viewer
    },
    methods: {
        openUserModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/users.php`,
                title: "Validación de asistencia"
            });
        },
        openSubjectModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/subject.php`,
                title: "Asunto del Acta"
            });
        },
        openTopicsModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/topics.php`,
                title: "Creación de temas"
            });
        },
        openTopicDescriptionModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/topic.php`,
                title: "Descripción del tema"
            });
        },
        openRoleModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/roles.php`,
                title: "Asignación de roles"
            });
        },
        openQuestionModal() {
            this.showModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/questions.php`,
                title: "Decisiones"
            });
        },
        openShareModal() {
            top.topModal({
                url: `views/modules/actas/src/entries/documentBuilder/modals/share.php`,
                title: "Compartir",
                onSuccess: data => {
                    this.$store.dispatch("shareRoute", data);
                    top.closeTopModal();
                }
            });
        },
        showModal(options) {
            top.topModal({
                ...options,
                onSuccess: data => {
                    this.$store.dispatch("syncData", data);
                    top.closeTopModal();
                }
            });
        },
        openTaskModal(taskId = null) {
            top.topModal({
                url: `views/tareas/crear.php`,
                params: {
                    id: taskId
                },
                centerAlign: false,
                size: "modal-lg",
                title: "Tarea o Recordatorio",
                buttons: {},
                onSuccess: data => {
                    data = {
                        id: data.id,
                        name: data.name,
                        managers: data.managers,
                        limitDate: data.finalDate
                    };
                    let tasks = this.documentInformation.tasks;

                    if (taskId) {
                        let index = tasks.findIndex(t => t.id == data.id);
                        tasks[index] = data;
                    } else {
                        tasks.push(data);
                    }

                    this.$store.dispatch("syncData", {
                        tasks: tasks
                    });
                }
            });
        },
        saveDocument() {
            this.$store
                .dispatch("checkRequiredData")
                .then(() => {
                    this.$store
                        .dispatch("syncData", this.documentInformation)
                        .then(response => {
                            let route = `${process.env.ABSOLUTE_SAIA_ROUTE}views/documento/index_acordeon.php?`;
                            route += $.param({
                                documentId: this.documentInformation.documentId
                            });
                            window.location.href = route;
                        })
                        .catch(response => {
                            top.notification({
                                type: "error",
                                message: response.message
                            });
                        });
                })
                .catch(message => {
                    top.notification({
                        type: "error",
                        message: message
                    });
                });
        }
    },
    mounted: function() {
        var url = new URL(window.location.href);
        var schedule = url.searchParams.get("schedule") || null;
        var documentId = url.searchParams.get("documentId") || null;

        this.$store.dispatch("setRequest", {
            schedule,
            documentId
        });
    },
    computed: {
        ...mapState(["documentInformation"])
    }
};
</script>
<style>
.template_parent {
    height: 100vh;
    overflow-y: auto;
}
</style>