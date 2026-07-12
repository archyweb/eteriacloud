<?php
require_once $main_root.'/private/_common/intents/settings/'.$_GET['domain'].'.php';
?>
<!-- Modal -->
<div class="modal fade" id="search_modal" tabindex="-1" role="dialog" aria-labelledby="search_modal_label" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="search_modal_label">Seleziona <span id="intent_singular"></span></h4>
      </div>
      <div class="modal-body">
         <iframe src="about:blank" class="modal_iframe"></iframe>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
