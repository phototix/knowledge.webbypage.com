var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateGlossary = $("#frmCreateGlossary"),
			$frmUpdateGlossary = $("#frmUpdateGlossary"),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined),
			spinner = ($.fn.spinner !== undefined),
			tipsy = ($.fn.tipsy !== undefined);		
		
		$(".field-int").spinner({
			min: 0
		});
		if (tipsy) {
			$(".listing-tip").tipsy({
				offset: 1,
				opacity: 1,
				html: true,
				gravity: "nw",
				className: "tipsy-listing"
			});
		}
		if ($frmCreateGlossary.length > 0) {
			$frmCreateGlossary.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		if ($frmUpdateGlossary.length > 0) {
			$frmUpdateGlossary.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		if($frmCreateGlossary.length > 0 || $frmUpdateGlossary.length > 0){
			tinymce.init({
			    selector: "textarea.mceEditor",
			    theme: "modern",
			    width: 580,
			    plugins: [
					        "advlist autolink lists link image charmap print preview anchor",
					        "searchreplace visualblocks code fullscreen",
					        "insertdatetime media table contextmenu paste"
					    ],
				toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
		        setup: function (editor) {
			    	editor.on('change', function (e) {
			    		editor.editorManager.triggerSave();
			    		$(":input[name='" + editor.id + "']").valid();
			    	});
			    }
			 }); 
		}
		
		if ($("#grid").length > 0 && datagrid) {
			
			var $grid = $("#grid").datagrid({
				buttons: [{type: "edit", url: "index.php?controller=pjAdminGlossaries&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminGlossaries&action=pjActionDeleteGlossary&id={:id}"}
				          ],
				columns: [{text: myLabel.word, type: "text", sortable: true, editable: true, width: 480, editableWidth: 460},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100,options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminGlossaries&action=pjActionGetGlossary",
				dataType: "json",
				fields: ['word', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminGlossaries&action=pjActionDeleteGlossaryBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminGlossaries&action=pjActionStatusGlossary", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminGlossaries&action=pjActionExportGlossary", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminGlossaries&action=pjActionSaveGlossary&id={:id}",
				select: {
					field: "id",
					name: "record[]"
				}
			});
		}
		
		$(document).on("click", ".btn-all", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(this).addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			var content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				status: "",
				q: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminGlossaries&action=pjActionGetGlossary", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".btn-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache"),
				obj = {};
			$this.addClass("pj-button-active").siblings(".pj-button").removeClass("pj-button-active");
			obj.status = "";
			obj[$this.data("column")] = $this.data("value");
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminGlossaries&action=pjActionGetGlossary", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", ".pj-status-1", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			return false;
		}).on("click", ".pj-status-0", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$.post("index.php?controller=pjAdminGlossaries&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminGlossaries&action=pjActionGetGlossary");
			});
			return false;
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val()
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminGlossaries&action=pjActionGetGlossary", "created", "DESC", content.page, content.rowCount);
			return false;
		});
	});
})(jQuery_1_8_2);