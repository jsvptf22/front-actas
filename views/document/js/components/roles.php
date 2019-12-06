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
<div class="row pt-3" id="role_container">
	<div class="col-12">
		<div class="form-group form-group-default form-group-default-select2">
			<label for="">Presidente</label>
			<select class="full-width form-control" id="president_select">
				<option value="">Seleccione..</option>
			</select>
		</div>
		<div class="form-group form-group-default form-group-default-select2">
			<label for="">Secretario</label>
			<select class="full-width form-control" id="secretary_select">
				<option value="">Seleccione..</option>
			</select>
		</div>
	</div>
</div>

<?= vue() ?>
<?= select2() ?>

<script>
	$(function() {
		let documentInformation = top.window.actDocumentData;
		$('#btn_success').on('click', function() {
			top.successModalEvent({
				roles: {
					secretary: $('#secretary_select').select2('data')[0],
					president: $('#president_select').select2('data')[0]
				}
			})
		});

		$('#president_select,#secretary_select').select2({
			data: getInternalUsers(documentInformation.userList),
		});

		if (documentInformation.roles.president) {
			$('#president_select')
				.val(documentInformation.roles.president.id)
				.trigger('change');
		}

		if (documentInformation.roles.secretary) {
			$('#secretary_select')
				.val(documentInformation.roles.secretary.id)
				.trigger('change');
		}

		function getInternalUsers(users) {
			return users.filter(u => +u.external == 0);
		}
	});
</script>