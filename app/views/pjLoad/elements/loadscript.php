<script type="text/javascript">
var pjQ = pjQ || {},
	StivaKB;
(function () {
	"use strict";
	var loadRemote = function(url, type, callback) {
		var _element, _type, _attr, scr, s, element;
		
		switch (type) {
		case 'css':
			_element = "link";
			_type = "text/css";
			_attr = "href";
			break;
		case 'js':
			_element = "script";
			_type = "text/javascript";
			_attr = "src";
			break;
		}
		
		scr = document.getElementsByTagName(_element);
		s = scr[scr.length - 1];
		element = document.createElement(_element);
		element.type = _type;
		if (type == "css") {
			element.rel = "stylesheet";
		}
		if (element.readyState) {
			element.onreadystatechange = function () {
				if (element.readyState == "loaded" || element.readyState == "complete") {
					element.onreadystatechange = null;
					callback();
				}
			};
		} else {
			element.onload = function () {
				callback();
			};
		}
		element[_attr] = url;
		s.parentNode.insertBefore(element, s.nextSibling);
	},
	loadScript = function (url, callback) {
		loadRemote(url, "js", callback);
	},
	loadCss = function (url, callback) {
		loadRemote(url, "css", callback);
	};

	var KBObj = {
		server: "<?php echo PJ_INSTALL_URL; ?>",
		folder: "<?php echo PJ_INSTALL_FOLDER; ?>",
		hash: "<?php echo $hash;?>",

		main_container: "kb_main_container_<?php echo $hash;?>",
		glossary_container: "kb_glossary_container_<?php echo $hash;?>",
		search_form: "frmKBSearch_<?php echo $hash;?>",
		category_id: "category_id_<?php echo $hash;?>",
		rating_message: "rating_message_<?php echo $hash;?>",
		avg_rating: "avg_rating_<?php echo $hash;?>",
		already_voted: "<?php __('label_already_voted', false, true);?>",
		thank_vote: "<?php __('label_thank_vote', false, true);?>",
		send_email: "kb_email_<?php echo $hash;?>",
		is_rating: <?php echo $_GET['action'] == 'pjActionView' ? 'false' : 'true'; ?>,
		glossary: <?php echo $_GET['action'] == 'pjActionGlossary' ? 'true' : 'false'; ?>,
		button_send: "<?php __('front_btn_send'); ?>",
		button_cancel: "<?php __('front_btn_cancel'); ?>",
	};
	<?php
	$dm = new pjDependencyManager(PJ_INSTALL_PATH, PJ_THIRD_PARTY_PATH);
	$dm->load(PJ_CONFIG_PATH . 'dependencies.php')->resolve();
	?>
	loadScript("<?php echo PJ_INSTALL_URL . preg_replace('|^' . PJ_INSTALL_PATH . '|', '', $dm->getPath('pj_jquery')); ?>pjQuery.min.js", function () {
		loadScript("<?php echo PJ_INSTALL_URL . preg_replace('|^' . PJ_INSTALL_PATH . '|', '', $dm->getPath('pj_validate')); ?>pjQuery.validate.min.js", function () {
			loadScript("<?php echo PJ_INSTALL_URL . preg_replace('|^' . PJ_INSTALL_PATH . '|', '', $dm->getPath('pj_validate')); ?>pjQuery.additional-methods.min.js", function () {
				loadScript("<?php echo PJ_INSTALL_URL . preg_replace('|^' . PJ_INSTALL_PATH . '|', '', $dm->getPath('pj_jquery_ui')); ?>js/pjQuery-ui.custom.min.js", function () {
					loadScript("<?php echo PJ_INSTALL_URL . preg_replace('|^' . PJ_INSTALL_PATH . '|', '', $dm->getPath('pj_jquery_stars')); ?>pjQuery-ui.stars.min.js", function () {
						loadScript("<?php echo PJ_INSTALL_URL . preg_replace('|^' . PJ_INSTALL_PATH . '|', '', $dm->getPath('overlay')); ?>overlay.js", function () {
							loadScript("<?php echo PJ_INSTALL_URL . preg_replace('|^' . PJ_INSTALL_PATH . '|', '', $dm->getPath('pj_jquery_print')); ?>pjQuery.print.js", function () {
								loadScript("<?php echo PJ_INSTALL_URL . preg_replace('|^' . PJ_INSTALL_PATH . '|', '', $dm->getPath('pj_masonry')); ?>pjQuery.masonry.pkgd.min.js", function () {
									loadScript("<?php echo PJ_INSTALL_URL . PJ_JS_PATH; ?>pjLoad.js", function () {
										StivaKB = new StivaKB(KBObj);
									});
								});
							});
						});
					});
				});
			});
		});
	});
})();
</script>