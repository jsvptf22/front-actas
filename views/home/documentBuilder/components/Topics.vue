<template>
  <div>
    <div class="row">
      <div class="col-12">
        <div class="input-group mb-3">
          <input
            v-model="topic"
            type="text"
            class="form-control"
            placeholder="Nombre del tema"
            aria-describedby="button-addon2"
          />
          <div class="input-group-append">
            <button
              class="btn btn-secondary"
              type="button"
              id="button-addon2"
              v-on:click="save"
            >Guardar</button>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <ul v-if="topicList.length">
          <li
            v-for="topic of topicList"
            v-bind:key="topic.id"
            style="height:40px;list-style-type:none;"
          >
            {{topic.label}}
            <button
              v-on:click="remove(topic.id)"
              class="btn btn-sm btn-danger float-left mr-2"
            >X</button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
export default {
  name: "Topics",
  data: function() {
    return {
      topic: "",
      topicList: []
    };
  },
  methods: {
    save() {
      let item = {
        id: new Date().getTime() + "-" + this.topicList.length,
        label: this.topic
      };

      this.topic = "";
      this.topicList.push(item);
      this.$store.commit("refreshDocumentInformation", {
        topicList: this.topicList
      });
    },
    remove(topicId) {
      this.topicList = this.topicList.filter(t => t.id != topicId);
      let topicListDescription = this.documentInformation.topicListDescription.filter(
        i => i.topic != topicId
      );
      this.$store.commit("refreshDocumentInformation", {
        topicList: this.topicList,
        topicListDescription: topicListDescription
      });
    }
  },
  computed: mapState(["documentInformation"]),
  created() {
    this.$store.commit("refreshDocumentInformation", {
      modalTitle: "Creación de temas"
    });

    this.topicList = this.documentInformation.topicList;
  }
};
</script>