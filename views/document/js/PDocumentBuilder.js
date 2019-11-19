import { mapState } from "vuex";

export default {
  name: "PDocumentBuilder",
  methods: {
    showModal(type) {
      switch (type) {
        case 1:
          this.$router.push("/users");
          break;
        case 2:
          this.$router.push("/subject");
          break;
        case 3:
          this.$router.push("/topics");
          break;
        case 4:
          this.$router.push("/topic");
          break;
        case 5:
          this.$router.push("/roles");
          break;
      }
      this.$bvModal.show("componentModal");
    },
    saveData() {
      this.$store.dispatch(
        "refreshDocumentInformation",
        this.documentInformation
      );
    },
    sendDocument() {
      this.$http
        .request({
          url: `${process.env.VUE_APP_MODULE_API_ROUTE}document/sendDocument`,
          method: "post",
          responseType: "json",
          data: {
            documentId: this.documentInformation.documentId,
            route: process.env.VUE_APP_PAGE_APPROVE_ROUTE
          },
          headers: {
            Authorization: this.$session.get("apiToken")
          }
        })
        .then(response => {
          if (response.data.success) {
            alert("documento enviado para aprobación");
          } else {
            alert("Error al guardar");
          }
        })
        .catch(response => {
          alert(response.message);
        });
    },
    getTopicLabel(topicId) {
      return this.documentInformation.topicList.find(i => i.id == topicId)
        .label;
    },
    getAssistants() {
      return this.documentInformation.userList.filter(u => u.externo == 0);
    },
    getInvited() {
      return this.documentInformation.userList.filter(u => u.externo == 1);
    },
    getInitialDate() {
      if (!this.documentInformation.initialDate) {
        return "";
      }

      let m = this.moment(
        this.documentInformation.initialDate,
        "YYYY-MM-DD HH:mm:ss"
      );
      return m.format("YYYY-MM-DD");
    },
    getInitialTime() {
      if (!this.documentInformation.initialDate) {
        return "";
      }

      let m = this.moment(
        this.documentInformation.initialDate,
        "YYYY-MM-DD HH:mm:ss"
      );
      return m.format("HH:mm:ss");
    },
    getFinaltime() {
      if (!this.documentInformation.finalDate) {
        return "";
      }

      let m = this.moment(
        this.documentInformation.finalDate,
        "YYYY-MM-DD HH:mm:ss"
      );
      return m.format("HH:mm:ss");
    }
  },
  computed: mapState(["documentInformation"])
};
