	
	//jQuery('#list_pages').multiSelect();
	jQuery('#SaveChanges').click(Submit_scripts);
	jQuery('#Reset').click(Reset_scripts);	
	function Submit_scripts()
	{	
		var insert_page = $("select#list_pages").val();
		$('#AdLugeVT_pages').val(insert_page);
		
	}
	function Reset_scripts()
	{
		$('#AdLugeVT_pages').val();
	}
	$('#list_pages').multiSelect({
	selectableHeader: "<div class='custom-header'><h4>Inserted Pages</h4></div>",
	selectionHeader: "<div class='custom-header'><h4>Omitted Pages</h4></div>"
	});


	
