<?php

class AdditionalFields
{
    protected $app;
    protected $db;

    public function __construct($app)
    {
        $this->app = $app;
        $this->db  = $app->get_db();
    }

    public function handle($where)
    {
        global $app;

        $tableName = isset($this->app->intent->table) ? $this->app->intent->table : '';

        if ($tableName === '') {
            return $where === 'get' ? [] : null;
        }

        $fields = $this->getFieldsForTable($tableName);

        if (!$fields) {
            return $where === 'get' ? [] : null;
        }

        $recordId = $this->getCurrentRecordId();
        $additionalFields = [];

        $app->rendering_additional_fields = true;

        foreach ($fields as $field) {
            $column = $this->buildColumn($field, $recordId, $where);

            if (!$column) {
                continue;
            }

            switch ($where) {
                case 'form':
                    if (isset($column['type']) && $column['type'] === 'html') {
                        echo $column['html'];
                    } else {
                        echo $app::form_element($column);
                    }
                    break;

                case 'list':
                    echo $app::view_element($column, 'list');
                    break;

                case 'grid':
                    echo $app::view_element($column, 'grid');
                    break;

                case 'get':
                    $additionalFields[] = $column;
                    break;
            }
        }
		
		if ($where === 'form') {
    echo $this->renderExtraFieldUploadModal();
}

        $app->rendering_additional_fields = false;

        return $where === 'get' ? $additionalFields : null;
    }

    public function getFieldsForTable($tableName)
    {
        $tableName = $this->db->real_escape_string($tableName);

        $sql = "
            SELECT
                afd.id AS domain_id,
                afd.field_id AS domain_field_id,
                afd.table_name,
                af.id AS field_id,
                af.name,
                af.type,
                af.options,
                af.required
            FROM additional_fields_domain afd
            INNER JOIN additional_fields af ON af.id = afd.field_id
            WHERE afd.table_name = '{$tableName}'
            ORDER BY afd.id ASC
        ";

        $result = $this->db->query($sql);

        if (!$result) {
            return [];
        }

        $fields = [];

        while ($row = mysqli_fetch_object($result)) {
            $fields[] = $row;
        }

        return $fields;
    }

    protected function buildColumn($field, $recordId = null, $where = 'form')
    {
        if (!$field || empty($field->domain_id)) {
            return null;
        }

        $column = [];
        $column['alias'] = 'additional_' . $field->domain_id;
        $column['label'] = $field->name;
        $column['type']  = $field->type;

        if ($field->type === 'file') {
            return $this->buildFileUploadColumn($field, $recordId);
        }

        if ($field->type === 'bulk_upload') {
            return $this->buildBulkUploadColumn($field, $recordId);
        }

        if ($field->type === 'select' || $field->type === 'multiple') {
            $column['options_type'] = 'array';
            $column['options'] = $this->safeUnserialize($field->options, []);
        }

        if (!empty($field->required)) {
            $column['required'] = true;
        }

        $value = $recordId ? $this->getValue($field->domain_id, $recordId) : $this->getEmptyValueForField($field);

        $column['value'] = $this->normalizeValueForRender($field, $value, $where);

        if ($where !== 'form') {
            $column['view_value'] = $this->formatViewValue($column);
        }

        return $column;
    }

protected function buildFileUploadColumn($field, $recordId)
{
    $currentValue = $recordId ? $this->getValue($field->domain_id, $recordId) : '';

    $html = '
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="form-group">
                <label class="control-label">'.$field->name.'</label>
                <div class="extra-upload-preview" id="extra_upload_preview_'.$field->domain_id.'">
    ';

    if ($currentValue != '') {
        $filename = explode('/', $currentValue);

        $html .= '
            <div class="small mb-2">
                <a href="'.$currentValue.'" target="_blank">'.end($filename).'</a>
                <button 
                    type="button" 
                    class="btn btn-sm btn-outline-danger ms-2 extra-delete-file"
                    data-record-id="'.(int)$recordId.'"
                    data-domain-id="'.(int)$field->domain_id.'"
                    data-mode="file"
                    data-file="'.htmlspecialchars($currentValue, ENT_QUOTES).'">
                    Elimina
                </button>
            </div>
        ';
    }

    $html .= '
                </div>

                <button
                    type="button"
                    class="btn btn-sm btn-primary extra-field-upload-btn"
                    data-record-id="'.(int)$recordId.'"
                    data-domain-id="'.(int)$field->domain_id.'"
                    data-mode="file"
                    data-label="'.htmlspecialchars($field->name, ENT_QUOTES).'">
                    Carica file
                </button>
            </div>
        </div>
    ';

    return [
        'alias' => 'additional_'.$field->domain_id,
        'label' => $field->name,
        'type' => 'html',
        'html' => $html,
        'value' => $html,
        'view_value' => $html
    ];
}

	protected function buildBulkUploadColumn($field, $recordId)
{
    $currentValue = $recordId ? $this->getValue($field->domain_id, $recordId) : '';
    $files = [];

    if ($this->isSerialized($currentValue)) {
        $files = @unserialize($currentValue);
    }

    if (!is_array($files)) {
        $files = [];
    }

    $html = '
        <div class="col-xs-12 col-sm-6 col-md-4">
            <div class="form-group">
                <label class="control-label">'.$field->name.'</label>
                <div class="extra-upload-preview" id="extra_upload_preview_'.$field->domain_id.'">
    ';

    if (!empty($files)) {
        foreach ($files as $path) {
            $filename = explode('/', $path);

            $html .= '
                <div class="small mb-2">
                    <a href="'.$path.'" target="_blank">'.end($filename).'</a>
                    <button 
                        type="button" 
                        class="btn btn-sm btn-outline-danger ms-2 extra-delete-file"
                        data-record-id="'.(int)$recordId.'"
                        data-domain-id="'.(int)$field->domain_id.'"
                        data-mode="bulk"
                        data-file="'.htmlspecialchars($path, ENT_QUOTES).'">
                        Elimina
                    </button>
                </div>
            ';
        }
    }

    $html .= '
                </div>

                <button
                    type="button"
                    class="btn btn-sm btn-primary extra-field-upload-btn"
                    data-record-id="'.(int)$recordId.'"
                    data-domain-id="'.(int)$field->domain_id.'"
                    data-mode="bulk"
                    data-label="'.htmlspecialchars($field->name, ENT_QUOTES).'">
                    Carica file
                </button>
            </div>
        </div>
    ';

    return [
        'alias' => 'additional_'.$field->domain_id,
        'label' => $field->name,
        'type' => 'html',
        'html' => $html,
        'value' => $html,
        'view_value' => $html
    ];
}

    protected function getEmptyValueForField($field)
    {
        if (!$field) {
            return '';
        }

        if (in_array($field->type, ['multiple', 'bulk_upload'])) {
            return serialize([]);
        }

        if ($field->type === 'checkbox') {
            return 0;
        }

        return '';
    }

    public function getValue($domainId, $recordId)
    {
        $domainId = (int)$domainId;
        $recordId = (int)$recordId;

        $result = $this->db->query("
            SELECT value
            FROM additional_fields_values
            WHERE domain_id = '{$domainId}'
            AND record_id = '{$recordId}'
            LIMIT 1
        ");

        if (!$result || $result->num_rows === 0) {
            return '';
        }

        $row = mysqli_fetch_object($result);

        return isset($row->value) && $row->value !== null ? $row->value : '';
    }

    protected function normalizeValueForRender($field, $value, $where)
    {
        if ($value === null) {
            $value = '';
        }

        if ($field->type === 'multiple') {
            if ($value === '') {
                return serialize([]);
            }

            if (is_array($value)) {
                return serialize($value);
            }

            if (!$this->isSerialized($value)) {
                return serialize([]);
            }

            return $value;
        }

        if ($field->type === 'date' || $field->type === 'data') {
            if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $value, $m)) {
                return $m[3].'-'.$m[2].'-'.$m[1];
            }
        }

        return $value;
    }

    public function saveFromPost($recordId, $postAdditional = null)
    {
        $recordId = (int)$recordId;

        if (!$recordId) {
            return false;
        }

        if ($postAdditional === null) {
            $postAdditional = isset($_POST['additional']) ? $_POST['additional'] : [];
        }

        if (!is_array($postAdditional)) {
            $postAdditional = [];
        }

        foreach ($postAdditional as $domainId => $value) {
            $domainId = str_replace('additional_', '', $domainId);
            $domainId = (int)$domainId;

            if ($domainId <= 0) {
                continue;
            }

            $field = $this->getFieldByDomainId($domainId);

            if (!$field) {
                continue;
            }

            if ($field->type === 'file' || $field->type === 'bulk_upload') {
                continue;
            }

            $value = $this->normalizeValueForStorage($field, $value);
            $value = $this->db->real_escape_string((string)$value);

            $this->db->query("
                DELETE FROM additional_fields_values
                WHERE domain_id = '{$domainId}'
                AND record_id = '{$recordId}'
            ");

            $this->db->query("
                INSERT INTO additional_fields_values
                SET
                    domain_id = '{$domainId}',
                    record_id = '{$recordId}',
                    value = '{$value}'
            ");
        }

        return true;
    }

    protected function normalizeValueForStorage($field, $value)
    {
        if ($value === null) {
            $value = '';
        }

        if ($field->type === 'checkbox') {
            if (is_array($value)) {
                return !empty($value) ? 1 : 0;
            }

            return $value ? 1 : 0;
        }

        if (is_array($value)) {
            $value = array_filter($value, function ($v) {
                return $v !== '' && $v !== null;
            });

            return serialize($value);
        }

        if ($field->type === 'multiple' && $value === '') {
            return serialize([]);
        }

        if (
            ($field->type === 'date' || $field->type === 'data' || $field->type === 'datetime')
            && !empty($value)
        ) {
            if (preg_match('/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/', $value, $m)) {
                $value = $m[3].'-'.$m[2].'-'.$m[1];
            }
        }

        return $value;
    }

    public function formatViewValue($column)
    {
        $value = isset($column['value']) ? $column['value'] : '';
        $type  = isset($column['type']) ? $column['type'] : 'text';

        switch ($type) {
            case 'date':
            case 'data':
                if (!$value) {
                    return '';
                }

                if (preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/', $value, $m)) {
                    return $m[3].'/'.$m[2].'/'.$m[1];
                }

                return $value;

            case 'checkbox':
                return $value ? 'Sì' : 'No';

            case 'select':
                return isset($column['options'][$value]) ? $column['options'][$value] : '';

            case 'multiple':
                $values = $this->safeUnserialize($value, []);
                $labels = [];

                foreach ($values as $key) {
                    if (isset($column['options'][$key])) {
                        $labels[] = $column['options'][$key];
                    }
                }

                return implode(', ', $labels);

            default:
                return $value;
        }
    }

    public function renderOptionsBuilder($field = null)
    {
        $options = [];

        if ($field && isset($field->options) && $field->options != '') {
            $options = $this->safeUnserialize($field->options, []);
        }

        if (!is_array($options)) {
            $options = [];
        }

        if (empty($options)) {
            $options = ['', '', ''];
        }

        $html = '
            <div class="mb-3" id="additional-options-builder">
                <label class="form-label">Opzioni</label>
                <div id="additional-options-list">
        ';

        foreach ($options as $option) {
            $html .= '
                <div class="input-group mb-2 additional-option-row">
                    <input 
                        type="text" 
                        name="options[]" 
                        class="form-control" 
                        value="'.htmlspecialchars($option, ENT_QUOTES).'">
                    <button 
                        type="button" 
                        class="btn btn-outline-danger remove-additional-option">
                        -
                    </button>
                </div>
            ';
        }

        $html .= '
                </div>

                <button 
                    type="button" 
                    class="btn btn-sm btn-outline-primary" 
                    id="add-additional-option">
                    + Aggiungi opzione
                </button>
            </div>

            <script>
            document.addEventListener("click", function(e) {
                if (e.target && e.target.id === "add-additional-option") {
                    const list = document.getElementById("additional-options-list");

                    const row = document.createElement("div");
                    row.className = "input-group mb-2 additional-option-row";
                    row.innerHTML = `
                        <input 
                            type="text" 
                            name="options[]" 
                            class="form-control" 
                            value="">
                        <button 
                            type="button" 
                            class="btn btn-outline-danger remove-additional-option">
                            -
                        </button>
                    `;

                    list.appendChild(row);
                }

                if (e.target && e.target.classList.contains("remove-additional-option")) {
                    const row = e.target.closest(".additional-option-row");

                    if (row) {
                        row.remove();
                    }
                }
            });
            </script>
        ';

        return $html;
    }

    public function normalizeOptionsForStorage($options)
    {
        if (!is_array($options)) {
            return serialize([]);
        }

        $clean = [];

        foreach ($options as $option) {
            $option = trim($option);

            if ($option !== '') {
                $clean[] = $option;
            }
        }

        return serialize($clean);
    }

    protected function getFieldByDomainId($domainId)
    {
        $domainId = (int)$domainId;

        $result = $this->db->query("
            SELECT af.*
            FROM additional_fields_domain afd
            INNER JOIN additional_fields af ON af.id = afd.field_id
            WHERE afd.id = '{$domainId}'
            LIMIT 1
        ");

        if (!$result || $result->num_rows === 0) {
            return false;
        }

        return mysqli_fetch_object($result);
    }

    protected function safeUnserialize($value, $fallback = [])
    {
        if ($value === null || $value === '') {
            return $fallback;
        }

        if (is_array($value)) {
            return $value;
        }

        $data = @unserialize($value);

        return is_array($data) ? $data : $fallback;
    }

    protected function isSerialized($value)
    {
        if (!is_string($value)) {
            return false;
        }

        return @unserialize($value) !== false || $value === 'b:0;';
    }

    protected function getCurrentRecordId()
    {
        if (isset($_GET['id']) && $_GET['id'] !== '') {
            return (int)$_GET['id'];
        }

        if (isset($_POST['intent_id']) && $_POST['intent_id'] !== '') {
            return (int)$_POST['intent_id'];
        }

        return null;
    }
	
	protected function renderExtraFieldUploadModal()
{
    return '
<div class="modal fade" id="extraFieldUploadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Carica allegato</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Chiudi"></button>
            </div>

            <div class="modal-body">
                <div id="extra_upload_message" class="mb-2"></div>

                <input type="file" id="extra_upload_file" class="form-control">

                <div id="extra_selected_files" class="small text-muted mt-2"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" id="extra_upload_confirm">Carica</button>
            </div>
        </div>
    </div>
</div>

<script>
var extraUploadData = {
    recordId: 0,
    domainId: 0,
    mode: "file",
	files: []
};

document.addEventListener("click", function(e) {
    var btn = e.target.closest(".extra-field-upload-btn");

    if (!btn) {
        return;
    }

    e.preventDefault();

    var recordId = parseInt(btn.getAttribute("data-record-id"), 10);

    if (!recordId) {
        var form = btn.closest("form") || document.querySelector("form");

        if (!form) {
            alert("Form non trovato.");
            return;
        }

        var input = document.createElement("input");
        input.type = "hidden";
        input.name = "save_and_stay";
        input.value = "1";
        form.appendChild(input);

        alert("La scheda deve essere salvata prima del caricamento. Procedo con Salva e rimani.");
        form.submit();
        return;
    }

    extraUploadData.recordId = recordId;
    extraUploadData.domainId = btn.getAttribute("data-domain-id");
    extraUploadData.mode = btn.getAttribute("data-mode");

    var fileInput = document.getElementById("extra_upload_file");
    fileInput.value = "";

    if (extraUploadData.mode === "bulk") {
        fileInput.setAttribute("multiple", "multiple");
    } else {
        fileInput.removeAttribute("multiple");
    }

    document.getElementById("extra_selected_files").innerHTML = "";
    document.getElementById("extra_upload_message").innerHTML = "";

    var modal = new bootstrap.Modal(document.getElementById("extraFieldUploadModal"));
    modal.show();
});

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - FILE INPUT CHANGE (DELEGATED)
|--------------------------------------------------------------------------
|
| Bootstrap modal + file input possono perdere il listener originale.
| Usiamo delegation sul document.
|
*/

document.addEventListener("change", function(e) {

    if (!e.target || e.target.id !== "extra_upload_file") {
        return;
    }

    extraUploadData.files = Array.from(e.target.files);

    var names = [];

    for (var i = 0; i < extraUploadData.files.length; i++) {
        names.push(extraUploadData.files[i].name);
    }

    document.getElementById("extra_selected_files").innerHTML = names.join("<br>");
});


document.getElementById("extra_upload_confirm").addEventListener("click", function() {

    console.log("CLICK CARICA EXTRA FIELD");

   if (!extraUploadData.files || extraUploadData.files.length === 0) {
    alert("Seleziona un file.");
    return;
}

    var formData = new FormData();
    formData.append("action", "upload");
    formData.append("record_id", extraUploadData.recordId);
    formData.append("domain_id", extraUploadData.domainId);
    formData.append("mode", extraUploadData.mode);

    for (var i = 0; i < extraUploadData.files.length; i++) {
    formData.append("extra_file[]", extraUploadData.files[i]);
}

    fetch("/srv/extra-fields-upload/upload_extrafield.php", {
        method: "POST",
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.status !== "success") {
            document.getElementById("extra_upload_message").innerHTML =
                "<div class=\"alert alert-danger\">" + data.message + "</div>";
            return;
        }

        document.getElementById("extra_upload_message").innerHTML =
            "<div class=\"alert alert-success\">Upload completato.</div>";

extraUploadData.files = [];
document.getElementById("extra_upload_file").value = "";
document.getElementById("extra_selected_files").innerHTML = "";
        var preview = document.getElementById("extra_upload_preview_" + extraUploadData.domainId);

        if (preview) {
            preview.innerHTML = data.html;
        }
    });
});

document.addEventListener("click", function(e) {
    var btn = e.target.closest(".extra-delete-file");

    if (!btn) {
        return;
    }

    e.preventDefault();

    if (!confirm("Eliminare questo file?")) {
        return;
    }

    var formData = new FormData();
    formData.append("action", "delete");
    formData.append("record_id", btn.getAttribute("data-record-id"));
    formData.append("domain_id", btn.getAttribute("data-domain-id"));
    formData.append("mode", btn.getAttribute("data-mode"));
    formData.append("file", btn.getAttribute("data-file"));

    fetch("/srv/extra-fields-upload/upload_extrafield.php", {
        method: "POST",
        body: formData
    })
    .then(function(res) { return res.json(); })
    .then(function(data) {
        if (data.status === "success") {
            var preview = document.getElementById("extra_upload_preview_" + btn.getAttribute("data-domain-id"));

            if (preview) {
                preview.innerHTML = data.html;
            }
        } else {
            alert(data.message);
        }
    });
});
</script>
';
}

/*
|--------------------------------------------------------------------------
| V1.3 - Colonne DataTable per campi aggiuntivi
|--------------------------------------------------------------------------
| Genera le colonne virtuali da mostrare nella modale "Seleziona colonne".
| Non modifica query, upload o salvataggi.
|--------------------------------------------------------------------------
*/
public function getGridColumnsForTable($tableName)
{
    $fields = $this->getFieldsForTable($tableName);

    if (!$fields) {
        return [];
    }

    $columns = [];

    foreach ($fields as $field) {

        $alias = 'additional_'.$field->domain_id;

        $column = [
            'alias' => $alias,
            'table' => 'additional_fields',
            'label' => $field->name,
            'type'  => $field->type,
            'additional_field' => true,
            'domain_id' => (int)$field->domain_id
        ];

        if ($field->type === 'select' || $field->type === 'multiple') {
            $column['options_type'] = 'array';
            $column['options'] = $this->safeUnserialize($field->options, []);
        }

        $columns[$alias] = $column;
    }

    return $columns;
}
}