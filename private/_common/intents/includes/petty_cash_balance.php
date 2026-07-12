<?php
$petty_cash=new PettyCash();
?>
<div class="row">
   <div class="col-md-4">
   	<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#petty_cash_modal">Esporta Prima Nota </button>
    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#refa_modal">Esporta REFA </button>
   </div>
	<?php foreach($petty_cash->books as $book_id=>$book):?>
	<div class="col-md-2 petty_cash_balance <?php echo $book->balance<0? 'text-danger' : ''?>">
   	<h3><?php echo $book->label?></h3>
      <h4><?php echo App::currency($book->balance)?></h4>
   </div>
   <?php endforeach;?>
</div>
<hr>
<!-- Modal Prima Nota -->
<div class="modal fade" id="petty_cash_modal" tabindex="-1" role="dialog" aria-labelledby="petty_cash_modal_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="petty_cash_modal_modal_label">Esporta Prima Nota</h4>
      </div>
      <form action="/archy/app.php" class="fileDownloadForm" method="get">
         <input type="hidden" name="do_action" value="export_petty_cash">
         <div class="modal-body">
            <div class="row">
               <div class="col-xs-6">
                  <div class="form-group">
                     <label for="range_start">Da data</label>
                     <div class="input-group">
                        <input type="text" class="form-control date" name="range_start" placeholder="__/__/____" maxlength="10" id="range_start">
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
                     </div>
                     <p class="help-block">Lascia bianco per non impostare una data di inizio</p>
                  </div>
               </div>
               <div class="col-xs-6">
                  <div class="form-group">
                     <label for="range_end">A data</label>
                     <div class="input-group">
                        <input type="text" class="form-control date" name="range_end" placeholder="__/__/____" maxlength="10" id="range_end">
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
                     </div>
                     <p class="help-block">Lascia bianco per non impostare una data di fine</p>
                  </div>
               </div>
               
               <div class="col-xs-12">
                  <div class="form-group">
                     <label for="range_end">Voce/i di costo</label>
                     <div class="input-group">
                        <select class="form-control select2" name="cost_centres_id[]" id="cost_centres" multiple style="width:100%">
                        <?php foreach($petty_cash->cost_centres as $cost_centre_id=>$cost_centre):?>
                        	<option value="<?php echo $cost_centre_id?>" selected><?php echo $cost_centre->label?></option>
                        <?php endforeach?>
                        </select>
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-list"></span></div>
                     </div>
                  </div>
               </div>
               
               <div class="col-xs-12">
                  <div class="form-group">
                     <label for="range_end">Libro/i</label>
                     <div class="input-group">
                        <select class="form-control select2" name="books_id[]" id="books" multiple style="width:100%">
                        <?php foreach($petty_cash->books as $book_id=>$book):?>
                        	<option value="<?php echo $book_id?>" selected><?php echo $book->label?></option>
                        <?php endforeach?>
                        </select>
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-book"></span></div>
                     </div>
                  </div>
               </div>       
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
            <button type="submit" class="btn btn-primary" id="export_petty_cash" onClick="$('#petty_cash_modal').modal('hide');">Esporta</button>
         </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->


<!-- Modal REFA -->
<div class="modal fade" id="refa_modal" tabindex="-1" role="dialog" aria-labelledby="refa_modal_label" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="refa_modal_modal_label">Esporta REFA</h4>
      </div>
      <form action="/archy/app.php" class="fileDownloadForm" method="get">
         <input type="hidden" name="do_action" value="export_refa">
         <div class="modal-body">
            <div class="row">
               <div class="col-xs-6">
                  <div class="form-group">
                     <label for="range_start_refa">Da data</label>
                     <div class="input-group">
                        <input type="text" class="form-control date" name="range_start_refa" placeholder="__/__/____" maxlength="10" id="range_start_refa">
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
                     </div>
                     <p class="help-block">Lascia bianco per non impostare una data di inizio</p>
                  </div>
               </div>
               <div class="col-xs-6">
                  <div class="form-group">
                     <label for="range_end_refa">A data</label>
                     <div class="input-group">
                        <input type="text" class="form-control date" name="range_end_refa" placeholder="__/__/____" maxlength="10" id="range_end_refa">
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-calendar"></span></div>
                     </div>
                     <p class="help-block">Lascia bianco per non impostare una data di fine</p>
                  </div>
               </div>
               
               <div class="col-xs-12">
                  <div class="form-group">
                     <label for="range_end">Voce/i di costo</label>
                     <div class="input-group">
                        <select class="form-control select2" name="cost_centres_id[]" id="cost_centres" multiple style="width:100%">
                        <?php foreach($petty_cash->cost_centres as $cost_centre_id=>$cost_centre):?>
                        	<option value="<?php echo $cost_centre_id?>" selected><?php echo $cost_centre->label?></option>
                        <?php endforeach?>
                        </select>
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-list"></span></div>
                     </div>
                  </div>
               </div>
               
               <div class="col-xs-12">
                  <div class="form-group">
                     <label for="range_end">Libro/i</label>
                     <div class="input-group">
                        <select class="form-control select2" name="books_id[]" id="books" multiple style="width:100%">
                        <?php foreach($petty_cash->books as $book_id=>$book):?>
                        	<option value="<?php echo $book_id?>" selected><?php echo $book->label?></option>
                        <?php endforeach?>
                        </select>
                        <div class="input-group-addon triggerCalendar"><span class="glyphicon glyphicon-book"></span></div>
                     </div>
                  </div>
               </div>       
            </div>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annulla</button>
            <button type="submit" class="btn btn-primary" id="export_refa" onClick="$('#refa_modal').modal('hide');">Esporta</button>
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