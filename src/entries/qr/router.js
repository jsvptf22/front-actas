import Vue from 'vue';
import VueRouter from 'vue-router';

Vue.use(VueRouter);

import Viewer from './components/Viewer.vue';
import Questions from './components/Questions.vue';

var url = new URL(window.location.href);
var documentId = url.searchParams.get('documentId');

const routes = [
    { path: '/document/:documentId', component: Viewer, props: true },
    { path: '/questions/:roomId', component: Questions, props: true },
    { path: '/', redirect: '/document/' + documentId },
];

const router = new VueRouter({
    routes,
});

export default router;
