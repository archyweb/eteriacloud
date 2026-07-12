<?php if(isset($_GET['id'])){ ?>
<input type="hidden" name="email" value="<?php echo $data['columns']['archy_users.email']['value']; ?>">
<?php } ?>

<?php
/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - USER ROLES FIELD
|--------------------------------------------------------------------------
|
| Campo ruoli utente gestito tramite tabella ponte archy_user_roles.
| Non viene salvato direttamente nella tabella archy_users.
|
*/

$user_roles = [];

if (isset($_GET['id'])) {

    $user_id = (int) $_GET['id'];

    $roles_query = $app->db->query("
        SELECT role_id
        FROM archy_user_roles
        WHERE user_id = ".$user_id."
    ");

    if ($roles_query) {
        while ($role = mysqli_fetch_assoc($roles_query)) {
            $user_roles[] = (int) $role['role_id'];
        }
    }
}
?>

<div class="col-md-12">
    <div class="form-group">
        <label for="user_roles">Ruoli utente</label>

        <select 
            name="user_roles[]" 
            id="user_roles" 
            class="form-control toSelect2" 
            multiple
            style="width:100%;"
        >
            <?php
            $roles = $app->db->query("
                SELECT id, label
                FROM archy_roles
                ORDER BY label ASC
            ");

            if ($roles) {
                while ($role = mysqli_fetch_assoc($roles)) {
                    $selected = in_array((int) $role['id'], $user_roles, true) ? 'selected' : '';
                    ?>
                    <option value="<?php echo (int) $role['id']; ?>" <?php echo $selected; ?>>
                        <?php echo htmlspecialchars($role['label']); ?>
                    </option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
</div>

<?php
/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - USER MENU PERMISSIONS FIELD
|--------------------------------------------------------------------------
|
| Menù accessibili per utente.
| Tabella utilizzata:
| archy_user_menus
|
*/

$user_menus = [];

if (isset($_GET['id'])) {

    $user_id = (int) $_GET['id'];

    $menus_query = $app->db->query("
        SELECT menu_key
        FROM archy_user_menus
        WHERE user_id = ".$user_id."
    ");

    if ($menus_query) {

        while ($menu = mysqli_fetch_assoc($menus_query)) {

            $user_menus[] = $menu['menu_key'];
        }
    }
}

/*
|--------------------------------------------------------------------------
| MENU DISPONIBILI
|--------------------------------------------------------------------------
|
| Prima versione stabile:
| elenco statico menù + sottomenù.
|
*/

$available_menus = [];

$config_menu = require $app->main_root . '/private/_common/config_menu.php';
$config_local = require $app->subdomain_root . '/config-local.php';

if (isset($config_menu['menu']) && is_array($config_menu['menu'])) {

    foreach ($config_menu['menu'] as $menu_key => $menu) {

        $menu_enabled = $menu['enabled'] ?? true;

        if (!$menu_enabled) {
            continue;
        }

        if (isset($config_local[$menu_key]) && $config_local[$menu_key] === false) {
            continue;
        }

        $menu_label = $menu['text'] ?? $menu_key;

        $available_menus[$menu_key] = strtoupper($menu_label);

        if (isset($menu['items']) && is_array($menu['items'])) {

            foreach ($menu['items'] as $item) {

                if (isset($item['divider']) && $item['divider'] === true) {
                    continue;
                }

                if (($item['enabled'] ?? true) === false) {
                    continue;
                }

                if (!empty($item['id'])) {
                    $available_menus[$item['id']] = $menu_label . ' > ' . strip_tags($item['text'] ?? $item['id']);
                }

                if (isset($item['items']) && is_array($item['items'])) {

                    foreach ($item['items'] as $sub_item) {

                        if (isset($sub_item['divider']) && $sub_item['divider'] === true) {
                            continue;
                        }

                        if (($sub_item['enabled'] ?? true) === false) {
                            continue;
                        }

                        if (!empty($sub_item['id'])) {
                            $available_menus[$sub_item['id']] =
                                $menu_label . ' > ' .
                                strip_tags($item['text'] ?? '') . ' > ' .
                                strip_tags($sub_item['text'] ?? $sub_item['id']);
                        }
                    }
                }
            }
        }
    }
}
?>

<?php
/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - USER MENU RESTRICTIONS - CHECKBOX UI
|--------------------------------------------------------------------------
|
| Selezione dei menu NON accessibili all'utente.
| Gli admin vedono tutto: i limiti menu vengono ignorati da App::load_menu().
|
*/

$user_menus = [];

if (isset($_GET['id'])) {

    $user_id = (int) $_GET['id'];

    $menus_query = $app->db->query("
        SELECT menu_key
        FROM archy_user_menus
        WHERE user_id = ".$user_id."
    ");

    if ($menus_query) {
        while ($menu = mysqli_fetch_assoc($menus_query)) {
            $user_menus[] = $menu['menu_key'];
        }
    }
}

$menu_config = $app->config_menu['menu'];
?>

<div class="col-md-12" id="user-menu-restrictions-wrapper">
    <div class="alert alert-warning">
        <strong>Limita accesso ai menù</strong><br>
        Seleziona le voci che l’utente <strong>NON deve visualizzare</strong>.
        Gli amministratori vedono sempre tutto.
    </div>

    <div class="row">
        <?php foreach ($menu_config as $main_key => $main_menu): ?>

            <?php
            if (isset($main_menu['enabled']) && !$main_menu['enabled']) {
                continue;
            }

            if (isset($app->config[$main_key]) && !$app->config[$main_key]) {
                continue;
            }

            $main_checked = in_array($main_key, $user_menus, true) ? 'checked' : '';
            ?>

            <div class="col-md-6 mb-3">
                <div class="card h-100">
                    <div class="card-header bg-body-tertiary">
                        <label class="form-check mb-0 fw-bold">
                            <input
                                type="checkbox"
                                class="form-check-input menu-parent"
                                name="user_menus[]"
                                value="<?php echo htmlspecialchars($main_key); ?>"
                                data-menu-parent="<?php echo htmlspecialchars($main_key); ?>"
                                <?php echo $main_checked; ?>
                            >
                            <?php echo htmlspecialchars(strip_tags($main_menu['text'] ?? $main_key)); ?>
                        </label>
                    </div>

                    <?php if (!empty($main_menu['items']) && is_array($main_menu['items'])): ?>
                        <div class="card-body">

                            <?php foreach ($main_menu['items'] as $second_level): ?>

                                <?php
                                if (isset($second_level['divider'])) {
                                    continue;
                                }

                                if (isset($second_level['enabled']) && !$second_level['enabled']) {
                                    continue;
                                }

                                if (empty($second_level['id'])) {
                                    continue;
                                }

                                $second_id = $second_level['id'];
                                $second_checked = in_array($second_id, $user_menus, true) ? 'checked' : '';
                                ?>

                                <div class="form-check mb-2 ms-2">
                                    <input
                                        type="checkbox"
                                        class="form-check-input menu-child"
                                        name="user_menus[]"
                                        value="<?php echo htmlspecialchars($second_id); ?>"
                                        data-parent="<?php echo htmlspecialchars($main_key); ?>"
                                        id="menu_<?php echo htmlspecialchars($second_id); ?>"
                                        <?php echo $second_checked; ?>
                                    >
                                    <label class="form-check-label" for="menu_<?php echo htmlspecialchars($second_id); ?>">
                                        <?php echo strip_tags($second_level['text'] ?? $second_id); ?>
                                    </label>
                                </div>

                                <?php if (!empty($second_level['items']) && is_array($second_level['items'])): ?>

                                    <?php foreach ($second_level['items'] as $third_level): ?>

                                        <?php
                                        if (isset($third_level['divider'])) {
                                            continue;
                                        }

                                        if (isset($third_level['enabled']) && !$third_level['enabled']) {
                                            continue;
                                        }

                                        if (empty($third_level['id'])) {
                                            continue;
                                        }

                                        $third_id = $third_level['id'];
                                        $third_checked = in_array($third_id, $user_menus, true) ? 'checked' : '';
                                        ?>

                                        <div class="form-check mb-2 ms-4">
                                            <input
                                                type="checkbox"
                                                class="form-check-input menu-child"
                                                name="user_menus[]"
                                                value="<?php echo htmlspecialchars($third_id); ?>"
                                                data-parent="<?php echo htmlspecialchars($main_key); ?>"
                                                data-second-parent="<?php echo htmlspecialchars($second_id); ?>"
                                                id="menu_<?php echo htmlspecialchars($third_id); ?>"
                                                <?php echo $third_checked; ?>
                                            >
                                            <label class="form-check-label" for="menu_<?php echo htmlspecialchars($third_id); ?>">
                                                <?php echo strip_tags($third_level['text'] ?? $third_id); ?>
                                            </label>
                                        </div>

                                    <?php endforeach; ?>

                                <?php endif; ?>

                            <?php endforeach; ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <?php endforeach; ?>
    </div>
</div>

<script>
jQuery(function($){

    $('.menu-parent').on('change', function(){

        var parent = $(this).data('menu-parent');
        var checked = $(this).is(':checked');

        $('.menu-child[data-parent="' + parent + '"]').prop('checked', checked);
    });

    function toggleMenuRestrictionsByAdmin()
    {
        var adminChecked = $('input[name="main[admin]"]').is(':checked');

        if (adminChecked) {
            $('#user-menu-restrictions-wrapper').hide();
        } else {
            $('#user-menu-restrictions-wrapper').show();
        }
    }

    toggleMenuRestrictionsByAdmin();

    $('input[name="main[admin]"]').on('change', function(){
        toggleMenuRestrictionsByAdmin();
    });

});
</script>

<script type="text/javascript">
window.onload=function(){
	$('input[name="main[email]"]').on('blur',function(){
		var value=$(this).val();
		if(value.trim()!==''){
                        var id='<?php if(isset($_GET['id'])){ echo $_GET['id']; } ?>';
			var intent='<?php if(isset($_GET['intent'])){ echo $_GET['intent']; } ?>'
			$.ajax({
				type: "POST",
				url: "app.php?do_action=check_unique&value="+$(this).val()+"&id="+id+"&table="+intent+"&column=email",
				data: '',
				cache: false,
				success: function(html){
					var target=$('input[name="main[email]"]').parents('div[class^="col-"]');
					if(html=='ko'){
                                            target.find('.additional-feedback').html('<span class="fs-10 text-danger">Questa email è già in uso da un altro utente, si prega di usare un altro indirizzo</span>');
                                            $('input[name="main[email]"]').val('');
                                        }
				}
			});
		}
	});
}

</script>