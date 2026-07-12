<script type="text/javascript">
window.onload=function(){
	var city_changed;
	var loaded_city='<?php echo isset($_GET['id'])? $data['columns']['archy_locations.city_id']['value'] : ''?>';
	$('#search_modal').on('show.bs.modal', function (event) {
		var modal_intent=$(event.relatedTarget).data('intent');
		city_changed=modal_intent=='archy_cities';
	});
	$('#search_modal').on('hidden.bs.modal', function (e) {
		if(city_changed){
			var city_id=$('[name="main[city_id]"]').val();
			if(city_id!=loaded_city){
				loaded_city=city_id;
				$.ajax({
					type: "POST",
					url: "/archy/app.php?do_action=get_cap",
					data: 'city_id='+city_id,
					cache: false,
					success: function(cap){
						$('input[name="main[zip]"]').val(cap);
					}
				});
			}
		}
	});
}
</script>