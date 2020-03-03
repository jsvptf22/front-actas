import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

import DocumentBuilder from "../documentBuilder/DocumentBuilder.vue";
import Questions from "./components/Questions.vue";

const routes = [
    { path: "/document/:viewer", component: DocumentBuilder, props: true },
    { path: "/questions/:roomId", component: Questions, props: true },
    { path: "/", redirect: "/document/1" }
];

const router = new VueRouter({
    routes
});

export default router;
