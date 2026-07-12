<?php
if(@$_GET['thread_id']!=''):
	
	$thread=new Support($_GET['thread_id']);
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
	
	?>
   <hr>
   <form method="post" action="app.php?do_action=ticket_reply" style="position:relative;">
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
   	<a class="btn btn-primary" href="javascript:void(0)" onClick="location.reload()"><span class="glyphicon glyphicon-refresh"></span> Aggiorna</a>
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