<?php
if(@$_GET['thread_id']!=''):
	
	$thread=new Support($_GET['thread_id']);
	?>
   <div id="edit_buttons">
   	<a class="btn btn-primary" href="app.php?view=tickets"><span class="glyphicon glyphicon-arrow-left"></span> Elenco Ticket</a>
   	<a class="btn btn-default" href="javascript:void(0)" onClick="location.reload();"><span class="glyphicon glyphicon-refresh"></span> Aggiorna</a>
   </div>
	<h3 style="margin-top:0">[<?php echo $thread->type?>] <?php echo $thread->subject?></h3>
	<h4><?php echo $thread->opener.' - '.$thread->date?></h4>
	<hr>
	<div class="chat">
	<?php
	
	foreach($thread->posts as $post):
		?>
      <div class="post <?php echo $post->is_customer? 'operator':'customer'?>">
         <bubble>
            <h4><?php echo $post->user?></h4>
            <div class="text">
               <?php echo $post->text?>
            </div>
         </bubble>
      </div>
      <?php
	endforeach;
	
	?></div><?php
	
	if(!$thread->thread_closed):
	?>
   <hr>
   <form method="post" action="app.php?do_action=ticket_user_reply" style="position:relative;">
   	<textarea rows="1" cols="80" class="form-control" placeholder="Rispondi al ticket" name="reply" id="reply" style="resize: none;
    overflow: hidden;
    min-height: 50px;
    padding-right:55px;"></textarea>
      <button type="submit" class="btn btn-success" style="position:absolute;right:0;top:0;bottom:0;width:45px;"><span class="glyphicon glyphicon-arrow-right"></span></button>
      <input type="hidden" name="thread_id" value="<?php echo $_GET['thread_id']?>">
   </form>
		<script type="text/javascript">
		window.onload=function(){
			$('#reply').keyup(function(){
				var element=$(this).get(0);
				element.style.height = "5px";
				element.style.height = (element.scrollHeight)+"px";
			});
			
		}
		</script>
   <?php
	endif;
	
else:
	
	$support=new Support();
	$header='
		<tr>
			<th>ID</th>
			<th>Status</th>
			<th>Utente</th>
			<th>Tipologia</th>
			<th>Oggetto</th>
			<th>E-mail Notifiche</th>
			<th>Data</th>
			<th>Azioni</th>
		</tr>
	';
	?>
   
   <?php if($app->options['customer_care']['phone']['number']!=''):?>
   <h3>Servizio clienti</h2>
   <hr>
   <h4>Telefono: <?php echo $app->options['customer_care']['phone']['number']?></h4>
   <?php echo $app->options['customer_care']['phone']['timetables']?>
   <hr>
   <?php endif?>
   
   <div class="row g-3">
        <div class="col-12 mb-3">
             <div class="row justify-content-center justify-content-sm-between">
                 <div class="col-sm-auto text-center">
                     <h5 class="d-inline-block p-2">Tickets</h5>
                 </div>
                 <div class="col-sm-auto d-flex justify-content-center mt-1 mt-sm-0">
                     <div class="btn-group" role="group">
                         <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ticketsModal"><span class="fas fa-plus"></span> Apri Ticket </button>
                     </div>
                 </div>	
             </div>
         </div>
         <div class="col-sm-12">
             <table class="table mb-0 data-table fs-10 table-striped" id="tickets">
                     <thead><?php echo $header?></thead>
                     <tbody><?php echo $support->user_tickets()?></tbody>
                     <tfoot><?php echo $header?></tfoot>
             </table>
             <script type="text/javascript">
             window.onload=function(){
                     $('#tickets').DataTable({
                             pageLength:10,
                             order: [[0,'desc']],
                             language: data_table_it_IT
                     });
             }
             </script>
         </div>
   </div>
	<?php

endif;
?>
<!-- Modal -->
<div class="modal fade" id="ticketsModal" tabindex="-1" role="dialog" aria-labelledby="ticketsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content position-relative">
    	<form method="post" action="/archy/app.php?do_action=open_ticket">
            
             <div class="modal-header">
           <h4 class="modal-title">Contatta il servizio clienti</h4>
         </div>
         <div class="modal-body">
            
            <div class="form-group">
               <label class="form-label" for="assistance_type">Tipo di assistenza</label>
               <select class="form-select" name="assistance_type" id="assistance_type">
                  <option value="tech">Assistenza tecnica</option>
                  <option value="admin">Assistenza amministrativa</option>
               </select>
            </div>

            <div class="form-group">
               <label class="form-label" for="assistance_reply">Invia notifiche all'indirizzo</label>
               <input type="text" class="form-control" name="assistance_reply" id="assistance_reply" placeholder="user@email.com">
            </div>
            
            <div class="form-group">
               <label class="form-label" for="assistance_subject">Oggetto</label>
               <input type="text" class="form-control" name="assistance_subject" id="assistance_subject">
            </div>
            
            <div class="form-group">
               <label class="form-label" for="assistance_request">Richiesta</label>
               <textarea class="form-control" name="assistance_request" id="assistance_request" rows="4" cols="80"></textarea>
            </div>
         </div>
         <div class="modal-footer">
             <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Apri Ticket</button>
         </div>
    	</form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->