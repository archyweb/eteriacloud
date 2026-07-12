<?php

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - ROLE MENU PERMISSIONS MANAGER 1.0
|--------------------------------------------------------------------------
|
| Gestione dei menù accessibili per ruolo tramite tabella:
| archy_role_menus
|
*/

class ArchyRoleMenus
{
    protected mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function saveRoleMenus(int $roleId, array $menus): void
    {
        $roleId = (int) $roleId;

        $this->db->query("
            DELETE FROM archy_role_menus
            WHERE role_id = ".$roleId."
        ");

        foreach ($menus as $menuKey) {

            $menuKey = trim($menuKey);

            if ($menuKey !== '') {

                $this->db->query("
                    INSERT INTO archy_role_menus
                    SET role_id = ".$roleId.",
                        menu_key = '".addslashes($menuKey)."'
                ");
            }
        }
    }

    public function getRoleMenus(int $roleId): array
    {
        $menus = [];

        $res = $this->db->query("
            SELECT menu_key
            FROM archy_role_menus
            WHERE role_id = ".(int)$roleId."
        ");

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $menus[] = $row['menu_key'];
            }
        }

        return $menus;
    }
}