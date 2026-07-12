<?php
$step1=['name','surname','address','city','zip','phone','fax','email','certificate_name','certificate_custom_name','catalog_name','catalog_custom_name'];
$step2=['title','technique','support','no_frame_height','no_frame_width','no_frame_depth','no_frame_diameter','frame_height','frame_width','frame_depth','frame_diameter','title_position','title_description','title_file','date_position','date_description','date','date_file','sign_position','sign_description','sign_file','text_position','text_description','text_file','label_position','label_description','label_file','stamp_position','stamp_description','stamp_file'];
$step3=['inventory_number','purchase_date','provenance','era_value','autenticity_certificates','auth_files','state_of_preservation','restorations','expositions','bibliography'];
$step4=['status','attachments'];
?>
<div class="container">

   <ul class="nav nav-tabs">
         <li class="active"><a href="#owner" data-toggle="tab" id="first">Dati possessore</a></li>
         <li><a href="#artwork1" data-toggle="tab" id="second">Dati opera</a></li>
         <li><a href="#artwork2" data-toggle="tab" id="third">Dati opera</a></li>
         <li><a href="#attachments" data-toggle="tab" id="fourth">Documentazione</a></li>
   </ul>
   
   <div class="settings">
	
      <div class="tab-content">

         
         <div class="tab-pane active" id="owner">
            <fieldset>
            	<legend>Dati possessore</legend>
               <div class="row">
               	<?php 
						foreach($step1 as $column):
							echo $app::form_element($data['hidden_columns']['authenticity_certificate_requests.'.$column],"col-md-3");
						endforeach;
						?>
               </div>
         	</fieldset>
         </div>
         
         <div class="tab-pane" id="artwork1">
            <fieldset>
            	<legend>Dati opera</legend>
               <div class="row">
               	<?php 
						foreach($step2 as $column):
							echo $app::form_element($data['hidden_columns']['authenticity_certificate_requests.'.$column],"col-md-4");
						endforeach;
						?>
               </div>
         	</fieldset>
         </div>
         
         <div class="tab-pane" id="artwork2">
            <fieldset>
            	<legend>Dati opera</legend>
               <div class="row">
               	<?php 
						foreach($step3 as $column):
							echo $app::form_element($data['hidden_columns']['authenticity_certificate_requests.'.$column],"col-md-3");
						endforeach;
						?>
               </div>
         	</fieldset>
         </div>
         
         <div class="tab-pane" id="attachments">
            <fieldset>
            	<legend>Documentazione</legend>
               <div class="row">
               	<?php 
						foreach($step4 as $column):
							echo $app::form_element($data['hidden_columns']['authenticity_certificate_requests.'.$column],"col-md-4");
						endforeach;
						?>
               </div>
         	</fieldset>
         </div>

         
      </div>
	
   </div>
   
</div>
