<?php
if(@$_GET['thread_id']!=''):
	
	$thread=new Support($_GET['thread_id'],'support');
	?>
   <div id="edit_buttons">
   	<a class="btn btn-primary" href="app.php?view=customer_care"><span class="glyphicon glyphicon-arrow-left"></span> Elenco Ticket</a>
   </div>
	<h3 style="margin-top:0">[<?php echo $thread->type?>] <?php echo $thread->subject?></h3>
	<h4><?php echo $thread->opener.' - '.$thread->domain.' - '.$thread->date?></h4>
	<hr>
	<div class="chat">
	<?php
	
	foreach($thread->posts as $post):
		?>
      <div class="post <?php echo $post->is_customer? 'customer':'operator'?>">
  
		 <div class="alert alert-warning" role="alert">
			<h4 class="alert-heading fw-semi-bold"><?php echo $post->user?></h4>
			<p><?php echo $post->text?></p>
			<hr />
  			<p class="mb-0"><?php echo $post->date?></p>
		</div>
      </div>
      <?php
	endforeach;
	
	?></div><?php
	
	?>
   <hr>
   <form method="post" action="app.php?do_action=ticket_reply">
	<div class="mb-3">
		<label for="reply">Rispondi al ticket</label>
		<div class="alert alert-info" style="font-size:0.9em">
			<strong>Nota:</strong> la risposta verrà inviata al cliente tramite e-mail.
		</div>
		</div>
		<div class="mb-3">
				<textarea rows="7" class="form-control" placeholder="Rispondi al ticket" name="reply" id="reply"></textarea>
		</div>
        <button type="submit" class="btn btn-success">Invia risposta <span class="far fa-arrow-alt-circle-right"></span></button>
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
	
else:
	
	$support=new Support();
	$header='
		<tr>
			<th>ID</th>
			<th>Status</th>
			<th>Utente</th>
			<th>Dominio</th>
			<th>Tipologia</th>
			<th>Oggetto</th>
			<th>Richiesta</th>
			<th>E-mail Notifiche</th>
			<th>Data</th>
			<th>Azioni</th>
		</tr>
	';
	?>
   <div id="edit_buttons">
   	<a class="btn btn-primary" href="javascript:void(0)" onClick="location.reload()"><span class="fas fa-sync"></span> Aggiorna</a>
   </div>
	<h3 style="margin-top:0">Tickets</h3>
	<hr>
		<table class="table table-bordered table-striped table-condensed table-hover" id="tickets">
			<thead><?php echo $header?></thead>
			<tbody><?php echo $support->tickets_list()?></tbody>
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
	<?php

endif;
?>
