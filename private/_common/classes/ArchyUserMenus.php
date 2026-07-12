<?php

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - USER MENU PERMISSIONS MANAGER 1.0
|--------------------------------------------------------------------------
|
| Gestione salvataggio permessi menu utente tramite tabella:
| archy_user_menus
|
*/

class ArchyUserMenus
{
    protected mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function saveUserMenus(int $userId, array $menus): void
    {
        $userId = (int) $userId;

        $this->db->query("
            DELETE FROM archy_user_menus
            WHERE user_id = ".$userId."
        ");

        foreach ($menus as $menuKey) {
            $menuKey = trim($menuKey);

            if ($menuKey === '') {
                continue;
            }

            $menuKey = $this->db->real_escape_string($menuKey);

            $this->db->query("
                INSERT INTO archy_user_menus
                SET user_id = ".$userId.",
                    menu_key = '".$menuKey."'
            ");
        }
    }

    public function getUserMenus(int $userId): array
    {
        $menus = [];

        $res = $this->db->query("
            SELECT menu_key
            FROM archy_user_menus
            WHERE user_id = ".(int)$userId."
        ");

        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $menus[] = $row['menu_key'];
            }
        }

        return $menus;
    }
}