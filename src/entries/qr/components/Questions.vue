<template>
  <div class="container" id="app">
    <div class="row">
      <div class="col-12 table-responsive px-0">
        <table class="table table-bordered my-0">
          <tbody>
          <tr>
            <th class="text-center bold">Pregunta</th>
            <th class="text-center bold">Opciones</th>
          </tr>
          <tr v-for="question in questions" :key="question.idact_question">
            <td>{{ question.label }}</td>
            <td class="text-center">
              <select
                  :readonly="!canVote(question.id)"
                  class="select2 full-width"
                  :data-question="question.id">
                <option value=""
                >
                  Seleccione..
                </option>
                <option
                    v-for="option in question.options"
                    v-bind:value="option.id"
                >
                  {{ option.label }}
                </option>
              </select>
            </td>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
<script>
import io from "socket.io-client";
import 'select2';
import 'select2/dist/css/select2.css';

export default {
  name: "Questions",
  props: ["documentId"],
  data: function () {
    return {
      socket: null,
      questions: []
    };
  },
  methods: {
    canVote(question) {
      let actions = localStorage.getItem("actions") || "{}";
      actions = JSON.parse(actions);

      let keys = actions[this.documentId]
          ? Object.keys(actions[this.documentId])
          : [];

      return !keys.map(Number).includes(question);
    },
    vote(question, option) {
      this.socket.emit("vote", {
        room: this.documentId + "-Manager",
        question: question,
        action: option
      });

      this.storeLocalAction(this.documentId, question, option);
    },
    storeLocalAction(room, question, action) {
      let actions = localStorage.getItem("actions") || "{}";
      actions = JSON.parse(actions);

      if (!actions[room]) {
        actions[room] = {};
      }

      actions[room][question] = action;
      actions = JSON.stringify(actions);
      localStorage.setItem("actions", actions);
    },
    setSelectsValue() {
      let actions = localStorage.getItem("actions") || "{}";
      actions = JSON.parse(actions);
      let executions = actions[this.documentId]

      for (let question in executions) {
        $(`[data-question='${question}']`).val(executions[question]).trigger('change');
      }
    },
    createSocket() {
      this.socket = io(process.env.ACTAS_NODE_SERVER + "meeting");

      this.socket.on("refreshClient", data => {
        this.questions = data;
      });
    },
    syncData(documentId) {
      this.socket.emit("defineRoom", documentId + "-QuestionViewer");
      this.socket.emit("getData", documentId + "-Manager");
    }
  },
  mounted: function () {
    this.createSocket();
    this.syncData(this.documentId);
  },
  updated: function () {
    let component = this;
    $(".select2").select2();

    $(document)
        .off('change', '.select2')
        .on('change', '.select2', function () {
          let question = +$(this).data('question');

          if (component.canVote(question)) {
            component.vote(question, +$(this).val());
          }
          $(this).select2({disabled: true});
        });

    component.setSelectsValue();
  }
};
</script>
