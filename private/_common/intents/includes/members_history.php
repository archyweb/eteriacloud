<script type="text/javascript">
window.onload=function(){
	$('input[name="main[city_id]"]').on('input',function(){
		$.ajax({
			type: "POST",
			url: "/archy/app.php?do_action=get_cap",
			data: 'city_id='+$(this).val(),
			cache: false,
			success: function(cap){
				$('input[name="main[zip]"]').val(cap);
			}
		});
	});
}
</script>