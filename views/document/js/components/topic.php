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
			<label for>Desarrollo</label>
			<textarea rows="10" class="form-control" v-model="description" v-bind:readonly="!value"></textarea>
		</div>
	</div>
</div>

<?= vue() ?>
<?= select2() ?>

<script>
	$(function() {
		var app = new Vue({
			el: '#topic_container',
			data: function() {
				return {
					value: null,
					options: [],
					description: "",
					topicListDescription: []
				};
			},
			watch: {
				value: function(val) {
					let item = this.topicListDescription.find(
						i => i.topic == val
					);
					this.description = item ? item.description : "";
				},
				description: function(val) {
					let index = this.topicListDescription.findIndex(i => i.topic == this.value);

					if (index == -1) {
						index = this.topicListDescription.length;
					}

					this.topicListDescription[index] = {
						topic: this.value,
						description: val
					};
				}
			},
			created() {
				this.options = top.window.actDocumentData.topicList.slice();
				this.topicListDescription = top.window.actDocumentData.topicListDescription.slice();
			}
		});

		$('#btn_success').on('click', function() {
			top.successModalEvent({
				topicListDescription: app._data.topicListDescription
			})
		});

		$('#topic_list').select2({
			data: convertOptions(),
		}).on('change', function(e) {
			app._data.value = $(this).val();
		})

		function convertOptions() {
			let data = [{
				'id': '',
				'text': 'Seleccione..'
			}];

			top.window.actDocumentData.topicList.forEach(t => {
				data.push({
					id: t.id,
					text: t.label
				});
			})

			return data;
		}
	});
</script>