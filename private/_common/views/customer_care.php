<?php
if(@$_GET['thread_id']!=''):

	$thread=new Support($_GET['thread_id'],'support');
	$ticket_subject=htmlspecialchars($thread->subject,ENT_QUOTES,'UTF-8');
	$ticket_type=htmlspecialchars($thread->type,ENT_QUOTES,'UTF-8');
	$ticket_opener=htmlspecialchars($thread->opener,ENT_QUOTES,'UTF-8');
	$ticket_domain=htmlspecialchars($thread->domain,ENT_QUOTES,'UTF-8');
	$ticket_date=htmlspecialchars($thread->date,ENT_QUOTES,'UTF-8');
	$ticket_closed=(bool)$thread->thread_closed;
	?>
	<div class="row g-3">
		<div class="col-12 ticket-detail-toolbar">
			<div class="d-flex justify-content-end mt-1 mt-sm-0">
				<div class="btn-toolbar" role="toolbar" aria-label="Eteria toolbars">
					<div class="btn-group me-2 mb-2" role="group">
						<a class="btn btn-secondary" target="_self" href="/archy/app.php?view=customer_care"><span class="fas fa-arrow-left"></span> Elenco Ticket</a>
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
				<li><?php echo $ticket_domain?></li>
				<li><?php echo $ticket_date?></li>
			</ul>
		</div>
		<div class="col-12">
			<form class="ticket-reply-panel" method="post" action="/archy/app.php?do_action=ticket_reply">
				<label class="form-label" for="reply">Rispondi al ticket</label>
				<div class="alert alert-info ticket-reply-note" role="alert">
					<strong>Nota:</strong> la risposta verra inviata al cliente tramite e-mail.
				</div>
				<textarea rows="3" class="form-control" placeholder="Scrivi una risposta..." name="reply" id="reply"></textarea>
				<div class="ticket-reply-actions">
					<button type="submit" class="btn btn-primary"><span class="fas fa-paper-plane me-1"></span> Invia risposta</button>
				</div>
				<input type="hidden" name="thread_id" value="<?php echo (int)$_GET['thread_id']?>">
			</form>
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
			<th>Attenzione</th>
			<th>Utente</th>
			<th>Dominio</th>
			<th>Tipologia</th>
			<th>Oggetto</th>
			<th>Richiesta</th>
			<th>Messaggi</th>
			<th>E-mail Notifiche</th>
			<th>Apertura</th>
			<th>Ultima attivita</th>
			<th>Azioni</th>
		</tr>
	';
	?>
	<div class="row g-3">
		<div class="col-12 mb-3">
			<div class="row">
				<div class="d-flex justify-content-end mt-1 mt-sm-0">
					<div class="btn-toolbar" role="toolbar" aria-label="Eteria toolbars">
						<div class="btn-group me-2 mb-2" role="group">
							<a class="btn btn-secondary" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Aggiorna scheda"><span class="fas fa-sync"></span> Ricarica</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-12 pl-4 w-100">
			<h4 class="d-inline-block">Customer Care</h4>
		</div>
		<div class="col-sm-12">
			<table class="table mb-0 data-table fs-10 table-striped overflow-hidden" id="tickets">
				<thead><?php echo $header?></thead>
				<tbody><?php echo $support->tickets_list()?></tbody>
				<tfoot><?php echo $header?></tfoot>
			</table>
		</div>
	</div>
	<script type="text/javascript">
	window.onload=function(){
		$('#tickets').DataTable({
			pageLength:10,
			scrollX:true,
			scrollCollapse:true,
			order: [[11,'desc']],
			language: data_table_it_IT
		});
	}
	</script>
	<?php

endif;
?>
