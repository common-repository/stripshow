(function($) {
inlineEditPostSS = {

	init : function() {
		var t = this, qeRow = $('#inline-edit'), bulkRow = $('#bulk-edit');

		t.type = $('table.widefat').hasClass('page') ? 'page' : 'post';
		t.what = '#'+t.type+'-';

		// get all editable rows
		t.rows = $('tr.iedit');

		// prepare the edit rows
		qeRow.keyup(function(e) { if(e.which == 27) return inlineEditPostSS.revert(); });
		bulkRow.keyup(function(e) { if (e.which == 27) return inlineEditPostSS.revert(); });

		$('a.cancel', qeRow).click(function() { return inlineEditPostSS.revert(); });
		$('a.save', qeRow).click(function() { return inlineEditPostSS.save(this); });
		$('td', qeRow).keydown(function(e) { if ( e.which == 13 ) return inlineEditPostSS.save(this); });

		$('a.cancel', bulkRow).click(function() { return inlineEditPostSS.revert(); });

		$('#inline-edit .inline-edit-private input[value=private]').click( function(){
			var pw = $('input.inline-edit-password-input');
			if ( $(this).attr('checked') ) {
				pw.val('').attr('disabled', 'disabled');
			} else {
				pw.attr('disabled', '');
			}
		});

		// add events
		t.addEvents(t.rows);

		$('#bulk-title-div').parents('fieldset').after(
			$('#inline-edit fieldset.inline-edit-categories').clone()
		).siblings( 'fieldset:last' ).prepend(
			$('#inline-edit label.inline-edit-tags').clone()
		);

		// categories expandable?
		$('span.catshow').click(function() {
			$('.inline-editor ul.cat-checklist').addClass("cat-hover");
			$('.inline-editor span.cathide').show();
			$(this).hide();
		});

		$('span.cathide').click(function() {
			$('.inline-editor ul.cat-checklist').removeClass("cat-hover");
			$('.inline-editor span.catshow').show();
			$(this).hide();
		});

		$('select[name="_status"] option[value="future"]', bulkRow).remove();

		$('#doaction, #doaction2').click(function(e){
			var n = $(this).attr('id').substr(2);
			if ( $('select[name="'+n+'"]').val() == 'edit' ) {
				e.preventDefault();
				t.setBulk();
			} else if ( $('form#posts-filter tr.inline-editor').length > 0 ) {
				t.revert();
			}
		});

		$('#post-query-submit').click(function(e){
			if ( $('form#posts-filter tr.inline-editor').length > 0 )
				t.revert();
		});

	},

	toggle : function(el) {
		var t = this;
		$(t.what+t.getId(el)).css('display') == 'none' ? t.revert() : t.edit(el);
	},

	addEvents : function(r) {
		r.each(function() {
			var row = $(this);
			$('a.editinline', row).click(function() { inlineEditPostSS.edit(this); return false; });
		});
	},

	setBulk : function() {
		var te = '', type = this.type, tax;
		this.revert();

		$('#bulk-edit td').attr('colspan', $('.widefat:first thead th:visible').length);
		$('table.widefat tbody').prepend( $('#bulk-edit') );
		$('#bulk-edit').addClass('inline-editor').show();

		$('tbody th.check-column input[type="checkbox"]').each(function(i){
			if ( $(this).attr('checked') ) {
				var id = $(this).val(), theTitle;
				theTitle = $('#inline_'+id+' .post_title').text() || inlineEditL10n.notitle;
				te += '<div id="ttle'+id+'"><a id="_'+id+'" class="ntdelbutton" title="'+inlineEditL10n.ntdeltitle+'">X</a>'+theTitle+'</div>';
			}
		});

		$('#bulk-titles').html(te);
		$('#bulk-titles a').click(function() {
			var id = $(this).attr('id').substr(1);

			$('table.widefat input[value="'+id+'"]').attr('checked', '');
			$('#ttle'+id).remove();
		});

		// enable autocomplete for tags
		if ( type == 'post' ) {
			// support multi taxonomies?
			tax = 'post_tag';
			$('tr.inline-editor textarea[name="tags_input"]').suggest( 'admin-ajax.php?action=ajax-tag-search&tax='+tax, { delay: 500, minchars: 2, multiple: true, multipleSep: ", " } );
		}
	},

	edit : function(id) {
		var t = this, fields, editRow, rowData, cats, status, pageOpt, f, pageLevel, nextPage, pageLoop = true, nextLevel, tax;
		t.revert();

		if ( typeof(id) == 'object' )
			id = t.getId(id);

		fields = ['post_title', 'post_name', 'post_author', '_status', 'jj', 'mm', 'aa', 'hh', 'mn', 'ss', 'post_password'];
		if ( t.type == 'page' ) fields.push('post_parent', 'menu_order', 'page_template');
		if ( t.type == 'post' ) fields.push('tags_input');

		// add the new blank row
		editRow = $('#inline-edit').clone(true);
		$('td', editRow).attr('colspan', $('.widefat:first thead th:visible').length);

		if ( $(t.what+id).hasClass('alternate') )
			$(editRow).addClass('alternate');
		$(t.what+id).hide().after(editRow);

		// populate the data
		rowData = $('#inline_'+id);
		stripShowData = $('#stripshow-inline_'+id);
		for ( f = 0; f < fields.length; f++ ) {
			$(':input[name="'+fields[f]+'"]', editRow).val( $('.'+fields[f], rowData).text() );
		}
		
		$('input[name="flooble_field"]', editRow).val( $('.flooble_field',stripShowData).text() );
		$('textarea[name="characters_input"]', editRow).val( $('.characters_input',stripShowData).text() );

		if ( $('.comment_status', rowData).text() == 'open' )
			$('input[name="comment_status"]', editRow).attr("checked", "checked");
		if ( $('.ping_status', rowData).text() == 'open' )
			$('input[name="ping_status"]', editRow).attr("checked", "checked");
		if ( $('.sticky', rowData).text() == 'sticky' )
			$('input[name="sticky"]', editRow).attr("checked", "checked");

		// categories
		if ( cats = $('.post_category', rowData).text() )
			$('ul.cat-checklist :checkbox', editRow).val(cats.split(','));

		// handle the post status
		status = $('._status', rowData).text();
		if ( status != 'future' ) $('select[name="_status"] option[value="future"]', editRow).remove();
		if ( status == 'private' ) {
			$('input[name="keep_private"]', editRow).attr("checked", "checked");
			$('input.inline-edit-password-input').val('').attr('disabled', 'disabled');
		}

		// remove the current page and children from the parent dropdown
		pageOpt = $('select[name="post_parent"] option[value="'+id+'"]', editRow);
		if ( pageOpt.length > 0 ) {
			pageLevel = pageOpt[0].className.split('-')[1];
			nextPage = pageOpt;
			while ( pageLoop ) {
				nextPage = nextPage.next('option');
				if (nextPage.length == 0) break;
				nextLevel = nextPage[0].className.split('-')[1];
				if ( nextLevel <= pageLevel ) {
					pageLoop = false;
				} else {
					nextPage.remove();
					nextPage = pageOpt;
				}
			}
			pageOpt.remove();
		}

		$(editRow).attr('id', 'edit-'+id).addClass('inline-editor').show();
		$('.ptitle', editRow).focus();

		// enable autocomplete for tags
		if ( t.type == 'post' ) {
			tax = 'post_tag';
			chartax = 'character';
			$('tr.inline-editor textarea[name="tags_input"]').suggest( 'admin-ajax.php?action=ajax-tag-search&tax='+tax, { delay: 500, minchars: 2, multiple: true, multipleSep: ", " } );
			$('tr.inline-editor textarea[name="characters_input"]').suggest( 'admin-ajax.php?action=ajax-tag-search&tax='+chartax, { delay: 500, minchars: 2, multiple: true, multipleSep: ", " } );
		}

		return false;
	},

	save : function(id) {
		var params, fields;

		if( typeof(id) == 'object' )
			id = this.getId(id);

		$('table.widefat .inline-edit-save .waiting').show();

		params = {
			action: 'inline-save',
			post_type: this.type,
			post_ID: id,
			edit_date: 'true'
		};

		fields = $('#edit-'+id+' :input').fieldSerialize();
		params = fields + '&' + $.param(params);

		// make ajax request
		$.post('admin-ajax.php', params,
			function(r) {
				$('table.widefat .inline-edit-save .waiting').hide();

				if (r) {
					if ( -1 != r.indexOf('<tr') ) {
						$(inlineEditPostSS.what+id).remove();
						$('#edit-'+id).before(r).remove();

						var row = $(inlineEditPostSS.what+id);
						row.hide();

						if ( 'draft' == $('input[name="post_status"]').val() )
							row.find('td.column-comments').hide();

						inlineEditPostSS.addEvents(row);
						row.fadeIn();

					} else {
						r = r.replace( /<.[^<>]*?>/g, '' );
						$('#edit-'+id+' .inline-edit-save').append('<span class="error">'+r+'</span>');
					}
				} else {
					$('#edit-'+id+' .inline-edit-save').append('<span class="error">'+inlineEditL10n.error+'</span>');
				}
			}
		, 'html');
		return false;
	},

	revert : function() {
		var id;

		if ( id = $('table.widefat tr.inline-editor').attr('id') ) {
			$('table.widefat .inline-edit-save .waiting').hide();

			if ( 'bulk-edit' == id ) {
				$('table.widefat #bulk-edit').removeClass('inline-editor').hide();
				$('#bulk-titles').html('');
				$('#inlineedit').append( $('#bulk-edit') );
			} else  {
				$('#'+id).remove();
				id = id.substr( id.lastIndexOf('-') + 1 );
				$(this.what+id).show();
			}
		}

		return false;
	},

	getId : function(o) {
		var id = o.tagName == 'TR' ? o.id : $(o).parents('tr').attr('id'), parts = id.split('-');
		return parts[parts.length - 1];
	}
};

$(document).ready(function(){inlineEditPostSS.init();});
})(jQuery);
