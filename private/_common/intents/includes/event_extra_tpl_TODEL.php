<tr>
   <td>
   	<div class="form-group">
      	<input type="text" class="form-control input-sm required extra description" name="event[extras][<?php echo @$event_index?>][description][]" placeholder="Descrizione costo extra" value="<?php echo @$extra['description']?>" <?php echo @$event['archy_activities_events.extras']['editable_extras']===false? 'readonly':''?>>
   	</div>
   </td>
   <td width="160">
   	<div class="form-group">
      	<div class="input-group">
         	<input type="text" class="form-control input-sm required numbers_only extra amount" name="event[extras][<?php echo @$event_index?>][amount][]" placeholder="Importo" value="<?php echo @$extra['amount']?>" <?php echo @$event['archy_activities_events.extras']['editable_extras']===false? 'readonly':''?>>
         	<div class="input-group-addon"><span class="glyphicon glyphicon-euro"></span></div>
      	</div>
      </div>
   </td>
   <td width="25" style="vertical-align:middle"><i class="glyphicon glyphicon-trash delete_extra" <?php echo @$event['archy_activities_events.extras']['editable_extras']===false? 'style="visibility:hidden"':''?>></i></td>
</tr>
