<?php
$recipients=isset($_GET['id'])? unserialize($data['hidden_columns']['archy_messages.recipients']['value']) : [];
if(!$recipients):
	$recipients=[];
endif;
$sms_disabled=$app->config['sms']['enabled']===true? '':'disabled';
$message=new Message();
$list=$message->get_list();
?>

    <div class="col-md-3">
         
        <label class="form-label" for="archy_messages_media">Canale <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></label>
        <select class="form-select" required="required" id="archy_messages_media" name="main[media]">
            <option value="E-mail">E-mail</option>
            <option value="SMS" <?php echo $data['hidden_columns']['archy_messages.media']['value']=='SMS'? 'selected':''?> <?php echo $sms_disabled?>>SMS</option>
        </select>
        <div class="invalid-feedback">Campo obbligatorio</div>
        <?php if(isset($_GET['id'])):?>
        <div class="bg-warning" style="padding:15px; margin-bottom:15px;">
            <strong>Data invio:<br>
            <?php echo $data['hidden_columns']['archy_messages.dispatch_time']['value']?></strong>
        </div>
        <?php endif?>
        <hr> 
        <label class="form-label">Destinatari <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></label>
        <ul class="list-none" id="recipients">
           <?php foreach($list as $item):?>
              <?php $checked=in_array($item->domain,$recipients)? 'checked':'';?>
            <li>
                <div class="form-check">
                    <label class="form-check-label"><?php echo $item->label?></label>
                    <input type="checkbox" name="main[recipients][]" value="<?php echo $item->domain?>" class="form-check-input first_level" <?php echo $checked?>> 
                </div>
                 <?php if(count($item->categories)):?>
                    <ul class="list-none pl-2">
                    <?php foreach($item->categories as $category_id=>$category):?>
                            <?php $search_in=is_array(@$recipients[$item->domain])? $recipients[$item->domain] : [];?>
                       <?php $checked=in_array($category_id,$search_in)? 'checked':'';?>
                        <li>
                        <div class="form-check">
                             <label class="form-check-label"><?php echo $category?></label>
                             <input type="checkbox" class="form-check-input" name="main[recipients][<?php echo $item->domain?>][]" value="<?php echo $category_id?>" <?php echo $checked?>> 
                        </div>
                        </li>
                    <?php endforeach?>
                    </ul>
                 <?php endif?>
                </li>
           <?php endforeach?>
        </ul>
        
    </div>
    <div class="col-md-9">         
        <div class="row g-3">
            	<?php echo $app::form_element($data['hidden_columns']['archy_messages.sender_name'],"col-md-6");?>
                <?php echo $app::form_element($data['hidden_columns']['archy_messages.sender_mail'],"col-md-6");?>
            
            
            <div class="form-group" id="form_subject_holder">
               <label class="form-label" for="form_subject">Oggetto <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></label>
               <input type="text" class="form-control" required="required" name="main[subject]" value="<?php echo @$data['hidden_columns']['archy_messages.subject']['value']?>" id="form_subject">
               <div class="invalid-feedback">Campo obbligatorio</div>
            </div>
            <div class="col-sm-8">
                <div id="plain_editor1">
                    <label class="form-label" for="1">Messaggio <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></label>
                    <textarea class="tinymce d-none" data-tinymce="data-tinymce" required name="main[message]" id="archy_messages_message" data-height="400"><?php echo @$data['hidden_columns']['archy_messages.message']['value']; ?></textarea>
                    <div class="invalid-feedback">Campo obbligatorio</div>
                </div>
            </div>
            <?php
                $Placeholder = new Placeholder();
                echo '<div class="col-4">
                        <div class="placeholder_container" data-rel="archy_messages_message">
                        <p><strong>Placeholder</strong></p>
                        '.$Placeholder->getAvailablePlaceholders('archy_messages').'
                        </div></div>'; 
            ?>
            <button type="button" class="btn btn-success" id="send_message">Invia messaggio</button>
        </div>
      </div>
 
      <?php //print_r($recipients); ?>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function () {
    document.addEventListener('change', function (e) {

        const el = e.target;

        if (!el.matches('input[type="checkbox"]')) {
            return;
        }

        /*
        =========================
        CHECKBOX "TUTTI"
        =========================
        */
        if (
            el.name === 'main[recipients][]' &&
            el.value === '*'
        ) {

            const allCheckboxes = document.querySelectorAll(
                'input[name^="main[recipients]"]'
            );

            allCheckboxes.forEach(cb => {
                cb.checked = el.checked;
            });

            return;
        }

        /*
        =========================
        CHECKBOX PADRE
        main[recipients][]
        =========================
        */
        if (
            el.name === 'main[recipients][]' &&
            el.value !== '*'
        ) {

            const parentKey = el.value;

            const children = document.querySelectorAll(
                `input[name="main[recipients][${parentKey}][]"]`
            );

            children.forEach(child => {
                child.checked = el.checked;
            });

            return;
        }

        /*
        =========================
        CHECKBOX FIGLIO
        main[recipients][suppliers][]
        =========================
        */
        const match = el.name.match(
            /^main\[recipients\]\[([^\]]+)\]\[\]$/
        );

        if (match) {

            const parentKey = match[1];

            const parentCheckbox = document.querySelector(
                `input[name="main[recipients][]"][value="${parentKey}"]`
            );

            if (parentCheckbox) {

                const anyChecked = document.querySelector(
                    `input[name="main[recipients][${parentKey}][]"]:checked`
                );

                parentCheckbox.checked = !!anyChecked;
            }
        }

    });
   /* $('#recipients input[value="*"]').click(function(){
            var targets=$('#recipients input[type="checkbox"]').not('[value="*"]');
            if($(this).is(':checked')){
                    targets.prop('checked',false).prop('disabled',true);
            }else{
                    targets.prop('disabled',false);
            }
    });

    $('.first_level').click(function(){
            var targets=$('ul input[type="checkbox"]',$(this).parents('li'));
            if($(this).is(':checked')){
                    targets.prop('checked',true).prop('disabled',true);
            }else{
                    targets.prop('checked',false).prop('disabled',false);
            }
    });*/

    <?php if(isset($_GET['id'])): ?>
        $('#recipients input[type="checkbox"]').prop('checked', false);

<?php
if(!empty($recipients)):

    foreach($recipients as $key => $value):

        /*
        =========================
        MACRO CATEGORIA
        =========================
        */
        if(!is_array($value)):
?>

            $('#recipients input[name="main[recipients][]"][value="<?php echo $value; ?>"]')
                .prop('checked', true);

<?php

        /*
        =========================
        SOTTO CATEGORIE
        =========================
        */
        else:

            foreach($value as $id):
?>

                $('#recipients input[name="main[recipients][<?php echo $key; ?>][]"][value="<?php echo $id; ?>"]')
                    .prop('checked', true);

<?php
            endforeach;

        endif;

    endforeach;

endif;
?>
    <?php endif;
    ?>

    $('#send_message').click(function(){
            /*$('.validate').trigger('check_validity');
            const hasRecipients = document.querySelector(
                'input[name="main[recipients][]"]:checked'
            );*/

            if (!document.querySelector('input[name="main[recipients][]"]:checked')) {
                   // if($('#recipients input[type="checkbox"]:checked').length==0){
                            alert('Seleziona uno o più destinatari per questo messaggio.');
                    
            }else{
                    if(confirm('Conferma invio:\nSi è certi di voler inviare questo messaggio?')){
                            //$('#console').slideDown(function(){
                                    //disable_buttonsbar();
                                    $('input[name="do_action"]').val('instant_dispatch');
                                    var timestamp=current_timestamp();
                                    var dispatchTimeInput = $('#editForm input[name="main[dispatch_time]"]');
                                    if (dispatchTimeInput.length) {
                                        dispatchTimeInput.val(timestamp);
                                    } else {
                                        $('#editForm').append('<input type="hidden" name="main[dispatch_time]" value="'+timestamp+'">');
                                    }
                                    instant_dispatch(1);
                           // });
                    }
            }
    });


    function instant_dispatch(page){
        var post_data=$('#editForm').serialize();
        post_data+='&main_message='+tinymce.get('archy_messages_message').getContent();
        post_data+='&page='+page;
        //console.log(post_data);
        $.ajax({
            type: "POST",
            url: "/archy/app.php",
            data: post_data,
            dataType: 'json',
            cache: false,
            success: function(response){
                    console.log(response);
                    //$("#progressbar").progressbar("value", response.complete_percentage);
                    if(response.message!=''){
                            alert(response.message);
                    }
                    
                    if(response.next_page!=''){
                            instant_dispatch(response.next_page);
                    }else{
                        $('input[name="do_action"]').val('store');

                        /*
                         * La modifica del solo campo do_action non genera una
                         * nuova richiesta: terminato l'accodamento, inviamo
                         * realmente il form per eseguire AppActions::store().
                         */
                        var editor = tinymce.get('archy_messages_message');
                        if (editor) {
                            editor.save();
                        }

                        if (!$('#editForm input[name="save_and_stay"]').length) {
                            $('#editForm').append('<input type="hidden" name="save_and_stay" value="1">');
                        }

                        document.getElementById('editForm').requestSubmit();
                       
                        /*$("#send_message").html('Inviate '+response.total_elements+' mail...');
                        $('#send_message').addClass('disabled');
                        $('#send_message').prop('disabled',true);*/
                    }
            }
        });	
    }

});
</script>
<style type="text/css">

#console{
	display:none;
	background: #fafafa;
	padding: 20px 20px 40px 20px;
	border: 1px solid #ccc;
	margin-bottom: 25px;
}
</style>
