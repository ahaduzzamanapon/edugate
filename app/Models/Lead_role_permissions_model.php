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

        $perm = $this->get_permission_by_role($role_id);
        if (!$perm || !$perm->id || empty($perm->allowed_transitions)) {
            return true; // If transition rules are not configured yet, permit
        }

        $transitions = json_decode($perm->allowed_transitions, true);
        if (!is_array($transitions)) {
            return true;
        }

        $from_key = (string)$from_status_id;
        if (!isset($transitions[$from_key])) {
            // If this source status is not defined in transitions, deny
            return false;
        }

        $allowed_targets = $transitions[$from_key];
        if (!is_array($allowed_targets)) {
            return false;
        }

        return in_array((int)$to_status_id, array_map('intval', $allowed_targets));
    }
}
