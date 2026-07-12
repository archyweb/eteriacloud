<?php

/*
|--------------------------------------------------------------------------
| ARCHY MEDIA - USER ROLES MANAGER 1.0
|--------------------------------------------------------------------------
|
| Gestione salvataggio ruoli utente tramite tabella ponte archy_user_roles.
|
*/

class ArchyUserRoles
{
    protected mysqli $db;

    public function __construct(mysqli $db)
    {
        $this->db = $db;
    }

    public function saveUserRoles(int $userId, array $roles): void
    {
        $userId = (int) $userId;

        $this->db->query("
            DELETE FROM archy_user_roles
            WHERE user_id = ".$userId."
        ");

        foreach ($roles as $roleId) {

            $roleId = (int) $roleId;

            if ($roleId > 0) {

                $this->db->query("
                    INSERT INTO archy_user_roles
                    SET user_id = ".$userId.",
                        role_id = ".$roleId."
                ");
            }
        }
    }

    public function getUserRoles(int $userId): array
    {
        $roles = [];

        $res = $this->db->query("
            SELECT role_id
            FROM archy_user_roles
            WHERE user_id = ".(int)$userId."
        ");

        if ($res) {

            while ($row = $res->fetch_assoc()) {

                $roles[] = (int) $row['role_id'];
            }
        }

        return $roles;
    }
}