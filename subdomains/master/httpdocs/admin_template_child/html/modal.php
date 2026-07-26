<!-- MODAL VIEWS -->
<div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="view_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="view_modal_label"><span class="intent_singular"></span></h4>
      </div>

      <div class="modal-body">
        <iframe src="about:blank" id="view_iframe" name="view_iframe" class="modal_iframe"></iframe>
      </div>
    </div>
  </div>
</div>

<!-- MODAL CALENDAR DETAILS -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border"></div>
    </div>
</div>

<!-- MODAL NOTICE DETAILS -->
<div class="modal fade" id="noticeDetailModal" tabindex="-1" aria-labelledby="noticeDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="rounded-top-3 py-3 ps-4 pe-6 bg-body-tertiary">
                    <h4 class="mb-1" id="noticeDetailModalLabel">Dettaglio notifica</h4>
                </div>
                <div class="p-4 pb-4 notice-detail-content">
                    <p class="text-600 mb-0">Caricamento...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-falcon-default" id="noticeDetailToggleSeen" data-notice_id="">Segna letta</button>
                <button type="button" class="btn btn-default" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>
        
<!-- MODAL CALENDAR ADD NEW EVENTS -->
<div class="modal fade" id="addEventModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border">
            <form id="addEventForm" autocomplete="off">
                <div class="modal-header px-x1 bg-body-tertiary border-bottom-0">
                  <h5 class="modal-title">Nuovo evento</h5>
                  <button class="btn-close me-n1" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-x1">
                  <div class="mb-3">
                    <label class="fs-9" for="eventTitle">Titolo</label>
                    <input class="form-control" id="eventTitle" type="text" name="title" required="required" />
                  </div>
                  <div class="mb-3">
                    <label class="fs-9" for="eventStartDate">Data di inizio</label>
                    <input class="form-control datetimepicker" id="eventStartDate" type="text" required="required" name="startDate" placeholder="yyyy/mm/dd hh:mm" data-options='{"static":"true","enableTime":"true","dateFormat":"Y-m-d H:i"}' />
                  </div>
                  <div class="mb-3">
                    <label class="fs-9" for="eventEndDate">Data di fine (facoltativa)</label>
                    <input class="form-control datetimepicker" id="eventEndDate" type="text" name="endDate" placeholder="yyyy/mm/dd hh:mm" data-options='{"static":"true","enableTime":"true","dateFormat":"Y-m-d H:i"}' />
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="eventAllDay" name="allDay" value="1" />
                    <label class="form-check-label" for="eventAllDay">Tutto il giorno
                    </label>
                  </div>
                <!--  <div class="mb-3">
                    <label class="fs-9">Schedule Meeting</label>
                    <div><a class="btn badge-subtle-success btn-sm" href="#!"><span class="fas fa-video me-2"></span>Add video conference link</a></div>
                  </div>-->
                  <div class="mb-3">
                    <label class="fs-9" for="eventDescription">Descrizione</label>
                    <textarea class="form-control" rows="3" name="description" id="eventDescription"></textarea>
                  </div>
                  <div class="mb-3">
                    <label class="fs-9" for="eventLabel">Etichetta</label>
                    <select class="form-select" id="eventLabel" name="label">
                      <option value="" selected="selected">Nessuna</option>
                      <option value="primary">Lavoro</option>
                      <option value="danger">Importante</option>
                      <option value="success">Personale</option>
                      <option value="warning">Promemoria</option>
                    </select>
                  </div>
                </div>
                <div class="modal-footer d-flex justify-content-end align-items-center bg-body-tertiary border-0">
                  <button class="btn btn-primary px-4" type="submit">Salva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL PDF OPTIONS -->
<div class="modal fade" id="pdfOptions" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content position-relative">
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="pdfOptionsWrapper">
                <div class="rounded-top-3 py-3 ps-4 pe-6 bg-body-tertiary">
                    <h4 class="mb-1" id="modalExampleDemoLabel">Opzioni PDF</h4>
                </div>
                <div class="p-4 pb-4">
                    <div class="row g-3">
                        <div class="col-sm-4">                            
                            <label class="form-label for="orientation">Orientamento</label>
                            <select class="form-control" name="orientation" id="orientation">
                               <option value="P">Verticale</option>
                               <option value="L">Orizzontale</option>
                            </select>                           
                        </div>
                        <div class="col-sm-4">                            
                            <label class="form-label for="sheet">Foglio</label>
                            <select class="form-control" name="sheet" id="sheet">
                               <option value="A6">A6</option>
                               <option value="A5">A5</option>
                               <option value="A4" selected>A4</option>
                               <option value="A3">A3</option>
                               <option value="A2">A2</option>
                               <option value="A1">A1</option>
                            </select>                           
                        </div>
                        <div class="col-sm-4">                           
                            <label class="form-label for="size">Carattere</label>
                            <div class="input-group">
                               <input type="text" class="form-control" id="size" name="size" value="12">
                               <div class="input-group-text">pt</div>
                            </div>                            
                        </div>
                 
                        <div class="col-sm-3">
                            <label class="form-label for="up">Marg. Superiore</label>
                            <div class="input-group">
                               <input type="text" class="form-control" id="up" name="up" value="50">
                               <div class="input-group-text">pt</div>
                            </div>
                         </div>
                         <div class="col-sm-3">
                            <label class="form-label for="down">Marg. Inferiore</label>
                            <div class="input-group">
                               <input type="text" class="form-control" id="down" name="down" value="70">
                               <div class="input-group-text">pt</div>
                            </div>
                         </div>
                         <div class="col-sm-3">
                            <label class="form-label for="left">Marg. Sinistro</label>
                            <div class="input-group">
                               <input type="text" class="form-control" id="left" name="left" value="50">
                               <div class="input-group-text">pt</div>
                            </div>
                         </div>
                         <div class="col-sm-3">
                            <label class="form-label for="right">Marg. Destro</label>
                            <div class="input-group">
                               <input type="text" class="form-control" id="right" name="right" value="50">
                               <div class="input-group-text">pt</div>
                            </div>
                         </div>
                   </div>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-info" id="applyPDFDefaults" style="float:left">Impostazioni di default</button>
              <button type="button" class="btn btn-default" data-bs-dismiss="modal">Annulla</button>
              <button type="button" class="btn btn-primary" id="applyPDFOptions">Applica</button>
            </div>
        </div>
    </div>
</div>

<?php if(isset($_GET['view']) && $_GET['view']=='grid' && isset($app->intent) && is_object($app->intent)){ ?>
<!-- MODAL COLUMNS SELECT -->
<div class="modal fade" id="columnsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable" role="document">  
        <form class="modal-content position-relative" method="post">
            
            <div class="position-absolute top-0 end-0 mt-2 me-2 z-1">
                <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="rounded-top-3 py-3 ps-4 pe-6 bg-body-tertiary">
                  <h4 class="mb-1" id="modalExampleDemoLabel">Seleziona e ordina colonne</h4>
                </div>
                <div class="p-4 pb-4">
                    
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                              <h2 class="accordion-header" id="heading1">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1" aria-expanded="true" aria-controls="collapse1">Colonne attive</button>
                              </h2>
                              <div class="accordion-collapse collapse show" id="collapse1" aria-labelledby="heading1" data-bs-parent="#accordionExample">
                                  <div class="accordion-body orderable_labels">
                                        <div class="row">
                                            <?php foreach($_SESSION[$app->intent->table.'-listing_columns'] as $column_name): // Colonne selezionate ?>
                                               <label class="col-md-4">
                                                  <input type="checkbox" name="listing_columns[<?php echo $column_name?>]" value="<?php echo $column_name?>" checked> <?php echo $app->cols[$column_name]['label']?>
                                               </label>
                                            <?php endforeach?>
                                        </div>
                                  </div>
                              </div>
                            </div>
							
                            <?php $cont=2;foreach($app->g_cols as $title=>$columns): ?>
                            <div class="accordion-item">
                              <h2 class="accordion-header" id="heading<?php echo $cont; ?>">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $cont; ?>" aria-expanded="true" aria-controls="collapse<?php echo $cont; ?>"><?php echo $title; ?></button>
                              </h2>
                              <div class="accordion-collapse collapse" id="collapse<?php echo $cont; ?>" aria-labelledby="heading<?php echo $cont; ?>" data-bs-parent="#accordionExample">
                                  <div class="accordion-body">
                                        <div class="row">
                                            <?php
                                            //$column_name  = array_column($columns, 'column_name');
                                            array_walk($columns, function (&$row) {
                                                $row['label'] = $row['label'] ?? null;
                                            });
                                            $column_label = array_column($columns, 'label');
                                            array_multisort($column_label, SORT_ASC, $columns);
                                            ?>
                                           <?php foreach($columns as $column_name=>$column): // nome campi da aggiungere... ?>
                                            <?php if(!in_array($column_name,$_SESSION[$app->intent->table.'-listing_columns'])): // se non è già stata selezionata... ?>
                                                <?php if(!in_array($column_name,$app->intent->exclude_columns)): // se non è un campo da escludere... ?>
                                                <label class="col-md-4">
                                                    <input type="checkbox" name="listing_columns[<?php echo $column_name; ?>]" value="<?php echo $column_name; ?>"> <?php echo isset($column['label'])?$column['label']:''; ?>
                                                 </label>
                                                 <?php endif; ?>
                                                 <?php endif; ?>
                                           <?php endforeach; ?>
                                       </div>
                                  </div>
                              </div>
                            </div>
                            <?php $cont++;endforeach; ?>
                        </div>

                    
                </div>
            </div>
            <div class="modal-footer">
               <a class="btn btn-info reset_order" href="app.php?do_action=reset_listing_columns&intent=<?php echo $app->intent->table; ?>">Colonne predefinite</a>
               <button type="button" class="btn btn-info" onClick="$('input[name^=listing_columns]').prop('checked',true);this.form.submit();">Tutte le colonne</button>
               <button type="button" class="btn btn-default" data-bs-dismiss="modal">Annulla</button>
               <button type="submit" class="btn btn-primary">Applica</button>
            </div>
            
        </form>
    </div>
</div>
<?php } ?>

<!-- MODAL SEARCH -->
<div class="modal fade" id="search_modal" tabindex="-1" role="dialog" aria-labelledby="search_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">  
    <div class="modal-content position-relative">        
        <div class="position-absolute top-0 end-0 mt-2 me-2 z-1">
            <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
            <div class="rounded-top-3 py-3 ps-4 pe-6 bg-body-tertiary">
              <h4 class="mb-1" id="search_modal_label">Seleziona <span class="intent_singular"></span></h4>
            </div>
            <div class="px-4 pb-3">
                <iframe src="about:blank" id="search_iframe" name="search_iframe" class="modal_iframe"></iframe>
            </div>
        </div>   
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Chiudi</button>
        </div>
    </div>
  </div>
</div>

<!-- MODAL UPLOADS -->
<div class="modal fade" id="uploads_modal" tabindex="-1" role="dialog" aria-labelledby="uploads_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content position-relative">
        <div class="position-absolute top-0 end-0 mt-2 me-2 z-1">
            <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
            <div class="rounded-top-3 py-3 ps-4 pe-6 bg-body-tertiary">
              <h4 class="mb-1" id="uploads_modal_label">File allegati</h4>
            </div>
            <div class="p-4 pb-4">
                <div class="row g-3 load_modal_files"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Chiudi</button>
        </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- MODAL VIEW CLOUD IMAGES -->
<div class="modal fade" id="view_cloud_modal" tabindex="-1" role="dialog" aria-labelledby="view_cloud_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
    <div class="modal-content position-relative">
        
        <div class="position-absolute top-0 end-0 mt-2 me-2 z-1">
            <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
            <div class="rounded-top-3 py-3 ps-4 pe-6 bg-body-tertiary">
              <h4 class="mb-1" id="uploads_modal_label">File allegati</h4>
            </div>
            <div class="p-4 pb-4">
                <div class="row g-3 load_modal_cloud_files"></div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-bs-dismiss="modal">Chiudi</button>
        </div>
        
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
