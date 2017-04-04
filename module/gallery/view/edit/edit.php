<form method="post">
	<div class="row">
		<div class="col12">
			<div class="block">
				<h4><?php echo helper::translate('Informations générales'); ?></h4>
				<div class="row">
					<div class="col6">
						<?php echo template::text('galleryEditName', [
							'label' => 'Nom',
							'required' => true,
							'value' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'config', 'name'])
						]); ?>
					</div>
					<div class="col6">
						<?php echo template::hidden('galleryEditDirectoryOld', [
							'value' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'config', 'directory'])
						]); ?>
						<?php echo template::select('galleryEditDirectory', [], [
							'label' => 'Dossier cible',
							'required' => true
						]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if($module::$pictures): ?>
		<?php echo template::table([4, 8], $module::$pictures, ['Image', 'Légende']); ?>
	<?php endif; ?>
	<div class="row">
		<div class="col2 offset8">
			<?php echo template::button('galleryEditBack', [
				'class' => 'grey',
				'href' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'value' => 'Retour'
			]); ?>
		</div>
		<div class="col2">
			<?php echo template::submit('galleryEditSubmit'); ?>
		</div>
	</div>
</form>