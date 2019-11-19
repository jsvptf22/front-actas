<template>
  <div class="row">
    <div class="col-12">
      <div class="form-group">
        <label for="subject">Asunto</label>
        <input id="subject" type="text" class="form-control" v-model="subject" />
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "Subject",
  data: function() {
    return {
      subject: ""
    };
  },
  watch: {
    subject: function(value) {
      this.$store.commit("refreshDocumentInformation", {
        subject: value
      });
    }
  },
  computed: mapState(["documentInformation"]),
  created() {
    this.$store.commit("refreshDocumentInformation", {
      modalTitle: "Asignación de asunto"
    });

    this.subject = this.documentInformation.subject;
  }
};
</script>