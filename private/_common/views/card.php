<form role="form" class="row g-3" novalidate="" action="/archy/app.php?do_action=creaCard" method="post">
    <div class="col-12 mb-3">
        <div class="row justify-content-center justify-content-sm-between">
            <div class="col-sm-auto text-center">
                <h5 class="d-inline-block p-2">Stampa tessere</h5>
            </div>
            <div class="col-sm-auto d-flex justify-content-center mt-1 mt-sm-0">
                <div class="btn-group" role="group">
                    <a class="btn btn-secondary me-1 mb-1" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Reimposta modulo"><span class="fas fa-sync"></span></a>
                    <a class="btn btn-success me-1 mb-1" href="/assets/documents/memberships/card_all.pdf">Visualizza ultima stampa</a>
                
                </div>
            </div>	
        </div>
    </div>
    <div class="col-12 mb-3">
        <div class="card mb-3">
            <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(/admin_template/assets/img/icons/spot-illustrations/corner-4.png);">
            </div> <!--/.bg-holder-->
            <div class="card-body position-relative">
              <div class="row">
                <div class="col-lg-8">
                  <p class="mt-2">
                      E' possibile la stampa delle tessere in uno specifico intervallo del numero di tessera (lasciano in bianco i campi per la data di versamento)<br>
          E' possibile la stampa delle tessera in uno specifico intervallo della data di versamento del rinnovo (lasciando in bianco i campi del numero tessera)<br>
          Oppure, selezionando tutti i campi, stampare le tessere per numero tessera e data rinnovo.<br>
                  </p>
                </div>
              </div>
            </div>
          </div>
    </div>    
    <div class="col-md-3">
            <label class="form-label" for="fromCard">Dalla tessera numero</label>
            <input type="text" class="form-control" name="from_card" id="fromCard" placeholder="prima tessera">
            <div class="form-text">Inserisci il numero della prima tessera</div>
      </div>

      <div class="col-md-3">
            <label class="form-label" for="toCard">Alla tessera numero</label>
            <input type="text" class="form-control" name="to_card" id="toCard" placeholder="ultima tessera">
            <div class="form-text">Inserisci il numero dell'ultima tessera</div>
      </div>

      <div class="col-md-3">
            <label class="form-label" for="fromDate">Dalla data di versamento</label>
            <div class="input-group">                
                <div class="input-group-text"><span class="fas fa-calendar-alt"></span></div>
                <input type="text" class="form-control form-date-control datetimepicker" id="fromDate" name="from_date" placeholder="dd/mm/yy" data-options='{"dateFormat":"d/m/Y","disableMobile":true,"allowInput":true}'>
            </div>
            <div class="form-text">Inserisci una data per la prima tessera</div>
      </div>

      <div class="col-md-3">
            <label class="form-label" for="toDate">Alla data di versamento</label>
            <div class="input-group">                
                <div class="input-group-text"><span class="fas fa-calendar-alt"></span></div>
                <input type="text" class="form-control form-date-control datetimepicker" id="toDate" name="to_date" placeholder="dd/mm/yy" data-options='{"dateFormat":"d/m/Y","disableMobile":true,"allowInput":true}'>
            </div>
            <div class="form-text">Inserisci una data per l'ultima tessera</div>
      </div>
       <div class="col-12"> 
            <button type="submit" id="submitButton" class="btn btn-primary">Stampa tessere</button>
       </div>
</form>