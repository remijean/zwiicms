<form id="blogEditForm" method="post">
	<div class="row">
		<div class="col12">
			<div class="block">
				<h4><?php echo helper::translate('Informations générales'); ?></h4>
				<div class="row">
					<div class="col6">
						<?php echo template::text('blogEditTitle', [
							'label' => 'Titre',
							'required' => true,
							'value' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'title'])
						]); ?>
					</div>
					<div class="col6">
						<?php echo template::file('blogEditPicture', [
							'help' => helper::translate('Taille optimale de l\'image de couverture :') . ' ' . ((int) substr($this->getData(['theme', 'site', 'width']), 0, -2) - (20 * 2)) . ' x 350 pixels.',
							'label' => 'Image de couverture',
							'lang' => $this->getData(['config', 'language']),
							'type' => 1,
							'required' => true,
							'value' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'picture'])
						]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo template::textarea('blogEditContent', [
		'class' => 'editor',
		'value' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'content'])
	]); ?>
	<div class="row">
		<div class="col6">
			<div class="block">
				<h4><?php echo helper::translate('Options de publication'); ?></h4>
				<?php echo template::select('blogEditUserId', $module::$users, [
					'label' => 'Auteur',
					'required' => true,
					'selected' => $this->getUser('id')
				]); ?>
				<?php echo template::date('blogEditPublishedOn', [
					'help' => 'L\'article n\'est pas visible lorsque la date de publication est dans le futur.',
					'label' => 'Date de publication',
					'required' => true,
					'value' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'publishedOn'])
				]); ?>
			</div>
		</div>
		<div class="col6">
			<div class="block">
				<h4><?php echo helper::translate('Options avancés'); ?></h4>
				<?php echo template::checkbox('blogEditCloseComment', true, 'Fermer les commentaires', [
					'checked' => $this->getData(['module', $this->getUrl(0), $this->getUrl(2), 'closeComment'])
				]); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col2">
			<?php echo template::button('blogEditBack', [
				'class' => 'grey',
				'href' => helper::baseUrl() . $this->getUrl(0) . '/config',
				'value' => 'Retour'
			]); ?>
		</div>
		<div class="col3 offset5">
			<?php echo template::button('blogEditDraft', [
				'uniqueSubmission' => true,
				'value' => 'Enregistrer en brouillon'
			]); ?>
			<?php echo template::hidden('blogEditStatus', [
				'uniqueSubmission' => true,
				'value' => true
			]); ?>
		</div>
		<div class="col2">
			<?php echo template::submit('blogEditSubmit'); ?>
		</div>
	</div>
</form>