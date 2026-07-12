<?php
$cash_budget=new CashBudget();
?>
<div class="row">
	<div class="col-md-2">
   	<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#cash_budget_modal">Esporta Bilancio di Cassa</button>
   </div>
	<div class="col-md-4 petty_cash_balance <?php echo $cash_budget->cash_budget<0? 'text-danger' : ''?>">
   	<h3>Bilancio di Cassa</h3>
      <h4><?php echo App::currency($cash_budget->cash_budget)?></h4>
   </div>
</div>
<hr>
<!-- Modal -->
<div class="modal fade" id="cash_budget_modal" tabindex="-1" role="dialog" aria-labelledby="cash_budget_modal_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="cash_budget_modal_label">Esporta Bilancio di Cassa</h4>
      </div>
      <form action="<?php echo $app->abs_root?>archy/app.php" class="fileDownloadForm" method="get">
         <input type="hidden" name="do_action" value="export_cash_budget">
         <div class="modal-body">
            <div class="row">
               <div class="col-xs-6">
                  <div class="form-group">
                     <label for="range_start">Dalla data</label>
                     <div class="input-group">
                        <input type="text" class="form-control date" name="range_start" placeholder="__/__/____" maxlength="10" id="range_start">
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
                     </div>
                     <p class="help-block">Lascia bianco per non impostare una data di inizio</p>
                  </div>
               </div>
               <div class="col-xs-6">
                  <div class="form-group">
                     <label for="range_end">Alla data</label>
                     <div class="input-group">
                        <input type="text" class="form-control date" name="range_end" placeholder="__/__/____" maxlength="10" id="range_end">
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
                     </div>
                     <p class="help-block">Lascia bianco per non impostare una data di fine</p>
                  </div>
               </div>
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
            <button type="submit" class="btn btn-primary" onClick="$('#cash_budget_modal').modal('hide');">Esporta</button>
         </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<style type="text/css">
#ui-datepicker-div{
	z-index:1150 !important; /* allow datepicker in bootstrap modal */
}
</style>