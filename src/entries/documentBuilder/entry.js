import "jquery";
window.jQuery = window.$ = jQuery;

import Vue from "vue";
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import "GlobalAssets/theme/pages/css/pages.min.css";
import "GlobalAssets/theme/pages/js/pages.min.js";
import "GlobalAssets/theme/assets/plugins/font-awesome/css/font-awesome.min.css";

Vue.config.productionTip = false;

import DocumentBuilder from "./DocumentBuilder.vue";
import store from "./store.js";

new Vue({
    render: h => h(DocumentBuilder),
    store
}).$mount("#app");
