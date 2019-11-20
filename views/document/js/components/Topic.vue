<template>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label>Listado de temas</label>
        <multiselect
          v-model="value"
          track-by="id"
          label="label"
          placeholder="Selecciona un tema"
          :options="options"
          :searchable="false"
          :allow-empty="false"
          @select="changeTopic"
        ></multiselect>
      </div>
      <div class="form-gr">
        <label for>Desarrollo</label>
        <textarea rows="10" class="form-control" v-model="description" v-bind:readonly="!value"></textarea>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
import Multiselect from "vue-multiselect";

export default {
  name: "Topic",
  components: { Multiselect },
  data: function() {
    return {
      value: null,
      options: [],
      description: ""
    };
  },
  methods: {
    changeTopic(selectedOption) {
      let item = this.documentInformation.topicListDescription.find(
        i => i.topic == selectedOption.id
      );
      this.description = item ? item.description : "";
    }
  },
  watch: {
    description: function(val) {
      let descriptions = this.documentInformation.topicListDescription;
      let index = descriptions.findIndex(i => i.topic == this.value.id);

      if (index == -1) {
        index = descriptions.length;
      }

      descriptions[index] = {
        topic: this.value.id,
        description: val
      };

      this.$store.commit("refreshDocumentInformation", {
        topicListDescription: descriptions
      });
    }
  },
  computed: mapState(["documentInformation"]),
  created() {
    this.$store.commit("refreshDocumentInformation", {
      modalTitle: "Descripción del tema"
    });

    this.options = this.documentInformation.topicList;
  }
};
</script>
<style src="../../../../../node_modules/vue-multiselect/dist/vue-multiselect.min.css"></style>