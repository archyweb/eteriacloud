<div class="container">
	<div class="form-group">
   	<label for="report_name">Nome Verbale <i class="glyphicon glyphicon-asterisk text-info tt" title="Campo richiesto"></i></label>
      <input type="text" class="form-control required" name="main[name]" value="<?php echo form_value($data['hidden_columns']['archy_reports.name']['value'])?>" id="report_name">
   </div>
   <div class="form-group">
   	<label for="">Carica Modello</label>
      <select class="form-control" id="load_model">
      	<option value="">Carica modello</option>
         <?php
			$options=$app->db->query("select id as value, name as text from archy_report_models order by text");
			while($option=mysqli_fetch_object($options)):
				?><option value="<?php echo $option->value?>"><?php echo $option->text?></option><?php
			endwhile;
			?>
      </select>
   </div>
	<div class="form-group">
   	<label for="report">Verbale <i class="glyphicon glyphicon-asterisk text-info tt" title="Campo richiesto"></i></label>
      <textarea class="form-control editor required" rows="4" cols="80" name="main[report]" id="report"><?php echo $data['hidden_columns']['archy_reports.report']['value']?></textarea>
   </div>
</div>
<script type="text/javascript">
window.onload=function(){
	$('#load_model').change(function(){
		var id=$(this).val();
		if(id!=''){
			if(confirm("L'attuale contenuto verrà sovrascritto.\nContinuare?")){
				$.ajax({
					type: "POST",
					url: "/archy/app.php?do_action=get_report_model",
					data: 'id='+id,
					cache: false,
					success: function(html){
						tinymce.get('report').setContent(html);
						tinymce.get('report').save();
					}
				});
			}else{
				$(this).val('');
			}
		}
	});
}
</script>