<style>
  #user-info-text {
    color: green;
  }
</style>

<h3>Genera attestati</h3>
<hr>
<!--<div>
E' possibile la stampa delle tessere in uno specifico intervallo del numero di tessera (lasciano in bianco i campi per la data di versamento)<br>
E' possibile la stampa delle tessera in uno specifico intervallo della data di versamento del rinnovo (lasciando in bianco i campi del numero tessera)<br>
Oppure, selezionando tutti i campi, stampare le tessere per numero tessera e data rinnovo.<br>
<br>-->
</div>
<div class="row dashboard" style="margin-bottom:15px;">
  <div class="col-md-12">
    <form method="post" action="/archy/app.php?do_action=generate_events_certificates">
        
        <div class="panel-group" id="sessions_accordion" role="tablist" aria-multiselectable="true">
            
            <?php
                $ev= new Event();
                echo $ev->load_events_for_certificates();
            ?>
            
        </div>
      <button type="submit" id="submitButton" class="btn btn-primary">Genera attestati</button>

    </form>
  </div>
</div>
