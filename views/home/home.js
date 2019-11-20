Vue.prototype.moment = moment;
Vue.config.productionTip = false;
Vue.use(Vuex);

import PHome from "./PHome.js";
import router from "./router.js";
import store from "./store.js";

let vue = new Vue({
    render: h => h(PHome),
    router,
    store
}).$mount("#app");

const attribute = document.getElementById("document_script").dataset.params;
const params = JSON.parse(attribute);
store.dispatch("refreshParams", params);
