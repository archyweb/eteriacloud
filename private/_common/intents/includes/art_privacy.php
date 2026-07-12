<ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item"><a class="nav-link active" id="meeting-tab" data-bs-toggle="tab" href="#tab-meeting" role="tab" aria-controls="tab-meeting" aria-selected="true">Dati Certificato</a></li>
    <li class="nav-item"><a class="nav-link" id="final-report-tab" data-bs-toggle="tab" href="#tab-final-report" role="tab" aria-controls="tab-final-report" aria-selected="false">Modello certificato</a></li>
</ul>
<div class="tab-content border border-top-0 p-3" id="myTabContent">
    <div class="tab-pane fade show active" id="tab-meeting" role="tabpanel" aria-labelledby="meeting-tab">
        <div class="row g-3">
             <?php
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_title'],"col-md-6");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.model_id'],"col-md-6");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_date'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_entity_name'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_entity_address'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_entity_zip'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_entity_city'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_entity_province'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_entity_cf'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_entity_vat'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_entity_email'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_place'],"col-md-4");
                    echo $app::form_element($data['hidden_columns']['archy_art_privacy.privacy_data_manager'],"col-md-4");
               ?>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-final-report" role="tabpanel" aria-labelledby="final-report-tab">
        <div class="row g-3">
             <?php echo $app::form_element($data['hidden_columns']['archy_art_privacy.model_template'],"col-md-12"); ?>
        </div>
    </div>
</div>