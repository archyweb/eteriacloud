<?php

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - PERMISSION SYSTEM 1.0
|--------------------------------------------------------------------------
|
| Sistema ruoli e permessi base.
|
| Logica:
| - un utente può avere uno o più ruoli;
| - ogni ruolo può avere più permessi;
| - i permessi sono stringhe tipo:
|   archy_art_catalog.view
|   archy_art_catalog.update
|   archy_art_catalog.delete
|
*/

class ArchyPermissions
{
    protected mysqli $db;
    protected int $userId;
    protected array $roles = [];
    protected array $permissions = [];

    public function __construct(mysqli $db, int $userId)
    {
        $this->db = $db;
        $this->userId = (int) $userId;

        $this->loadRoles();
        $this->loadPermissions();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function can(string $permissionKey): bool
    {
        return in_array($permissionKey, $this->permissions, true);
    }

    public function canIntent(string $intent, string $action): bool
    {
        return $this->can($intent . '.' . $action);
    }

    public function cannot(string $permissionKey): bool
    {
        return !$this->can($permissionKey);
    }

    public function cannotIntent(string $intent, string $action): bool
    {
        return !$this->canIntent($intent, $action);
    }

    protected function loadRoles(): void
    {
        $sql = "
            SELECT r.id, r.name, r.label
            FROM archy_roles r
            INNER JOIN archy_user_roles ur ON ur.role_id = r.id
            WHERE ur.user_id = " . $this->userId . "
        ";

        $res = $this->db->query($sql);

        if (!$res) {
            return;
        }

        while ($row = $res->fetch_assoc()) {
            $this->roles[] = $row;
        }
    }

    protected function loadPermissions(): void
    {
        $sql = "
            SELECT DISTINCT p.permission_key
            FROM archy_permissions p
            INNER JOIN archy_role_permissions rp ON rp.permission_id = p.id
            INNER JOIN archy_user_roles ur ON ur.role_id = rp.role_id
            WHERE ur.user_id = " . $this->userId . "
        ";

        $res = $this->db->query($sql);

        if (!$res) {
            return;
        }

        while ($row = $res->fetch_assoc()) {
            $this->permissions[] = $row['permission_key'];
        }
    }
	
	/*
|--------------------------------------------------------------------------
| MENU VIETATI UTENTE
|--------------------------------------------------------------------------
*/

public function getForbiddenMenus(): array
{
    $menus = [];

    $res = $this->db->query("
        SELECT menu_key
        FROM archy_user_menus
        WHERE user_id = ".$this->userId."
    ");

    if ($res) {

        while ($row = $res->fetch_assoc()) {

            $menus[] = $row['menu_key'];
        }
    }

    return $menus;
}

/*
|--------------------------------------------------------------------------
| ACCESSO MENU
|--------------------------------------------------------------------------
*/

public function canAccessMenu(string $menuKey): bool
{
    return !in_array(
        $menuKey,
        $this->getForbiddenMenus(),
        true
    );
}


/*
|--------------------------------------------------------------------------
| CHECK RUOLO UTENTE
|--------------------------------------------------------------------------
|
| Verifica se l'utente possiede un ruolo specifico.
|
| Esempio:
| $Permissions->userHasRole('administrator')
|
*/

public function userHasRole(string $roleName): bool
{
    foreach ($this->roles as $role) {

        if (
            isset($role['name']) &&
            $role['name'] === $roleName
        ) {
            return true;
        }
    }

    return false;
}
}