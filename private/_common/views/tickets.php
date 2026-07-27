<?php
if(@$_GET['thread_id']!=''):
	
	$thread=new Support($_GET['thread_id'],'customer');
	$ticket_subject=htmlspecialchars($thread->subject,ENT_QUOTES,'UTF-8');
	$ticket_type=htmlspecialchars($thread->type,ENT_QUOTES,'UTF-8');
	$ticket_opener=htmlspecialchars($thread->opener,ENT_QUOTES,'UTF-8');
	$ticket_date=htmlspecialchars($thread->date,ENT_QUOTES,'UTF-8');
	$ticket_closed=(bool)$thread->thread_closed;
	?>
	<div class="row g-3">
		<div class="col-12 ticket-detail-toolbar">
			<div class="d-flex justify-content-end mt-1 mt-sm-0">
				<div class="btn-toolbar" role="toolbar" aria-label="Eteria toolbars">
					<div class="btn-group me-2 mb-2" role="group">
						<a class="btn btn-secondary" target="_self" href="/archy/app.php?view=tickets"><span class="fas fa-arrow-left"></span> Elenco Ticket</a>
						<a class="btn btn-falcon-default" href="javascript:void(0)" onClick="location.reload();"><span class="fas fa-sync"></span> Aggiorna</a>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 ticket-detail-title">
			<h4><?php echo $ticket_subject?></h4>
			<ul class="ticket-meta">
				<li><span class="badge bg-info me-1"><?php echo $ticket_type?></span></li>
				<li><span class="badge <?php echo $ticket_closed? 'bg-success':'bg-warning text-dark'?>"><?php echo $ticket_closed? 'Chiuso':'Aperto'?></span></li>
				<li><?php echo $ticket_opener?></li>
				<li><?php echo $ticket_date?></li>
			</ul>
		</div>
		<div class="col-12">
			<?php if(!$ticket_closed):?>
			<form class="ticket-reply-panel" method="post" action="/archy/app.php?do_action=ticket_user_reply">
				<label class="form-label" for="reply">Rispondi al ticket</label>
				<textarea rows="3" class="form-control" placeholder="Scrivi una risposta..." name="reply" id="reply"></textarea>
				<div class="ticket-reply-actions">
					<button type="submit" class="btn btn-primary"><span class="fas fa-paper-plane me-1"></span> Invia risposta</button>
				</div>
				<input type="hidden" name="thread_id" value="<?php echo (int)$_GET['thread_id']?>">
			</form>
			<?php else:?>
			<div class="alert alert-warning ticket-closed-alert" role="alert">
				Questo ticket e chiuso, non e possibile inviare nuove risposte.
			</div>
			<?php endif?>
			<div class="ticket-conversation">
				<?php foreach($thread->posts as $post):
					$is_customer=(bool)$post->is_customer;
					$post_user=htmlspecialchars($post->user,ENT_QUOTES,'UTF-8');
					$post_date=htmlspecialchars($post->date,ENT_QUOTES,'UTF-8');
					?>
					<div class="ticket-message <?php echo $is_customer? 'ticket-message-customer':'ticket-message-operator'?>">
						<?php if($is_customer):?>
						<div class="ticket-avatar ticket-avatar-customer" aria-hidden="true">
							<span class="fas fa-user"></span>
						</div>
						<?php endif?>
						<div class="ticket-bubble">
							<div class="ticket-message-meta">
								<span class="ticket-message-user"><?php echo $post_user?></span>
								<span><?php echo $post_date?></span>
							</div>
							<div class="ticket-message-text">
								<?php echo $post->text?>
							</div>
						</div>
						<?php if(!$is_customer):?>
						<div class="ticket-avatar ticket-avatar-operator" aria-hidden="true">
							<img src="/admin_template_child/img/logo_archy_art.png" alt="">
						</div>
						<?php endif?>
					</div>
				<?php endforeach;?>
			</div>
		</div>
	</div>
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
	
else:
	
	$support=new Support();
	$header='
		<tr>
			<th>ID</th>
			<th>Status</th>
			<th>Aggiornamento</th>
			<th>Utente</th>
			<th>Tipologia</th>
			<th>Oggetto</th>
			<th>Messaggi</th>
			<th>E-mail Notifiche</th>
			<th>Apertura</th>
			<th>Ultima attivita</th>
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
             <div class="row">
                 <div class="d-flex justify-content-end mt-1 mt-sm-0">
                     <div class="btn-toolbar" role="toolbar" aria-label="Eteria toolbars">
                         <div class="btn-group me-2 mb-2" role="group">
                             <a class="btn btn-secondary" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Aggiorna scheda"><span class="fas fa-sync"></span> Ricarica</a>
                             <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ticketsModal"><span class="fas fa-plus"></span> Apri Ticket</button>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="col-12 pl-4 w-100">
             <h4 class="d-inline-block">Tickets</h4>
         </div>
         <div class="col-sm-12">
             <table class="table mb-0 data-table fs-10 table-striped overflow-hidden" id="tickets">
                     <thead><?php echo $header?></thead>
                     <tbody><?php echo $support->user_tickets()?></tbody>
                     <tfoot><?php echo $header?></tfoot>
             </table>
             <script type="text/javascript">
             window.onload=function(){
                     $('#tickets').DataTable({
                             pageLength:10,
                             scrollX:true,
                             scrollCollapse:true,
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
