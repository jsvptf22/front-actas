import "jquery";
window.jQuery = window.$ = jQuery;

import Vue from "vue";
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import "GlobalAssets/theme/pages/css/pages.min.css";
import "GlobalAssets/theme/pages/js/pages.min.js";
import "GlobalAssets/theme/assets/plugins/font-awesome/css/font-awesome.min.css";

Vue.config.productionTip = false;

import QrDashboard from "./components/QrDashboard.vue";
import router from "./router";
import store from "../documentBuilder/store";

new Vue({
    render: h => h(QrDashboard),
    router,
    store
}).$mount("#app");
