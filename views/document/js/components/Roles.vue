<template>
  <div class="row pt-3">
    <div class="col-12">
      <table class="table table-bordered">
        <tr>
          <td class="text-center">Asistente</td>
          <td class="text-center">Rol</td>
        </tr>
        <tr>
          <td>
            <multiselect
              v-model="president"
              track-by="id"
              label="nombre_completo"
              placeholder="Selecciona un asistente"
              :options="options"
              :searchable="false"
              :allow-empty="false"
              @select="selectPresident"
            ></multiselect>
          </td>
          <td>Presidente</td>
        </tr>
        <tr>
          <td>
            <multiselect
              v-model="secretary"
              track-by="id"
              label="nombre_completo"
              placeholder="Selecciona un asistente"
              :options="options"
              :searchable="false"
              :allow-empty="false"
              @select="selectSecretary"
            ></multiselect>
          </td>
          <td>Secretario</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
import Multiselect from "vue-multiselect";

export default {
  name: "Roles",
  components: { Multiselect },
  data: function() {
    return {
      president: "",
      secretary: "",
      options: [],
      roles: {}
    };
  },
  methods: {
    selectPresident(selectedOption) {
      console.log(this.president);
      this.roles.president = selectedOption;
      this.$store.commit("refreshDocumentInformation", {
        roles: this.roles
      });
    },
    selectSecretary(selectedOption) {
      console.log(this.secretary);

      this.roles.secretary = selectedOption;
      this.$store.commit("refreshDocumentInformation", {
        roles: this.roles
      });
    }
  },
  watch: {
    roles: function(value) {
      if (value.secretary) {
        this.secretary = value.secretary;
      }

      if (value.president) {
        this.president = value.president;
      }
    }
  },
  computed: mapState(["documentInformation"]),
  created() {
    this.$store.commit("refreshDocumentInformation", {
      modalTitle: "Asignación de roles"
    });

    this.options = this.documentInformation.userList;
    this.roles = this.documentInformation.roles;
  }
};
</script>
<style src="../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>