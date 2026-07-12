<?php
$u = $app->currentUser();
?>
<div class="row mb-3">
    <div class="col">
        <div class="card bg-100 shadow-none border">
            <div class="row gx-0 flex-between-center">
                <div class="col-sm-auto d-flex align-items-center"><img class="ms-n2" src="/admin_template/assets/img/illustrations/crm-bar-chart.png" alt="" width="90" />
                    <div>
                        <h6 class="text-primary fs-10 mb-0">Benvenuti su </h6>
                        <h4 class="text-primary fw-bold mb-0">Archy Art</h4>
                    </div><img class="ms-n4 d-md-none d-lg-block" src="/admin_template/assets/img/illustrations/crm-line-chart.png" alt="" width="150" />
                </div>
                <div class="col-md-auto p-3">
                    <form class="row align-items-center g-3">
                        <div class="col-auto">
                            <h6 class="text-700 mb-0">Oggi:</h6>
                        </div>
                        <div class="col-md-auto position-relative">
                            <?php echo date('d/m/Y'); ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3 g-3 dashboard">
    <div class="col-lg-8">
        <?php if($app->options['dashboard']['calendar']==true){ ?>
        <!-- CALENDAR -->
        <div class="card overflow-hidden dashboard-calendar">
            <div class="card-header">
                <div class="row gx-0 align-items-center">
                    <div class="col-auto d-flex justify-content-end order-md-1">
                        <button class="btn icon-item icon-item-sm shadow-none p-0 me-1 ms-md-2" type="button" data-event="prev" data-bs-toggle="tooltip" title="Previous"><span class="fas fa-arrow-left"></span></button>
                        <button class="btn icon-item icon-item-sm shadow-none p-0 me-1 me-lg-2" type="button" data-event="next" data-bs-toggle="tooltip" title="Next"><span class="fas fa-arrow-right"></span></button>
                    </div>
                    <div class="col-auto col-md-auto order-md-2">
                        <h4 class="mb-0 fs-9 fs-sm-8 fs-lg-7 calendar-title"></h4>
                    </div>
                    <div class="col col-md-auto d-flex justify-content-end order-md-3">
                        <button class="btn btn-falcon-primary btn-sm" type="button" data-event="today">Oggi</button>
                    </div>
                    <div class="col-md-auto d-md-none">
                        <hr />
                    </div>
                    <div class="col-auto d-flex order-md-0">
                        <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#addEventModal"> <span class="fas fa-plus me-2"></span>Aggiungi evento</button>
                    </div>
                    <div class="col d-flex justify-content-end order-md-2">
                        <div class="dropdown font-sans-serif me-md-2">
                            <button class="btn btn-falcon-default text-600 btn-sm dropdown-toggle dropdown-caret-none" type="button" id="email-filter" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span data-view-title="data-view-title">Vista mese</span><span class="fas fa-sort ms-2 fs-10"></span></button>
                            <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="email-filter">
                                <a class="active dropdown-item d-flex justify-content-between" href="#!" data-fc-view="dayGridMonth">Vista mese<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a>
                                <a class="dropdown-item d-flex justify-content-between" href="#!" data-fc-view="timeGridWeek">Settimana<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a>
                                <a class="dropdown-item d-flex justify-content-between" href="#!" data-fc-view="timeGridDay">Giorno<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a>
                                <a class="dropdown-item d-flex justify-content-between" href="#!" data-fc-view="listWeek">Lista<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a>
                                <a class="dropdown-item d-flex justify-content-between" href="#!" data-fc-view="listYear">Anno<span class="icon-check"><span class="fas fa-check" data-fa-transform="down-4 shrink-4"></span></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="calendar-outline" id="appCalendar"></div>
            </div>
        </div>
        <?php } ?>
    </div>
    <div class="col-lg-4">
        
        <?php if($app->options['dashboard']['notify']==true){ ?>
        <div class="card dashboard-notify">
            <div class="card-header d-flex flex-between-center py-2 border-bottom">
                <h6 class="mb-0">Ultime notifiche</h6>
                <div class="dropdown font-sans-serif btn-reveal-trigger">
                  <!--<button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="dropdown-most-leads" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-11"></span></button>
                  <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-most-leads"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                    <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                  </div>-->
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="row align-items-center">
                    <?php
                    $notice = new Notice();
                    $notice_response = $notice->html();
                    echo $notice_response->html;
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>
        
        <?php if($app->options['dashboard']['stats']==true){ ?>
        <div class="card dashboard-stats">
            <div class="card-header d-flex flex-between-center py-2 border-bottom">
                <?php $stats = new StatsCloudStorageService(); $get_stats = $stats->getDashboardStats(); ?>
                <h6 class="mb-0">Spazio 10GB Disponibili - Utilizzato <?php echo $get_stats['used_percent'].' %'; ?></h6>
                <div class="dropdown font-sans-serif btn-reveal-trigger">
                  <!--<button class="btn btn-link text-600 btn-sm dropdown-toggle dropdown-caret-none btn-reveal" type="button" id="dropdown-most-leads" data-bs-toggle="dropdown" data-boundary="viewport" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs-11"></span></button>
                  <div class="dropdown-menu dropdown-menu-end border py-2" aria-labelledby="dropdown-most-leads"><a class="dropdown-item" href="#!">View</a><a class="dropdown-item" href="#!">Export</a>
                    <div class="dropdown-divider"></div><a class="dropdown-item text-danger" href="#!">Remove</a>
                  </div>-->
                </div>
            </div>
            <div class="card-body d-flex flex-column justify-content-between">
                <div class="row align-items-center">
                    <div class="col-md-5 col-xxl-12 mb-xxl-1">
                      <div class="position-relative">
                        <!-- Find the JS file for the following chart at: src/js/charts/echarts/most-leads.js-->
                        <!-- If you are not using gulp based workflow, you can find the transpiled code at: public/assets/js/theme.js-->
                        <div id="main-chart" class="echart-doughnut-chart" style="height:400px;"></div>
                      
                      </div>
                    </div>
                    <div class="col-xxl-12 col-md-7">
                        <hr class="mx-nx1 mb-0 d-md-none d-xxl-block" />
                        <div class="d-flex flex-between-center border-bottom py-3 pt-md-0 pt-xxl-3">
                            <div class="d-flex">
                                <h6 class="text-700 mb-0"><i class="fas fa-photo-video"></i> Immagini</h6>
                            </div>
                            <h6 class="text-700 mb-0"><?php echo $get_stats['images']['count'];  ?></h6>
                        </div>
                        <div class="d-flex flex-between-center border-bottom py-3">
                            <div class="d-flex">
                                <h6 class="text-700 mb-0"><i class="fas fa-video"></i> Video</h6>
                            </div>
                            <h6 class="text-700 mb-0"><?php  echo $get_stats['videos']['count']; ?></h6>
                        </div>
                        <div class="d-flex flex-between-center border-bottom py-3">
                            <div class="d-flex">
                                
                                <h6 class="text-700 mb-0"><i class="far fa-file-alt"></i> Allegati</h6>
                            </div>
                            <h6 class="text-700 mb-0"><?php  echo $get_stats['attachments']['count']; ?></h6>
                        </div>
                       <!--<div class="d-flex flex-between-center border-bottom py-3">
                            <div class="d-flex"><img class="me-2" src="/admin_template/assets/img/crm/call.svg " width="16" height="16" alt="..." />
                                <h6 class="text-700 mb-0">Cloudflare video </h6>
                            </div>
                            <p class="fs-10 text-500 mb-0 fw-semi-bold">2535 vs 1486</p>
                            <h6 class="text-700 mb-0">4%</h6>
                        </div>
                        <div class="d-flex flex-between-center border-bottom py-3 border-bottom-0 pb-0">
                            <div class="d-flex"><img class="me-2" src="/admin_template/assets/img/crm/other.svg " width="16" height="16" alt="..." />
                                <h6 class="text-700 mb-0">Cloudflare docs </h6>
                            </div>
                            <p class="fs-10 text-500 mb-0 fw-semi-bold">256 vs 189</p>
                            <h6 class="text-700 mb-0">13%</h6>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        
    </div>
</div>
 
<script>
    
    document.addEventListener('DOMContentLoaded', function () {
  fetch('/archy/dashboard-stats.php')
    .then(res => res.json())
    .then(stats => {
      initCharts(stats); // ✅ ora hai i dati veri
    });
});
function initCharts(stats){
    
    // Initialize the echarts instance based on the prepared dom
    var myChart = echarts.init(document.getElementById('main-chart'));
    
    // Specify the configuration items and data for the chart
    var option = {
        tooltip: {
          trigger: 'item',
          formatter: function (params) {
    let size = params.value / (1024*1024);
    return params.name + ': ' + size.toFixed(2) + ' MB';
  }
        },
        legend: {
          top: '5%',
          left: 'center'
        },
        series: [
          {
            name: 'Access From',
            type: 'pie',
            radius: ['40%', '70%'],
            avoidLabelOverlap: false,
            itemStyle: {
              borderRadius: 10,
              borderColor: '#fff',
              borderWidth: 2
            },
            label: {
              show: false,
              position: 'center'
            },
            emphasis: {
              label: {
                show: true,
                fontSize: 40,
                fontWeight: 'bold'
              }
            },
            labelLine: {
              show: false
            },
            data: stats.chart
          
          }
        ]
      };
    // Display the chart using the configuration items and data just specified.
    myChart.setOption(option);
}
    </script>

<?php

$acube=new Acube();

//$acube->simulate_supplier_invoice();

//$acube->dump(json_decode('{"notification":{"uuid":"de442a5b-70a0-446f-bfa0-4dc29efbc02b","invoice_uuid":"6300838e-1972-11e9-a552-021240aba4cc","created_at":"2019-01-16T10:52:34+00:00","type":"NS","message":{"identificativo_sdi":"111","nome_file":"IT01234567890_11111.xml.p7m","data_ora_ricezione":"2013-06-06T12:00:00+00:00","riferimento_archivio":{"identificativo_sdi":"100","nome_file":"IT01234567890_11111.zip"},"lista_errori":{"Errore":{"Codice":"00100","Descrizione":"Certificato di firma scaduto"}},"message_id":"123456","note":"Note"},"downloaded":false},"invoice":{"fattura_elettronica_header":{"dati_trasmissione":{"id_trasmittente":{"id_paese":"IT","id_codice":"10442360961"},"progressivo_invio":"02N7B","formato_trasmissione":"FPR12","codice_destinatario":"0000000","contatti_trasmittente":{"telefono":"0287176004","email":"info@acubeapi.com"},"pec_destinatario":"movieweb@ergopec.it"},"cedente_prestatore":{"dati_anagrafici":{"id_fiscale_iva":{"id_paese":"IT","id_codice":"10144440962"},"codice_fiscale":"10144440962","anagrafica":{"denominazione":"Archy Media Srl"},"regime_fiscale":"RF01"},"sede":{"indirizzo":"Via San Vito, 18","cap":"20123","comune":"Milano","provincia":"MI","nazione":"IT"},"contatti":{"telefono":"0272080602","email":"amministrazione@archymedia.it"},"riferimento_amministrazione":"dev-31"},"cessionario_committente":{"dati_anagrafici":{"id_fiscale_iva":{"id_paese":"IT","id_codice":"09334360964"},"codice_fiscale":"DRSNDR79E16F205R","anagrafica":{"denominazione":"Movie & Web di Andrea De Rose"}},"sede":{"indirizzo":"Via Senigallia, 7","cap":"20161","comune":"Milano","provincia":"MI","nazione":"IT"}}},"fattura_elettronica_body":[{"dati_generali":{"dati_generali_documento":{"tipo_documento":"TD01","divisa":"EUR","data":"2019-01-09T00:00:00+00:00","numero":"1","importo_totale_documento":"2440.00"}},"dati_beni_servizi":{"dettaglio_linee":[{"numero_linea":1,"descrizione":"test xml sdi","quantita":"1.00","unita_misura":"PZ","prezzo_unitario":"1000.00","prezzo_totale":"1000.00","aliquota_iva":"22.00"},{"numero_linea":2,"descrizione":"test xml sdi #2","quantita":"2.00","unita_misura":"PZ","prezzo_unitario":"500.00","prezzo_totale":"1000.00","aliquota_iva":"22.00"}],"dati_riepilogo":[{"aliquota_iva":"22.00","imponibile_importo":"2000.00","imposta":"440.00","esigibilita_iva":"I"}]}}]}}'));


//$acube=new Acube();
//$acube->simulate_customer_notification('NS',31);
//$acube->dump(json_decode('{"notification":{"uuid":"de442a5b-70a0-446f-bfa0-4dc29efbc02b","invoice_uuid":"6300838e-1972-11e9-a552-021240aba4cc","created_at":"2019-01-16T10:52:34+00:00","type":"NS","message":{"identificativo_sdi":"111","nome_file":"IT01234567890_11111.xml.p7m","data_ora_ricezione":"2013-06-06T12:00:00+00:00","riferimento_archivio":{"identificativo_sdi":"100","nome_file":"IT01234567890_11111.zip"},"lista_errori":{"Errore":{"Codice":"00100","Descrizione":"Certificato di firma scaduto"}},"message_id":"123456","note":"Note"},"downloaded":false},"invoice":{"fattura_elettronica_header":{"dati_trasmissione":{"id_trasmittente":{"id_paese":"IT","id_codice":"10442360961"},"progressivo_invio":"02N7B","formato_trasmissione":"FPR12","codice_destinatario":"0000000","contatti_trasmittente":{"telefono":"0287176004","email":"info@acubeapi.com"},"pec_destinatario":"movieweb@ergopec.it"},"cedente_prestatore":{"dati_anagrafici":{"id_fiscale_iva":{"id_paese":"IT","id_codice":"10144440962"},"codice_fiscale":"10144440962","anagrafica":{"denominazione":"Archy Media Srl"},"regime_fiscale":"RF01"},"sede":{"indirizzo":"Via San Vito, 18","cap":"20123","comune":"Milano","provincia":"MI","nazione":"IT"},"contatti":{"telefono":"0272080602","email":"amministrazione@archymedia.it"},"riferimento_amministrazione":"dev-31"},"cessionario_committente":{"dati_anagrafici":{"id_fiscale_iva":{"id_paese":"IT","id_codice":"09334360964"},"codice_fiscale":"DRSNDR79E16F205R","anagrafica":{"denominazione":"Movie & Web di Andrea De Rose"}},"sede":{"indirizzo":"Via Senigallia, 7","cap":"20161","comune":"Milano","provincia":"MI","nazione":"IT"}}},"fattura_elettronica_body":[{"dati_generali":{"dati_generali_documento":{"tipo_documento":"TD01","divisa":"EUR","data":"2019-01-09T00:00:00+00:00","numero":"1","importo_totale_documento":"2440.00"}},"dati_beni_servizi":{"dettaglio_linee":[{"numero_linea":1,"descrizione":"test xml sdi","quantita":"1.00","unita_misura":"PZ","prezzo_unitario":"1000.00","prezzo_totale":"1000.00","aliquota_iva":"22.00"},{"numero_linea":2,"descrizione":"test xml sdi #2","quantita":"2.00","unita_misura":"PZ","prezzo_unitario":"500.00","prezzo_totale":"1000.00","aliquota_iva":"22.00"}],"dati_riepilogo":[{"aliquota_iva":"22.00","imponibile_importo":"2000.00","imposta":"440.00","esigibilita_iva":"I"}]}}]}}'));

?>



