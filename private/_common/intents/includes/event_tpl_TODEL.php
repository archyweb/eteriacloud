<fieldset>
	<div class="row event_item">
   	
		<?php
        // var_dump($event);

        if($name=='main'):
			// echo $app::form_element($event['archy_events_sessions.id'],"col-md-12",$name,$name_suffix='[]');
		endif;
        echo $app::form_element($event['archy_events_sessions.title'],"col-md-12",$name,$name_suffix='[]');
		echo $app::form_element($event['archy_events_sessions.description'],"col-md-12",$name,$name_suffix='[]',$id_suffix=$event_index);
        ?>
        <div class="col-xs-12"><hr></div>
        <?php
		echo $app::form_element($event['archy_events_sessions.credits'],"col-md-3",$name,$name_suffix='[]');
		echo $app::form_element($event['archy_events_sessions.location_id'],"col-md-3",$name,$name_suffix='[]');
		echo $app::form_element($event['archy_events_sessions.members_price'],"col-md-3",$name,$name_suffix='[]');
		echo $app::form_element($event['archy_events_sessions.price'],"col-md-3",$name,$name_suffix='[]');        
		?>
        <div class="col-xs-12"><hr></div>
        <?php include 'event_date_tpl.php'?>
        
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped" id="extras_table">
                    <thead>
                        <tr><th colspan="3">Costi Extra</th></tr>
                    </thead>
                    <tbody>
                        <?php 
                        $extras=$event['archy_events_sessions.extras']['value'];
                        if($app->is_serialized($extras)):
                            $extras=unserialize($extras);
                            if(count($extras)>0):
                                foreach($extras as $index=>$extra):
                                    include 'event_extra_tpl.php';
                                endforeach;
                            endif;
                        endif;
                        ?>
                    </tbody>
                    <tfoot>
                        <tr><th colspan="3" style="text-align:center">
                        <button type="button" class="btn btn-default btn-sm add_extra" data-event_index="<?php echo @$event_index?>"><i class="glyphicon glyphicon-plus"></i> Aggiungi costo extra</button>
                        </th></tr>
                    </tfoot>
                </table>
            </div>
        </div>
      
		<?php echo $app::form_element($event['archy_events_sessions.certificates_model'],"col-md-12",$name,$name_suffix='[]',$id_suffix=$event_index)?>
      
        <?php /*
        <?php if($name=='event'):?>
            <div class="col-xs-12"><hr></div>
            <div class="col-md-6">
                <button type="button" class="btn btn-primary add_event"><i class="glyphicon glyphicon-plus"></i> Aggiungi evento</button> <span class="msg text-success">Aggiunto</span>
            </div>
            <div class="col-md-6" style="text-align:right">
                <button type="button" class="btn btn-danger delete_event"><i class="glyphicon glyphicon-trash"></i> Elimina evento</button>
            </div>
        <?php endif?>
        */ ?>
	</div>
</fieldset>