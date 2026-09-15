<div class="card-body p20">
    <div class="row">
        <div class="col-md-12 mb20">
            <div class="alert alert-info" role="alert">
                <i data-feather="info" class="icon-16 mr5"></i>
                <strong>Role-Based Lead Status & Transition Configuration:</strong> 
                Select a staff role to configure which lead statuses they can view and which status transitions they are authorized to perform.
            </div>
        </div>

        <div class="col-md-5 mb20">
            <label for="lead-role-selector" class="form-label fw-bold">Select Role:</label>
            <select id="lead-role-selector" class="form-select form-control">
                <option value="">-- Choose a Role --</option>
                <?php foreach ($roles_dropdown as $role) { ?>
                    <option value="<?php echo $role->id; ?>"><?php echo $role->title; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-7 mb20 text-end pt20">
            <button type="button" id="btn-save-role-permissions" class="btn btn-primary" disabled>
                <i data-feather="check-circle" class="icon-16 mr5"></i> Save Permissions
            </button>
        </div>
    </div>

    <div id="role-permissions-container" style="display: none;">
        <hr class="mt0 mb20" />

        <!-- 1. Visible Statuses -->
        <div class="card mb20 border">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold"><i data-feather="eye" class="icon-16 mr5 text-primary"></i> 1. Status Visibility (Which leads can this role VIEW?)</span>
                <div>
                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-select-all-view">Select All</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-deselect-all-view">Clear All</button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted small mb15">Staff with this role will only see leads matching the checked statuses in Lead List, Kanban, and Filters.</p>
                <div class="row">
                    <?php foreach ($statuses as $st) { ?>
                        <div class="col-md-4 col-sm-6 mb10">
                            <div class="form-check">
                                <input class="form-check-input chk-view-status" type="checkbox" value="<?php echo $st->id; ?>" id="view_st_<?php echo $st->id; ?>" data-status-id="<?php echo $st->id; ?>">
                                <label class="form-check-label" for="view_st_<?php echo $st->id; ?>">
                                    <span class="badge" style="background-color: <?php echo $st->color; ?>; color: #fff; font-size: 11px; margin-right: 4px;">&bull;</span>
                                    <strong><?php echo $st->title; ?></strong>
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- 2. Allowed Transitions -->
        <div class="card mb20 border">
            <div class="card-header bg-light py-2">
                <span class="fw-bold"><i data-feather="git-commit" class="icon-16 mr5 text-info"></i> 2. Status Transitions (From which status can they CONVERT to which status?)</span>
            </div>
            <div class="card-body">
                <p class="text-muted small mb15">For each current status, check the allowed target statuses this role can move the lead into.</p>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Current Status (From)</th>
                                <th style="width: 75%;">Allowed Next Statuses (To)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($statuses as $from_st) { ?>
                                <tr id="transition-row-<?php echo $from_st->id; ?>">
                                    <td class="align-top">
                                        <div class="d-flex align-items-center">
                                            <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background-color:<?php echo $from_st->color; ?>; margin-right:8px;"></span>
                                            <strong><?php echo $from_st->title; ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="row">
                                            <?php foreach ($statuses as $to_st) { 
                                                if ($from_st->id == $to_st->id) continue; // skip same
                                            ?>
                                                <div class="col-md-4 col-sm-6 mb5">
                                                    <div class="form-check">
                                                        <input class="form-check-input chk-transition" type="checkbox" 
                                                               data-from="<?php echo $from_st->id; ?>" 
                                                               data-to="<?php echo $to_st->id; ?>" 
                                                               id="tr_<?php echo $from_st->id; ?>_<?php echo $to_st->id; ?>">
                                                        <label class="form-check-label small" for="tr_<?php echo $from_st->id; ?>_<?php echo $to_st->id; ?>">
                                                            <span style="color: <?php echo $to_st->color; ?>;">&#9632;</span> <?php echo $to_st->title; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 3. Additional Capabilities -->
        <div class="card mb20 border">
            <div class="card-header bg-light py-2">
                <span class="fw-bold"><i data-feather="file-text" class="icon-16 mr5 text-warning"></i> 3. Document / File Upload Permission</span>
            </div>
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="chk-can-upload-files" value="1">
                    <label class="form-check-label fw-bold" for="chk-can-upload-files">
                        Enable Document Upload for this Role
                    </label>
                    <div class="text-muted small">If enabled, staff in this role can upload student documents (NID, Passport, Certificates, Payment slips). Typically enabled for Senior Counseling (Tier 4) and Admission Team (Tier 5).</div>
                </div>
            </div>
        </div>

    </div>

    <div id="role-empty-message" class="text-center py-5 text-muted">
        <i data-feather="arrow-up" class="icon-32 mb10 text-muted"></i>
        <h5>Please select a Role from the dropdown above to view and configure its lead permissions.</h5>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    feather.replace();

    var currentRoleId = 0;

    // When role changes
    $('#lead-role-selector').on('change', function() {
        var roleId = $(this).val();
        currentRoleId = roleId;

        if (!roleId) {
            $('#role-permissions-container').hide();
            $('#role-empty-message').show();
            $('#btn-save-role-permissions').prop('disabled', true);
            return;
        }

        appLoader.show();
        $.ajax({
            url: '<?php echo get_uri("lead_status/get_role_status_permissions_data"); ?>/' + roleId,
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                appLoader.hide();
                if (res.success) {
                    $('#role-empty-message').hide();
                    $('#role-permissions-container').show();
                    $('#btn-save-role-permissions').prop('disabled', false);

                    // Reset all checkboxes
                    $('.chk-view-status').prop('checked', false);
                    $('.chk-transition').prop('checked', false);
                    $('#chk-can-upload-files').prop('checked', false);

                    // 1. Populate View Statuses
                    if (res.can_view_status_ids && res.can_view_status_ids.length > 0) {
                        res.can_view_status_ids.forEach(function(sid) {
                            $('#view_st_' + sid).prop('checked', true);
                        });
                    }

                    // 2. Populate Transitions
                    if (res.allowed_transitions) {
                        $.each(res.allowed_transitions, function(fromId, toIds) {
                            if (Array.isArray(toIds)) {
                                toIds.forEach(function(toId) {
                                    $('#tr_' + fromId + '_' + toId).prop('checked', true);
                                });
                            }
                        });
                    }

                    // 3. Populate Upload Files
                    if (res.can_upload_files == 1) {
                        $('#chk-can-upload-files').prop('checked', true);
                    }

                    feather.replace();
                } else {
                    appAlert.error(res.message || "Failed to load permissions");
                }
            },
            error: function() {
                appLoader.hide();
                appAlert.error("An error occurred while loading permissions.");
            }
        });
    });

    // Select All / Clear All buttons
    $('#btn-select-all-view').on('click', function() {
        $('.chk-view-status').prop('checked', true);
    });

    $('#btn-deselect-all-view').on('click', function() {
        $('.chk-view-status').prop('checked', false);
    });

    // Save Permissions
    $('#btn-save-role-permissions').on('click', function() {
        if (!currentRoleId) return;

        var canViewStatusIds = [];
        $('.chk-view-status:checked').each(function() {
            canViewStatusIds.push($(this).val());
        });

        var allowedTransitions = {};
        $('.chk-transition:checked').each(function() {
            var fromId = $(this).data('from');
            var toId = $(this).data('to');
            if (!allowedTransitions[fromId]) {
                allowedTransitions[fromId] = [];
            }
            allowedTransitions[fromId].push(toId);
        });

        var canUploadFiles = $('#chk-can-upload-files').is(':checked') ? 1 : 0;

        var $btn = $(this);
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm mr5"></span> Saving...');

        $.ajax({
            url: '<?php echo get_uri("lead_status/save_role_status_permissions"); ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                role_id: currentRoleId,
                can_view_status_ids: canViewStatusIds,
                allowed_transitions: allowedTransitions,
                can_upload_files: canUploadFiles
            },
            success: function(res) {
                $btn.prop('disabled', false).html('<i data-feather="check-circle" class="icon-16 mr5"></i> Save Permissions');
                feather.replace();
                if (res.success) {
                    appAlert.success(res.message);
                } else {
                    appAlert.error(res.message || "Error saving permissions");
                }
            },
            error: function() {
                $btn.prop('disabled', false).html('<i data-feather="check-circle" class="icon-16 mr5"></i> Save Permissions');
                feather.replace();
                appAlert.error("An error occurred while saving.");
            }
        });
    });
});
</script>
