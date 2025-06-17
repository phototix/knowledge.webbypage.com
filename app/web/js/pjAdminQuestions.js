var jQuery_1_8_2 = jQuery_1_8_2 || $.noConflict();
(function ($, undefined) {
	$(function () {
		"use strict";
		var $frmCreateQuestion = $("#frmCreateQuestion"),
			$frmUpdateQuestion = $("#frmUpdateQuestion"),
			$dialogReset = $("#dialogReset"),
			datagrid = ($.fn.datagrid !== undefined),
			dialog = ($.fn.dialog !== undefined),
			spinner = ($.fn.spinner !== undefined),
			chosen = ($.fn.chosen !== undefined),
			tipsy = ($.fn.tipsy !== undefined),
			reseturl = null,
			multiselect = ($.fn.multiselect !== undefined);
		
		if (chosen){
			$("#user_id").chosen();
			$("#category_id").chosen();
		}
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
		
		if ($frmCreateQuestion.length > 0) {
			$frmCreateQuestion.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
		}
		if ($frmUpdateQuestion.length > 0) {
			$frmUpdateQuestion.validate({
				errorPlacement: function (error, element) {
					error.insertAfter(element.parent());
				},
				onkeyup: false,
				errorClass: "err",
				wrapper: "em",
				ignore: ""
			});
			$(".kb-rating").stars({
			    inputType: "select",
			    oneVoteOnly: true,
			    disabled: true,
			    callback: function(ui, type, value) {}
			});
			
			if ($dialogReset.length > 0 && dialog) {
				$dialogReset.dialog({
					modal: true,
					autoOpen: false,
					resizable: false,
					draggable: false,
					width: 400,
					buttons: {
						"Reset": function () {
							$(this).dialog("close");
							window.location.href = reseturl;
						},
						"Cancel": function () {
							$(this).dialog("close");
						}
					}
				});
			}
		}
		
		if($frmCreateQuestion.length > 0 || $frmUpdateQuestion.length > 0){
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
				buttons: [{type: "edit", url: "index.php?controller=pjAdminQuestions&action=pjActionUpdate&id={:id}"},
				          {type: "delete", url: "index.php?controller=pjAdminQuestions&action=pjActionDeleteQuestion&id={:id}"}
				          ],
				columns: [{text: myLabel.question, type: "text", sortable: true, editable: true, width: 300, editableWidth: 280},
				          {text: myLabel.author, type: "text", sortable: true, editable: false, width: 180},
				          {text: myLabel.status, type: "select", sortable: true, editable: true, width: 100,options: [
				                                                                                     {label: myLabel.active, value: "T"}, 
				                                                                                     {label: myLabel.inactive, value: "F"}
				                                                                                     ], applyClass: "pj-status"}],
				dataUrl: "index.php?controller=pjAdminQuestions&action=pjActionGetQuestion",
				dataType: "json",
				fields: ['question', 'name', 'status'],
				paginator: {
					actions: [
					   {text: myLabel.delete_selected, url: "index.php?controller=pjAdminQuestions&action=pjActionDeleteQuestionBulk", render: true, confirmation: myLabel.delete_confirmation},
					   {text: myLabel.revert_status, url: "index.php?controller=pjAdminQuestions&action=pjActionStatusQuestion", render: true},
					   {text: myLabel.exported, url: "index.php?controller=pjAdminQuestions&action=pjActionExportQuestion", ajax: false}
					],
					gotoPage: true,
					paginate: true,
					total: true,
					rowCount: true
				},
				saveUrl: "index.php?controller=pjAdminQuestions&action=pjActionSaveQuestion&id={:id}",
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
				q: "",
				user_id: "",
				category_id: "",
				question: "",
				answer: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminQuestions&action=pjActionGetQuestion", "created", "DESC", content.page, content.rowCount);
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
			$grid.datagrid("load", "index.php?controller=pjAdminQuestions&action=pjActionGetQuestion", "created", "DESC", content.page, content.rowCount);
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
			$.post("index.php?controller=pjAdminQuestions&action=pjActionSetActive", {
				id: $(this).closest("tr").data("object")['id']
			}).done(function (data) {
				$grid.datagrid("load", "index.php?controller=pjAdminQuestions&action=pjActionGetQuestion");
			});
			return false;
		}).on("click", ".pj-button-detailed, .pj-button-detailed-arrow", function (e) {
			e.stopPropagation();
			$(".pj-form-filter-advanced").toggle();
		}).on("submit", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var obj = {},
				$this = $(this),
				arr = $this.serializeArray(),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			for (var i = 0, iCnt = arr.length; i < iCnt; i++) {
				obj[arr[i].name] = arr[i].value;
			}
			$.extend(cache, obj);
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminQuestions&action=pjActionGetQuestion", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("reset", ".frm-filter-advanced", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			$(".pj-button-detailed").trigger("click");
			if (chosen) {
				$("#user_id").val('').trigger("liszt:updated");
				$("#category_id").val('').trigger("liszt:updated");
			}
			$('#question').val('');
			$('#answer').val('');			
		}).on("submit", ".frm-filter", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			var $this = $(this),
				content = $grid.datagrid("option", "content"),
				cache = $grid.datagrid("option", "cache");
			$.extend(cache, {
				q: $this.find("input[name='q']").val(),
				user_id: "",
				category_id: "",
				question: "",
				answer: ""
			});
			$grid.datagrid("option", "cache", cache);
			$grid.datagrid("load", "index.php?controller=pjAdminQuestions&action=pjActionGetQuestion", "created", "DESC", content.page, content.rowCount);
			return false;
		}).on("click", "#rating_reset", function (e) {
			if (e && e.preventDefault) {
				e.preventDefault();
			}
			reseturl = $(this).attr('href');
			$dialogReset.dialog("open");
		});
	});
})(jQuery_1_8_2);