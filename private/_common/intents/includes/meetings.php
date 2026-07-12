<?php
$examinee=$app->db->query("
	select e.*, r.label, r.tax_code, c.category
	from archy_memberships_examinee as e 
	left join archy_registry as r on (e.registry_id=r.id)
	left join archy_registry_categories as c on (e.category_id=c.id)
	where e.request_status=2
");
$quitted=$app->db->query("
	select m.*, r.label, r.tax_code, c.category
	from archy_memberships as m 
	left join archy_registry as r on (m.registry_id=r.id)
	left join archy_registry_categories as c on (m.category_id=c.id)
	where m.status='3'
");
$form=$data['hidden_columns'];

?>



<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="first-tab" data-bs-toggle="tab" href="#tab-first" role="tab" aria-controls="tab-first" aria-selected="true">Adunanza</a></li>
    <?php if(!isset($_GET['id'])):?>
    <li class="nav-item"><a class="nav-link" id="second-tab" data-bs-toggle="tab" href="#tab-second" role="tab" aria-controls="tab-second" aria-selected="false">Candidati soci</a></li>
    <li class="nav-item"><a class="nav-link" id="third-tab" data-bs-toggle="tab" href="#tab-third" role="tab" aria-controls="tab-third" aria-selected="false">Associati dimessi</a></li>
    <li class="nav-item"><a class="nav-link" id="fourth-tab" data-bs-toggle="tab" href="#tab-fourth" role="tab" aria-controls="tab-fourth" aria-selected="false">Associati da cessare</a></li>
    <li class="nav-item"><a class="nav-link" id="fifth-tab" data-bs-toggle="tab" href="#tab-fifth" role="tab" aria-controls="tab-fifth" aria-selected="false">Verbale adunanza</a></li>
    <?php endif; ?>
</ul>
<div class="tab-content border border-top-0 p-3" id="myTabContent">
    <div class="tab-pane fade show active" id="tab-first" role="tabpanel" aria-labelledby="first-tab">
        <div class="row">
            <?php
            echo $app::form_element($form['archy_meetings_book.title'],"col-md-4");
            echo $app::form_element($form['archy_meetings_book.meeting_category_id'],"col-md-4");
            echo $app::form_element($form['archy_meetings_book.place'],"col-md-4");
            echo $app::form_element($form['archy_meetings_book.date_time'],"col-md-4");
            //echo $app::form_element($form['archy_meetings_book.time'],"col-md-4");
            echo $app::form_element($form['archy_meetings_book.president'],"col-md-4");
            echo $app::form_element($form['archy_meetings_book.secretary'],"col-md-4");
            ?>
            <div class="col-md-12"><hr></div>
            <?php
            echo $app::form_element($form['archy_meetings_book.signed_report'],"col-md-4");
            ?>
         </div>
    </div>
    <div class="tab-pane fade" id="tab-second" role="tabpanel" aria-labelledby="second-tab">
        <div class="row">
            <div class="col-3">
                <label for="notify_by_mail">Notifica via mail</label>
                <select name="notify_by_mail" class="form-select js-choice">
                    <option value="none">Non inviare e-mail di notifica</option>
                    <option value="approved">Notifica solo associati accettati</option>
                    <option value="refused">Notifica solo associati respinti</option>
                    <option value="all">Notifica sia accettati che respinti</option>
                </select>               
            </div>
            <div class="col-3">
                <label for="actions_second">Azioni multiple</label>
                <select name="actions_second" class="bulk_action form-select js-choice">
                    <option value="">Seleziona</option>
                    <option value="approve">Approva tutti</option>
                    <option value="refuse">Respingi tutti</option>
                    <option value="delay">Nessuna azione</option>
                </select>               
            </div>
        </div>
        <hr>
        <div class="row gt-2">
            <div class="col">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="actions_second">
                       <thead>
                          <tr>
                             <th scope="col">Data richiesta</th>
                             <th scope="col">Candidato</th>
                             <th scope="col">Categoria</th>
                             <th scope="col">Azioni</th>
                          </tr>
                       </thead>
                       <tbody>
                       <?php while($member=mysqli_fetch_object($examinee)):?>

                          <tr>
                             <td><?php echo App::latin_date($member->request_date)?></td>
                             <td><?php echo $member->label.' ('.$member->tax_code.')'?></td>
                             <td><?php echo $member->category?></td>
                             <td>
                                <div class="form-check">
                                    <input class="form-check-input" id="examineeradio_1" type="radio" name="examinee[<?php echo $member->id?>]" value="approve"/>
                                    <label class="form-check-label" for="examinee_radio_1">Approva</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" id="examinee_radio_2" type="radio" name="examinee[<?php echo $member->id?>]" value="refuse"/>
                                    <label class="form-check-label" for="examinee_radio_2">Respingi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" id="examinee_radio_3" type="radio" name="examinee[<?php echo $member->id?>]" value="delay" checked/>
                                    <label class="form-check-label" for="examinee_radio_3">Nessuna azione</label>
                                </div>
                                 
                             </td>
                          </tr>

                       <?php endwhile?>
                       </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-third" role="tabpanel" aria-labelledby="third-tab">
        <div class="row">             
            <div class="col-3">
                <label for="notify_quitted_by_mail">Notifica via mail</label>
                <select name="notify_quitted_by_mail" class="form-select js-choice">
                   <option value="none">Non inviare e-mail di notifica</option>
                   <option value="ceased">Notifica cessazione via mail</option>
                </select>               
            </div>
            <div class="col-3">
                <label for="actions_third">Azioni multiple</label>
                <select name="actions_third" class="bulk_action form-select js-choice">
                    <option value="">Seleziona</option>
                    <option value="ceased">Imposta tutti come cessati</option>
                    <option value="delay">Nessuna azione</option>
                 </select>
            </div>
        </div>
        <hr>
        <div class="row gt-2">
            <div class="col">      
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="actions_third">
                       <thead>
                          <tr>
                             <th scope="col">Scadenza tessera</th>
                             <th scope="col">Candidato</th>
                             <th scope="col">Categoria</th>
                             <th scope="col">Azioni</th>
                          </tr>
                       </thead>
                       <tbody>
                       <?php while($member=mysqli_fetch_object($quitted)):?>

                          <tr>
                             <td><?php echo App::latin_date($member->membership_end)?></td>
                             <td><?php echo $member->label.' ('.$member->tax_code.')'?></td>
                             <td><?php echo $member->category?></td>
                             <td>
                                <div class="form-check">
                                    <input class="form-check-input" id="quitted_radio_1" type="radio" name="quitted[<?php echo $member->id?>]" value="ceased"/>
                                    <label class="form-check-label" for="quitted_radio_1">Imposta come cessato</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" id="quitted_radio_2" type="radio" name="quitted[<?php echo $member->id?>]" value="delay" checked/>
                                    <label class="form-check-label" for="quitted_radio_2">Nessuna azione</label>
                                </div>
                             </td>
                          </tr>

                       <?php endwhile?>
                       </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-fourth" role="tabpanel" aria-labelledby="fourth-tab">
        <div class="row gt-2">
            <div class="col">    
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="members_statuses">
                       <thead>
                            <tr>
                               <th scope="col">Socio</th>
                               <th scope="col">Motivazione</th>
                               <th scope="col">Azioni</th>
                            </tr>
                       </thead>
                       <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row gt-2">
            <div class="col">
                <button type="button" class="btn btn-default btn-sm" id="new_member_status"><span class="far fa-plus-square"></span> Aggiungi socio</button>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-fifth" role="tabpanel" aria-labelledby="fifth-tab">
        <div class="row">
            <?php echo $app::form_element($form['archy_meetings_book.unsigned_report'],"col-md-12")?>
        </div>
    </div>
</div>



<script type="text/javascript">
	window.onload=function(){
		$('#new_member_status').click(function(){
			$('#members_statuses tbody').append($('#member_status').html());
			$('.select_members').autocomplete({
				source: root+'archy/app.php?do_action=members_select2',
				select: function( event, ui ) {$(this).next().val(ui.item.id)}
			});
			
		});
		
		$('#members_statuses').on('click','.remove_row',function(){
			if(confirm('Conferma rimozione')){
				$(this).parents('tr').fadeOut(function(){$(this).remove()});
			}
		});
		
		$('input[name="main[meeting_category_id]"]').on('input',function(){
			var id=$(this).val();
			if(id!=''){
				$.ajax({
					type: "POST",
					url: "/archy/app.php?do_action=get_report_model",
					data: 'id='+id,
					cache: false,
					success: function(html){
						tinymce.get('archy_meetings_book_unsigned_report').setContent(html);
						tinymce.get('archy_meetings_book_unsigned_report').save();
					}
				});
			}
		});
		
		$('.bulk_action').change(function(){
			if($(this).val()!=''){
                            var attr = $(this).attr('name');
				var scope=$('table#'+attr);
				var option=$(this).val();
				$('input[type="radio"][value="'+option+'"]',scope).prop('checked',true);
			}
		});
		
	}
</script>
<script type="text/html" id="member_status">
	<tr>
		<td class="form-group">
			<input class="select_members form-control required" name="member[name][]" placeholder="Cerca nominativo...">
			<input type="hidden" name="member[id][]">
		</td>
		<td class="form-group"><textarea rows="1" cols="40" name="member[reason][]" class="form-control required"></textarea></td>
		<td align="center" style="vertical-align:middle"><span class="far fa-trash-alt remove_row"></span></td>
	</tr>
</script>
<style type="text/css">
   .remove_row{
	cursor:pointer;
    } 
</style>
<!--
<style type="text/css">
.inline_labels label{
	margin-bottom:0;
	font-weight:normal;
	padding-right:5px;
}
.bulk_action{
	float:right;
}
.remove_row{
	cursor:pointer;
}
textarea[name="member[reason][]"]{
	width:100% !important;
	margin:0 !important;
}
</style>-->