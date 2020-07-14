<template>
    <div>
        <div class="row">
            <div class="col-12">
                <h6>Próximos eventos</h6>
            </div>
        </div>
        <div class="row">
            <div class="col-12" id="list" v-if="items.length">
                <div class="card" v-for="item of items" v-bind:key="item.id">
                    <div class="card-header bold">
                        <h6 class="d-inline">{{ item.label }}</h6>
                        <span class="float-right text-master">{{
                            item.date
                        }}</span>
                        <hr class="mb-0">
                    </div>
                    <div class="card-body">
                        <div class="row mx-0 py-0" v-if="item.maker">
                            <div class="col px-0">
                                <span class="bold">Organizador</span>
                                <p class="bg-master-lighter p-2 ml-3 my-1">
                                    {{item.maker.name}}
                                </p>
                                <hr class="my-1">
                            </div>
                        </div>
                        <div class="row mx-0 py-0" v-if="item.internalAssistants.length">
                            <div class="col px-0">
                                <span class="bold">Asistentes internos</span>
                                <p class="bg-master-lighter p-2 ml-3 my-1" v-for="assistant of item.internalAssistants">
                                    {{assistant.name}}
                                </p>
                                <hr class="my-1">
                            </div>
                        </div>
                        <div class="row mx-0 py-0" v-if="item.externalAssistants.length">
                            <div class="col px-0">
                                <span class="bold">Asistentes externos</span>
                                <p class="bg-master-lighter p-2 ml-3 my-1" v-for="assistant of item.externalAssistants">
                                    {{assistant.name}}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <span
                                class="cursor float-right mx-2 f-20"
                                v-on:click="openForm(item.documentId)"
                                title="Iniciar reunión"
                        >
                            <i class="fa fa-arrow-right"></i>
                        </span>
                        <span
                                class="cursor float-right mx-2 f-20"
                                v-on:click="sendNotification(item.documentId)"
                                title="Enviar recordatorio"
                        >
                            <i class="fa fa-share-alt"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default {
        name: "List",
        data: function () {
            return {
                items: []
            };
        },
        methods: {
            refreshList() {
                $.post(
                    `${process.env.ABSOLUTE_ACTAS_API_ROUTE}agendamiento/listar.php`,
                    {
                        key: localStorage.getItem("key"),
                        token: localStorage.getItem("token")
                    },
                    response => {
                        if (response.success) {
                            this.items = response.data.list || [];
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
            openForm(documentId) {
                $.post(
                    `${process.env.ABSOLUTE_SAIA_ROUTE}app/formato/consulta_rutas.php`,
                    {
                        key: localStorage.getItem("key"),
                        token: localStorage.getItem("token"),
                        formatName: "acta",
                        documentId: documentId
                    },
                    function (response) {
                        if (response.success) {
                            window.location.href =
                                process.env.ABSOLUTE_SAIA_ROUTE +
                                response.data.ruta_adicionar;
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
            sendNotification(documentId) {
                $.post(
                    `${process.env.ABSOLUTE_ACTAS_API_ROUTE}documento/enviar_enlace.php`,
                    {
                        key: localStorage.getItem('key'),
                        token: localStorage.getItem('token'),
                        documentId: documentId
                    },
                    (response) => {
                        if (response.success) {
                            top.notification({
                                type: 'success',
                                message: response.message
                            })
                        }
                    },
                    'json'
                );
            }
        },
        mounted: function () {
            this.$root.$on("resfreshScheduleList", () => {
                this.refreshList();
            });
        }
    };
</script>
