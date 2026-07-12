<div class="container">
	<div class="form-group">
   	<label for="model_name">Nome Modello</label>
      <input type="text" class="form-control" name="main[name]" value="<?php echo form_value($data['hidden_columns']['archy_report_models.name']['value'])?>" id="model_name">
   </div>
	<div class="form-group">
   	<label for="model">Modello</label>
      <textarea class="form-control editor" rows="4" cols="80" name="main[model]" id="model" data-purpose="meeting" data-height="400"><?php echo $data['hidden_columns']['archy_report_models.model']['value']?></textarea>
   </div>
</div>