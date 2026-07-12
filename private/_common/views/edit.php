<?php
// Modifica RECORD
$title=$app->get_view_title();
$data=$app->get_form();
//echo $app->intent->help['edit'];
if($app->user_can_add_to($app->intent->table) or isset($_GET['id'])):
?>

<form role="form" class="row g-3 needs-validation" id="editForm" novalidate="" method="post" action="/archy/app.php" enctype="multipart/form-data">

    <div class="col-12 mb-3">
        <div class="row">
            <div class="d-flex justify-content-end mt-1 mt-sm-0">
                <div class="btn-toolbar" role="toolbar" aria-label="Eteria toolbars">
                    <div class="btn-group me-2 mb-2" role="group">
                        <?php if(@$_GET['id']!='' && isset($app->intent->add)):?>
                           <a class="btn btn-info" href="/archy/app.php?intent=<?php echo $app->intent->table?>&view=edit<?php if(isset($app->intent->filter) && $app->intent->filter!=''){echo'&intent_filter='.$app->intent->filter;} ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $app->intent->add?>"><span class="fas fa-plus"></span> Nuovo</a>
                        <?php endif?>
                        <a class="btn btn-info" href="/archy/app.php?intent=<?php echo $app->intent->table?>&view=grid<?php if(isset($app->intent->filter) && $app->intent->filter!=''){echo'&intent_filter='.$app->intent->filter;} ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $app->intent->title?>">
                           <span class="fas fa-list"></span> Elenco</a>
                    </div>
                    <div class="btn-group me-2 mb-2" role="group">
                        <!--<a class="btn btn-secondary me-1 mb-1" data-toggle="tooltip" data-placement="bottom" title="Indietro" href="javascript:void(0)" onClick="javascript:history.back()"><span class="fas fa-backward"></span></a>-->
                        <a class="btn btn-secondary" href="javascript:void(0)" onClick="location.reload()" data-toggle="tooltip" data-placement="bottom" title="Reimposta modulo"><span class="fas fa-sync"></span> Ricarica</a>
                        <!--<a class="btn btn-secondary me-1 mb-1" data-toggle="tooltip" data-placement="bottom" title="Avanti" href="javascript:void(0)" onClick="javascript:history.forward()"><span class="fas fa-forward"></span></a>-->
                    </div>
                    <div class="btn-group me-2 mb-2" role="group">
                        <?php if(@$_GET['safe']=='') { ?>                        
                        <button type="submit" class="btn btn-success" name="save_and_stay"><?php echo @$_GET['id']!=''? 'Aggiorna':'Salva'?> e rimani</button>
                        <?php if(isset($app->intent->add)):?>
                            <button type="submit" class="btn btn-success" name="save_and_new"><?php echo @$_GET['id']!=''? 'Aggiorna':'Salva'?> e nuovo</button>
                        <?php endif?>
                        <button type="submit" class="btn btn-success" name="save"><?php echo @$_GET['id']!=''? 'Aggiorna':'Salva'?> e chiudi</button>
                        
                        <?php } else { ?>
                            <a class="btn btn-danger" href="/archy/app.php?intent=<?php echo $app->intent->table?>&view=grid<?php if(isset($app->intent->filter) && $app->intent->filter!=''){echo'&intent_filter='.$app->intent->filter;} ?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $app->intent->title?>">
                               Chiudi
                            </a>
                        <?php } ?>
                    </div>
                    <div class="btn-group me-2 mb-2" role="group">
                        <?php if(isset($app->intent->restore_button_in_edit) && $app->intent->restore_button_in_edit){ ?>
                            <a class="btn btn-warning regenerates_attachments" data-rel="<?php echo $app->intent->table; ?>" href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Rigenera allegati"><span class="fas fa-sync"></span> Rigenera allegati</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>	
        
    </div>
    <div class="col-12 pl-4 w-100"><h4 class="d-inline-block"><?php echo $title; ?></h4></div>
    <?php
    foreach($data['columns'] as $column):
       $custom_wrapper=isset($column['custom_wrapper'])? $column['custom_wrapper'] : NULL;
       echo $app::form_element($column,$custom_wrapper);
    endforeach;
    ?>
     
      <?php
      if(isset($app->intent->include_in_form)):
        require_once $main_root.'/private/_common/'.$app->intent->include_in_form;
      endif;
      if(isset($app->intent->include_additional_fields)):    
        ?>
        <div>
            <legend>Campi aggiuntivi</legend>
            <div class="row">
                <?php $app->additional_fields('form'); ?>
            </div>
        </div>
        <?php
      endif;
      ?>
      <input type="hidden" name="do_action" value="store">
      <?php if(isset($app->intent->extra_action)):?>
         <input type="hidden" name="extra_action" value="<?php echo $app->intent->extra_action?>">
      <?php endif?>
      <?php if(isset($app->intent->before_update) and isset($_GET['id'])):?>
         <input type="hidden" name="before_update" value="<?php echo $app->intent->before_update?>">
      <?php endif?>
      <?php if(isset($app->intent->after_update) and isset($_GET['id'])):?>
         <input type="hidden" name="after_update" value="<?php echo $app->intent->after_update?>">
      <?php endif?>
      <input type="hidden" name="action" value="<?php echo @$_GET['id']!=''? 'update':'insert into'?>">
      <input type="hidden" name="intent" value="<?php echo $app->intent->table?>">
      <input type="hidden" name="intent_id" value="<?php echo @$_GET['id']?>">
      <input type="hidden" name="intent_name" value="<?php echo $app->intent->singular?>">
      <?php if(isset($app->intent->filter)): ?>
      <input type="hidden" name="intent_filter" value="<?php echo $app->intent->filter; ?>">
      <?php endif?>
      <?php if(isset($app->intent->update_extra_notice) and isset($_GET['id'])):?>
      	
         <input type="hidden" name="extra_notice" value="<?php echo $app->intent->update_extra_notice?>">
         
      <?php endif?>
      
</form>
   
   
<?php
else:
	echo $app->options['single_record'][$app->intent->table];
endif;
?>