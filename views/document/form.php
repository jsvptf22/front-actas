<?php
$max_salida = 10;
$rootPath = $ruta = '';

while ($max_salida > 0) {
  if (is_file($ruta . 'sw.js')) {
    $rootPath = $ruta;
    break;
  }

  $ruta .= '../';
  $max_salida--;
}

include_once $rootPath . 'assets/librerias.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <?= jquery() ?>
  <?= bootstrap() ?>
  <?= theme() ?>
  <?= icons() ?>
  <?= moment() ?>

  <link rel="stylesheet" href="<?= $rootPath ?>views/modules/actas/views/document/css/form.css">
</head>

<body>
  <div id="component_container">
    <div class="row">
      <div class="col-auto">
        <div class="row">
          <div class="col-12">
            <div class="btn-group-vertical mr-2" role="group" aria-label="First group">
              <button type="button" class="btn btn-secondary" v-on:click="showModal(1)">Validar asistentes</button>
              <button type="button" class="btn btn-secondary" v-on:click="showModal(2)">Crear asunto</button>
              <button type="button" class="btn btn-secondary" v-on:click="showModal(3)">Crear temas</button>
              <button type="button" class="btn btn-secondary" v-on:click="showModal(4)">Desarrollo de tema</button>
              <button type="button" class="btn btn-secondary" v-on:click="showModal(5)">Asignación de roles</button>
            </div>
          </div>
        </div>
        <div class="row pt-3">
          <div class="col-12">
            <div class="btn-group-vertical mr-2" role="group" aria-label="First group">
              <button class="btn btn-primary btn-block" v-on:click="sendDocument">Solicitar aprobación</button>
            </div>
          </div>
        </div>
      </div>
      <div class="col" id="template_parent">
        <div class="template p-5">
          <div class="row-fluid mb-5">
            <div class="col-12 text-center p-3">
              <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Aut maiores libero officiis, maxime ut esse necessitatibus distinctio ad dolorem reprehenderit obcaecati ratione eum commodi! Modi possimus consequuntur aliquid rerum beatae!</p>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <table class="table table-bordered">
                <tr>
                  <td>Acta N°</td>
                  <td>{{documentInformation.identificator}}</td>
                  <td>Tema / Asunto</td>
                  <td colspan="3">{{documentInformation.subject}}</td>
                </tr>
                <tr>
                  <td>Fecha</td>
                  <td>{{getInitialDate()}}</td>
                  <td>Hora Inicio</td>
                  <td>{{getInitialTime()}}</td>
                  <td>Hora Final</td>
                  <td>{{getFinaltime()}}</td>
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
                  <td class="text-center">Participantes</td>
                </tr>
                <tr>
                  <td>
                    Asistentes:
                    <span v-for="user of getAssistants()" v-bind:key="user.id">{{user.nombre_completo}},&nbsp;&nbsp;</span>
                  </td>
                </tr>
                <tr>
                  <td>
                    Invitados:
                    <span v-for="user of getInvited()" v-bind:key="user.id">{{user.nombre_completo}},&nbsp;&nbsp;</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <table class="table table-bordered">
                <tr>
                  <td class="text-center">Puntos a Tratar / Orden del día</td>
                </tr>
                <tr>
                  <td>
                    <ul>
                      <li v-for="topic of documentInformation.topicList" v-bind:key="topic.id">{{topic.label}}</li>
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
                  <td class="text-center">Puntos Tratados / Desarrollo</td>
                </tr>
                <tr>
                  <td>
                    <ul>
                      <li v-for="item of documentInformation.topicListDescription" v-bind:key="item.id">
                        <span>{{getTopicLabel(item.topic)}}</span>
                        <br />
                        <p>{{item.description}}</p>
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
                    <span v-if="documentInformation.roles.secretary">{{documentInformation.roles.secretary.nombre_completo}}</span>
                  </td>
                  <td class="firm_square">
                    Aprobado por:
                    <span v-if="documentInformation.roles.president">{{documentInformation.roles.president.nombre_completo}}</span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
          <div class="row-fluid mt-5">
            <div class="col-12 text-center p-3">Lorem ipsum dolor sit amet consectetur adipisicing elit. Deserunt eveniet excepturi accusantium corrupti quae, sapiente aperiam quam vitae error quibusdam atque, at, iusto perferendis quas debitis quia voluptas nisi suscipit?</div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <b-modal id="componentModal" @hide="saveData()" ref="modal" v-bind:title="documentInformation.modalTitle">
          <template slot="default">
            <router-view></router-view>
          </template>
          <template slot="modal-footer" slot-scope="{ ok }">
            <b-button size="sm" variant="primary" @click="ok()">Ver documento</b-button>
          </template>
        </b-modal>
      </div>
    </div>
  </div>
  <script src="<?= $rootPath ?>views/modules/actas/node_modules/vue/dist/vue.min.js"></script>
  <script src="<?= $rootPath ?>views/modules/actas/node_modules/vuex/dist/vuex.min.js"></script>
  <script type="module" src="<?= $rootPath ?>views/modules/actas/views/document/js/form.js"></script>
</body>

</html>