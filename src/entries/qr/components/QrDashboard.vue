<template>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul
                    class="nav nav-pills nav-fill mb-3"
                    id="pills-tab"
                    role="tablist"
                >
                    <li class="nav-item">
                        <a
                            v-on:click="openTab('/document/1')"
                            class="nav-link active"
                            data-toggle="pill"
                            role="tab"
                            aria-controls="pills-home"
                            aria-selected="true"
                            >Documento</a
                        >
                    </li>
                    <li class="nav-item">
                        <a
                            v-on:click="
                                openTab(
                                    '/questions/' +
                                        documentInformation.questions.room
                                )
                            "
                            class="nav-link"
                            data-toggle="pill"
                            role="tab"
                            aria-controls="pills-profile"
                            aria-selected="false"
                            >Decisiones</a
                        >
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
import { mapState } from "vuex";
export default {
    name: "QrDashboard",
    computed: mapState(["documentInformation"]),
    methods: {
        openTab(route) {
            this.$router.push(route);
        }
    }
};
</script>
