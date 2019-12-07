export default {
    name: "BaseComponent",
    methods: {
        openUserModal() {
            this.showModal({
                url: `views/modules/actas/views/document/js/components/users.php`,
                title: "Validación de asistencia"
            });
        },
        openSubjectModal() {
            this.showModal({
                url: `views/modules/actas/views/document/js/components/subject.php`,
                title: "Asignación de asunto"
            });
        },
        openTopicsModal() {
            this.showModal({
                url: `views/modules/actas/views/document/js/components/topics.php`,
                title: "Creación de temas"
            });
        },
        openTopicDescriptionModal() {
            this.showModal({
                url: `views/modules/actas/views/document/js/components/topic.php`,
                title: "Descripción del tema"
            });
        },
        openRoleModal() {
            this.showModal({
                url: `views/modules/actas/views/document/js/components/roles.php`,
                title: "Asignación de roles"
            });
        },
        showModal(options) {
            top.window.actDocumentData = {
                ...this.$store.state.documentInformation
            };
            top.topModal({
                ...options,
                onSuccess: data => {
                    this.$store.dispatch("refreshDocumentInformation", data);
                    top.closeTopModal();
                }
            });
        },
        openTaskModal(taskId = null) {
            top.topModal({
                url: `views/tareas/crear.php`,
                params: {
                    id: taskId
                },
                centerAlign: false,
                size: "modal-lg",
                title: "Tarea o Recordatorio",
                buttons: {},
                onSuccess: data => {
                    data = {
                        id: data.id,
                        name: data.name,
                        managers: data.managers
                    };
                    let tasks = this.documentInformation.tasks;

                    if (taskId) {
                        let index = tasks.findIndex(t => t.id == data.id);
                        tasks[index] = data;
                    } else {
                        tasks.push(data);
                    }

                    this.$store.dispatch("refreshDocumentInformation", {
                        tasks: tasks
                    });
                }
            });
        },
        saveDocument() {
            let _this = this;
            this.$store
                .dispatch("checkRequiredData")
                .then(() => {
                    let baseUrl = this.params.baseUrl;

                    $.post(
                        `${this.apiRoute}documento/guardar.php`,
                        {
                            key: localStorage.getItem("key"),
                            token: localStorage.getItem("token"),
                            documentInformation: JSON.stringify(
                                this.documentInformation
                            )
                        },
                        function(response) {
                            if (response.success) {
                                _this.$store.dispatch(
                                    "updateDocumentInformation",
                                    response.data
                                );

                                let route = `${baseUrl}views/documento/index_acordeon.php?`;
                                route += $.param({
                                    documentId:
                                        _this.documentInformation.documentId
                                });
                                window.location.href = route;
                            } else {
                                top.notification({
                                    type: "error",
                                    message: response.message
                                });
                            }
                        },
                        "json"
                    );
                })
                .catch(message => {
                    console.log("error");

                    top.notification({
                        type: "error",
                        message: message
                    });
                });
        },
        getTopicLabel(topicId) {
            return this.documentInformation.topicList.find(i => i.id == topicId)
                .label;
        },
        getAssistants() {
            return this.documentInformation.userList.filter(
                u => +u.external == 0
            );
        },
        getInvited() {
            return this.documentInformation.userList.filter(
                u => +u.external == 1
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
        },
        getUserName(userId) {
            var index = this.userNames.findIndex(u => u.iduser == userId);

            if (index == -1) {
                let baseUrl = this.params.baseUrl;
                $.ajax({
                    url: `${baseUrl}app/funcionario/consulta_funcionario.php`,
                    type: "POST",
                    dataType: "json",
                    data: {
                        key: localStorage.getItem("key"),
                        token: localStorage.getItem("token"),
                        type: "userInformation",
                        userId: userId
                    },
                    async: false,
                    success: response => {
                        if (response.success) {
                            this.userNames.push(response.data);
                        } else {
                            top.notification({
                                type: "error",
                                message: response.message
                            });
                        }
                    }
                });
            }

            index = this.userNames.findIndex(u => u.iduser == userId);
            return this.userNames[index].name;
        },
        getTasksUsers(users) {
            let names = [];

            users.forEach(userId => {
                names.push(this.getUserName(userId));
            });

            return names.join(", ", name);
        }
    },
    computed: Vuex.mapState([
        "documentInformation",
        "userNames",
        "params",
        "apiRoute"
    ]),
    template: `<div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div id="component_container">
          <div class="row">
            <div class="col-12 col-md-auto">
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
                      v-on:click="openUserModal"
                    >
                      Validar asistentes
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="openSubjectModal"
                    >
                      Crear asunto
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="openTopicsModal"
                    >
                      Crear temas
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="openTopicDescriptionModal"
                    >
                      Desarrollo de tema
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="openRoleModal"
                    >
                      Asignación de roles
                    </button>
                    <button
                      type="button"
                      class="btn btn-secondary"
                      v-on:click="openTaskModal()"
                    >
                      Responsabilidades
                    </button>
                    <button
                      class="btn btn-primary btn-block"
                      v-on:click="saveDocument"
                    >
                      Guardar
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md" id="template_parent">
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
                            >{{ user.name }},&nbsp;&nbsp;</span
                          >
                        </td>
                      </tr>
                      <tr>
                        <td>
                          Invitados:
                          <span v-for="user of getInvited()" v-bind:key="user.id"
                            >{{ user.name }},&nbsp;&nbsp;</span
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
                        <td class="text-center">
                          Responsabilidades
                        </td>
                      </tr>
                      <tr>
                        <td>
						<table v-if="documentInformation.tasks.length" class="table">	
							<tr>
								<td>Tarea</td>
								<td>Responsable</td>
								<td>Ver</td>
							</tr>
							<tr v-for="task of documentInformation.tasks">
								<td>{{task.name}}</td>
								<td>{{getTasksUsers(task.managers)}}</td>
								<td>
									<button class="btn" v-on:click="openTaskModal(task.id)">
										<span class="fa fa-eye"></span>
									</button>
								</td>
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
                        <td class="firm_square">
                          Revisado por:
                          <span v-if="documentInformation.roles.secretary">{{
                            documentInformation.roles.secretary.name
                          }}</span>
                        </td>
                        <td class="firm_square">
                          Aprobado por:
                          <span v-if="documentInformation.roles.president">{{
                            documentInformation.roles.president.name
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
  </div>`
};
