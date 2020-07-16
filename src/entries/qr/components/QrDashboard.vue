<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills nav-fill mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a
                            v-on:click="openTab('/document/')"
                            class="nav-link active"
                            data-toggle="pill"
                            role="tab"
                            aria-controls="pills-home"
                            aria-selected="true"
                        >Documento</a>
                    </li>
                    <li class="nav-item">
                        <a
                            v-on:click="openTab('/questions/')"
                            class="nav-link"
                            data-toggle="pill"
                            role="tab"
                            aria-controls="pills-profile"
                            aria-selected="false"
                        >Decisiones</a>
                    </li>
                </ul>
                <hr />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <router-view></router-view>
            </div>
        </div>
    </div>
</template>
<script>
    import {mapState} from "vuex";

    export default {
    name: "QrDashboard",
    data: function() {
        return {
            documentId: 0
        };
    },
    computed: mapState(["documentInformation"]),
    methods: {
        openTab(route) {
            this.$router.push(route + this.documentId);
        }
    },
    mounted: function() {
        var url = new URL(window.location.href);
        this.documentId = url.searchParams.get("documentId");
    }
};
</script>
<style>
    .nav-link{
        background: white !important;
    }
    .nav-link.active{
        background: #6a6a6a !important;
        color: white !important;
    }
</style>
