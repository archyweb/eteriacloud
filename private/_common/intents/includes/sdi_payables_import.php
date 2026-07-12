<?php
$firms=$app->db->query("select vat_id, firm_name from archy_firms order by firm_name");
if($firms->num_rows>0):
?>
	<button type="button" class="btn btn-sm btn-default" onClick="$('#payables_import').slideToggle();">Importa da SDI</button>
	<div style="display:none; padding-top:20px; position:relative;" id="payables_import">
		<form method="post" role="form" action="/archy/app.php?do_action=import_payables" class="form-inline" onSubmit="return confirm('Si è certi di voler importare le fatture passive?')">
			
			<div class="form-group">
				<label class="control-label" for="firm">Registro sezionale</label>
				<div class="input-group">
					<select class="form-control" id="firm" name="vat_id">
					<?php
					while($firm=mysqli_fetch_object($firms)):
						echo '<option value="'.$firm->vat_id.'">'.$firm->firm_name.'</option>';
					endwhile;
					?>
					</select>

				</div>
			</div>
			
			<div class="form-group">
            <label class="control-label" for="after">A partire dalla data</label>
				<div class="input-group">
					<input type="text" class="form-control date" id="after" name="after" value="<?php echo '01/01/'.date('Y')?>" placeholder="__/__/____" maxlength="10">
					<div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
					<div class="input-group-addon" style="cursor:pointer" data-toggle="tooltip" title="Vuota il campo per importare tutti i documenti" onClick="$('#after').val('')"><span class="glyphicon glyphicon-remove"></span></div>
				</div>
				<label class="control-label" for="before">fino alla data</label>
				<div class="input-group">
					<input type="text" class="form-control date" id="before" name="before" value="<?php echo '01/01/'.date('Y')?>" placeholder="__/__/____" maxlength="10">
					<div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
					<div class="input-group-addon" style="cursor:pointer" data-toggle="tooltip" title="Vuota il campo per importare tutti i documenti" onClick="$('#before').val('')"><span class="glyphicon glyphicon-remove"></span></div>
				</div>
			</div>
			
			<button type="submit" class="btn btn-primary">Importa fatture passive dal Sistema di Interscambio</button>

		</form>

		<p style="margin-top:25px;">L'importazione avviene automaticamente per i nuovi documenti, questo strumento rende possibile importare i documenti pregressi.</p>

	</div>
	<hr>
<?php
endif;
?>