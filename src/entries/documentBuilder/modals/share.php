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
<div class="row pt-3" id="role_container">
  <div class="col-12">
    <div class="form-group form-group-default form-group-default-select2">
      <label for="">Destinos</label>
      <select class="full-width form-control" id="destinations" multiple="multiple">
        <option value="">Seleccione..</option>
      </select>
    </div>
  </div>
</div>

<?= select2() ?>

<script>
    $(function () {
        let documentInformation = top.window.actDocumentData;
        $('#destinations').select2({
            data: documentInformation.userList,
        });

        $('#btn_success').on('click', function () {
            let data = $('#destinations').select2('data').map(i => {
                return {
                    id: i.id,
                    external: i.external,
                    name: i.name
                };
            });
            top.successModalEvent(data);
        });
    });
</script>