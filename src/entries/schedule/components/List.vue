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
                    <div class="card-body">
                        {{ item.label }}
                        <span class="float-right text-master">{{
                            item.date
                        }}</span>
                    </div>
                    <div class="card-footer">
                        <button
                                class="btn btn-complete float-right"
                                v-on:click="openForm(item.id)"
                        >
                            Iniciar
                        </button>
                        <button
                                class="btn btn-success float-right mx-2"
                                v-on:click="sendNotification(item.id)"
                        >
                            Enviar recordatorio
                        </button>
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
            openForm(id) {
                $.post(
                    `${process.env.ABSOLUTE_SAIA_ROUTE}app/formato/consulta_rutas.php`,
                    {
                        key: localStorage.getItem("key"),
                        token: localStorage.getItem("token"),
                        formatName: "acta",
                        schedule: id
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
            sendNotification(id) {
                $.post(
                    `${process.env.ABSOLUTE_ACTAS_API_ROUTE}documento/enviar_enlace.php`,
                    {
                        key: localStorage.getItem('key'),
                        token: localStorage.getItem('token'),
                        shedule: id
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
