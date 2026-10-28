<?php
$notice = new Notice();
$categories = $notice->category_options();
$priorities = $notice->priority_options();
$customers = [];
$customers_path = isset($app->config['core_update_customers_path'])? $app->config['core_update_customers_path'] : 'private/_common/update/customers.csv';
$customers_file = $app->main_root.'/'.ltrim($customers_path,'/');
$customers_file_exists = is_file($customers_file);
if($customers_file_exists && ($handle=fopen($customers_file,'r'))):
	$headers=fgetcsv($handle,0,';');
	if($headers && count($headers)==1 && strpos($headers[0],',')!==false):
		$headers=str_getcsv($headers[0],',');
	endif;
	$headers=array_map('trim',$headers ?: []);
	while(($row=fgetcsv($handle,0,';'))!==false):
		if(count($row)==1 && strpos($row[0],',')!==false):
			$row=str_getcsv($row[0],',');
		endif;
		if(count(array_filter($row,'strlen'))==0):
			continue;
		endif;
		$row=array_slice(array_pad($row,count($headers),''),0,count($headers));
		$customer=array_combine($headers,$row);
		if(!$customer || trim((string)$customer['customer'])==''):
			continue;
		endif;
		$enabled=isset($customer['enabled'])? strtolower(trim((string)$customer['enabled'])) : '1';
		if(in_array($enabled,['0','false','no','off'],true)):
			continue;
		endif;
		$customers[]=$customer;
	endwhile;
	fclose($handle);
endif;
?>

<div class="row g-3">
	<div class="col-12">
		<div class="card">
			<div class="card-header d-flex flex-between-center py-2 border-bottom">
				<h5 class="mb-0">Invia notifica</h5>
				<div class="btn-group" role="group">
					<a class="btn btn-falcon-default btn-sm" href="/archy/app.php?view=notice_broadcasts">
						<span class="fas fa-history me-1"></span> Invii
					</a>
					<a class="btn btn-falcon-default btn-sm" href="/archy/app.php?intent=archy_notices&view=grid">
						<span class="fas fa-list me-1"></span> Elenco
					</a>
				</div>
			</div>
			<div class="card-body">
				<form method="post" action="/archy/app.php?do_action=send_notice" class="row g-3 needs-validation" novalidate="">
					<div class="col-md-4">
						<label class="form-label" for="notice_category">Categoria</label>
						<select class="form-select" id="notice_category" name="category" required>
							<?php foreach($categories as $category_key=>$category): ?>
								<option value="<?php echo htmlspecialchars($category_key,ENT_QUOTES,'UTF-8'); ?>"<?php echo $category_key=='update'? ' selected':''; ?>>
									<?php echo htmlspecialchars($category['label'],ENT_QUOTES,'UTF-8'); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-4">
						<label class="form-label" for="notice_priority">Priorità</label>
						<select class="form-select" id="notice_priority" name="priority" required>
							<?php foreach($priorities as $priority_key=>$priority): ?>
								<option value="<?php echo htmlspecialchars($priority_key,ENT_QUOTES,'UTF-8'); ?>"<?php echo $priority_key=='normal'? ' selected':''; ?>>
									<?php echo htmlspecialchars($priority['label'],ENT_QUOTES,'UTF-8'); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-4">
						<label class="form-label" for="notice_title">Titolo</label>
						<input class="form-control" type="text" id="notice_title" name="title" maxlength="160">
					</div>
					<div class="col-md-4">
						<label class="form-label" for="notice_recipients_mode">Destinatari</label>
						<select class="form-select" id="notice_recipients_mode" name="recipients_mode">
							<option value="local">Solo questo pannello</option>
							<option value="all">Tutti i clienti</option>
							<option value="selected">Clienti selezionati</option>
						</select>
					</div>
					<div class="col-md-8 notice-customers-wrapper d-none">
						<label class="form-label" for="notice_customers">Clienti</label>
						<select class="form-select" id="notice_customers" name="customers[]" multiple size="8">
							<?php foreach($customers as $customer): ?>
								<option value="<?php echo htmlspecialchars($customer['customer'],ENT_QUOTES,'UTF-8'); ?>">
									<?php echo htmlspecialchars($customer['customer'].' - '.$customer['db_name'],ENT_QUOTES,'UTF-8'); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<?php if(count($customers)==0): ?>
							<?php if(!$customers_file_exists): ?>
								<div class="form-text text-warning">File clienti non trovato: <?php echo htmlspecialchars($customers_path,ENT_QUOTES,'UTF-8'); ?></div>
							<?php else: ?>
								<div class="form-text text-warning">Nessun cliente abilitato disponibile in <?php echo htmlspecialchars($customers_path,ENT_QUOTES,'UTF-8'); ?>.</div>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<div class="col-12">
						<label class="form-label" for="notice_message">Testo</label>
						<textarea class="form-control" id="notice_message" name="message" rows="8" required></textarea>
					</div>
					<div class="col-12 d-flex justify-content-end">
						<button class="btn btn-primary" type="submit">
							<span class="fas fa-paper-plane me-1"></span> Invia notifica
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
(function(){
	function toggleNoticeCustomers(){
		var mode = document.getElementById('notice_recipients_mode');
		var wrapper = document.querySelector('.notice-customers-wrapper');
		if(!mode || !wrapper){
			return;
		}
		if(mode.value === 'selected'){
			wrapper.classList.remove('d-none');
		}else{
			wrapper.classList.add('d-none');
		}
	}
	var mode = document.getElementById('notice_recipients_mode');
	if(mode){
		mode.addEventListener('change', toggleNoticeCustomers);
	}
	toggleNoticeCustomers();
})();
</script>
