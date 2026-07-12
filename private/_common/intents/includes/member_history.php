<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="member-tab" data-bs-toggle="tab" href="#tab-member" role="tab" aria-controls="tab-member" aria-selected="true">Associato</a></li>
    <?php if(isset($_GET['id'])):?>
    <li class="nav-item"><a class="nav-link" id="members-book-tab" data-bs-toggle="tab" href="#tab-members-book" role="tab" aria-controls="tab-members-book" aria-selected="false">Libro Soci</a></li>
    <li class="nav-item"><a class="nav-link" id="activities-tab" data-bs-toggle="tab" href="#tab-activities" role="tab" aria-controls="tab-activities" aria-selected="false">Attività</a></li>
    <li class="nav-item"><a class="nav-link" id="additional-fields-tab" data-bs-toggle="tab" href="#tab-additional-fields" role="tab" aria-controls="tab-additional-fields" aria-selected="false">Campi aggiuntivi</a></li>
    <?php endif; ?>
</ul>

<div class="tab-content border border-top-0 p-3" id="myTabContent">
    <div class="tab-pane fade show active" id="tab-member" role="tabpanel" aria-labelledby="member-tab">
        <div class="row g-3">
            <?php
                echo $app::form_element($data['hidden_columns']['archy_memberships.registry_id'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.signatory'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.category_id'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.card'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.membership_begin'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.membership_end'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                if(isset($_GET['id'])):
                        echo $app::form_element($data['hidden_columns']['archy_memberships.status'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                endif;
                echo $app::form_element($data['hidden_columns']['archy_memberships.request_date'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.acceptance_date'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                //echo $app::form_element($data['hidden_columns']['archy_memberships.acceptance_document'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.picture'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.documents'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.resignation_date'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.resignation_reason'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.notes'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.medical_certificate'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.medical_certificate_begin'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
                echo $app::form_element($data['hidden_columns']['archy_memberships.medical_certificate_end'],'col-xs-12 col-sm-6 col-md-3 col-lg-3');
            ?>
        </div>
        <?php if(!isset($_GET['id'])):?>
        <hr>
        <div class="row g-3">
         	<?php echo $app::form_element($data['hidden_columns']['archy_memberships.status'],'col-xs-12 col-sm-6 col-md-3')?>
            <div class="col-xs-12 col-sm-6 col-md-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="subscription_paid" name="subscription_paid" value="1" />
                    <label class="form-check-label" for="subscription_paid">La quota associativa è stata versata</label>
                </div>
            </div>
        </div>
        <div class="row g-3" id="subscription_wrapper">
            <div class="col-md-12">
               <h3>Quota Associativa</h3>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3">
               <div class="form-group">
                  <label class="form-label" for="subscription_date">Data versamento <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></label>
                  <div class="input-group has-validation">
                        <div class="input-group-text"><span class="fas fa-calendar-alt"></span></div>
                        <input type="text" class="form-control form-date-control datetimepicker" required="required" id="subscription_date" name="subscription_date" value="<?php echo date('d/m/Y')?>" placeholder="dd/mm/yy" data-options=\'{"dateFormat":"d/m/Y","disableMobile":true,"allowInput":true}\'>
                    </div>
                  <div class="invalid-feedback">Campo obbligatorio</div>
               </div>
            </div>
            <div class="col-xs-12 col-sm-6 col-md-3">
               <div class="form-group">
                  <label class="form-label" for="subscription_amount">Importo <span class="fas fa-asterisk required-label" title="Campo richiesto"></span></label>
                  <div class="input-group has-validation">
                      <div class="input-group-text"><span class="fas fa-euro-sign"></span></div>
                     <input type="text" class="form-control numbers_only" required="required" id="subscription_amount" name="subscription_amount" placeholder="Importo">
                  </div>
                  <div class="invalid-feedback">Campo obbligatorio</div>
               </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <?php if(isset($_GET['id'])):?>
    <div class="tab-pane fade" id="tab-members-book" role="tabpanel" aria-labelledby="members-book-tab">
        <div class="row g-3">
            <?php
            $header=['Nominativo','Codice Fiscale','N° Tessera','Data ammissione','Data cessazione'];
            $membership=new Membership($_GET['id']);
            $history=$membership->get_history($member_id=$_GET['id']);
            ?>
            <div class="table-responsive">
               <table class="table table-striped table-condensed" id="history_table">
                  <thead><th><?php echo join('</th><th>',$header)?></th></thead>
                  <tbody>
                  <?php foreach($history as $item):?>
                     <tr>
                        <td><?php echo $item->full_name?></td>
                        <td><?php echo $item->tax_code?></td>
                        <td><?php echo $item->card?></td>
                        <td data-order="<?php echo $item->acceptance_date?>"><?php echo App::latin_date($item->acceptance_date)?></td>
                        <td data-order="<?php echo $item->resignation_date?>"><?php echo App::latin_date($item->resignation_date)?></td></tr>
                  <?php endforeach?>
                  </tbody>
                  <tfoot><th><?php echo join('</th><th>',$header)?></th></tfoot>
               </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-activities" role="tabpanel" aria-labelledby="activities-tab">
        <div class="row g-3">
            <?php 
            $activities=Membership::activities($membership->member->registry_id);
            $header=['Data','Evento','Sessione','Crediti','Ore formative'];
            ?>
            <div class="table-responsive">
               <table class="table table-striped table-condensed" id="activities_table">
                  <thead><th><?php echo join('</th><th>',$header)?></th></thead>
                  <tbody>
                  <?php foreach($activities as $event):?>
                     <tr>
                         <td><?php echo App::latin_date($event->start_date); ?></td>
                         <td><?php echo $event->title?></td>
                        <td><?php echo $event->session_title?></td>
                        <td><?php echo $event->credits?></td>
                        <td><?php echo $event->training_hours; ?></td>
                     </tr>
                  <?php endforeach?>
                  </tbody>
                  <tfoot><th><?php echo join('</th><th>',$header)?></th></tfoot>
               </table>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-additional-fields" role="tabpanel" aria-labelledby="additional-fields-tab">
        <div class="row g-3">
            <?php $app->additional_fields('form'); ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if(isset($_GET['id'])):?>
  
   <script type="text/javascript">
	window.onload=function(){
		$('#history_table, #activities_table').DataTable({
			pageLength:10,
			order: [[0,'desc']],
			language: data_table_it_IT
		});
	}
    </script>
<?php else:?>
   <style type="text/css">
	#subscription_wrapper{display:none}
	</style>
   <script type="text/javascript">
	window.onload=function(){
		
		$('#subscription_paid').click(function(){
			if($(this).is(':checked')){
				$('#subscription_wrapper').slideDown();
				$('#archy_memberships_status').val('1').trigger('change');
				$('#subscription_date, #subscription_amount').prop('required',true);
			}else{
				$('#subscription_wrapper').slideUp();
				$('#archy_memberships_status').val('5').trigger('change');
				$('#subscription_date, #subscription_amount').prop('required',false);
			}
		});
		
		$('input[name="main[category_id]"]').on('input',function(){
			
			$.ajax({
				type: "POST",
				url: "/archy/app.php?do_action=get_subscription_fee",
				data: 'category_id='+$(this).val(),
				cache: false,
				success: function(fee){
					$('input[name="subscription_amount"]').val(fee);
				}
			});
			
		});
		
		$('input[name="main[registry_id]"]').on('input',function(){
			var registry_id=$(this).val();
			
			$.ajax({
				type: "POST",
				url: "app.php?do_action=member_duplicated_entry&registry_id="+registry_id,
				data: '',
				dataType: 'json',
				cache: false,
				success: function(data){
					if(data.is_duplicated){
						$('#anchor').attr('data-url',root+'archy/app.php?intent=archy_memberships&view=edit&id='+data.member_id);
						$('#duplicated_entry .duplicated_entry_desc').html("L'anagrafica <strong>"+data.member_name+"</strong> è già registrata come Associato");
						$('#duplicated_entry').modal('show');
						$('input[name="main[registry_id]"], #archy_memberships_registry_id').val('');
					}
				}
			});
			
		});
		
	}
	</script>
<?php endif; ?>

<div class="modal fade" id="duplicated_entry" tabindex="-1" role="dialog" aria-labelledby="duplicated_entryLabel" data-backdrop="static">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content position-relative">
        <div class="position-absolute top-0 end-0 mt-2 me-2 z-1">
        <button class="btn-close btn btn-sm btn-circle d-flex flex-center transition-base" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
        <div class="modal-body p-0">
            <div class="rounded-top-3 py-3 ps-4 pe-6 bg-body-tertiary">
            <h4 class="mb-1" id="modalExampleDemoLabel">Duplicazione associato</h4>
          </div>
            <div class="p-4 duplicated_entry_desc"></div>
        </div>
      <div class="modal-footer">
        <!--button type="button" class="btn btn-default" data-dismiss="modal">Close</button-->
        <button type="button" class="btn btn-primary" id="anchor" data-url="" onClick="location.href=this.dataset.url">Portami alla scheda associato</button>
      </div>
    </div>
  </div>
</div>