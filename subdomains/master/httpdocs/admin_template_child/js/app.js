//iframe detect


if (self === top) {
    //location.href='/'+location.search;
}


/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - UPLOAD NATIVO GESTIONALE
|--------------------------------------------------------------------------
|
| Escludiamo la modale dei campi aggiuntivi.
| Quella usa upload_extrafield.php e NON store_files.php.
|
*/
document.querySelectorAll('#editForm input[type="file"]:not(#extra_upload_file)').forEach(input => {

    input.addEventListener("change", function () {
        
        const fileInput = this;
        let id = document.querySelector('[name="intent_id"]').value;
        
        if (!fileInput.files.length) return;
        
        // 👉 Se NON c'è ID → salva prima
        if (!id) {
            // 1. salva il form
            $.ajax({
                url: '/archy/real_save.php',
                method: 'POST',
                data: $('#editForm').serialize(),
                success: function (response) {
                    //console.log('ID CREATO'+response);
                    let newId = response;
                    document.querySelector('[name="intent_id"]').value = newId;
                    document.querySelector('[name="action"]').value = 'update';
                    
                    addUploadButton(fileInput, newId);
                    
                    // 2. ora carica immagine
                    uploadImage(fileInput, newId);
                }
            });
        } else {        
            
            addUploadButton(fileInput, id);
            
            uploadImage(fileInput, id);
        }
    });

});

function addUploadButton(input, id) {

    const files = input.files;
    if (!files.length) return;

    const intent = document.querySelector('[name="intent"]').value;
    const totalFiles = files.length;

    const prefix = intent + '_';

    const fieldName = input.id.startsWith(prefix)
        ? input.id.slice(prefix.length)
        : input.id;

    const data_table = intent;
    const data_field = fieldName;

    const hiddenName = `file_target_${fieldName}`;
    const hidden = document.querySelector(`[name="${hiddenName}"]`);

    // 🔥 cerca wrapper esistente
    let wrapper = input.closest('.input-group');

    // 👉 se non esiste → crealo
    if (!wrapper) {
        wrapper = document.createElement('div');
        wrapper.className = 'input-group';

        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);
    }

    // 🔥 cerca bottone esistente
    let button = wrapper.querySelector('.upload-btn');

    if (!button) {

        // 👉 crea bottone
        button = document.createElement('button');
        button.className = 'btn btn-outline-secondary upload-btn';
        button.type = 'button';

        button.setAttribute('data-bs-toggle', 'modal');
        button.setAttribute('data-bs-target', '#uploads_modal');
        button.setAttribute('data-table', data_table);
        button.setAttribute('data-location', hidden ? hidden.value : '');
        button.setAttribute('data-id', id);
        button.setAttribute('data-field', data_field);

        // 👉 contatore iniziale
        button.innerHTML = `<i class="far fa-file"></i><sup class="file_counter">${totalFiles}</sup>`;

        wrapper.appendChild(button);

    } else {

        // 🔥 aggiorna contatore esistente
        let counter = button.querySelector('.file_counter');

        if (counter) {
            let current = parseInt(counter.innerText) || 0;
            current += totalFiles;
            counter.innerText = current;
        } else {
            // fallback (se manca il sup)
            button.innerHTML = `<i class="far fa-file"></i><sup class="file_counter">${totalFiles}</sup>`;
        }

        // 👉 aggiorna anche id (nel caso sia stato appena creato)
        button.setAttribute('data-id', id);
    }
}

function uploadImage(input, id) {

    if (input.id === 'extra_upload_file') {
        console.warn('Upload campi aggiuntivi ignorato da app.js');
        return;
    }    
    const files = input.files;
    if (!files.length) return;

    const formData = new FormData();
    const totalFiles = input.files.length;

    const inputId = input.id;
    const intent = document.querySelector('[name="intent"]').value;
    formData.append("intent", intent);
    
    const prefix = intent + '_';
    const fieldName = inputId.startsWith(prefix)
    ? inputId.slice(prefix.length)
    : inputId;
    
    // 🔥 2. ricavo nome campo (tolgo intent_)
    // es: archy_art_catalog_foto_ufficiale → foto_ufficiale
    const fieldFullName = input.name;
    //const fieldName = fieldFullName.replace(intent + '_', '');

    // 🔥 3. trovo hidden collegato
    const hiddenName = `file_target_${fieldName}`;
    const hidden = document.querySelector(`[name="${hiddenName}"]`);
    //console.log(hiddenName);
    if (hidden) {
      formData.append(hidden.name, hidden.value);
    }

    // 🔥 4. aggiungo file
    for (let file of files) {
      formData.append(fieldFullName, file);
    }

    // 🔥 campi che vuoi passare
    formData.append("action", document.querySelector('[name="action"]').value);
    formData.append("real_save_id", id);

    fetch("/archy/store_files.php", {
      method: "POST",
      body: formData
    })
    .then(res => res.json())
    .then(data => {
      console.log("Upload completato:", data);

      // reset solo questo input
      input.value = "";
    })
    .catch(err => console.error(err));
}

function init_tinymce() {

    if (typeof(tinymce) == 'undefined') {
        return;
    }

   const fallbackFontFormats =
    "Roboto=roboto; " +
    "Helvetica=helvetica; " +
    "Arial=arial,helvetica,sans-serif; " +
    "Times New Roman=times new roman,times; " +
    "Courier New=courier new,courier; " +
    "Montserrat=montserrat; " +
    "Lora=lora; " +
    "Open Sans=open sans";

    function initTinyWithFonts(fontFormats) {

        tinymce.init({
            selector: 'textarea.tinymce',
            height: 400,
            menubar: 'file edit view insert format tools table tc help',
            plugins: [
                'advlist autolink lists link charmap print preview anchor',
                'searchreplace visualblocks code fullscreen',
                'table paste code wordcount'
            ],
            toolbar: 'undo redo | fontselect fontsizeselect formatselect | ' +
            'bold italic underline forecolor backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat',
            font_formats: fontFormats || fallbackFontFormats,
            fontsize_formats:
            "8pt 9pt 10pt 11pt 12pt 14pt 16pt 18pt 20pt 24pt 28pt 32pt 36pt 48pt",
            content_style: 'body { font-family:Roboto,Helvetica,Arial,sans-serif; font-size:14px }'
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHY MEDIA - FONT PDF DINAMICI PER TINYMCE
    |--------------------------------------------------------------------------
    |
    | Prima prova a leggere la lista font realmente disponibili lato PDF.
    | Se l'endpoint non è presente o restituisce errore, resta attivo il fallback
    | sicuro con i font già verificati nel sistema.
    |
    */
    if (window.ARCHY_PDF_FONT_FORMATS) {
        initTinyWithFonts(window.ARCHY_PDF_FONT_FORMATS);
        return;
    }

    fetch('/archy/pdf_fonts.php', { cache: 'no-store' })
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data && data.font_formats) {
                window.ARCHY_PDF_FONT_FORMATS = data.font_formats;
                initTinyWithFonts(data.font_formats);
            } else {
                window.ARCHY_PDF_FONT_FORMATS = fallbackFontFormats;
                initTinyWithFonts(fallbackFontFormats);
            }
        })
        .catch(function() {
            window.ARCHY_PDF_FONT_FORMATS = fallbackFontFormats;
            initTinyWithFonts(fallbackFontFormats);
        });
}
if (typeof(tinymce) != 'undefined') {
    init_tinymce();
}

function sortFunction(a, b) {
    if (a[1] === b[1]) {
        return 0;
    }
    else {
        return (a[1] < b[1]) ? -1 : 1;
    }
}

function resetGridTable(){
    var table = $('#grid_table').DataTable();

    table.state.clear();

    localStorage.removeItem('DataTables_' + intent + "_" + username);
    sessionStorage.removeItem('DataTables_' + intent + "_" + username);

    window.location.reload();	
}

// Grid
var data_table_it_IT=
{
    "sEmptyTable":     "Nessun dato presente nella tabella",
    "sInfo":           "Mostrando da _START_ a _END_ di _TOTAL_",
    "sInfoEmpty":      "Mostrando da 0 a 0 di 0 elementi",
    "sInfoFiltered":   "(filtrati da _MAX_ elementi totali)",
    "sInfoPostFix":    "",
    "sInfoThousands":  ".",
    "sLengthMenu":     "Visualizza _MENU_ elementi",
    "sLoadingRecords": "Caricamento...",
    "sProcessing":     "Elaborazione...",
    "sSearch":         "Cerca:",
    "sZeroRecords":    "La ricerca non ha prodotto alcun risultato.",
    "oPaginate": {
        "sFirst":      "Inizio",
        "sPrevious":   "<span class='fas fa-step-backward'></span>",
        "sNext":       "<span class='fas fa-step-forward'></span>",
        "sLast":       "Fine"
    },
    "oAria": {
        "sSortAscending":  ": attiva per ordinare la colonna in ordine crescente",
        "sSortDescending": ": attiva per ordinare la colonna in ordine decrescente"
    }
}
var table;
function archyGetRecordIdFromAction($el) {
    var id = $el.data('id');

    if (!id && $el.attr('href')) {
        var match = $el.attr('href').match(/[?&]id=([0-9]+)/);
        if (match) {
            id = match[1];
        }
    }

    return id;
}

$(document).on('click', '#grid_table .btn-edit, #grid_table .btn-view, #grid_table a[href*="view=edit"], #grid_table a[href*="view=view"]', function() {
    var id = archyGetRecordIdFromAction($(this));

    if (!id || typeof intent === 'undefined') {
        return;
    }

    sessionStorage.setItem('archy_return_intent', intent);
    sessionStorage.setItem('archy_return_record_id', id);

    if (typeof table !== 'undefined' && table) {
        sessionStorage.setItem('archy_return_page', table.page());
    }

    var $wrapper = $('#grid_table_wrapper .table-responsive');
    if ($wrapper.length) {
        sessionStorage.setItem('archy_return_scroll_left', $wrapper.scrollLeft());
    }
});
if ($('#grid_table').length > 0) {
    $('#grid_table tfoot th').each(function() {
        var title = $(this).text();
        var column = $(this).data('column');
        var column_filter = $(this).data('column-filter');
        var class_name = $(this).attr('class');
        if (title != 'Azioni') {
           switch (class_name) {

    case 'filter_text':
    case 'filter_file':
    case 'filter_bulk_upload':
    case 'filter_textarea':
    case 'filter_user':
    case 'filter_amount':
    case 'filter_date':
    case 'filter_datetime':
    case 'filter_editor':
    case 'filter_plain_editor':

        $(this).html(
            '<input type="text" ' +
            'data-column-filter="' + column_filter + '" ' +
            'data-column="' + column + '" ' +
            'placeholder="' + title + '" ' +
            'class="export_filter form-control ' + class_name + '">'
        );

        break;

    case 'filter_select':
case 'filter_multiple':

    /*
    |--------------------------------------------------------------------------
    | ARCHY ART - FILTRI SELECT COME TESTO
    |--------------------------------------------------------------------------
    | Evita select enormi e problemi di allineamento colonne.
    | Il backend continuerà a filtrare correttamente tramite search_db.
    */

    $(this).html(
        '<input type="text" ' +
        'data-column-filter="' + column_filter + '" ' +
        'data-column="' + column + '" ' +
        'placeholder="' + title + '" ' +
        'class="export_filter form-control filter_text">'
    );

    break;

case 'filter_checkbox':

    $(this).html(
        '<div class="checkbox">' +
            '<label><input type="radio" name="' + column + '" value="" class="export_filter ' + class_name + '" checked> Tutti</label> ' +
            '<label><input type="radio" name="' + column + '" value="1" class="export_filter ' + class_name + '"> Sì</label> ' +
            '<label><input type="radio" name="' + column + '" value="0" class="export_filter ' + class_name + '"> No</label>' +
        '</div>'
    );

    break;

case 'filter_search':

    $(this).html(
        '<select class="search-ajax export_filter ' + class_name + '" ' +
        'data-column-filter="' + column_filter + '" ' +
        'data-column="' + column + '" ' +
        'style="width:100%"></select>'
    );

    break;
	            }
        }
    });
	
	
	
    var domain = $('.modal_iframe', window.parent.document).data('domain') || '';
    
    if (typeof(Storage) !== "undefined") {
       
        dtPageLegth = localStorage.getItem(intent + "_" + username + "_page_length") == null ? 10 : Number(localStorage.getItem(intent + "_" + username + "_page_length"));
        dtOrder = localStorage.getItem(intent + "_" + username + "_order") == null ? [
            [0, 'desc']
        ] : JSON.parse(localStorage.getItem(intent + "_" + username + "_order"));
    } else {
        dtPageLegth = 10;
        dtOrder = [
            [0, 'desc']
        ];
    }
    
    table = $('#grid_table').
    on('order', function() {
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem(intent + "_" + username + "_order", JSON.stringify(table.order()));
        }
    }).
	
	
    DataTable({
        pageLength: dtPageLegth,
        lengthMenu: [10, 25, 50],
        order: dtOrder,
        language: data_table_it_IT,
        processing: true,
        serverSide: true,
        autoWidth: false,
		scrollX: true,
		scrollCollapse: true,
		stateSave: true,
		stateSaveCallback: function(settings, data) {
			sessionStorage.setItem(
				'DataTables_' + intent + "_" + username,
				JSON.stringify(data)
			);
		},

		stateLoadCallback: function(settings) {
			var data = JSON.parse(
				sessionStorage.getItem('DataTables_' + intent + "_" + username)
			);

			if (!data) {
				return null;
			}

			// Se NON sto tornando da una scheda modificata,
			// forza sempre pagina 1
			if (!sessionStorage.getItem('archy_return_record_id')) {
				data.start = 0;
			}

			return data;
		},
        fixedHeader: {
                header: true,
                footer: false
        },
        columnDefs: [
            { orderable: false, targets: -1 }
         ],
        ajax: {
            "url": "/archy/datatable_source.php?intent=" + intent + "&domain=" + domain + "&intent_filter=" + intent_filter,
            "type": "POST"
        },
        layout: {
            top2Start: 'pageLength',
            top2End: 'search',
            topStart: 'info',
            topEnd: 'paging',
            bottomStart: 'pageLength',
            bottomEnd: 'search',
            bottom2Start: 'info',
            bottom2End: 'paging'
        }
    });
	
	function archyRestoreGridPosition() {
    var returnIntent = sessionStorage.getItem('archy_return_intent');
    var returnId = sessionStorage.getItem('archy_return_record_id');

    if (returnIntent !== intent || !returnId) {
        return;
    }
	
	var returnPage = sessionStorage.getItem('archy_return_page');

	if (returnPage !== null && !sessionStorage.getItem('archy_return_page_done')) {
		sessionStorage.setItem('archy_return_page_done', '1');
		table.page(parseInt(returnPage, 10)).draw('page');
		return;
	}
    var $target = $('#grid_table .btn-edit[data-id="' + returnId + '"], #grid_table .btn-view[data-id="' + returnId + '"], #grid_table a[href*="id=' + returnId + '"]');

    if (!$target.length) {
        return;
    }

    var $row = $target.closest('tr');

    var scrollLeft = sessionStorage.getItem('archy_return_scroll_left');
    var $wrapper = $('#grid_table_wrapper .table-responsive');

    if ($wrapper.length && scrollLeft !== null) {
        $wrapper.scrollLeft(parseInt(scrollLeft, 10));
    }

    $row.addClass('table-warning');

    $('html, body').animate({
        scrollTop: $row.offset().top - 180
    }, 300);

    setTimeout(function() {
        $row.removeClass('table-warning');
    }, 2500);

    sessionStorage.removeItem('archy_return_intent');
    sessionStorage.removeItem('archy_return_record_id');
    sessionStorage.removeItem('archy_return_page');
    sessionStorage.removeItem('archy_return_scroll_left');
	sessionStorage.removeItem('archy_return_page_done');
}

table.on('draw.archyReturnPosition', function() {
    setTimeout(archyRestoreGridPosition, 150);
});

setTimeout(archyRestoreGridPosition, 500);


    table.columns().every(function() {
        var that = this;
        $('input,select', this.footer()).on('keyup change', function() {
            if (that.search() !== this.value) {
                if ($(this).hasClass('filter_date')) {
                    var formatted_query = this.value.split('/');
                    formatted_query.reverse();
                    var search_query = formatted_query.join('-');
                } else {
                    var search_query = this.value;
                }
                that
                    .search(search_query)
                    .draw();
            }
        });
    });
	
	//$("#grid_table_wrapper table, .mce-container").wrap('<div class="table-responsive" style="border:0 !important;"></div>');
	

	
	/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - FOOTER FILTRI FISSO CUSTOM
|--------------------------------------------------------------------------
*/

function initArchyFixedFiltersFooter() {

    var $wrapper = $('#grid_table_wrapper .dt-scroll-body');
    var $table = $('#grid_table');
    var $tfoot = $('#grid_table tfoot');

    if (!$wrapper.length || !$table.length || !$tfoot.length) return;

    $('.archy-fixed-filters-footer').remove();

    var $fixedFooter = $('<div class="archy-fixed-filters-footer"></div>');
    var $cloneTable = $('<table class="' + $table.attr('class') + '"></table>');
    var $cloneFoot = $tfoot.clone(false, false);

    $cloneTable.append($cloneFoot);
    $fixedFooter.append($cloneTable);
    $('body').append($fixedFooter);

    // filtri funzionanti sul clone fisso
    $fixedFooter.on('keyup change', '.export_filter', function () {

        var $input = $(this);
        var columnName = $input.data('column');
        var value = $input.val();

        if ($input.hasClass('filter_date') && value) {
            var parts = value.split('/');
            parts.reverse();
            value = parts.join('-');
        }

        var columnIndex = $('#grid_table thead th').filter(function () {
            return $(this).data('column') === columnName;
        }).index();

        if (columnIndex < 0) return;

        table.column(columnIndex).search(value).draw();

        // sincronizza anche il footer originale per export
        $('#grid_table tfoot .export_filter[data-column="' + columnName + '"]').val($input.val());
    });

    function syncArchyFixedFiltersFooter() {

        var el = $wrapper.get(0);
        if (!el) return;

        var rect = el.getBoundingClientRect();

        $fixedFooter.css({
            position: 'fixed',
            left: rect.left + 'px',
            bottom: '18px',
            width: rect.width + 'px',
            overflow: 'hidden',
            background: '#fff',
            zIndex: 99998
        });

        $cloneTable.css({
            width: $table.outerWidth() + 'px',
            transform: 'translateX(' + (-$wrapper.scrollLeft()) + 'px)'
        });

        $('#grid_table thead th').each(function(index) {
            var w = $(this).outerWidth();

            $cloneTable.find('tfoot th').eq(index).css({
                width: w + 'px',
                minWidth: w + 'px',
                maxWidth: w + 'px'
            });
        });
    }

    $wrapper.off('scroll.archyFixedFilters').on('scroll.archyFixedFilters', syncArchyFixedFiltersFooter);

    $(window)
        .off('resize.archyFixedFilters scroll.archyFixedFilters')
        .on('resize.archyFixedFilters scroll.archyFixedFilters', syncArchyFixedFiltersFooter);

    table
        .off('draw.dt.archyFixedFilters order.dt.archyFixedFilters search.dt.archyFixedFilters column-sizing.dt.archyFixedFilters')
        .on('draw.dt.archyFixedFilters order.dt.archyFixedFilters search.dt.archyFixedFilters column-sizing.dt.archyFixedFilters', function() {
            setTimeout(syncArchyFixedFiltersFooter, 250);
        });

    setTimeout(syncArchyFixedFiltersFooter, 300);
}

setTimeout(initArchyFixedFiltersFooter, 600);
    
    
    /*$('body').on('change', '.dt-length select', function() {
        if (typeof(Storage) !== "undefined") {
            localStorage.setItem(intent + "_" + username + "_page_length", $('.dt-length select').val());
        }
    });*/
    $('.reset_order').click(function() { // columns modal buttons
        if (typeof(Storage) !== "undefined") {
            localStorage.removeItem('DataTables_'+intent + "_" + username);
        }
    });

    $('.search-ajax').select2({
        ajax: {
            url: root + 'archy/app.php?do_action=select2_datasource',
            dataType: 'json',
            data: function(params) {
                var query = {
                    search: params.term,
                    intent: $(this).data('column'),
                    intent_filter: $(this).data('column-filter')
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        },
        placeholder: 'Tutti',
        allowClear: true
    });
    $('.toSelect2').select2({placeholder:'Seleziona...',allowClear: true});
    
    $('.regenerates_attachments').click(function() {
        if( confirm('Sei sicuro di voler rigenerare tutti gli allegati?' )) {            
            $.ajax({
                type: "POST",
                url: '/archy/app.php?do_action=regenerates_attachments',
                data: 'intent=' + intent,
                cache: false,
                beforeSend: function() {
                    $('#unloading').css('display', 'block');
                },
                success: function(response) {
                    if(response== 'OK'){ alert('Generazione avvenuta con successo'); }
                    location.reload();
                },
                error: function (request, status, error) {
                    alert('Errore nella generazione, riprovare più tardi');
                    $('#unloading').css('display', 'none');
                }
            });
        }
    });
	
	
    
    $(document).on('click', '.export', function(e) {

        e.preventDefault();

        const $btn = $(this);

        // 🔥 PREPARE URL (NON export diretto)
        let query = root + 'archy/app.php?intent=' + intent + '&do_action=export&';

        let columns = [];
        let filters = '';

        // colonne
        $('#grid_table thead th[data-column]').each(function() {
            columns.push($(this).data('column'));
        });
        query += 'columns=' + encodeURIComponent(columns.join(',')) + '&';

        // filtri
        $('#grid_table tfoot th .export_filter').each(function() {

            if (($(this).attr('type') == 'text' || $(this).hasClass('search-ajax') || $(this).is('select')) && ($(this).val())) {

                let search_query = $(this).val();

                if ($(this).hasClass('filter_date')) {
                    let parts = search_query.split('/');
                    parts.reverse();
                    search_query = parts.join('-');
                }

                filters += encodeURIComponent('filter[' + $(this).data('column') + ']') + '=' + encodeURIComponent(search_query) + '&';
            }

            if ($(this).attr('type') == 'radio' && $(this).is(':checked') && $(this).val()) {
                filters += encodeURIComponent('filter[' + $(this).attr('name') + ']') + '=' + encodeURIComponent($(this).val()) + '&';
            }
        });

        query += filters;

        // ricerca globale
        const keyword = $('#dt-search-0').val();
        if (keyword) {
            query += 'search=' + encodeURIComponent(keyword) + '&';
        }

        // formato
        query += 'format=' + $btn.data('format') + '&';

        // ordinamento
        $('#grid_table thead th').each(function() {
            if ($(this).hasClass('dt-ordering-desc') || $(this).hasClass('dt-ordering-asc')) {
                query += 'order_by=' + encodeURIComponent($(this).data('column')) + '&';
                query += 'order=' + ($(this).hasClass('dt-ordering-desc') ? 'desc' : 'asc') + '&';
            }
        });

        // opzioni PDF
        if ($btn.data('format') === 'pdf') {
            const p = $('#pdf_options');

            /*
            |--------------------------------------------------------------------------
            | LETTURA OPZIONI PDF AGGIORNATE IN TEMPO REALE
            |--------------------------------------------------------------------------
            |
            | jQuery .data() mette in cache i valori data-* letti al caricamento.
            | Per questo, dopo avere cliccato "Applica" nella modale, l'export
            | continuava a usare i vecchi valori finché non si ricaricava la pagina.
            |
            | Qui leggiamo prima l'attributo aggiornato e usiamo .data() solo
            | come fallback.
            |
            */

            const pdfOpt = function(name, fallback) {
                const attrValue = p.attr('data-' + name);
                if (typeof attrValue !== 'undefined' && attrValue !== '') {
                    return attrValue;
                }

                const dataValue = p.data(name);
                if (typeof dataValue !== 'undefined' && dataValue !== '') {
                    return dataValue;
                }

                return fallback;
            };

            query += 'orientation=' + encodeURIComponent(pdfOpt('orientation', 'L')) + '&';
            query += 'sheet=' + encodeURIComponent(pdfOpt('sheet', 'A4')) + '&';
            query += 'size=' + encodeURIComponent(pdfOpt('size', '7')) + '&';
            query += 'up=' + encodeURIComponent(pdfOpt('up', '8')) + '&';
            query += 'down=' + encodeURIComponent(pdfOpt('down', '8')) + '&';
            query += 'left=' + encodeURIComponent(pdfOpt('left', '5')) + '&';
            query += 'right=' + encodeURIComponent(pdfOpt('right', '5')) + '&';
        }

        // 🚀 START LOADER
        globalLoader.start();

        // 1️⃣ CHIAMATA PREPARE
        $.ajax({
            type: "GET",
            url: query,
            dataType: "json",
            success: function(res) {

                if (!res.job_id) {
                    globalLoader.stop();
                    console.error('Nessun job_id');
                    return;
                }

                startPolling(res.job_id);
            },
            error: function(err) {
                globalLoader.stop();
                console.error(err);
            }
        });

        // 🔁 POLLING PROGRESS
        function startPolling(jobId) {

            const interval = setInterval(() => {

                $.get(root + 'archy/app.php', {
                    intent: intent,
                    do_action: 'export_status',
                    job_id: jobId
                }, function(res) {

                    // aggiorna progress bar
                    if (res.progress !== undefined) {
                        $('#ajax-progress-bar').css('width', res.progress + '%');
                    }

                    // se pronto → download
                    if (res.ready) {

                        clearInterval(interval);

                        globalLoader.stop();

                        // 🔥 DOWNLOAD VERO
                        window.location.href = root + 'archy/app.php?intent=' + intent + '&do_action=export_download&job_id=' + jobId;
                    }

                }, 'json');

            }, 1000);
        }

    });

    /*$('body').on('click', '.download', function(event) {
        event.preventDefault();
        var href = $(this).attr('href');
        currentDownload = $.fileDownload(href, {
            preparingMessageHtml: 'Attendi...',
            failMessageHtml: "Errore nel recupero del file, si prega di contattare gli amministratori."
        });
    });*/

    if ($('.modal_iframe', window.parent.document).length > 0) {
        $('body').addClass('modal_grid');
    }
}










/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - SCROLL ORIZZONTALE FISSO AL PIEDE DATATABLE
|--------------------------------------------------------------------------
|
| Barra orizzontale fissa in basso, sotto i filtri della tabella.
| La posizione e la larghezza vengono calcolate dinamicamente.
|
*/

function initStickyHorizontalScrollUnderFilters() {

    var $wrapper = $('#grid_table_wrapper .dt-scroll-body');

    if (!$wrapper.length) {
        return;
    }

    // rimuove eventuali barre create dai test precedenti
    $('.archy-filter-scrollbar').remove();

    var $scrollbar = $('<div class="archy-filter-scrollbar"><div class="archy-filter-scrollbar-inner"></div></div>');
    var $inner = $scrollbar.find('.archy-filter-scrollbar-inner');

    $('body').append($scrollbar);

    function syncScrollbar() {

        var el = $wrapper.get(0);
        if (!el) return;

        var rect = el.getBoundingClientRect();

        // larghezza e posizione responsive
        $scrollbar.css({
            left: rect.left + 'px',
            width: rect.width + 'px'
        });

        // larghezza reale della tabella
        $inner.width(el.scrollWidth);

        // sincronizza posizione
        $scrollbar.scrollLeft($wrapper.scrollLeft());
		syncFixedHeaderHorizontal();

        // mostra solo se serve davvero lo scroll orizzontale
        $scrollbar.show();
    }

    function syncFixedHeaderHorizontal() {
    var scrollLeft = $wrapper.scrollLeft();

    $('.fixedHeader-floating, .fixedHeader-locked').each(function() {
        $(this).css({
            transform: 'translateX(' + (-scrollLeft) + 'px)'
        });
    });
	}

	var isSyncingScroll = false;

$scrollbar.off('scroll.archy').on('scroll.archy', function() {
    if (isSyncingScroll) return;

    isSyncingScroll = true;

    var left = $scrollbar.scrollLeft();
    $wrapper.scrollLeft(left);
    syncFixedHeaderHorizontal();

    setTimeout(function() {
        isSyncingScroll = false;
    }, 30);
});

$wrapper.off('scroll.archy').on('scroll.archy', function() {
    if (isSyncingScroll) return;

    isSyncingScroll = true;

    var left = $wrapper.scrollLeft();
    $scrollbar.scrollLeft(left);
    syncFixedHeaderHorizontal();

    setTimeout(function() {
        isSyncingScroll = false;
    }, 30);
});

    setTimeout(syncScrollbar, 300);

    $(window).off('resize.archyScroll scroll.archyScroll').on('resize.archyScroll scroll.archyScroll', function() {
        syncScrollbar();
    });

 if (typeof table !== 'undefined' && table) {

    table.off('.archyScroll');

    table.on(
        'draw.dt.archyScroll page.dt.archyScroll length.dt.archyScroll search.dt.archyScroll',
        function () {
            setTimeout(function () {
                syncScrollbar();
            }, 150);
        }
    );

    table.on('order.dt.archyScroll', function () {
        setTimeout(function () {
            if (table.fixedHeader && typeof table.fixedHeader.adjust === 'function') {
                table.fixedHeader.adjust();
            }

            syncScrollbar();
        }, 300);
    });
}
}

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - INIT SCROLL ORIZZONTALE FISSO AL PIEDE
|--------------------------------------------------------------------------
*/

$(window).on('load', function() {
    setTimeout(function() {
        initStickyHorizontalScrollUnderFilters();
    }, 500);
});










$('#applyPDFOptions').click(function() {
    var values = $('#pdfOptionsWrapper :input').serializeArray();
    var pdf_options = [];
    for (var i in values) {
        /*
        |--------------------------------------------------------------------------
        | AGGIORNAMENTO LIVE OPZIONI PDF
        |--------------------------------------------------------------------------
        |
        | Aggiorniamo sia attr() sia data().
        | In questo modo il PDF successivo usa subito le nuove impostazioni,
        | senza richiedere il reload della DataTable o della pagina.
        |
        */
        $('#pdf_options')
            .attr('data-' + values[i]['name'], values[i]['value'])
            .data(values[i]['name'], values[i]['value']);

        if (typeof(Storage) !== "undefined") {
            pdf_options.push(values[i]['name'] + '=' + values[i]['value']);
        }
    }
    if (typeof(Storage) !== "undefined") {
        localStorage.setItem('pdf_options', pdf_options.join('&'));
    }
    $('#pdfOptions').modal('hide');
});
$('#applyPDFDefaults').click(function() {
    // Default consigliato per export tabellare leggibile.
		$('#orientation').val('L');
		$('#sheet').val('A4');
		$('#size').val('10');
		$('#up').val('10');
		$('#right').val('10');
		$('#down').val('10');
		$('#left').val('10');
		$('#pdfDefaultsApplied').fadeIn(function() { setTimeout(function() { $('#pdfDefaultsApplied').fadeOut() }, 1000) });
    //$('#pdfOptions').modal('hide');
});
if (typeof(Storage) !== "undefined") {
    var pdf_options = localStorage.getItem('pdf_options');
    if (pdf_options != null) {
        pdf_options = pdf_options.split('&');
        for (var i = 0; i < pdf_options.length; i++) {
            var option = pdf_options[i].split('=');
            $('#pdf_options')
                .attr('data-' + option[0], option[1])
                .data(option[0], option[1]);
            $('#' + option[0]).val(option[1]);
        }
    }
}
/*$('#view_pdf').click(function() {
    var query = root + 'archy/app.php?intent=' + intent + '&do_action=view_pdf&id=' + $(this).data('id');
    var p = $('#pdf_options');
    query += '&orientation=' + p.attr('data-orientation') + '&sheet=' + p.attr('data-sheet') + '&size=' + p.attr('data-size') + '&up=' + p.attr('data-up') + '&down=' + p.attr('data-down') + '&left=' + p.attr('data-left') + '&right=' + p.attr('data-right');
    currentDownload = $.fileDownload(query, {
        preparingMessageHtml: 'Sto preparando il file PDF, per favore attendi...',
        failMessageHtml: "Risorse non sufficienti per la generazione del file PDF, si prega di contattare gli amministratori."
    });
});*/

$('.orderable_labels .row').sortable({
    connectWith: ".orderable_labels .row",
    placeholder: "ui-state-highlight col-md-4",
    helper: 'clone',
    forceHelperSize: true,
    forcePlaceholderSize: true,
    start: function(e, ui) {
        ui.placeholder.height(ui.item.height() - 2);
        ui.placeholder.width(ui.item.width() - 13);
    }
});
$('.dashboard').sortable({ handle: '.glyphicon' });

$(document).on("submit", "form.fileDownloadForm", function(e) {
    $.fileDownload($(this).prop('action'), {
        preparingMessageHtml: "Sto elaborando il modulo, per favore attendi...",
        failMessageHtml: "Risorse non sufficienti per l'elaborazione del modulo, si prega di contattare gli amministratori.",
        httpMethod: $(this).prop('method'),
        data: $(this).serialize()
    });
    e.preventDefault();
});

//Form
if ($('[name="main[domain]"]').length > 0) {
    var domain = $('.modal_iframe', window.parent.document).data('domain') || '';
    if (domain != '') {
        $('[name="main[domain]"]').val(domain);
        if ($('[name="main[domain]"]').hasClass('select2')) {
            $('[name="main[domain]"]').trigger('change');
        }
    }
}





/* FUNZIONI PER RICERCA CAMPI SU ALTRE INTENT */
var search_modal_button, lookup, lookup_filter;

$('#search_modal').on('show.bs.modal', function(event) {
    search_modal_button = $(event.relatedTarget);

    var target = search_modal_button.data('bs-target');
    var intent = search_modal_button.data('intent');
    var domain = search_modal_button.data('domain') || '';

    lookup = search_modal_button.data('lookup') || 'id';
    lookup_filter = search_modal_button.data('lookup-filter') || '';
    var intent_name = search_modal_button.data('intent_name');

    $(target + ' .intent_singular').text(intent_name);

    if (target == '#search_modal') {
        $('#search_modal .modal_iframe')
            .attr('src', root + 'archy/app.php?intent=' + intent + '&view=grid&body_class=modal_grid&intent_filter=' + encodeURIComponent(lookup_filter))
            .attr('data-domain', domain)
            .attr('data-intent', intent)
            .attr('data-lookup', lookup)
            .attr('data-lookup-filter', lookup_filter);
    }
});

$('body').on('focus', '.search', function() {
    $(this).next().next().trigger('click');
});

$('.modal_grid').on('click', '#grid_table tbody tr td:not(:last-child)', function() {

    var $row = $(this).closest('tr');

    var id =
        $('.btn-view', $row).data('id') ||
        $('.btn-edit', $row).data('id') ||
        $('a[data-id]', $row).first().data('id');

    if (!id) {
        var href = $('a[href*="id="]', $row).first().attr('href') || '';
        var match = href.match(/[?&]id=([0-9]+)/);
        if (match) {
            id = match[1];
        }
    }

    if (!id) {
        id = $row.find('td').first().text().trim();
    }

    var search_intent =
        window.parent.search_modal_button.data('intent') ||
        $('.modal_iframe', window.parent.document).attr('data-intent');

    var lookup =
        $('.modal_iframe', window.parent.document).attr('data-lookup') ||
        window.parent.search_modal_button.data('lookup') ||
        'id';

    var lookup_filter =
        $('.modal_iframe', window.parent.document).attr('data-lookup-filter') ||
        window.parent.search_modal_button.data('lookup-filter') ||
        '';

    var scope = window.parent.search_modal_button.parents('.input-group');

    $('input[type="hidden"]', scope).val(id).trigger('change');

    var rowLabel = $row.find('td:not(:last-child)').map(function() {
        return $(this).text().trim();
    }).get().filter(Boolean).join(' ');

    $.ajax({
        type: "POST",
        url: root + "archy/app.php?do_action=get_lookup_text",
        data: {
            table: search_intent,
            lookup: lookup,
            id: id,
            lookup_filter: lookup_filter
        },
        cache: false,
        success: function(text) {
            text = $.trim(text);

            if (
                text === '' ||
                text.indexOf('Warning') !== -1 ||
                text.indexOf('<br') !== -1
            ) {
                text = rowLabel;
            }

            $('.search', scope).val(text);
        }
    });

    window.parent.$('#search_modal').modal('hide');
});




$('.cbox').click(function() {
    var target = $('input[type="hidden"]', $(this).parents('.form-check'));
    var value = $(this).is(':checked') ? 1 : 0;
    target.val(value);
});
$('.form-check-input-add').on('change', function() {
    const target = $(this).closest('.form-check').find('input[type="hidden"]');
    target.val(this.checked ? 1 : 0);
    
    const target_tab = $(this).data('target'); // es: #tab2
    const $tab = $('a[href="' + target_tab + '"]');
    const isActive = $tab.hasClass('active');
    
    if (this.checked) {
        // attiva tab
        $tab.removeClass('disabled');
        $tab.tab('show');
    } else {
        // disabilita tab
        $tab.addClass('disabled');

        // 🔥 SE è attiva → vai alla prima disponibile
        if (isActive) {
            const $firstAvailable = $('.nav-link:not(.disabled)').first();
            $firstAvailable.tab('show');
        }
    }
});

$('body').on('click', '.close_editor', function() {
    $(this).parents('.editor_holder').slideUp();
});
$("#type_options ol").sortable({
    handle: ".order_option"
});

// Prevent Bootstrap dialog from blocking focusin
document.addEventListener('focusin', (e) => {
  if (e.target.closest(".tox-tinymce, .tox-tinymce-aux, .moxman-window, .tam-assetmanager-root") !== null) {
    e.stopImmediatePropagation();
  }
});

var cloud_modal_button;
$('#view_cloud_modal').on('show.bs.modal', function(event) {
    cloud_modal_button = $(event.relatedTarget);
    var images = cloud_modal_button.attr('data-images') != '' ? cloud_modal_button.attr('data-images').split(/,/g) : '';
    //var images = modal_button.attr('data-images') != '' ? modal_button.attr('data-images').split(',https') : '';
    //var videos = cloud_modal_button.attr('data-videos') != '' ? cloud_modal_button.attr('data-videos').split(/,/g) : '';
    var files = cloud_modal_button.attr('data-files') != '' ? cloud_modal_button.attr('data-files').split(/,/g) : '';
    var html = '';
    //if (images !== '') { html += '<h3>Immagini</h3>'; }else if(files !== ''){ html += '<h3>Allegati</h3>'; }
    $.ajax({
        type: "POST",
        url: root + "archy/app.php?do_action=get_cloud_attachments",
        data: 'data_images=' + images + '&data_files=' + files,
        cache: false,
        success: function(response) {
            $('#view_cloud_modal .modal-body .load_modal_cloud_files').html(html+response);
        }
    });   
    

    /*if (files != '') {
        html += '<h3>Documenti</h3>';
        for (var i = 0; i < files.length; i++) {
            var filename = files[i].split(/\//g);
            filename = filename[filename.length - 1];
            html += '<div class="btn-group item_holder"><a href="' + files[i] + '" download title="Scarica" class="btn btn-primary btn-sm tt">' + filename + '</a><a href="#" class="btn btn-danger btn-sm tt delete_attachment" title="Elimina allegato" data-file="' + files[i] + '"><i class="glyphicon glyphicon-trash"></i></a></div> ';
        }
    }*/
});

var file_counter, modal_button;
$('#uploads_modal').on('show.bs.modal', function(event) {
    modal_button = $(event.relatedTarget);
    //var images = modal_button.attr('data-images') != '' ? modal_button.attr('data-images').split(/,/g) : '';
    //var files = modal_button.attr('data-files') != '' ? modal_button.attr('data-files').split(/,/g) : '';
    //var html = '';
    //var modal_label = '';
    //var table = $('#uploads_modal .load_modal_files').attr('data-table');
    var datas = 'source_table='+modal_button.data('table');
    datas += '&source_field='+modal_button.data('field');
    datas += '&source_id='+modal_button.data('id');
    datas += '&source_location='+ modal_button.data('location');
    $.ajax({
        type: "POST",
        url: root + "archy/app.php?do_action=get_cloud_attachments",
        data: datas,
        cache: false,
        success: function(response) {
            $('#uploads_modal .load_modal_files').html(response);
        }
    });  
    
    
    /*
    if (images != '') {
        modal_label = 'Immagini';
        //html += '<h3>Immagini</h3>';
        for (var i = 0; i < images.length; i++) {
            var filename = images[i].split(/\//g);
            filename = filename[filename.length - 1];
            html += '<div class="col-sm-3"><div class="hoverbox rounded-3 text-center item_holder"><img class="img-fluid" src="' + images[i] + '" alt="' + filename + '" /><div class="hoverbox-content bg-dark p-5 flex-center" data-bs-theme="light"><div><a class="btn btn-tertiary btn-sm mt-1 delete_attachment" href="#!" data-file="' + images[i] + '"><span class="fas fa-trash-alt"></span>Elimina</a></div></div></div></div>';
            //html += '<figure class="figure item_holder"><a href="' + images[i] + '" download><img src="' + images[i] + '" alt="' + filename + '" title="Scarica" class="figure-img img-fluid rounded"></a><figcaption class="figure-caption">' + filename + ' <i class="fas fa-trash-alt delete_attachment tt" title="Elimina allegato" data-file="' + images[i] + '"></i></figcaption></figure>';
        }
    }
    if (files != '') {
        modal_label = 'Allegati';
        html += '<div class="col-sm-12"><ul class="list-group">';
        for (var i = 0; i < files.length; i++) {
            var filename = files[i].split(/\//g);
            filename = filename[filename.length - 1];
            html += '<li class="list-group-item d-flex justify-content-between align-items-center"><a href="' + files[i] + '" class="">' + filename + '</a><a href="#" class="delete_attachment" title="Elimina allegato" data-file="' + files[i] + '"><span class="badge badge-subtle-primary rounded-pill"><i class="fas fa-trash-alt"></i></span></a></li>';
        }
        html += '</ul></div>';
    }*/
    $('#uploads_modal .load_modal_files').attr('data-table', modal_button.data('table'));
    $('#uploads_modal .load_modal_files').attr('data-id', modal_button.data('id'));
    $('#uploads_modal .load_modal_files').attr('data-field', modal_button.data('field'));
    $('#uploads_modal .load_modal_files').attr('data-location', modal_button.data('location'));
    /*$('#uploads_modal .load_modal_files').html(html);
    $('#uploads_modal #uploads_modal_label').html(modal_label);
    file_counter = $('.file_counter', modal_button);
    input_field = $('.form-control', modal_button.parents('.form-group'));
    is_required = $('.text-info', modal_button.parents('.form-group')).length > 0;
    file_counter = $('.file_counter', modal_button);*/
});

$('body').on('click', '.delete_attachment', function (e) {
    
    e.preventDefault(); // 🔥 QUESTO ERA IL PROBLEMA
    e.stopPropagation();
    
    const btn = $(this);
    const item_holder = btn.parents('tr').first(); // 🔥 FIX

    if (!confirm('Cancellare definitivamente?')) return;

    const filename = btn.data('file');

    const container = $('#uploads_modal .load_modal_files');

    const table = container.data('table');
    const id = container.data('id');
    const field = container.data('field');
    const location = container.data('location');
   
    $.ajax({
        type: "POST",
        url: root + "archy/app.php?do_action=delete_attachment",
        dataType: 'json',
        data: {
            table,
            field,
            id,
            filename,
            location
        },
        cache: false,

        success: function (res) {
            console.log("SUCCESS:", res);
            // 🔥 rimuove riga
            item_holder.slideUp(200, function () {
                $(this).remove();
            });

            // 🔥 aggiorna contatore GIUSTO
            updateUploadCounter(field);

        },
        error: function (err) {
            console.error("ERRORE AJAX:", err);
        }
    });

});

function updateUploadCounter(field) {

    const btn = document.querySelector(`.upload-btn[data-field="${field}"]`);

    if (!btn) return;

    const counter = btn.querySelector('.file_counter');

    if (!counter) return;

    let count = parseInt(counter.innerText) || 0;

    count = Math.max(0, count - 1);

    if (count === 0) {
        counter.remove();
    } else {
        counter.innerText = count;
    }
}

$('.cities_autocomplete .form-control').autocomplete({
    source: root + "archy/app.php?do_action=cities_autocomplete",
    minLength: 2
});

$('body').on('click', '.clear', function() {
    $('input', $(this).parents('.input-group')).val('');
});

$('.customer_service').click(function() {
    console.log('fired');
    parent.$('[data-id="archy_tickets"]').trigger('click');
});
//Form

$('.toggle_password').click(function() {
    var target = $(this).prev();
    if (target.attr('type') == 'password') {
        target.attr('type', 'text');
        $('i', $(this)).removeClass('far fa-eye').addClass('far fa-eye-slash');
    } else {
        target.attr('type', 'password');
        $('i', $(this)).removeClass('far fa-eye-slash').addClass('far fa-eye');
    }
});

//Invoice

$('#invoice_body').on('input', '.invoice_item', function() {
    update_invoice();
});
$('#invoice_body #add_row').click(function() {
    $(this).tooltip('hide');
    $('#invoice_body tbody').append($('#invoice_row').html());
    var scope = $('#invoice_body tbody tr:last');
    $('input[name$="[tax_percentage][]"]', scope).val(tax_percentage);
    $('input[name$="[tax_rate_id][]"]', scope).val(tax_rate_id);
});
$('#invoice_body').on('click', '.delete_row', function() {
    var tr = $(this).parents('tr');
    var require_confirm = $('[name$="[description][]"]', tr).val() + $('[name$="[amount][]"]', tr).val().replace("0.00", "");
    if (require_confirm == '') {
        tr.fadeOut(function() { $(this).remove() });
    } else {
        if (confirm('Eliminare riga?')) {
            tr.fadeOut(function() {
                $(this).remove();
                update_invoice()
            });
        }
    }
});
$('#invoice_body tbody, #receipt_body tbody').sortable();

function update_invoice() {
    var amount_total = tax_total = invoice_total = 0;

    $('#invoice_body tbody tr').each(function(index, element) {
        var row = $(this);

        var quantity = Number($('.invoice_quantity', row).val());
        var price = Number($('.invoice_price', row).val());

        var amount = quantity * price;
        $('.invoice_amount span', row).text(format_amount(amount));
        $('.invoice_amount_holder', row).val(amount);

        amount_total += amount;

        var tax_rate = Number($('.invoice_tax_rate', row).val());
        var tax = amount * tax_rate / 100;
        tax_total += tax;

        var subtotal = tax + amount;
        invoice_total += subtotal;

        $('.invoice_subtotal', row).text(format_amount(subtotal));
        $('.invoice_tax', row).text(format_amount(tax));
        $('.field_subtotal', row).val(subtotal);
        $('.field_tax', row).val(tax);

    });

    $('#amount_total').text(format_amount(amount_total));
    $('#tax_total').text(format_amount(tax_total));
    $('#invoice_total').text(format_amount(invoice_total));

    $('#field_taxable_amount').val(amount_total);
    $('#field_vat').val(tax_total);
    $('#field_total').val(invoice_total);
}

function format_amount(amount) {
    return '€ ' + amount.toFixed(2).replace(".", ",").toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
//Invoice

//Receipt
$('#receipt_body').on('input', '.receipt_item', function() {
    update_receipt();
});
$('#receipt_body #add_row').click(function() {
    $(this).tooltip('hide');
    $('#receipt_body tbody').append($('#receipt_row').html());
});
$('#receipt_body').on('click', '.delete_row', function() {
    var tr = $(this).parents('tr');
    var require_confirm = $('[name$="[description][]"]', tr).val() + $('[name$="[amount][]"]', tr).val().replace("0.00", "");
    if (require_confirm == '') {
        tr.fadeOut(function() { $(this).remove() });
    } else {
        if (confirm('Eliminare riga?')) {
            tr.fadeOut(function() {
                $(this).remove();
                update_invoice();
            });
        }
    }
});

function update_receipt() {
    var total = 0;

    $('#receipt_body tbody tr').each(function(index, element) {
        var row = $(this);

        var quantity = Number($('.receipt_quantity', row).val());
        var price = Number($('.receipt_price', row).val());

        var amount = quantity * price;
        $('.receipt_amount span', row).text(format_amount(amount));
        $('.receipt_amount_holder', row).val(amount);

        total += amount;

    });
    $('#receipt_total').text(format_amount(total));
    $('#field_total').val(total);
}
//Receipt

//closes index menu on embed iframes click
$('body').click(function() {
    $('body', window.parent.document).trigger('click');
});


$('.filter_registry').click(function() {
    var extrawhere = [];
    $('.filter_registry').each(function() {
        var t = $(this);
        if (t.is(':checked')) {
            extrawhere.push(t.val());
            t.parents('label').removeClass('btn-falcon-default').addClass('btn-falcon-success');
        } else {
            t.parents('label').removeClass('btn-falcon-success').addClass('btn-falcon-default');
        }
    });
    if (extrawhere.length > 0) {
        table.ajax.url('/archy/datatable_source.php?intent=' + intent + '&extrawhere=' + extrawhere.join("|")).load();
        //$("#filter_msg").html("Filtro attivo: "+extrawhere);
    } else {
        table.ajax.url('/archy/datatable_source.php?intent=' + intent).load();
        //$("#filter_msg").html("");
    }
});

// views
/*$('.nav.nav-tabs').not('.no-drop').append('<li class="dropdown pull-right overflow_item"><a href="#" id="overflow_container" class="dropdown-toggle" data-toggle="dropdown">Altri <b class="caret"></b></a><ul class="dropdown-menu overflow_container" role="menu" aria-labelledby="overflow_container"></ul></li>').droptabs();
$('body').on('click', 'li.overflow_item a', function() {
    setTimeout(function() { $('.nav.nav-tabs').not('.no-drop').droptabs() }, 250);
});*/

function check_fields_visibility(filter_table, filter_value) {
    
    $('[id^="'+intent+'_"]').closest('[class^="col-"]').show();
    
    $.ajax({
        type: "POST",
        dataType: 'json',
        url: "/archy/app.php?do_action=check_fields_visibility",
        data: {
            table: intent,
            filter_table: filter_table,
            filter_value: filter_value
        },
        cache: false,
        success: function (data) {

            if (data && Object.keys(data).length > 0) {

                Object.values(data).forEach(function (value) {

                    let clean = value.replace(/[\[\]]/g, '');

                    console.log("Nascondo:", clean);

                    $('#'+intent+'_'+clean)
                        .closest('[class^="col-"]')
                        .hide();
                });
            }
        }
    });
}

// Se è Italia, CAP vuoto oppure 5 caratteri numerici 
// Se non è Italia, CAP vuoto o libero
function isValidZip() {

    var value=$('input[name="main[vat_country_id]"]').val().replace(/ /g,'');
    var isItaly = (value=='1');
    //console.log('vat_id '+value);

    var value=$('input[name="main[zip]"]').val().replace(/ /g,'');
    if(isItaly){
       // console.log('isItaly');
        var isValid = (value=='') || (/\d{5}/.test(value));
    } else {
        var isValid = true;
    }
    if(!isValid){
        $('input[name="main[zip]"]').parent().addClass('has-error');
        $('input[name="main[zip]"]').focus();
    } else {
        $('input[name="main[zip]"]').parent().removeClass('has-error');
    }
    return isValid;
}

function isValidVat() {
    var value=$('input[name="main[vat_id]"]').val().replace(/ /g,'');
    var isValid = (value=='') || (/^\d{11}$/.test(value));
    if(!isValid){
        $('input[name="main[vat_id]"]').parent().addClass('has-error');
        $('input[name="main[vat_id]"]').focus();
    } else {
        $('input[name="main[vat_id]"]').parent().removeClass('has-error');
    }
    return isValid;
}

function isValidTaxCode() {
    var value=$('input[name="main[tax_code]"]').val().replace(/ /g,'').toUpperCase();
    var isValid = (value=='') || (/^\d{11}$/.test(value)) || (/^(?:[A-Z][AEIOU][AEIOUX]|[B-DF-HJ-NP-TV-Z]{2}[A-Z]){2}(?:[\dLMNP-V]{2}(?:[A-EHLMPR-T](?:[04LQ][1-9MNP-V]|[15MR][\dLMNP-V]|[26NS][0-8LMNP-U])|[DHPS][37PT][0L]|[ACELMRT][37PT][01LM]|[AC-EHLMPR-T][26NS][9V])|(?:[02468LNQSU][048LQU]|[13579MPRTV][26NS])B[26NS][9V])(?:[A-MZ][1-9MNP-V][\dLMNP-V]{2}|[A-M][0L](?:[1-9MNP-V][\dLMNP-V]|[0L][1-9MNP-V]))[A-Z]$/.test(value));
    if(!isValid){
        $('input[name="main[tax_code]"]').parent().addClass('has-error');
        $('input[name="main[tax_code]"]').focus();
    } else {
        $('input[name="main[tax_code]"]').parent().removeClass('has-error');
    }
    return isValid;
}

function isValidEmail() {
    var value=$('input[name="main[email]"]').val().replace(/ /g,'');
    var isValid = (value=='') || (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,})+$/.test(value));
    if(!isValid){
        $('input[name="main[email]"]').parent().addClass('has-error');
        $('input[name="main[email]"]').focus();
    } else {
        $('input[name="main[email]"]').parent().removeClass('has-error');
    }
    return isValid;
}

function retrieve_tax_code_info(code){
        $.ajax({
                type: "POST",
                url: '/archy/app.php?do_action=valid_tax_code',
                data: 'CodiceFiscale='+code,
                cache: false,
                success: function(response){
                        if(response){
                                populate_tax_code_fields(code);
                        }
                }
        });
}

function populate_tax_code_fields(code){			
        var code_months={'A':'01','B':'02','C':'03','D':'04','E':'05','H':'06','L':'07','M':'08','P':'09','R':'10','S':'11','T':'12'}

        var current_year=(new Date()).getFullYear().toString().substring(2,4);
        var year=code.substring(6,8);
        if(Number(year)>=Number(current_year)){// millenium bug fix
                year='19'+year;
        }else{
                year='20'+year;
        }
        var month_code=code.substring(8,9);
        var month=code_months[month_code];
        var day=code.substring(9,11);

        if(Number(day)>40){
                var gender=2;
                var day=Number(day)-40;
                if(day<10){
                        day='0'+day;
                }
        }else{
                var gender=1;
        }

        var city_code=code.substring(11,15)
        $.ajax({
                type: "POST",
                url: '/archy/app.php?do_action=get_city_by_tax_code',
                data: 'CodiceComune='+city_code,
                cache: false,
                success: function(city){
                        $('#archy_registry_birth_place').val(city);
                }
        });


        $('#archy_registry_gender').val(gender).trigger('change');

        $('#archy_registry_birth_date').val(day+'/'+month+'/'+year);
}

function manageFields(selectId, configurazione, options = {}) {
  const select = document.getElementById(selectId);

  if (!select) {
    console.error("Select non trovata:", selectId);
    return;
  }

  const mode = options.mode || "show"; // show | hide
  const scope = options.scope || "configOnly"; // configOnly | all

  function trovaColParent(el) {
    return el.closest('[class^="col-"], [class*=" col-"]') || el.parentElement;
  }

  function getGroup(el) {
    if (!el.name) return [el];
    return document.querySelectorAll(`[name="${el.name}"]`);
  }

  function toggleAsterisk(el, required) {
    const label = document.querySelector(`label[for="${el.id}"]`);
    if (!label) return;

    let star = label.querySelector(".required-label");

    if (required) {
      if (!star) {
        const span = document.createElement("span");
        span.className = "fas fa-asterisk required-label";
        span.title = "Campo richiesto";
        label.appendChild(span);
      }
    } else {
      if (star) star.remove();
    }
  }

  function getAllFieldIds() {
    return Array.from(document.querySelectorAll("input, select, textarea"))
      .map(el => el.id)
      .filter(id => id);
  }

  // 🔹 RACCOLTA ID
  let allIds = [];

  if (scope === "all") {
    allIds = getAllFieldIds();
  } else {
    Object.values(configurazione).forEach(arr => {
      arr.forEach(item => {
        const id = typeof item === "string" ? item : item.id;
        if (!allIds.includes(id)) allIds.push(id);
      });
    });
  }

  // 🔹 SALVA REQUIRED ORIGINALE
  allIds.forEach(id => {
    const el = document.getElementById(id);
    if (!el) return;

    getGroup(el).forEach(input => {
      input.dataset.originalRequired = input.hasAttribute("required") ? "true" : "false";
    });
  });

  function aggiorna() {
    const valore = select.value;
    const config = configurazione[valore] || [];

    // 🔹 RESET BASE (ripristina stato iniziale)
    allIds.forEach(id => {
      const el = document.getElementById(id);
      const parent = el ? trovaColParent(el) : null;

      if (!el) return;

      getGroup(el).forEach(input => {
        // ripristina required originale
        if (input.dataset.originalRequired === "true") {
          input.setAttribute("required", "required");
          toggleAsterisk(input, true);
        } else {
          input.removeAttribute("required");
          toggleAsterisk(input, false);
        }

        input.disabled = false;
      });

      if (parent) parent.classList.remove("d-none");
    });

    // 🔹 MODE SHOW (originale)
    if (mode === "show") {

      // nasconde tutto
      allIds.forEach(id => {
        const el = document.getElementById(id);
        const parent = el ? trovaColParent(el) : null;
        if (parent) parent.classList.add("d-none");
      });

      // mostra selezionati
      config.forEach(item => {
        const id = typeof item === "string" ? item : item.id;
        const required = typeof item === "object" ? item.required : false;

        const el = document.getElementById(id);
        const parent = el ? trovaColParent(el) : null;

        if (!el) return;

        getGroup(el).forEach(input => {
          if (required) {
            input.setAttribute("required", "required");
            toggleAsterisk(input, true);
          }
        });

        if (parent) parent.classList.remove("d-none");
      });

    }

    // 🔹 MODE HIDE (inverso)
    if (mode === "hide") {

      config.forEach(item => {
        const id = typeof item === "string" ? item : item.id;
        const hide = typeof item === "object" ? item.hide : true;
        const required = typeof item === "object" ? item.required : null;

        const el = document.getElementById(id);
        const parent = el ? trovaColParent(el) : null;

        if (!el) return;

        const gruppo = getGroup(el);

        // gestione required
        if (required !== null) {
          gruppo.forEach(input => {
            if (required) {
              input.setAttribute("required", "required");
              toggleAsterisk(input, true);
            } else {
              input.removeAttribute("required");
              toggleAsterisk(input, false);
            }
          });
        }

        // gestione visibilità
        if (hide) {
          if (parent) parent.classList.add("d-none");

          gruppo.forEach(input => {
            input.removeAttribute("required");
            toggleAsterisk(input, false);
            input.disabled = true;
          });
        }
      });
    }
  }

  select.addEventListener("change", aggiorna);
  aggiorna();
}


/*
|--------------------------------------------------------------------------
| FIX FINALE RELOAD OPZIONI PDF
|--------------------------------------------------------------------------
|
| Questo listener è volutamente aggiunto in fondo al file.
|
| Motivo:
| - il salvataggio delle opzioni PDF funziona;
| - dopo refresh manuale il PDF usa le impostazioni corrette;
| - quindi il problema è solo il mancato refresh automatico dopo "Applica".
|
| Intercettiamo il click in modo generico:
| - bottone con id #applyPDFOptions;
| - eventuale bottone dentro la modale #pdfOptions con testo "Applica".
|
| Il timeout lascia prima completare il salvataggio già presente nel codice.
|
*/
document.addEventListener('click', function(e) {

    var target = e.target;
    if (!target) return;

    var btn = target.closest ? target.closest('#applyPDFOptions, #pdfOptions button, #pdfOptions .btn') : null;
    if (!btn) return;

    var btnText = (btn.textContent || '').toLowerCase();

    var isApplyPdfButton =
        btn.id === 'applyPDFOptions' ||
        (
            btn.closest &&
            btn.closest('#pdfOptions') &&
            (
                btnText.indexOf('applica') !== -1 ||
                btnText.indexOf('salva') !== -1 ||
                btnText.indexOf('apply') !== -1
            )
        );

    if (!isApplyPdfButton) return;

    console.log('[PDF OPTIONS] Applica intercettato: reload pagina in corso');

    setTimeout(function() {
        var url = window.location.href.split('#')[0];

        // Cache buster per evitare che browser/server mantengano stato vecchio.
        if (url.indexOf('_pdf_reload=') === -1) {
            url += (url.indexOf('?') === -1 ? '?' : '&') + '_pdf_reload=' + new Date().getTime();
        } else {
            url = url.replace(/_pdf_reload=\d+/, '_pdf_reload=' + new Date().getTime());
        }

        window.location.replace(url);
    }, 700);

}, false);

$(window).on('load', function () {
    if (typeof tinymce === 'undefined') return;

    tinymce.remove('textarea.tinymce');

    init_tinymce();
});

$(document).on('click', 'a[href*="logout"], a[href*="do_action=logout"], .logout', function() {
    Object.keys(sessionStorage).forEach(function(key) {
        if (
            key.indexOf('DataTables_') === 0 ||
            key.indexOf('archy_return_') === 0
        ) {
            sessionStorage.removeItem(key);
        }
    });

    Object.keys(localStorage).forEach(function(key) {
        if (key.indexOf('DataTables_') === 0) {
            localStorage.removeItem(key);
        }
    });
});
