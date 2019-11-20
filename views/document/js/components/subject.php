<div class="row">
	<div class="col-12">
		<div class="form-group form-group-default">
			<label for="subject">Asunto</label>
			<input id="subject" type="text" class="form-control">
		</div>
	</div>
</div>
<script>
	$(function() {
		$('#btn_success').on('click', function() {
			top.successModalEvent({
				subject: $("#subject").val()
			})
		});
	})
</script>