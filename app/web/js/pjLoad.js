(function (window, undefined){
	"use strict";
	var document = window.document,
		validate = (pjQ.$.fn.validate !== undefined);
	
	function StivaKB(opts) {
		if (!(this instanceof StivaKB)) {
			return new StivaKB(opts);
		}
		this.reset.call(this);
		this.init.call(this, opts);
		return this;
	}
	
	StivaKB.prototype = {
		reset: function () {
			this.opts = null;
			this.$container = null;
			this.container = null;
			this.search_form = null;
			this.send_email = null;
			this.menu_status = 0;
			
			return this;
		},
		bingRating: function(){
			var self = this;
			if(pjQ.$('.question-rating').length > 0){
				pjQ.$(".question-rating").stars({
				    inputType: "select",
				    oneVoteOnly: true,
				    disabled: self.opts.is_rating,
				    callback: function(ui, type, value) {
				    	var frm = ui.$form,
				    		rate = value,
				    		question_id = frm.find("input[name='question_id']").val();
				    	pjQ.$.ajax({
							type: "POST",
							dataType: "json",
							data: {rate: rate, id: question_id},
							url: self.opts.folder + 'index.php?controller=pjLoad&action=pjActionRating',
							success: function (result) {
								switch (result.code) {
									case 101:
										pjQ.$('#'+self.opts.rating_message).html(self.opts.already_voted);
										break;
									case 102:
										pjQ.$('#'+self.opts.rating_message).html(self.opts.thank_vote);
										pjQ.$('#'+self.opts.avg_rating).html(result.avg_rate);
										break;
								}
							}
						});
				    }
				});
			}
		},
		submitSearch: function(){
			var self = this;
			if(pjQ.$(self.search_form).length > 0)
			{
				if(pjQ.$('#seo_url_' + self.opts.hash).val() == 'Yes')
				{
					self.buildSeoUrl();
					return false;
				}else{
					self.search_form.submit();
				}
			}
		},
		buildSeoUrl: function()
		{
			var self = this,
				clone_url = pjQ.$('#clone_seo_url_' + self.opts.hash).val(),
				keyword = pjQ.$('#keyword_' + self.opts.hash).val(),
				sortby = pjQ.$('#sortby_' + self.opts.hash).val(),
				category_id = pjQ.$('#category_id_' + self.opts.hash).val();
			keyword = keyword != '' ? '/keyword/' + encodeURIComponent(keyword) : '';
			sortby = sortby != '' ? '/sortby/' + sortby : '';
			category_id = category_id != '' ? '/category/' + category_id  : '';
			
			clone_url = clone_url.replace(/\{sortby\}/g, sortby);
			clone_url = clone_url.replace(/\{keyword\}/g, keyword);
			clone_url = clone_url.replace(/\{category\}/g, category_id);
			window.location.href = encodeURI(clone_url + '/');
		},
		init: function (opts) {
			var self = this;
			this.opts = opts;
			this.container = document.getElementById(self.opts.main_container);
			this.search_form = document.getElementById(self.opts.search_form);
			this.send_email = document.getElementById(self.opts.send_email);
			
			this.$container = pjQ.$(this.container);
			
			self.bingRating();
						
			this.$container.on('mouseenter ', '.kb-question-box', function(e){
				pjQ.$(this).addClass('kb-question-hover');
			}).on('mouseleave', '.kb-question-box', function(e){
				pjQ.$(this).removeClass('kb-question-hover');
			}).on('click', '.kb-tab-item', function(e){
				self.search_form.sortby.value = pjQ.$(this).attr('rev');
				self.submitSearch();
			}).on('change', "#" + self.opts.category_id, function(e){
				self.submitSearch();
			}).on('keydown', '#keyword_' + self.opts.hash, function(e){
				if(pjQ.$('#seo_url_' + self.opts.hash).val() == 'Yes')
				{
					if (e.keyCode == 13) 
					{
						self.search_form.onsubmit = function() {
						    return false;
						}
						self.buildSeoUrl();
					}
				}
			}).on('click', '#' + self.opts.send_email, function(e){
				self.overlaySharing.open();
			}).on('click', '.kb-label-tab', function(e){
				if(self.menu_status == 1){
					self.menu_status = 0;
					pjQ.$('.kb-toggle-tab').css('display', 'none');
				}else{
					pjQ.$('.kb-toggle-tab').css('display', 'block');
					self.menu_status = 1;
				}
			}).on('click', '.kb-printer', function(e){
				if (e && e.preventDefault) {
					e.preventDefault();
				}
				pjQ.$('#kb_preview_container').show().print();
				pjQ.$('#kb_preview_container').hide();
			}).on('click', '.kb-question-box', function(e){
				var id = pjQ.$(this).attr('lang');
				window.location.href= pjQ.$('#kb_view_detail_' + id).attr('href');
			});
			
			if(self.opts.glossary == true){
				var $glossary_container = pjQ.$('#' + self.opts.glossary_container);
				$glossary_container.masonry({
					itemSelector: '.kb-glossary-box'
				});
			}
			
			if(pjQ.$(this.send_email).length > 0){
				var href = pjQ.$(this.send_email).attr('rev'),
					sharing_url = pjQ.$(this.send_email).attr('rel');
				
				self.overlaySharing = new OverlayJS({
					selector: "kb_email_dialog",
					modal: true,
					width: 550,
					height: 460,
					onBeforeOpen: function () {
						var $that = this;
						pjQ.$.ajax({
							type: "POST",
							dataType: "html",
							url: href,
							data: {sharing_url: sharing_url},
							success: function (result) {
								$that.content.innerHTML = result;
							}
						});
					},
					buttons: (function () {
						var buttons = {};
						buttons[self.opts.button_send] = function () {
							var $this = this;
							if(pjQ.$('#frmKBSharing').length > 0){
								pjQ.$('#frmKBSharing').validate({ 
									errorPlacement: function (error, element) {
										pjQ.$('#kb_validate_' + element.attr('name')).html(error.text());
									}
							    });
								if(pjQ.$('#frmKBSharing').valid()){
									pjQ.$.ajax({
										type: "POST",
										dataType: "html",
										data: pjQ.$('#frmKBSharing').serialize(),
										url: pjQ.$('#frmKBSharing').attr('action'),
										success: function (result) {
											$this.close();
										}
									});
								}
							}else{
								this.close();
							}
						};
						buttons[self.opts.button_cancel] = function () {
							this.close();
						};
						
						return buttons;
					})()
				});
			}
		}
	};
	
	window.StivaKB = StivaKB;
})(window);