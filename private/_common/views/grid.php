<?php
// Visulizza GRIGLIA RECORD
$columns=$app->set_listing_columns();

if(!isset($app->intent->exclude_columns)):
	$app->intent->exclude_columns=[];
endif;

// URL file merge catalogo vecchio/sincrono.
// Lo lasciamo per compatibilità, ma nella nuova versione job non viene più chiamato direttamente dal bottone.
//$merge_catalogo_url = '/srv/catalog-pdf/merge-catalog.php';

//echo $app->intent->help['grid'];
?>

<div class="row g-3">
    <div class="col-12 mb-3">
        <div class="row">
            <div class="d-flex justify-content-end mt-1 mt-sm-0">
                <div class="btn-toolbar archy-grid-toolbar" role="toolbar" aria-label="Eteria toolbars">
                    <div class="btn-group me-2 mb-2" role="group">
                        <?php if(isset($app->intent->add)):?>
                            <?php if( (in_array($app->intent->table,$app->admin_edit_only) and $app->is_admin) or !in_array($app->intent->table,$app->admin_edit_only) ):?>
                                <a class="btn btn-success" href="/archy/app.php?intent=<?php echo $app->intent->table?>&view=edit<?php if(isset($app->intent->filter) && $app->intent->filter!=''){echo'&intent_filter='.$app->intent->filter;} ?>"><span class="fas fa-plus-circle"></span> <?php echo $app->intent->add; ?></a>
                            <?php endif?>
                        <?php endif?>
                    </div>
					<div class="dropdown archy-grid-toolbar-more">
                        <button class="btn btn-outline-secondary dropdown-toggle archy-grid-toolbar-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Utilità
                        </button>
                        <div class="dropdown-menu dropdown-menu-end archy-grid-toolbar-actions">
							<div class="btn-group me-2 mb-2" role="group">
								<a class="btn btn-secondary " href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Aggiorna scheda"><span class="fas fa-sync"></span> Ricarica</a>
								<a class="btn btn-secondary" href="javascript:void(0)" onClick="resetGridTable()" data-toggle="tooltip" data-placement="bottom" title="Resetta filtri"><span class="fas fa-backspace"></span> Resetta filtri</a>
								<button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#columnsModal"><span class="fas fa-table"></span> Sel. colonne</button>
							</div>                    

							<div class="btn-group me-2 mb-2" role="group">               
							   <?php if(@$_GET['body_class']!='modal_grid'):?>
								  <a class="btn btn-info export" data-format="excel" data-toggle="tooltip" data-placement="bottom" title="Esporta in Excel" href="javascript:void(0)"><span class="far fa-file-excel"></span> Excel</a>
								  <a class="btn btn-info export" data-format="pdf" data-toggle="tooltip" data-placement="bottom" title="Esporta in PDF" href="javascript:void(0)"><span class="far fa-file-pdf"></span> PDF</a>
								  <a class="btn btn-info" href="#" id="pdf_options" data-bs-toggle="modal" title="Opzioni PDF" data-placement="bottom" data-bs-target="#pdfOptions"
										 data-orientation="P" data-sheet="A4" data-size="12" data-up="50" data-down="70" data-left="50" data-right="50"
								  ><span class="fas fa-cogs"></span></a>
							   <?php endif?>
							</div>

							<?php if ($app->intent->table == 'archy_art_catalog'): ?>

							<div class="btn-group me-2 mb-2" role="group">

								<button type="button" class="btn btn-danger" id="merge_catalogo_pdf">
									<span class="far fa-file-pdf"></span> Catalogo PDF
								</button>

								<a href="/srv/catalog-pdf/last-catalog-pdf.php" target="_blank" id="download_last_generated_pdf" class="btn btn-outline-danger"
								data-toggle="tooltip" data-placement="bottom" title="Apri ultimo catalogo PDF generato"><span class="fas fa-history"></span></a>

							</div>

							<?php endif; ?>

							<div class="btn-group me-2 mb-2" role="group">
								<?php if(isset($app->intent->restore_button_in_grid) && $app->intent->restore_button_in_grid){ ?>
									<?php if ($app->intent->table == 'archy_art_catalog'): ?>
										<button type="button" class="btn btn-warning" id="regenerate_attachments_job" data-rel="<?php echo $app->intent->table; ?>" title="Rigenera allegati">
											<span class="fas fa-sync"></span> Rigenera allegati
										</button>
									<?php else: ?>
										<a class="btn btn-warning regenerates_attachments" data-rel="<?php echo $app->intent->table; ?>" href="javascript:void(0)" title="Rigenera allegati"><span class="fas fa-sync"></span> Rigenera allegati</a>
									<?php endif; ?>
								<?php } ?>
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>	
    </div>

    <div class="col-12 pl-4 w-100">
        <h4 class="d-inline-block"><?php echo $app->intent->title; ?></h4>
    </div>
</div>

<?php
if(isset($app->intent->include_in_grid)):
	require_once $main_root.'/private/_common/'.$app->intent->include_in_grid;
endif;
?>

<?php $header=$app->grid_table_header(); ?>
        
<table class="table mb-0 data-table fs-10 table-striped overflow-hidden" id="grid_table">
    <thead class="bg-200"><?php echo $header; ?></thead>
    <tbody></tbody>
    <tfoot><?php echo $header; ?></tfoot>
</table>

<script>
document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | HELPER PARAMETRI URL
    |--------------------------------------------------------------------------
    */

    function appendQueryParam(url, key, value) {
        const separator = url.indexOf('?') === -1 ? '?' : '&';
        return url + separator + encodeURIComponent(key) + '=' + encodeURIComponent(value);
    }

    /*
    |--------------------------------------------------------------------------
    | RECUPERO ID FILTRATI LATO SERVER
    |--------------------------------------------------------------------------
    | Questa funzione usa la stessa richiesta AJAX della DataTable corrente.
    | In questo modo recupera TUTTI gli ID filtrati, non solo quelli visibili
    | nella pagina corrente.
    */

    function collectAllFilteredIds(callback) {
    if (typeof jQuery === 'undefined' || !jQuery.fn.DataTable) {
        callback([]);
        return;
    }

    let table = jQuery('#grid_table').DataTable();
    let ajaxUrl = table.ajax.url();
    ajaxUrl = appendQueryParam(ajaxUrl, 'ids_only', '1');

    let baseParams = table.ajax.params() || {};
    baseParams.ids_only = 1;

    let allIds = [];
    let start = 0;
    let batchSize = 100;

    function fetchBatch() {
        let params = Object.assign({}, baseParams);
        params.start = start;
        params.length = batchSize;

        jQuery.ajax({
            url: ajaxUrl,
            method: 'POST',
            data: params,
            dataType: 'json',
            success: function (response) {
                if (response && response.error) {
                    alert(
                        'Errore recupero ID filtrati:\n' +
                        response.error +
                        (response.sql ? '\n\nSQL:\n' + response.sql : '')
                    );
                    callback([]);
                    return;
                }

                let ids = [];

                if (response && Array.isArray(response.ids)) {
                    ids = response.ids
                        .map(function (id) {
                            return String(id).replace(/[^0-9]/g, '').trim();
                        })
                        .filter(function (id) {
                            return id && /^[0-9]+$/.test(id);
                        });
                }

                allIds = allIds.concat(ids);

                let total = parseInt(response.recordsFiltered || response.recordsTotal || 0, 10);

                start += batchSize;

                if (ids.length === batchSize && (!total || start < total)) {
                    fetchBatch();
                    return;
                }

                allIds = [...new Set(allIds)];

                console.log('IDS FILTRATI TOTALI', allIds.length, allIds);

                callback(allIds);
            },
            error: function (xhr) {
                alert(
                    'Errore durante il recupero degli ID filtrati.\n\n' +
                    'Blocco start: ' + start + '\n\n' +
                    'Risposta:\n' + xhr.responseText
                );
                callback([]);
            }
        });
    }

    fetchBatch();
}

    /*
    |--------------------------------------------------------------------------
    | CONTROLLO STATO JOB CATALOGO PDF
    |--------------------------------------------------------------------------
    | Dopo la creazione del job, questa funzione interroga periodicamente
    | catalog-job-status.php per sapere se il PDF è pronto.
    */

    function checkCatalogJobStatus(jobId) {

        let button = document.getElementById('merge_catalogo_pdf');

        let interval = setInterval(function () {

            fetch('/srv/catalog-pdf/catalog-job-status.php?job_id=' + encodeURIComponent(jobId))
                .then(function(response) {
                    return response.text();
                })
                .then(function(text) {

                    if (!text || !text.trim()) {
                        clearInterval(interval);
                        alert('catalog-job-status.php ha restituito risposta vuota.');
                        return;
                    }

                    let data;

                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        clearInterval(interval);
                        alert('Risposta NON JSON da catalog-job-status.php:\n\n' + text);
                        return;
                    }

                    if (!data.success) {
                        clearInterval(interval);
                        alert(data.message || 'Errore controllo job.');
                        return;
                    }

                    let job = data.job;
                    let progress = parseInt(job.progress || 0, 10);
                    let total = parseInt(job.total || 0, 10);

                    if (button && total > 0 && job.status !== 'completed') {
                        button.disabled = true;
                        button.classList.remove('btn-success', 'btn-danger');
                        button.classList.add('btn-danger');
                        button.innerHTML = '<span class="fas fa-sync fa-spin"></span> Catalogo PDF ' + progress + ' / ' + total;
                    }

                    if (job.status === 'completed') {
                        clearInterval(interval);

                        if (button) {
                            button.disabled = false;
                            button.classList.remove('btn-danger');
                            button.classList.add('btn-success');
                            button.innerHTML = '<span class="far fa-file-pdf"></span> Catalogo PDF completato';
                        }

                        if (job.result_file) {
                            let downloadButton = document.getElementById('download_last_generated_pdf');

                            if (downloadButton) {
                                downloadButton.href = job.result_file + '?t=' + new Date().getTime();
                                downloadButton.classList.remove('btn-outline-danger');
                                downloadButton.classList.add('btn-success');
                                downloadButton.title = 'Scarica ultimo PDF generato';
                            }
                        }

                        let finalProgress = total > 0 ? total : progress;

                        alert(
                            'Catalogo PDF generato correttamente.\n\n' +
                            'PDF elaborati: ' + finalProgress + ' / ' + total + '\n\n' +
                            'Usa il pulsante verde accanto a Catalogo PDF.'
                        );

                        return;
                    }

                    if (job.status === 'failed') {
                        clearInterval(interval);

                        if (button) {
                            button.disabled = false;
                            button.classList.remove('btn-danger');
                            button.classList.add('btn-warning');
                            button.innerHTML = '<span class="fas fa-exclamation-triangle"></span> Errore Catalogo PDF';
                        }

                        alert(
                            'Errore durante la generazione del Catalogo PDF.\n\n' +
                            (job.log ? job.log : 'Nessun dettaglio disponibile.')
                        );

                        return;
                    }

                })
                .catch(function(error) {
                    clearInterval(interval);

                    if (button) {
                        button.disabled = false;
                    }

                    alert('Errore controllo stato job: ' + error);
                    console.error(error);
                });

        }, 5000);
    }

    /*
    |--------------------------------------------------------------------------
    | BOTTONE CATALOGO PDF
    |--------------------------------------------------------------------------
    | Versione 1.0 job asincrono senza cron:
    | 1. recupera tutti gli ID filtrati;
    | 2. crea un job in archy_jobs;
    | 3. il worker viene avviato in background da create-catalog-job.php;
    | 4. il browser controlla lo stato ogni 5 secondi.
    */

    const mergeButton = document.getElementById('merge_catalogo_pdf');

    if (mergeButton) {
        mergeButton.addEventListener('click', function () {

            collectAllFilteredIds(function (ids) {

                if (!ids.length) {
                    alert('Nessun ID trovato nelle righe filtrate.');
                    return;
                }

                if (!confirm('Vuoi creare il Catalogo PDF per ' + ids.length + ' record filtrati?')) {
                    return;
                }

                mergeButton.disabled = true;
                mergeButton.classList.remove('btn-success', 'btn-warning');
                mergeButton.classList.add('btn-danger');
                mergeButton.innerHTML = '<span class="fas fa-sync fa-spin"></span> Catalogo PDF 0 / ' + ids.length;

                let idsString = ids.join(',');

                let formData = new FormData();
                formData.append('ids', idsString);

                fetch('/srv/catalog-pdf/create-catalog-job.php', {
                    method: 'POST',
                    body: formData
                })
                
				.then(function(response) {
    return response.text();
})
.then(function(text) {

    console.log('RISPOSTA RAW create-catalog-job.php:', text);

    if (!text || !text.trim()) {
        alert(
            'create-catalog-job.php ha restituito risposta vuota.\n\n' +
            'Controllare error_log PHP.'
        );
        return;
    }

    let data;

    try {
        data = JSON.parse(text);
    } catch (e) {

        alert(
            'Risposta NON JSON da create-catalog-job.php:\n\n' +
            text
        );

        return;
    }

    if (!data.success) {
        mergeButton.disabled = false;
        mergeButton.innerHTML = '<span class="far fa-file-pdf"></span> Catalogo PDF';
        alert(data.message || 'Errore creazione job Catalogo PDF.');
        return;
    }

    alert(
        'Catalogo PDF in generazione.\n\n' +
        'Record: ' + data.total + '\n' +
        'Job ID: ' + data.job_id
    );

    checkCatalogJobStatus(data.job_id);

})
				
				
                .catch(function(error) {
                    mergeButton.disabled = false;
                    mergeButton.innerHTML = '<span class="far fa-file-pdf"></span> Catalogo PDF';
                    alert('Errore AJAX creazione job: ' + error);
                    console.error(error);
                });

            });

        });
    }



/*
|--------------------------------------------------------------------------
| NUOVO RIGENERA ALLEGATI - WORKER BATCH
|--------------------------------------------------------------------------
*/

const regenerateButton =
document.getElementById(
    'regenerate_attachments_job'
);

if(regenerateButton){

    regenerateButton.addEventListener(
        'click',
        function(){

        collectAllFilteredIds(
            function(ids){

            if(!ids.length){

                alert(
                    'Nessun ID trovato'
                );

                return;
            }

            if(
                !confirm(
                    'Vuoi rigenerare '+
                    ids.length+
                    ' allegati?'
                )
            ){
                return;
            }

            regenerateButton.disabled=true;

            regenerateButton.innerHTML=
            '<span class="fas fa-sync fa-spin"></span> Avvio...';

            let formData=
            new FormData();

            formData.append(
                'ids',
                ids.join(',')
            );

            formData.append(
                'intent',
                '<?php echo $app->intent->table; ?>'
            );

            fetch(
                '/srv/catalog-pdf/create-regenerate-job.php',
                {
                    method:'POST',
                    body:formData
                }
            )

            .then(
                response=>response.json()
            )

            .then(
                data=>{

                if(
                    !data.success
                ){

                    regenerateButton.disabled=false;

                    alert(
                        data.message||
                        'Errore job'
                    );

                    return;
                }

                processRegenerateBatch(
                    data.job_id,
                    data.total
                );

            });

        });

    });

}

function processRegenerateBatch(
    jobId,
    total
){

fetch(
'/srv/catalog-pdf/run-regenerate-jobs.php?job_id='+
encodeURIComponent(jobId)+
'&_='+
Date.now()
)

.then(
response=>response.json()
)

.then(
data=>{

let progress=
parseInt(
data.progress||0
);

let realTotal=
parseInt(
data.total||
total||
0
);

regenerateButton.innerHTML=
'<span class="fas fa-sync fa-spin"></span> Rigenera '+progress+' / '+realTotal;

if(
progress>=realTotal
){

regenerateButton.disabled=false;

regenerateButton.innerHTML=
'<span class="fas fa-check"></span> Completato';

alert(
'Rigenerazione completata'
);

return;
}

setTimeout(
function(){

processRegenerateBatch(
jobId,
realTotal
);

},
1000
);

}

);

}


});
</script>