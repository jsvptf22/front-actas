import 'jquery';
import Vue from 'vue';
import 'bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'GlobalAssets/theme/pages/css/pages.min.css';
import './custompages';
import 'GlobalAssets/theme/assets/plugins/font-awesome/css/font-awesome.min.css';
import QrDashboard from './components/QrDashboard.vue';
import router from './router';
import store from '../documentBuilder/store';

window.jQuery = window.$ = jQuery;

Vue.config.productionTip = false;

new Vue({
    render: (h) => h(QrDashboard),
    router,
    store,
}).$mount('#app');
