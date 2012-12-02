/**
 * javascript file used by the mce plugin pop-up dialog
 */

tinyMCEPopup.requireLangPack();

var HungryFeedDialog = {
		
	/**
	 * initialize the form
	 */
	init : function() {

		// Get the selected contents as text and place it in the input
		var selectedText = tinyMCEPopup.editor.selection.getContent({format : 'text'});
		
		// this is not currently used
		var shortcode = tinyMCEPopup.getWindowArg('shortcode');
	},

	/**
	 * Insert the contents from the input into the document
	 */
	insert : function() {
		
		var f = document.forms[0];
		
		var url = f.url.value;
		
		if (url == '')
		{
			alert('Feed URL is required');
			return;
		}
		
		var feed_fields = f.feed_fields_title.checked ? 'title,' : '';
		feed_fields += f.feed_fields_description.checked ? 'description,' : '';
		if (feed_fields != '') feed_fields = feed_fields.slice(0, -1);

		var item_fields = f.item_fields_title.checked ? 'title,' : '';
		item_fields += f.item_fields_description.checked ? 'description,' : '';
		item_fields += f.item_fields_author.checked ? 'author,' : '';
		item_fields += f.item_fields_date.checked ? 'date,' : '';
		if (item_fields != '') item_fields = item_fields.slice(0, -1);
		
		var link_item_title = f.link_item_title_yes.checked ? '1' : '0';
		var link_target = f.link_target.options[f.link_target.selectedIndex].value;
		var template = f.template.options[f.template.selectedIndex].value;
		var truncate_description = f.truncate_description.value;
		var allowed_tags = f.allowed_tags.value;
		var strip_ellipsis = f.strip_ellipsis_yes.checked ? '1' : '0';
		var date_format = f.date_format.value;
		var max_items = f.max_items.value;
		var filter = f.filter.value;
		var filter_out = f.filter_out.value;
		var page_size = f.page_size.value;
		var order = f.order.options[f.order.selectedIndex].value;
		
		var force_feed = f.force_feed_yes.checked ? '1' : '0';
		var decode_url = f.decode_url_yes.checked ? '1' : '0';
		var xml_dump = f.xml_dump_yes.checked ? '1' : '0';
		var show_data = f.show_data_yes.checked ? '1' : '0';

		var contents = '[hungryfeed url="' + url + '"';
		
		contents += (feed_fields == 'title,description' ? '' : ' feed_fields="' + feed_fields + '"');
		contents += (item_fields == 'title,description,author,date' ? '' : ' item_fields="' + item_fields + '"');
		
		if (link_item_title == '0') contents += ' link_item_title="0"';
		if (link_target != '') contents += ' link_target="'+link_target+'"';
		if (template != '') contents += ' template="'+template+'"';
		if (truncate_description != '0') contents += ' truncate_description="'+truncate_description+'"';
		if (allowed_tags != '') contents += ' allowed_tags="'+allowed_tags+'"';
		if (strip_ellipsis == '1') contents += ' strip_ellipsis="1"';
		if (date_format != '') contents += ' date_format="'+date_format+'"';
		if (max_items != '0') contents += ' max_items="'+max_items+'"';
		if (filter != '') contents += ' filter="'+filter+'"';
		if (filter_out != '') contents += ' filter_out="'+filter_out+'"';
		if (page_size != '0') contents += ' page_size="'+page_size+'"';
		if (order != '') contents += ' order="'+order+'"';
		if (force_feed == '1') contents += ' force_feed="1"';
		if (decode_url == '0') contents += ' decode_url="0"';
		if (xml_dump == '1') contents += ' xml_dump="1"';
		if (show_data == '1') contents += ' show_data="1"';
		
		contents += ']';

		tinyMCEPopup.editor.execCommand('mceInsertContent', false, contents);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(HungryFeedDialog.init, HungryFeedDialog);
