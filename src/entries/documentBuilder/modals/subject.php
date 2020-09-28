<?php
$max_salida = 10;
$rootPath = $ruta = '';

while ($max_salida > 0) {
    if (is_file($ruta . 'index.php')) {
        $rootPath = $ruta;
        break;
    }

    $ruta .= '../';
    $max_salida--;
}

include_once $rootPath . 'views/assets/librerias.php';

?>
<div class="row" id="subject_container">
  <div class="col-12">
    <div class="form-group form-group-default">
      <label for="subject">Asunto</label>
      <input id="subject" type="text" class="form-control" v-model="subject">
    </div>
  </div>
</div>

<?= vue() ?>
<script>
    $(function () {
        var app = new Vue({
            el: '#subject_container',
            data: function () {
                return {
                    subject: "",
                };
            },
            created() {
                this.subject = top.window.actDocumentData.subject;
            }
        });

        $('#btn_success').on('click', function () {
            top.successModalEvent({
                subject: app._data.subject
            })
        });
    });
</script>