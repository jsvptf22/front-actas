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
<div id="topics_container">
	<div class="row">
		<div class="col-12">
			<div class="input-group mb-3">
				<input v-model="topic" type="text" class="form-control" placeholder="Nombre del tema" aria-describedby="button-addon2" />
				<div class="input-group-append">
					<button class="btn btn-secondary" type="button" v-on:click="save">Crear tema</button>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-12">
			<ul v-if="topicList.length">
				<li v-for="topic of topicList" v-bind:key="topic.id" style="height:40px;list-style-type:none;">
					{{topic.label}}
					<span v-on:click="remove(topic.id)" class="text-danger float-left mr-2 f-20 cursor" title="eliminar">
						<i class="fa fa-trash"></i>
					</span>
				</li>
			</ul>
		</div>
	</div>
</div>

<?= vue() ?>
<script>
	$(function() {
		var app = new Vue({
			el: '#topics_container',
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
				},
				remove(topicId) {
					this.topicList = this.topicList.filter(t => t.id != topicId);
				}
			},
			created() {
				this.topicList = top.window.actDocumentData.topicList.slice();
			}
		});

		$('#btn_success').on('click', function() {
			top.successModalEvent({
				topicList: app._data.topicList
			})
		});
	})
</script>