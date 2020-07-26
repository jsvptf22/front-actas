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
		<div class="col-12" v-if="topicList.length">
			<div v-for="topic of topicList" v-bind:key="topic.id" class="card mb-1">
				<div class="card-body">
					<div class="row">
						<div class="col-auto f-20 d-flex align-items-center pr-1 mr-1">
							<i v-on:click="remove(topic.id)" class="text-danger cursor fa fa-times" title="eliminar"></i>
						</div>
						<div class="col">
							<span>
								{{topic.label}}
							</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?= vue() ?>
<script>
	$(function() {
		let app = new Vue({
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
					this.topicList = this.topicList.filter(t => +t.id !== +topicId);
				}
			},
			created() {
				this.topicList = top.window.actDocumentData.topics.slice();
			}
		});

		$('#btn_success').on('click', function() {
			top.successModalEvent({
				topics: app._data.topicList
			})
		});
	})
</script>