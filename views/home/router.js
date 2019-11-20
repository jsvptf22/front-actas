Vue.prototype.$http = axios;
Vue.use(VueRouter);

import PDocumentBuilder from "./documentBuilder/PDocumentBuilder.js";

const router = new VueRouter({
    routes: [
        {
            path: "/document",
            component: PDocumentBuilder
        },
        {
            path: "",
            redirect: "/document"
        }
    ]
});

export { router as default };
