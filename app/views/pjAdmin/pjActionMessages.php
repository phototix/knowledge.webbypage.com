<?php
$months = __('months', true);
$days = __('days', true);
?>
if (jQuery_1_8_2.datagrid !== undefined) {
	jQuery_1_8_2.extend(jQuery_1_8_2.datagrid.messages, {
		empty_result: "<?php __('gridEmptyResult', false, true); ?>",
		choose_action: "<?php __('gridChooseAction', false, true); ?>",
		goto_page: "<?php __('gridGotoPage', false, true); ?>",
		total_items: "<?php __('gridTotalItems', false, true); ?>",
		items_per_page: "<?php __('gridItemsPerPage', false, true); ?>",
		prev_page: "<?php __('gridPrevPage', false, true); ?>",
		prev: "<?php __('gridPrev', false, true); ?>",
		next_page: "<?php __('gridNextPage', false, true); ?>",
		next: "<?php __('gridNext', false, true); ?>",
		month_names: ['<?php echo $months[1]; ?>', '<?php echo $months[2]; ?>', '<?php echo $months[3]; ?>', '<?php echo $months[4]; ?>', '<?php echo $months[5]; ?>', '<?php echo $months[6]; ?>', '<?php echo $months[7]; ?>', '<?php echo $months[8]; ?>', '<?php echo $months[9]; ?>', '<?php echo $months[10]; ?>', '<?php echo $months[11]; ?>', '<?php echo $months[12]; ?>'],
		day_names: ['<?php echo $days[1]; ?>', '<?php echo $days[2]; ?>', '<?php echo $days[3]; ?>', '<?php echo $days[4]; ?>', '<?php echo $days[5]; ?>', '<?php echo $days[6]; ?>', '<?php echo $days[0]; ?>'],
		delete_title: "<?php __('gridDeleteConfirmation', false, true); ?>",
		delete_text: "<?php __('gridConfirmationTitle', false, true); ?>",
		action_title: "<?php __('gridActionTitle', false, true); ?>",
		btn_ok: "<?php __('gridBtnOk', false, true); ?>",
		btn_cancel: "<?php __('gridBtnCancel', false, true); ?>",
		btn_delete: "<?php __('gridBtnDelete', false, true); ?>"
	});
}

if (jQuery_1_8_2.multilang !== undefined) {
	jQuery_1_8_2.extend(jQuery_1_8_2.multilang.messages, {
		tooltip: "<?php __('multilangTooltip', false, true); ?>"
	});
}

if (jQuery_1_8_2.gallery !== undefined) {
	jQuery_1_8_2.extend(jQuery_1_8_2.gallery.messages, {
		alt: "<?php __('galleryAlt'); ?>",
		name: "<?php __('galleryName'); ?>",
		description: "<?php __('galleryDescription'); ?>",
		url: "<?php __('galleryUrl'); ?>",
		btn_delete: "<?php __('galleryBtnDelete'); ?>",
		btn_cancel: "<?php __('galleryBtnCancel'); ?>",
		btn_save: "<?php __('galleryBtnSave'); ?>",
		btn_set_watermark: "<?php __('galleryBtnSetWatermark'); ?>",
		btn_clear_current: "<?php __('galleryBtnClearCurrent'); ?>",
		btn_compress: "<?php __('galleryBtnCompress'); ?>",
		btn_recreate: "<?php __('galleryBtnRecreate'); ?>",
		compress_note: "<?php __('galleryCompressionNote'); ?>",
		compression: "<?php __('galleryCompression'); ?>",
		delete_all: "<?php __('galleryDeleteAll'); ?>",
		delete_confirmation: "<?php __('galleryDeleteConfirmation'); ?>",
		delete_confirmation_single: "<?php __('galleryConfirmationSingle'); ?>",
		delete_confirmation_multi: "<?php __('galleryConfirmationMulti'); ?>",
		edit: "<?php __('galleryEdit'); ?>",
		empty_result: "<?php __('galleryEmptyResult'); ?>",
		erase: "<?php __('galleryDelete'); ?>",
		image_settings: "<?php __('galleryImageSettings'); ?>",
		move: "<?php __('galleryMove'); ?>",
		originals: "<?php __('galleryOriginals'); ?>",
		photos: "<?php __('galleryPhotos'); ?>",
		position: "<?php __('galleryPosition'); ?>",
		resize: "<?php __('galleryResize'); ?>",
		rotate: "<?php __('galleryRotate'); ?>",
		thumbs: "<?php __('galleryThumbs'); ?>",
		upload: "<?php __('galleryUpload'); ?>",
		watermark: "<?php __('galleryWatermark'); ?>",
		watermark_position: "<?php __('galleryWatermarkPosition'); ?>",
		watermark_positions: {
			tl: "<?php __('galleryTopLeft'); ?>",
			tr: "<?php __('galleryTopRight'); ?>",
			tc: "<?php __('galleryTopCenter'); ?>",
			bl: "<?php __('galleryBottomLeft'); ?>",
			br: "<?php __('galleryBottomRight'); ?>",
			bc: "<?php __('galleryBottomCenter'); ?>",
			cl: "<?php __('galleryCenterLeft'); ?>",
			cr: "<?php __('galleryCenterRight'); ?>",
			cc: "<?php __('galleryCenterCenter'); ?>"
		}
	});
}