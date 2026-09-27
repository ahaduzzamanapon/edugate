<?php
$transition_map_json = isset($status_transition_map) ? json_encode($status_transition_map) : "{}";
$is_admin_user = (isset($login_user) && $login_user->is_admin) ? "true" : "false";

$default_statuses = array();
if (isset($lead_statuses)) {
    foreach ($lead_statuses as $status) {
        $default_statuses[] = array("id" => $status->id, "text" => $status->title);
    }
}
?>

<script type="text/javascript">
    $(document).ready(function () {
        var statusTransitionMap = <?php echo $transition_map_json; ?>;
        var isAdminUser = <?php echo $is_admin_user; ?>;
        var defaultStatuses = <?php echo json_encode($default_statuses); ?>;

        $('body').on('click', '[data-act=update-lead-status]', function (e) {
            e.preventDefault();
            var $this = $(this);
            var currentStatusId = parseInt($this.attr('data-value')) || 0;
            var leadId = $this.attr('data-id');

            var allowedOptions = defaultStatuses;
            if (statusTransitionMap && statusTransitionMap[currentStatusId] !== undefined) {
                allowedOptions = statusTransitionMap[currentStatusId];
            }

            // If non-admin and only 1 or 0 options (meaning only current status is present, no alternative transition allowed)
            if (!isAdminUser && allowedOptions && allowedOptions.length <= 1) {
                appAlert.warning("এই স্ট্যাটাস পরিবর্তন করার অনুমতি আপনার রোলে নেই।");
                return false;
            }

            // Open transfer modal to select new status and role-qualified assignee
            var $trigger = $('<a href="#" class="hide" data-act="ajax-modal" data-title="Transfer Lead / লিড স্থানান্তর" data-action-url="<?php echo_uri("leads/transfer_modal_form"); ?>" data-post-lead_id="' + leadId + '" data-post-from_context="table"></a>');
            $('body').append($trigger);
            $trigger.trigger('click');
            $trigger.remove();

            return false;
        });
    });
</script>