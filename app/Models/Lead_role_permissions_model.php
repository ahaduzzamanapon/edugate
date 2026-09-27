<?php

namespace App\Models;

class Lead_role_permissions_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'lead_role_status_permissions';
        parent::__construct($this->table);
    }

    /**
     * Get permission record for a specific role ID
     */
    function get_permission_by_role($role_id) {
        if (!$role_id) {
            return null;
        }
        return $this->get_one_where(array("role_id" => $role_id));
    }

    /**
     * Get list of status IDs that this user/role is allowed to view
     * Returns null if admin (access to all), or array of IDs.
     */
    function get_allowed_view_status_ids($login_user) {
        if (!$login_user || !isset($login_user->id)) {
            return array();
        }

        // Admins can see all statuses
        if ($login_user->is_admin) {
            return null;
        }

        $role_id = isset($login_user->role_id) ? (int)$login_user->role_id : 0;
        if (!$role_id) {
            return null; // Fallback to all if no role assigned
        }

        $perm = $this->get_permission_by_role($role_id);
        if (!$perm || !$perm->id || empty($perm->can_view_status_ids)) {
            return null; // If not configured yet, allow all by default
        }

        $decoded = json_decode($perm->can_view_status_ids, true);
        if (is_array($decoded) && !empty($decoded)) {
            return array_map('intval', $decoded);
        }

        return null;
    }

    /**
     * Get list of status IDs that this user/role is allowed to move/transfer leads into.
     * Returns null if admin (all allowed), or array of allowed target status IDs.
     */
    function get_allowed_move_status_ids($login_user, $from_status_id = 0) {
        if (!$login_user || !isset($login_user->id)) {
            return array();
        }

        if ($login_user->is_admin) {
            return null; // Admin can move to all
        }

        $role_id = isset($login_user->role_id) ? (int)$login_user->role_id : 0;
        if (!$role_id) {
            return null; // No role, allow all by default
        }

        $perm = $this->get_permission_by_role($role_id);
        if (!$perm || !$perm->id || $perm->allowed_transitions === null || $perm->allowed_transitions === '') {
            return null; // Not configured yet, allow all by default
        }

        $transitions = json_decode($perm->allowed_transitions, true);
        if (!is_array($transitions)) {
            return null;
        }

        if (empty($transitions)) {
            return array(); // Explicitly empty: no move allowed
        }

        // Check if flat array of status IDs: e.g. [16] or [16, 17]
        $first_val = reset($transitions);
        if (!is_array($first_val)) {
            return array_values(array_unique(array_map('intval', $transitions)));
        }

        // If dictionary format: e.g. {"15": [16]}
        if ($from_status_id) {
            $from_key = (string)$from_status_id;
            if (isset($transitions[$from_key]) && is_array($transitions[$from_key])) {
                return array_values(array_unique(array_map('intval', $transitions[$from_key])));
            }
            return array();
        }

        // If from_status_id not given, collect all targets
        $targets = array();
        foreach ($transitions as $from_key => $to_ids) {
            if (is_array($to_ids)) {
                foreach ($to_ids as $tid) {
                    $targets[] = (int)$tid;
                }
            }
        }
        return array_values(array_unique($targets));
    }

    /**
     * Get list of status IDs allowed when adding/creating a new lead.
     * Combines statuses the user can view AND statuses the user can transition/move into.
     * Returns null if admin (all allowed), or array of allowed status IDs.
     */
    function get_allowed_create_status_ids($login_user) {
        if (!$login_user || !isset($login_user->id)) {
            return array();
        }

        if ($login_user->is_admin) {
            return null;
        }

        $role_id = isset($login_user->role_id) ? (int)$login_user->role_id : 0;
        if (!$role_id) {
            return null;
        }

        $allowed_views = $this->get_allowed_view_status_ids($login_user);
        $allowed_moves = $this->get_allowed_move_status_ids($login_user, 0);

        // If both are unrestricted, allow all
        if ($allowed_views === null && $allowed_moves === null) {
            return null;
        }

        $combined = array();
        if (is_array($allowed_views)) {
            $combined = array_merge($combined, $allowed_views);
        }
        if (is_array($allowed_moves)) {
            $combined = array_merge($combined, $allowed_moves);
        }

        $combined = array_values(array_unique(array_map('intval', $combined)));

        if (empty($combined)) {
            if ($allowed_views === null || $allowed_moves === null) {
                return null;
            }
            return array();
        }

        return $combined;
    }

    /**
     * Check if a user's role is allowed to transition from one status to another
     */
    function can_transition($login_user, $from_status_id, $to_status_id) {
        if (!$login_user || !isset($login_user->id)) {
            return false;
        }

        // Admin can do any transition
        if ($login_user->is_admin) {
            return true;
        }

        // Same status is always permitted
        if ((int)$from_status_id === (int)$to_status_id) {
            return true;
        }

        $role_id = isset($login_user->role_id) ? (int)$login_user->role_id : 0;
        if (!$role_id) {
            return true;
        }

        $allowed_move_ids = $this->get_allowed_move_status_ids($login_user, $from_status_id);
        if ($allowed_move_ids === null) {
            return true;
        }

        return in_array((int)$to_status_id, $allowed_move_ids);
    }

    /**
     * Get list of staff users who have view access to a specific status ID.
     * Admin users always have access.
     */
    function get_staff_with_access_to_status($status_id) {
        $status_id = (int)$status_id;
        if (!$status_id) {
            return array();
        }

        $permissions = $this->get_all_where(array())->getResult();
        $allowed_role_ids = array();
        $configured_role_ids = array();

        foreach ($permissions as $p) {
            $configured_role_ids[] = (int)$p->role_id;
            if (!empty($p->can_view_status_ids)) {
                $decoded = json_decode($p->can_view_status_ids, true);
                if (is_array($decoded) && in_array($status_id, array_map('intval', $decoded))) {
                    $allowed_role_ids[] = (int)$p->role_id;
                }
            } else {
                $allowed_role_ids[] = (int)$p->role_id;
            }
        }

        $users_table = $this->db->prefixTable('users');
        $roles_table = $this->db->prefixTable('roles');

        $where = "WHERE $users_table.user_type = 'staff' AND $users_table.deleted = 0 AND $users_table.status = 'active'";

        $sql = "SELECT $users_table.id, $users_table.first_name, $users_table.last_name, $users_table.image, $users_table.is_admin, $users_table.role_id, $roles_table.title AS role_title
                FROM $users_table
                LEFT JOIN $roles_table ON $roles_table.id = $users_table.role_id
                $where
                ORDER BY $users_table.first_name ASC";

        $users = $this->db->query($sql)->getResult();

        $qualified_users = array();
        foreach ($users as $u) {
            if ($u->is_admin) {
                $qualified_users[] = $u;
                continue;
            }

            $u_role = (int)$u->role_id;
            if (in_array($u_role, $allowed_role_ids)) {
                $qualified_users[] = $u;
            } else if (!in_array($u_role, $configured_role_ids)) {
                $qualified_users[] = $u;
            }
        }

        return $qualified_users;
    }
}
