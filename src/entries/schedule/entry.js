import "jquery";
window.jQuery = window.$ = jQuery;

import Vue from "vue";
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import "GlobalAssets/theme/pages/css/pages.min.css";
import "GlobalAssets/theme/pages/js/pages.min.js";
import "GlobalAssets/theme/assets/plugins/font-awesome/css/font-awesome.min.css";

Vue.config.productionTip = false;

import Schedule from "./components/Schedule.vue";

new Vue({
    render: h => h(Schedule)
}).$mount("#app");
