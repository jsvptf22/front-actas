import store from "./store.js";

store.commit("refreshDocumentInformation", {
    subject: "123213123"
});
console.log(store, store.state.documentInformation);
