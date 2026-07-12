<hr>
<div class="dropdown archy-grid-extra-filters">
    <button class="btn btn-outline-secondary dropdown-toggle archy-grid-extra-filters-toggle" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <span class="fas fa-filter"></span> Filtri
    </button>
    <div class="btn-group px-2 archy-grid-extra-filters-menu dropdown-menu" role="group">
        <label class="btn btn-falcon-primary me-1 mb-1"><input type="checkbox" value="Cliente" class="filter_registry"> Clienti</label>
        <label class="btn btn-falcon-primary me-1 mb-1"><input type="checkbox" value="Fornitore" class="filter_registry"> Fornitori</label>
        <?php if($app->config['association']): ?>
        <label class="btn btn-falcon-primary me-1 mb-1"><input type="checkbox" value="Socio" class="filter_registry"> Soci</label>
        <?php endif ?>
       <label class="btn btn-falcon-primary me-1 mb-1"><input type="checkbox" value="Generica" class="filter_registry"> Generiche</label>
       <!--<label class="btn"><span id="filter_msg"></span></label>-->
    </div>
</div>
