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

$Formato = Formato::findByAttributes(['nombre' => 'acta']);
$CamposFormato = CamposFormato::findByAttributes([
	'nombre' => 'asistentes_externos'
]);

?>
<div class="row" id="users_container">
	<div class="col-12">
		<div class='form-group form-group-default form-group-default-select2'>
			<label title='{$this->CamposFormato->ayuda}'>Asistentes</label>
			<select class="full-width" id='user_select' multiple="multiple"></select>
		</div>
	</div>
</div>

<?= select2() ?>

<script>
	$(function() {
		var realBaseUrl = Session.getBaseUrl();
		var selectedUsers = top.window.actDocumentData.userList;

		$('#btn_success').on('click', function() {
			top.successModalEvent({
				userList: $("#user_select").select2('data')
			})
		});

		$("#user_select").select2({
			minimumInputLength: 3,
			language: 'es',
			data: selectedUsers,
			ajax: {
				url: `${realBaseUrl}app/modules/actas/app/funcionario/asistentes.php`,
				dataType: 'json',
				data: function(params) {
					return {
						term: params.term,
						key: localStorage.getItem('key'),
						token: localStorage.getItem('token')
					};
				},
				processResults: function(response) {
					if (!response.data.length) {
						response.data = [{
							id: 9999,
							name: 'Crear tercero',
							showModal: true
						}]
					}

					return {
						results: response.data.map(u => {
							u.text = u.name;
							return u;
						})
					}
				}
			}
		}).on('select2:selecting', function(e) {
			let data = e.params.args.data;

			if (data.showModal) {
				e.preventDefault();

				openModal();
			}
		});

		(function displaySelected() {
			let idList = [];
			selectedUsers.forEach(u => {
				idList.push(u.id);
			});
			$("#user_select").val(idList).trigger('change');
		})();

		function openModal() {
			$("#user_select").select2('close');
			top.window.actDocumentData.userList = $("#user_select").select2('data');

			top.topModal({
				url: `views/tercero/formularioDinamico.php`,
				params: {
					fieldId: '<?= $CamposFormato->getPK() ?>',
				},
				title: 'Tercero',
				onSuccess: function(data) {
					top.window.actDocumentData.userList.push({
						id: data.id,
						text: data.text,
						name: data.text,
						external: "1"
					});
					top.closeTopModal();
				}
			});
		}
	})
</script>