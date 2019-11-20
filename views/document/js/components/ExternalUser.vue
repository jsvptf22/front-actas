<template>
  <div>
    <b-modal
      v-model="lunchModal"
      ref="modal"
      title="Crear usuario"
      @show="resetModal"
      @hidden="hideModal"
      @ok="handleOk"
    >
      <form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group label="Identificación" label-for="identificationInput">
          <b-form-input id="identificationInput" v-model="identification"></b-form-input>
        </b-form-group>
        <b-form-group label="Nombre" label-for="nameInput">
          <b-form-input id="nameInput" v-model="username"></b-form-input>
        </b-form-group>
        <b-form-group label="Correo electrónico" label-for="emailInput">
          <b-form-input id="emailInput" v-model="email"></b-form-input>
        </b-form-group>
      </form>
    </b-modal>
  </div>
</template>

<script>
const axios = require("axios");

export default {
  name: "ExternalUser",
  props: ["newUserName", "showModal"],
  data() {
    return {
      lunchModal: this.showModal,
      username: this.newUserName,
      email: "",
      identification: ""
    };
  },
  methods: {
    hideModal() {
      this.lunchModal = false;
      this.resetModal();
      this.$emit("toggleModal", this.lunchModal);
    },
    resetModal() {
      this.name = "";
      this.email = "";
    },
    handleOk(bvModalEvt) {
      bvModalEvt.preventDefault();
      const ENVDATA = process.env;

      axios
        .request({
          url: `${ENVDATA.VUE_APP_MODULE_API_ROUTE}user/createExternal`,
          method: "post",
          responseType: "json",
          data: {
            email: this.email,
            username: this.username,
            identification: this.identification,
            key: this.$session.get("key"),
            externalToken: this.$session.get("externalToken")
          },
          headers: {
            Authorization: this.$session.get("apiToken")
          }
        })
        .then(response => {
          if (response.data.success) {
            this.$emit("createUser", {
              id: response.data.data.userId,
              nombre_completo: response.data.data.name,
              externo: 1
            });

            this.$nextTick(() => {
              this.$refs.modal.hide();
            });
          }
        })
        .catch(response => {
          alert(response.message);
        });
    }
  }
};
</script>
