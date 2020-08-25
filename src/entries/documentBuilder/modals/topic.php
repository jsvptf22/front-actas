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

include_once $rootPath . 'views/assets/librerias.php';

?>
<div class="row" id="topic_container">
  <div class="col-12">
    <div class="form-group form-group-default form-group-default-select2">
      <label>Listado de temas</label>
      <select class="form-control" id="topic_list"></select>
    </div>
    <div class="form-group form-group-default">
      <label for>DESARROLLO DEL TEMA</label>
      <textarea rows="3" class="form-control" id="description" v-model="description"></textarea>
    </div>
  </div>
</div>

<?= vue() ?>
<?= ckeditor() ?>
<?= select2() ?>

<script>
    $(function () {
        let editor = null;
        const app = new Vue({
            el: '#topic_container',
            data: function () {
                return {
                    topics: [],
                    value: null,
                    description: "",
                };
            },
            watch: {
                value: function (val) {
                    let topic = this.topics.find(
                        i => +i.id === +val
                    );

                    this.description = topic.description || "";
                    editor.setData(this.description);
                },
                description: function (val) {
                    let index = this.topics.findIndex(i => +i.id === +this.value);

                    if (index === -1) {
                        index = this.topics.length;
                    }

                    let topic = this.topics[index];
                    topic.description = val;
                    this.topics[index] = topic;
                }
            },
            created() {
                this.topics = top.window.actDocumentData.topics.slice();
            }
        });

        CKEDITOR.replace('description', {
            extraPlugins: 'contextmenu,justify',
        });
        editor = CKEDITOR.instances['description'];
        editor.on('change', () => {
            setTimeout(() => {
                app.description = editor.getData();
            }, 0);
        });

        $('#btn_success').on('click', function () {
            top.successModalEvent({
                topics: app._data.topics
            })
        });

        $('#topic_list').select2({
            data: convertOptions(),
        }).on('change', function (e) {
            app._data.value = $(this).val();
        });

        function convertOptions() {
            let data = [{
                'id': '',
                'text': 'Seleccione..'
            }];

            top.window.actDocumentData.topics.forEach(t => {
                data.push({
                    id: t.id,
                    text: t.label
                });
            });

            return data;
        }
    });
</script>