import "jquery";
import Vue from "vue";
import "bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import "GlobalAssets/theme/pages/css/pages.min.css";
import "GlobalAssets/theme/pages/js/pages.min.js";
import "GlobalAssets/theme/assets/plugins/font-awesome/css/font-awesome.min.css";
import DocumentBuilder from "./DocumentBuilder.vue";
import store from "./store.js";

window.jQuery = window.$ = jQuery;

Vue.config.productionTip = false;

new Vue({
    render: h => h(DocumentBuilder),
    store
}).$mount("#app");
