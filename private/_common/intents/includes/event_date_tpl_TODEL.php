<div class="col-md-2">
  <div class="form-group " title="Compila questo campo">
    <label class="control-label" for="date<?php echo @$date_index?>">Data <span class="glyphicon glyphicon-asterisk text-info" data-toggle="tooltip" title="Campo richiesto"></span></label>
    <div class="input-group">
      <input type="text" class="form-control date required" id="date<?php echo @$date_index?>" name="event[dates][<?php echo @$date_index?>][date][]" value="" placeholder="__/__/____" maxlength="10">
      <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
    </div>
  </div>
</div>

<div class="col-md-2">
    <label class="control-label" for="start<?php echo @$date_index?>">Ora inizio <span class="glyphicon glyphicon-asterisk text-info" data-toggle="tooltip" title="Campo richiesto"></span></label>
    <div class="input-group clockpicker" data-autoclose="true">
    	<input type="text" class="form-control required" value="" id="start<?php echo @$date_index?>">
    	<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
    </div>
</div>

<div class="col-md-2">
    <label class="control-label" for="end<?php echo @$date_index?>">Ora fine</label>
    <div class="input-group clockpicker" data-autoclose="true">
    	<input type="text" class="form-control" value="" id="end<?php echo @$date_index?>">
    	<span class="input-group-addon"><span class="glyphicon glyphicon-time"></span></span>
    </div>
</div>
