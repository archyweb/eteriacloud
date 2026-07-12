<?php
$options = [];

if (
    isset($data['hidden_columns']['additional_fields.options']['value']) &&
    $data['hidden_columns']['additional_fields.options']['value'] != ''
) {
    $options = @unserialize($data['hidden_columns']['additional_fields.options']['value']);
}

if (!is_array($options)) {
    $options = [];
}

if (empty($options)) {
    $options = ['', '', ''];
}
?>

<div id="additional_field_options_box" style="display:none">
   <hr>
   <h3 style="text-align:center">Definisci opzioni</h3>

   <div class="row">
      <div class="col-md-4 col-xs-12 col-md-offset-4">
         <ol id="additional_options_list">
            <?php foreach ($options as $option): ?>
               <li class="form-group additional-option-row" style="margin-bottom:10px;">
                  <div style="display:flex; gap:8px; align-items:center;">
                     <input
                        name="main[options][]"
                        type="text"
                        class="form-control input-sm"
                        placeholder="Inserisci opzione..."
                        value="<?php echo htmlspecialchars($option, ENT_QUOTES); ?>"
                     >

                     <button
                        type="button"
                        class="btn btn-danger btn-sm"
                        onclick="return removeAdditionalOption(this);">
                        Elimina
                     </button>
                  </div>
               </li>
            <?php endforeach; ?>
         </ol>

         <div style="text-align:center; margin-top:15px;">
            <button
               type="button"
               class="btn btn-primary btn-sm"
               onclick="return addAdditionalOption();">
               + Aggiungi opzione
            </button>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">
function toggleAdditionalFieldOptions() {
    var typeSelect = document.getElementById('additional_fields_type');
    var box = document.getElementById('additional_field_options_box');

    if (!typeSelect || !box) {
        return;
    }

    if (typeSelect.value === 'select' || typeSelect.value === 'multiple') {
        box.style.display = 'block';
    } else {
        box.style.display = 'none';
    }
}

function addAdditionalOption() {
    var container = document.getElementById('additional_options_list');

    if (!container) {
        return false;
    }

    var li = document.createElement('li');
    li.className = 'form-group additional-option-row';
    li.style.marginBottom = '10px';

    li.innerHTML =
        '<div style="display:flex; gap:8px; align-items:center;">' +
            '<input name="main[options][]" type="text" class="form-control input-sm required" placeholder="Inserisci opzione...">' +
            '<button type="button" class="btn btn-danger btn-sm" onclick="return removeAdditionalOption(this);">Elimina</button>' +
        '</div>';

    container.appendChild(li);

    return false;
}

function removeAdditionalOption(button) {
    var li = button;

    while (li && li.tagName && li.tagName.toLowerCase() !== 'li') {
        li = li.parentNode;
    }

    if (li && li.parentNode) {
        li.parentNode.removeChild(li);
    }

    return false;
}

window.addEventListener('load', function() {
    toggleAdditionalFieldOptions();

    var typeSelect = document.getElementById('additional_fields_type');

    if (typeSelect) {
        typeSelect.addEventListener('change', toggleAdditionalFieldOptions);
    }
});
</script>