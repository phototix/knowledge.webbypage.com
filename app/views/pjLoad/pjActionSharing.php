<?php 
$question = pjSanitize::html(stripslashes($tpl['arr']['question']));
?>
<form id="frmKBSharing" name="frmKBSharing" action="<?php echo PJ_INSTALL_FOLDER;?>index.php?controller=pjLoad&amp;action=pjActionSendSharing" class="kb-form">
	<p>
		<label class="title"><span><?php __('label_to')?>:</span><span id="kb_validate_to" class="kb-validation"></span></label>
		<input type="text" name="to" class="kb-text kb-w300 required email" />
	</p>
	<p>
		<label class="title"><span><?php __('label_from')?>:</span><span id="kb_validate_from" class="kb-validation"></span></label>
		<input type="text" name="from" class="kb-text kb-w300 required email" />
	</p>
	<p>
		<label class="title"><span><?php __('label_subject')?>:</span><span id="kb_validate_subject" class="kb-validation"></span></label>
		<input type="text" name="subject" class="kb-text kb-w500 required" value="<?php echo $question;?>" />
	</p>
	<p>
		<label class="title"><span><?php __('label_message')?>:</span><span id="kb_validate_message" class="kb-validation"></span></label>
		<textarea name="message" class="kb-textarea kb-w500 kb-h120 required"><?php echo $_POST['sharing_url'];?></textarea>
	</p>
</form>