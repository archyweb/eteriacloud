<?php if(!isset($_GET['id'])):?>
   <hr>
   
      <div id="new_dates" style="display:none;">
            <div class="col-sm-8">
                <div class="card mb-3">
                  <div class="card-body" id="member_status">
                    
                  </div>
                </div>
            </div>
            <div class="col-sm-4">                  
                <div class="form-group">
                   <label class="form-label" for="new_begin">Nuovo inizio validità</label>
                   <div class="input-group has-validation">
                       <div class="input-group-text"><span class="fas fa-calendar-alt"></span></div>
                        <input type="text" class="form-control form-date-control datetimepicker" required="required"  placeholder="dd/mm/yy" data-options='{"dateFormat":"d/m/Y","disableMobile":true,"allowInput":true}' name="begin" id="new_begin">
                   </div>
                </div>

                <div class="form-group">
                   <label class="form-label" for="new_end">Nuova fine validità</label>
                   <div class="input-group has-validation">
                       <div class="input-group-text"><span class="fas fa-calendar-alt"></span></div>
                        <input type="text" class="form-control form-date-control datetimepicker" required="required" placeholder="dd/mm/yy" data-options='{"dateFormat":"d/m/Y","disableMobile":true,"allowInput":true}' name="end" id="new_end">
                   </div>
                </div>
            </div>
            
      </div>
   
   <script type="text/javascript">
   window.onload=function(){
      $('input[name="main[member_id]"]').on('input',function(){
         $.ajax({
            type: "POST",
            url: "/archy/app.php?do_action=get_subscription_info&id="+$(this).val(),
            dataType: 'json',
            cache: false,
            success: function(row){	
                if(row.status=='4'){
                        $('input[name="main[member_id]"], #archy_subscriptions_member_id').val('');
                        alert('L\'associato \''+row.registry.label+'\' è cessato, la sua quota non può essere rinnovata.');
                }else{
                        var status='Status Socio: '+row.status_label;
                        $('#member_status').html('<div class="card-title"><h3>Socio '+row.registry.label+' - Tessera N° '+row.card+'</h3></div><div class="card-text"><h4>Validità tessera: Dal '+row.membership_begin+' Al '+row.membership_end+'</h4><p class="mt-2">'+status+'</p></div>');
                        $('#new_begin').val(row.new_begin);
                        $('#new_end').val(row.new_end);
                        $('#archy_subscriptions_amount').val(row.fee_amount);
                        $('#new_dates').css('display',row.dates_display);
                        flatpickr(".datetimepicker", {});
                }					
            }
         });         
      });				
   }
   </script>
<?php endif; ?>
