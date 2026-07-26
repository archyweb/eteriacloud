<?php
$notice = new Notice();
$header='
	<tr>
		<th>ID</th>
		<th>Titolo</th>
		<th>Stato</th>
		<th>Priorita</th>
		<th>Categoria</th>
		<th>Origine</th>
		<th>Data</th>
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
						<a class="btn btn-falcon-default" href="javascript:void(0)" id="notices_mark_all_read" data-toggle="tooltip" data-placement="bottom" title="Segna tutte come gia lette"><span class="fas fa-check-double"></span> Segna tutte come gia lette</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-12 pl-4 w-100">
		<h4 class="d-inline-block">Notifiche</h4>
	</div>
</div>

<table class="table mb-0 data-table fs-10 table-striped overflow-hidden" id="notices_table">
	<thead><?php echo $header; ?></thead>
	<tbody><?php echo $notice->table_rows_html(); ?></tbody>
	<tfoot><?php echo $header; ?></tfoot>
</table>

<script type="text/javascript">
window.onload=function(){
	$('#notices_table').DataTable({
		pageLength:25,
		scrollX:true,
		scrollCollapse:true,
		order: [[0,'desc']],
		language: data_table_it_IT
	});
	
	$('#notices_mark_all_read').on('click', function(){
		$.ajax({
			type: "POST",
			url: "/archy/app.php?do_action=mark_notices_as_read",
			cache: false,
			success: function(){
				location.reload();
			}
		});
	});
}
</script>
