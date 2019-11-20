/**
 * pendiente agregar 
 * 
 * <style>
        .template {
        border: 1px solid #cacaca;
        margin-bottom: 8px;
        box-shadow: 2px 2px 8px #c6c6c6;
        }

        #template_parent {
        height: 100vh;
        overflow-y: auto;
        }

        .firm_square {
        height: 150px;
        }
      </style> 
 */
export default {
    name: "BaseComponent",
    template: `<div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div id="component_container">
          <div class="row">
            <div class="col-auto">
              <div class="row">
                <div class="col-12">
                  <div
                    class="btn-group-vertical mr-2"
                    role="group"
                    aria-label="First group"
                  >
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="showModal(1)"
                    >
                      Validar asistentes
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="showModal(2)"
                    >
                      Crear asunto
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="showModal(3)"
                    >
                      Crear temas
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="showModal(4)"
                    >
                      Desarrollo de tema
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="showModal(5)"
                    >
                      Asignación de roles
                    </button>
                  </div>
                </div>
              </div>
              <div class="row pt-3">
                <div class="col-12">
                  <div
                    class="btn-group-vertical mr-2"
                    role="group"
                    aria-label="First group"
                  >
                    <button
                      class="btn btn-primary btn-block"
                      v-on:click="sendDocument"
                    >
                      Solicitar aprobación
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col" id="template_parent">
              <div class="template p-5">
                <div class="row-fluid mb-5">
                  <div class="col-12 text-center p-3">
                    <p>
                      Lorem ipsum dolor sit amet consectetur adipisicing elit. Aut
                      maiores libero officiis, maxime ut esse necessitatibus
                      distinctio ad dolorem reprehenderit obcaecati ratione eum
                      commodi! Modi possimus consequuntur aliquid rerum beatae!
                    </p>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <table class="table table-bordered">
                      <tr>
                        <td>Acta N°</td>
                        <td>
                          {{ documentInformation.identificator }}
                        </td>
                        <td>Tema / Asunto</td>
                        <td colspan="3">
                          {{ documentInformation.subject }}
                        </td>
                      </tr>
                      <tr>
                        <td>Fecha</td>
                        <td>{{ getInitialDate() }}</td>
                        <td>Hora Inicio</td>
                        <td>{{ getInitialTime() }}</td>
                        <td>Hora Final</td>
                        <td>{{ getFinaltime() }}</td>
                      </tr>
                      <tr>
                        <td>Lugar</td>
                        <td colspan="5"></td>
                      </tr>
                    </table>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <table class="table table-bordered">
                      <tr>
                        <td class="text-center">
                          Participantes
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Asistentes:
                          <span
                            v-for="user of getAssistants()"
                            v-bind:key="user.id"
                            >{{ user.nombre_completo }},&nbsp;&nbsp;</span
                          >
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Invitados:
                          <span v-for="user of getInvited()" v-bind:key="user.id"
                            >{{ user.nombre_completo }},&nbsp;&nbsp;</span
                          >
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <table class="table table-bordered">
                      <tr>
                        <td class="text-center">
                          Puntos a Tratar / Orden del día
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <ul>
                            <li
                              v-for="topic of documentInformation.topicList"
                              v-bind:key="topic.id"
                            >
                              {{ topic.label }}
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
                        <td class="text-center">
                          Puntos Tratados / Desarrollo
                        </td>
                      </tr>
                      <tr>
                        <td>
                          <ul>
                            <li
                              v-for="item of documentInformation.topicListDescription"
                              v-bind:key="item.id"
                            >
                              <span>{{ getTopicLabel(item.topic) }}</span>
                              <br />
                              <p>
                                {{ item.description }}
                              </p>
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
                        <td class="firm_square">
                          Revisado por:
                          <span v-if="documentInformation.roles.secretary">{{
                            documentInformation.roles.secretary.nombre_completo
                          }}</span>
                        </td>
                        <td class="firm_square">
                          Aprobado por:
                          <span v-if="documentInformation.roles.president">{{
                            documentInformation.roles.president.nombre_completo
                          }}</span>
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
                <div class="row-fluid mt-5">
                  <div class="col-12 text-center p-3">
                    Lorem ipsum dolor sit amet consectetur adipisicing elit.
                    Deserunt eveniet excepturi accusantium corrupti quae, sapiente
                    aperiam quam vitae error quibusdam atque, at, iusto
                    perferendis quas debitis quia voluptas nisi suscipit?
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  `,
    methods: {
        showModal(type) {
            switch (type) {
                case 1:
                    var file = "users.php";
                    var title = "";
                    break;
                case 2:
                    var file = "subject.php";
                    var title = "Asignación de asunto";
                    break;
                case 3:
                    var file = "topics.php";
                    var title = "";
                    break;
                case 4:
                    var file = "topic.php";
                    var title = "";
                    break;
                case 5:
                    var file = "roles.php";
                    var title = "";
                    break;
            }
            let _this = this;
            top.topModal({
                url: `views/modules/actas/views/document/js/components/${file}`,
                title,
                onSuccess: function(data) {
                    _this.$store.dispatch("refreshDocumentInformation", data);
                    top.closeTopModal();
                }
            });
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
                    url: `${this.params.baseUrl}app/modules/actas/document/sendDocument`,
                    method: "post",
                    responseType: "json",
                    data: {
                        documentId: this.documentInformation.documentId,
                        route: process.env.VUE_APP_PAGE_APPROVE_ROUTE
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
            return this.documentInformation.userList.filter(
                u => u.externo == 0
            );
        },
        getInvited() {
            return this.documentInformation.userList.filter(
                u => u.externo == 1
            );
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
    computed: Vuex.mapState(["documentInformation"])
};
