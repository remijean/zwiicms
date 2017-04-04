<form method="post">
	<?php echo template::text('userForgotId', [
		'label' => 'Identifiant',
		'required' => true
	]); ?>
	<div class="row">
		<div class="col3 offset6">
			<?php echo template::button('userForgotBack', [
				'class' => 'grey',
				'href' => helper::baseUrl() . 'user/login/' . $this->getUrl(2),
				'value' => 'Retour'

			]); ?>
		</div>
		<div class="col3">
			<?php echo template::submit('userForgotSubmit', [
				'value' => 'Valider'
			]); ?>
		</div>
	</div>
</form>