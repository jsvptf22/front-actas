<template>
  <div class="template p-4 p-md-5 bg-white">
    <div class="row-fluid mb-3" v-if="documentInformation.headers">
      <div class="col-12 text-center py-3 px-0" v-html="documentInformation.headers.header"></div>
    </div>
    <div class="row">
      <div class="col-12">
        <table class="table table-bordered">
          <tr>
            <td class="bold">Acta N°</td>
            <td>{{ documentInformation.identificator }}</td>
            <td
                v-if="this.documentInformation.qrUrl"
                rowspan="4"
                id="qr"
                class="text-center align-middle"
            >
              <img :src="absoluteQrRoute" width="90"/>
            </td>
          </tr>
          <tr>
            <td class="bold">Tema / Asunto</td>
            <td>{{ documentInformation.subject }}</td>
          </tr>
          <tr>
            <td class="bold">Inicio</td>
            <td>
              {{ documentInformation.initialDate }}
            </td>
          </tr>
          <tr>
            <td class="bold">Fin</td>
            <td>{{ documentInformation.finalDate }}</td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <table class="table table-bordered">
          <tr>
            <td class="text-center bold">Participantes</td>
          </tr>
          <tr>
            <td>
              <span class="bold">Asistentes:</span>
              <div v-for="user of getAssistants()" v-bind:key="user.id">
                                <span class="ml-5">
                                    {{
                                    user.name
                                  }}
                                </span>
                <br/>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <span class="bold">Invitados:</span>
              <div v-for="user of getInvited()" v-bind:key="user.id">
                                <span class="ml-5">
                                    {{
                                    user.name
                                  }}
                                </span>
                <br/>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <table class="table table-bordered">
          <tr>
            <td class="text-center bold">Puntos a Tratar / Orden del día</td>
          </tr>
          <tr>
            <td>
              <ul>
                <li
                    v-for="topic of documentInformation.topics"
                    v-bind:key="topic.id"
                >{{ topic.label }}
                </li>
              </ul>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <table class="table table-bordered">
          <tr>
            <td class="text-center bold">Puntos Tratados / Desarrollo</td>
          </tr>
          <tr>
            <td>
              <ul>
                <li v-for="item of documentInformation.topics" v-bind:key="item.id">
                  <span>{{ item.label }}</span>
                  <br/>
                  <p v-html="item.description"></p>
                </li>
              </ul>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <table class="table table-bordered">
          <thead>
          <tr>
            <td class="text-center bold" colspan="3">Decisiones</td>
          </tr>
          </thead>
          <tbody v-if="documentInformation.questions">
          <tr>
            <th class="text-center bold">Pregunta</th>
            <th class="text-center bold">Resultados</th>
            <th class="text-center bold">#votantes</th>
          </tr>
          <tr
              v-for="(question,index) of documentInformation.questions"
              v-bind:key="index"
          >
            <td>{{ question.label }}</td>
            <td class="text-center">
              <table class="table">
                <thead>
                <tr>
                  <td>Opción</td>
                  <td># votos</td>
                  <td>% votación</td>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(option,index) of getQuestionResumen(question)" v-bind:key="index">
                  <td>{{ option.label }}</td>
                  <td>{{ option.votes }}</td>
                  <td>{{ option.percent }}</td>
                </tr>
                </tbody>
              </table>
            </td>
            <th class="text-center">{{ getTotalQuestionVotes(question) }}</th>
          </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <table class="table table-bordered">
          <tr>
            <td class="text-center bold">Compromisos</td>
          </tr>
          <tr>
            <td>
              <table
                  v-if="
                                    documentInformation.tasks &&
                                    documentInformation.tasks.length
                                "
                  class="table"
              >
                <tr>
                  <th class="text-center bold">Tarea</th>
                  <th class="text-center bold">Responsable</th>
                  <th class="text-center bold">Fecha límite</th>
                </tr>
                <tr
                    v-for="(task,index) of documentInformation.tasks"
                    v-bind:key="index"
                >
                  <td>{{ task.name }}</td>
                  <td>{{ task.managers }}</td>
                  <td>{{ task.limitDate }}</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <table class="table table-bordered">
          <tr>
            <td>
              <span class="bold">SECRETARIO:</span>
              <span
                  v-if="
                                    documentInformation.roles && 
                                    documentInformation.roles.secretary
                                "
              >{{ documentInformation.roles.secretary.name }}</span>
            </td>
            <td>
              <span class="bold">PRESIDENTE:</span>
              <span
                  v-if="
                                    documentInformation.roles &&
                                    documentInformation.roles.president
                                "
              >{{ documentInformation.roles.president.name }}</span>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row-fluid mt-3" v-if="documentInformation.headers">
      <div class="col-12 text-center py-3 px-0" v-html="documentInformation.headers.footer"></div>
    </div>
  </div>
</template>
<script>
import io from "socket.io-client";

export default {
  name: "Viewer",
  data: function () {
    return {
      documentInformation: {},
      socket: null,
      userNames: []
    };
  },
  props: ["documentId"],
  methods: {
    getAssistants() {
      if (this.documentInformation.userList) {
        var response = this.documentInformation.userList.filter(
            u => +u.external === 0
        );
      } else {
        var response = {};
      }

      return response;
    },
    getInvited() {
      if (this.documentInformation.userList) {
        var response = this.documentInformation.userList.filter(
            u => +u.external === 1
        );
      } else {
        var response = {};
      }

      return response;
    },
    getTotalQuestionVotes(question) {
      let total = 0;
      question.options.forEach(o => total += +o.votes);

      return total;
    },
    getQuestionResumen(question) {
      let total = this.getTotalQuestionVotes(question);
      let resumen = [];

      question.options.forEach(o => {
        let percent = (o.votes * 100) / total;

        if (isNaN(percent)) {
          percent = 0;
        }

        o.percent = percent;
        resumen.push(o);
      });

      return resumen;
    },
    createSocket() {
      this.socket = io(process.env.ACTAS_NODE_SERVER + "meeting");

      this.socket.on("refreshClient", data => {
        this.documentInformation = data;
      });
    },
    syncData(documentId) {
      this.socket.emit("defineRoom", documentId + "-DocumentViewer");
      this.socket.emit("getData", documentId + "-Manager");
    }
  },
  mounted: function () {
    this.createSocket();
    this.syncData(this.documentId);
  },
  watch: {
    documentId: function (value) {
      this.syncData(value);
    }
  },
  computed: {
    absoluteQrRoute() {
      return (
          process.env.ABSOLUTE_SAIA_ROUTE + this.documentInformation.qrUrl
      );
    }
  }
};
</script>
<style>
.template {
  border: 1px solid #cacaca;
  margin-bottom: 8px;
  box-shadow: 2px 2px 8px #c6c6c6;
}

.template_parent {
  height: 95vh;
  overflow-y: auto;
}
</style>
