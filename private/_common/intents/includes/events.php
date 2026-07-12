<span class="text-bg-primary px-3 py-2 text-uppercase">Dettagli evento/corso/seminario</span>

    <?php
    echo $app::form_element($data['hidden_columns']['archy_events.title'],'col-md-6');

    echo $app::form_element($data['hidden_columns']['archy_events.category_id'],'col-md-3');
    echo $app::form_element($data['hidden_columns']['archy_events.total_credits'],'col-md-3');
    echo $app::form_element($data['hidden_columns']['archy_events.minimum_credits'],'col-md-3');
    echo $app::form_element($data['hidden_columns']['archy_events.max_subscribers'],'col-md-3');

    echo $app::form_element($data['hidden_columns']['archy_events.cost_centre_id'],'col-md-3');
    echo $app::form_element($data['hidden_columns']['archy_events.accounting_reason'],'col-md-6');
    echo $app::form_element($data['hidden_columns']['archy_events.tax_rate_id'],'col-md-3');

    echo $app::form_element($data['hidden_columns']['archy_events.contact_mail'],'col-md-3');
    echo $app::form_element($data['hidden_columns']['archy_events.contact_phone'],'col-md-3');
    echo $app::form_element($data['hidden_columns']['archy_events.image'],'col-md-3');
    echo $app::form_element($data['hidden_columns']['archy_events.docs'],'col-md-3');

    echo $app::form_element($data['hidden_columns']['archy_events.description'],'col-md-12');
    ?>



<div class="accordion mt-5" id="eventsAccordion">
    <div class="accordion-item">
        <h2 class="accordion-header" id="eventCertificateHeading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#eventCertificateCollpase" aria-expanded="true" aria-controls="eventCertificateCollpase">Attestato finale</button>
        </h2>
        <div class="accordion-collapse collapse" id="eventCertificateCollpase" aria-labelledby="eventCertificateHeading" data-bs-parent="#eventsAccordion">
            <div class="accordion-body">
                <div class="row g-3">
                    <?php echo $app::form_element($data['hidden_columns']['archy_events.model_width'],'col-md-4'); ?>
                    <?php echo $app::form_element($data['hidden_columns']['archy_events.model_height'],'col-md-4'); ?>                            
                    <?php echo $app::form_element($data['hidden_columns']['archy_events.model_background'],'col-md-4'); ?>
                    <?php echo $app::form_element($data['hidden_columns']['archy_events.model_template'],'col-md-12'); ?>
                    <?php echo $app::form_element($data['hidden_columns']['archy_events.model_css'],'col-md-12'); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="accordion-item">
        <h2 class="accordion-header" id="eventCommunicationHeading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#eventCommunicationCollpase" aria-expanded="true" aria-controls="eventCommunicationCollpase">Comunicazioni</button>
        </h2>
        <div class="accordion-collapse collapse" id="eventCommunicationCollpase" aria-labelledby="eventCommunicationHeading" data-bs-parent="#eventsAccordion">
            <div class="accordion-body">
                <div class="row g-3">
                    <?php echo $app::form_element($data['hidden_columns']['archy_events.event_mail_subject'],'col-md-6'); ?>   
                    <?php echo $app::form_element($data['hidden_columns']['archy_events.event_mail_body'],'col-md-12'); ?>
                </div>
            </div>
        </div>
    </div>
</div>


<span class="text-bg-primary px-3 py-2 text-uppercase">Extra eventi</span>
          
    <div class="row mt-3" id="extras">
        <?php
        if(isset($_GET['id'])):
                $event_id=$_GET['id'];
                $event=new Event($_GET['id']);
                foreach($event->extras as $extra):
                        $_GET['id']=$extra->id;
                        $event->load_event_extras($extra->id);
                endforeach;
                $_GET['id']=$event_id;
        endif;

        ?>
    </div>
    <?php if(@$_GET['safe']=='') { ?>
    <div class="row mt-3">
        <div class="col-2">
            <button type="button" class="btn btn-falcon-default btn-sm me-1 mb-1" id="add_extra"><span class="fas fa-plus me-1" data-fa-transform="shrink-3"></span>Aggiungi extra</button>
        </div>
    </div>
    <?php } ?>

            
<span class="text-bg-primary px-3 py-2 text-uppercase">Sessioni</span>
    <div class="row mt-3" id="session">
        <div class="accordion" id="sessions_accordion">
                <?php
                if(isset($_GET['id']))://editing
                        $event_id=$_GET['id'];
                        //$event=new Event($_GET['id']);
                //var_dump($event->sessions);
                        foreach($event->sessions as $session):
                                $_GET['id']=$session->id;
                                $event->event_session($id=$session->id,$visibility='out',$session->speeches,$session->extras);
                        endforeach;
                        $_GET['id']=$event_id;
                else://new event
                        $now=new DateTime();
                        $event=new Event();
                        $event->event_session($id=$now->format('Ymdhisv'),$visibility='in');
                endif;
                ?>
        </div>
        <?php if(@$_GET['safe']=='') { ?>
        <div class="row mt-3">
            <div class="col-2">
		<button type="button" class="btn btn-falcon-default btn-sm me-1 mb-1" id="add_session"><span class="fas fa-plus me-1" data-fa-transform="shrink-3"></span>Aggiungi sessione</button>
            </div>
        </div>
        <?php } ?>
    </div>
        
    

<script type="text/javascript">
	window.onload=function(){
                const confTime = {
                    enableTime: true,
                    noCalendar: true,
                    time_24hr: true
                };
                const confDate = {
                    dateFormat: "d/m/y"
                };
		$('#add_extra').click(function(){
                    $.ajax({
                            type: "POST",
                            url: "/archy/app.php?do_action=load_event_extras",
                            cache: false,
                            success: function(html){
                                    $('#extras').append(html);
                            }
                    });
		});
		$('#extras').sortable({handle:".move",cancel: ''});
		$('#extras').on('click','.trash',function(){
			if(confirm('Rimuovere extra evento?')){                            
                            $(this).closest('.event-extras').slideUp(function(){$(this).remove()});
                            var id=$(this).closest('.event-extras').find('input:first').val();
                            $.ajax({
                                    type: "POST",
                                    url: "/archy/app.php?do_action=clear_event_add",
                                    data: 'id='+id+'&table=archy_events_extras&domain=event',
                                    cache: false
                            });
			}
		});
                
                
		$('#add_session').click(function(){
                        alert("Aggiungi una nuova sessione?");
			$.ajax({
				type: "POST",
				url: "/archy/app.php?do_action=load_event_session",
				data: 'visibility=out',
				cache: false,
				success: function(html){
					$('#sessions_accordion').append(html);
					$('.row.speakers,.row.session_extras',$('#sessions_accordion .accordion-item').last()).sortable({handle:".move"});
					flatpickr(".form-date-control", confDate);
					tinymce.init({selector: 'textarea'});
                                        $('.select2').select2({ placeholder: 'Seleziona ev...', allowClear: true }); // patch per select2
				}
			});
		});
		$('#sessions_accordion').sortable({handle:".move_session",cancel: ''});
		$('#sessions_accordion').on('click','.trash_session',function(){
			if(confirm('Rimuovere questa sessione?')){
                            $(this).parents('.accordion-item').slideUp(function(){$(this).remove()});
                            var id=$(this).parents('.accordion-item').find('input:first').val();
                            $.ajax({
                                    type: "POST",
                                    url: "/archy/app.php",
                                    data: 'do_action=dispatch_ajax_load&class=Event&method=clear_session&id='+id,
                                    cache: false
                            });
			}
		});
		
        
                $('#sessions_accordion').on('input','input[id^="archy_events_sessions_title"]',function(){
			if($(this).val()==''){
				$('.accordion-header button',$(this).parents('.accordion-item')).text('Sessione');
			}else{
				$('.accordion-header button',$(this).parents('.accordion-item')).text($(this).val());
			}
		});
		$('input[id^="archy_events_sessions_title"]').trigger('input');
		
                
                $('#sessions_accordion').on('click','.add_speech',function(){
                        alert("Aggiungi un nuovo intervento?");
			var id=$(this).data('session');
			var target=$('.row.speakers',$(this).parents('.accordion-item'));
			var d=new Date();
			var id_suffix=d.getTime();
			$.ajax({
				type: "POST",
				url: "/archy/app.php?do_action=load_event_speech",
				data: 'id='+id+'&id_suffix='+id_suffix,
				cache: false,
				success: function(html){
					target.append(html);
					//dates();
                                        flatpickr(".form-date-control", confDate);
                                        flatpickr(".form-time-control", confTime);
					tinymce.init({selector: 'textarea'});
                                        $('.select2').select2({ placeholder: 'Seleziona', allowClear: true }); // patch per select2
				}
			});
		});
		$('.row.speakers').sortable({handle:".move",cancel: ''});
		$('#sessions_accordion').on('click','.speakers p .trash',function(){
			if(confirm('Rimuovere questo intervento?')){
                            $(this).closest('.event-speech').slideUp(function(){$(this).remove()});
                            var id=$(this).closest('.event-speech').find('input:first').val();
                            $.ajax({
                                    type: "POST",
                                    url: "/archy/app.php?do_action=clear_event_add",
                                    data: 'id='+id+'&table=archy_events_speeches',
                                    cache: false
                            });
			}
		});
		
		$('#sessions_accordion').on('click','.add_session_extra',function(){
			var id=$(this).data('session');
			var scope=$(this).parents('.accordion-item');
			$.ajax({
				type: "POST",
				url: "/archy/app.php?do_action=load_session_extra",
				data: 'id='+id,
				cache: false,
				success: function(html){
					$('.row.session_extras',scope).append(html);
				}
			});
		});
		$('.row.session_extras').sortable({handle:".move",cancel: ''});
		$('#sessions_accordion').on('click','.session_extras p .trash',function(){
			if(confirm('Rimuovere questo extra?')){
                            $(this).closest('.event-session-extras').slideUp(function(){$(this).remove()});
                            var id=$(this).closest('.event-session-extras').find('input:first').val();
                            $.ajax({
                                    type: "POST",
                                    url: "/archy/app.php?do_action=clear_event_add",
                                    data: 'id='+id+'&table=archy_events_extras&domain=session',
                                    cache: false
                            });
			}
		});
		
		$('#sessions_accordion').on('click','.duplicate_session',function(){
			$(".date").datepicker("destroy");
			tinyMCE.remove();
			
			var session=$(this).parents('.accordion-item').get(0).outerHTML;
			$('#sessions_accordion').append(session);
			
			var d=new Date();
			var id=d.getTime();
			var scope=$('#sessions_accordion .panel:last');
			var title_input=$('input[id^="archy_events_sessions_title"]',scope);
			title_input.val(title_input.val()+' (Copia)').trigger('input');
			$('.panel-title a[data-toggle="collapse"]',scope).attr('href','#'+id);
			$('.panel-collapse',scope).attr('id',id);
			var counter=0;
			$('input.date',scope).each(function(){
				$(this).attr('id',id+'_'+counter);
				counter++;
			});
			$('textarea.editor',scope).each(function(){
				$(this).attr('id',id+'_'+counter);
				counter++;
			});
			$('.row.speakers,.row.session_extras',$('#sessions_accordion .panel').last()).sortable({handle:".move"});
			flatpickr(".datetimepicker", {});
                        tinymce.init({selector: 'textarea'});
		});
	}
</script>