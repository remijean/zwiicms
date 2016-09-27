<form>
	<h2>Options du site</h2>
	<div class="row">
		<div class="col4 block">
			<h3>Couleurs et image</h3>
			<div class="row">
				<div class="col6">
					<?php echo template::input('themeBackgroundColor', [
						'label' => 'Couleur du fond',
						'value' => $this->getData(['theme', 'background', 'color'])
					]); ?>
				</div>
				<div class="col6">
					<?php echo template::input('themeTitleColor', [
						'label' => 'Couleur des titres',
						'value' => $this->getData(['theme', 'title', 'color'])
					]); ?>
				</div>
			</div>
			<?php echo template::select('themeBackgroundImage', [], [
				'label' => 'Image du fond',
				'help' => 'Seule une image de format .png, .gif, .jpg ou .jpeg du gestionnaire de fichiers est acceptée.',
				'selected' => $this->getData(['theme', 'background', 'image'])
			]); ?>
			<div id="backgroundImageOptions">
				<div class="row">
					<div class="col6">
						<?php echo template::select('themeBackgroundRepeat', $module::$repeats, [
							'label' => 'Répétition',
							'selected' => $this->getData(['theme', 'background', 'repeat'])
						]); ?>
					</div>
					<div class="col6">
						<?php echo template::select('themeBackgroundPosition', $module::$positions, [
							'label' => 'Alignement',
							'selected' => $this->getData(['theme', 'background', 'position'])
						]); ?>
					</div>
				</div>
				<div class="row">
					<div class="col6">
						<?php echo template::select('themeBackgroundAttachment', $module::$attachments, [
							'label' => 'Position',
							'selected' => $this->getData(['theme', 'background', 'attachment'])
						]); ?>
					</div>
				</div>
			</div>
		</div>
		<div class="col4 block">
			<h3>Polices</h3>
			<?php echo template::select('themeTitleFont', $this->fonts, [
				'label' => 'Police des titres',
				'selected' => $this->getData(['theme', 'title', 'font'])
			]); ?>
			<?php echo template::select('themeTextFont', $this->fonts, [
				'label' => 'Police du texte',
				'selected' => $this->getData(['theme', 'text', 'font'])
			]); ?>
		</div>
	</div>
	<button type="button" data-form="pageSave">Créer</button>
	<?php echo template::button('theme', [
		'type' => 'submit',
		'value' => 'Se connecter'
	]); ?>
</form>
<script>
	// Affiche/Cache les options de l'image du fond
	$("#backgroundImage").on("change", function() {
		$("#backgroundImageOptions").slideToggle($(this).val() === "");
	}).trigger("change");
	// Aperçu en direct
	$("themeForm").on("change", function() {
		// Supprime l'ancien css
		$("#themePreview").remove();
		// Polices de caractères
		var fontTitle = $("#themeFontTitle").val();
		var fontText = $("#themeFontText").val();
		var css = "@import url('https://fonts.googleapis.com/css?family=" + fontTitle + "|" + fontText + "');";
		// Couleurs
		$(".colorPicker").each(function() {
			var colorPicker = $(this);
			var rgba = colorPicker.val().split(',');
			var colorNormal = "rgba(" + rgba[0] + "," + rgba[1] + "," + rgba[2] + "," + rgba[3] + ")";
			var colorDark = "rgba(" + (rgba[0] - 20) + "," + (rgba[1] - 20) + "," + (rgba[2] - 20) + "," + rgba[3] + ")";
			var colorVeryDark = "rgba(" + (rgba[0] - 25) + "," + (rgba[1] - 25) + "," + (rgba[2] - 25) + "," + rgba[3] + ")";
			var textVariant = "rgba(" + (.213 * rgba[1] + .715 * rgba[2] + .072 * rgba[3] > 127.5) ? "inherit" : "white" + ")";
			switch(colorPicker.attr("id")) {
				case "themeColorBody":
					css += "body{background-color:" + colorNormal + "}";
					break;
				case "themeColorElement":
					css += ""; // TODO ajouter après refonte du template
					break;
				case "themeColorHeader":
					css += "header{background-color:" + colorNormal + "}";
					css += "header h1{color:" + textVariant + "}";
					break;
				case "themeColorMenu":
					css += "nav{background-color:" + colorNormal + "}";
					css += "nav a{color:" + textVariant + "}";
					css += "nav a:hover{background-color:" + colorDark + "}";
					css += "nav a:target{background-color:" + colorVeryDark + "}";
					break;
			}
			// Polices
			var font = <?php echo json_encode($this->fonts); ?>;
			css += "body{font-family:'" + fonts[fontTitle] + "',sans-serif}";
			css += "h1,h2,h3,h4,h5,h6{font-family:'" + fonts[fontText] + "',sans-serif}";
			// Images
			css += "body{background-image:url('" + $("#themeImageBody").val() + "')}";
			css += "header{background-image:url('" + $("#themeImageHeader").val() + "')}";

		});
		// Applique le nouveau css
		$("<style>").attr("id", "themePreview").text(css).appendTo("head");
	}).trigger("change");
</script>