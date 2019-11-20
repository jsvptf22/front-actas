<template>
  <div class="row">
    <div class="col-12">
      <multiselect
        v-model="usersSelected"
        placeholder="Busca un asistente"
        label="nombre_completo"
        track-by="id"
        tagPlaceholder="Click para crear funcionario"
        selectLabel=" "
        :loading="loading"
        :options="userList"
        :multiple="true"
        :taggable="true"
        :value="Array"
        :hideSelected="true"
        @tag="lunchModal"
        @search-change="findUsers"
      ></multiselect>
      <div v-if="modalShow">
        <external-user
          v-bind:showModal="modalShow"
          v-bind:newUserName="newUserName"
          v-on:toggleModal="toggleModal"
          v-on:createUser="createUser"
        />
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

import Multiselect from "vue-multiselect";
import ExternalUser from "./ExternalUser.vue";

const axios = require("axios");

export default {
  name: "UserAutocomplete",
  components: { Multiselect, ExternalUser },
  data: function() {
    return {
      userList: [],
      usersSelected: [],
      loading: false,
      modalShow: false,
      newUserName: ""
    };
  },
  methods: {
    lunchModal(newTag) {
      this.newUserName = newTag;
    },
    createUser(user) {
      this.usersSelected.push(user);
    },
    toggleModal(value) {
      this.modalShow = value;
    },
    findUsers(query) {
      this.loading = true;

      const ENVDATA = process.env;

      axios
        .request({
          url: `${ENVDATA.VUE_APP_MODULE_API_ROUTE}user/autocomplete`,
          method: "get",
          responseType: "json",
          params: {
            query
          },
          headers: {
            Authorization: this.$session.get("apiToken")
          }
        })
        .then(response => {
          this.userList = response.data ? response.data : [];
          this.loading = false;
        })
        .catch(response => {
          alert(response.message);
          this.loading = false;
        });
    }
  },
  watch: {
    newUserName: function() {
      this.modalShow = true;
    },
    usersSelected: function(value) {
      this.$store.commit("refreshDocumentInformation", {
        userList: value
      });
    }
  },
  computed: mapState(["documentInformation"]),
  created() {
    this.$store.commit("refreshDocumentInformation", {
      modalTitle: "Validación de asistencia"
    });

    this.usersSelected = this.documentInformation.userList;
  }
};
</script>
<style src="../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>

