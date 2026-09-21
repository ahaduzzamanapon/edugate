<div class="card-body p20">
    <div class="row align-items-center mb20 pb15 border-bottom">
        <div class="col-md-5">
            <label for="lead-role-selector" class="form-label fw-bold mb5">
                <i data-feather="user-check" class="icon-16 mr5 text-primary"></i> Select Role:
            </label>
            <select id="lead-role-selector" class="form-select form-control">
                <option value="">-- Choose a Role --</option>
                <?php foreach ($roles_dropdown as $role) { ?>
                    <option value="<?php echo $role->id; ?>"><?php echo $role->title; ?></option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-7 text-end pt15">
            <button type="button" id="btn-save-role-permissions" class="btn btn-primary" disabled>
                <i data-feather="check-circle" class="icon-16 mr5"></i> Save Permissions
            </button>
        </div>
    </div>

    <div id="role-permissions-container" style="display: none;">

        <!-- 1. Visible Statuses -->
        <div class="card mb20 border">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold">
                    <i data-feather="eye" class="icon-16 mr5 text-primary"></i> 1. View Permission (কোন কোন স্ট্যাটাসের লিড দেখতে পারবে?)
                </span>
                <div>
                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-select-all-view">Select All</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-deselect-all-view">Clear All</button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted small mb15">এই রোলের ইউজাররা শুধুমাত্র টিক দেওয়া স্ট্যাটাসগুলোর লিড লিস্ট ও কানবান বোর্ডে দেখতে পাবে।</p>
                <div class="row">
                    <?php foreach ($statuses as $st) { ?>
                        <div class="col-md-4 col-sm-6 mb10">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input chk-view-status me-2" type="checkbox" value="<?php echo $st->id; ?>" id="view_st_<?php echo $st->id; ?>">
                                <label class="form-check-label d-flex align-items-center cursor-pointer mb0" for="view_st_<?php echo $st->id; ?>">
                                    <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background-color:<?php echo $st->color; ?>; margin-right:8px;"></span>
                                    <span><?php echo $st->title; ?></span>
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- 2. Allowed Move / Destination Statuses -->
        <div class="card mb20 border">
            <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                <span class="fw-bold">
                    <i data-feather="arrow-right-circle" class="icon-16 mr5 text-success"></i> 2. Move / Transfer Permission (কোন কোন স্ট্যাটাসে পাঠাতে পারবে?)
                </span>
                <div>
                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-select-all-move">Select All</button>
                    <button type="button" class="btn btn-xs btn-outline-secondary" id="btn-deselect-all-move">Clear All</button>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted small mb15">এই রোলের ইউজাররা লিড ড্র্যাগ করে বা এডিট করে শুধুমাত্র এই অনুমোদিত স্ট্যাটাসগুলোতে পাঠাতে পারবে।</p>
                <div class="row">
                    <?php foreach ($statuses as $st) { ?>
                        <div class="col-md-4 col-sm-6 mb10">
                            <div class="form-check d-flex align-items-center">
                                <input class="form-check-input chk-move-status me-2" type="checkbox" value="<?php echo $st->id; ?>" id="move_st_<?php echo $st->id; ?>">
                                <label class="form-check-label d-flex align-items-center cursor-pointer mb0" for="move_st_<?php echo $st->id; ?>">
                                    <span style="display:inline-block; width:10px; height:10px; border-radius:50%; background-color:<?php echo $st->color; ?>; margin-right:8px;"></span>
                                    <span><?php echo $st->title; ?></span>
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <!-- 3. Document / File Upload Permission -->
        <div class="card mb20 border">
            <div class="card-header bg-light py-2">
                <span class="fw-bold">
                    <i data-feather="file-text" class="icon-16 mr5 text-warning"></i> 3. Document / File Upload Permission
                </span>
            </div>
            <div class="card-body">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="chk-can-upload-files" value="1">
                    <label class="form-check-label fw-bold cursor-pointer" for="chk-can-upload-files">
                        Enable Document / File Upload for this Role
                    </label>
                    <div class="text-muted small">অনুমোদন দিলে এই রোলের স্টাফরা লিডের ফাইল/ডকুমেন্ট (পাসপোর্ট, সার্টিফিকেট, ইত্যাদি) আপলোড করতে পারবে।</div>
                </div>
            </div>
        </div>

    </div>

    <div id="role-empty-message" class="text-center py-5 text-muted">
        <i data-feather="arrow-up" class="icon-32 mb10 text-muted"></i>
        <h5>অনুগ্রহ করে উপরে থেকে একটি Role নির্বাচন করুন।</h5>
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
                    $('.chk-move-status').prop('checked', false);
                    $('#chk-can-upload-files').prop('checked', false);

                    // 1. Populate View Statuses
                    if (res.can_view_status_ids && res.can_view_status_ids.length > 0) {
                        res.can_view_status_ids.forEach(function(sid) {
                            $('#view_st_' + sid).prop('checked', true);
                        });
                    }

                    // 2. Populate Move Statuses
                    if (res.can_move_to_status_ids && res.can_move_to_status_ids.length > 0) {
                        res.can_move_to_status_ids.forEach(function(sid) {
                            $('#move_st_' + sid).prop('checked', true);
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

    // View: Select All / Clear All buttons
    $('#btn-select-all-view').on('click', function() {
        $('.chk-view-status').prop('checked', true);
    });
    $('#btn-deselect-all-view').on('click', function() {
        $('.chk-view-status').prop('checked', false);
    });

    // Move: Select All / Clear All buttons
    $('#btn-select-all-move').on('click', function() {
        $('.chk-move-status').prop('checked', true);
    });
    $('#btn-deselect-all-move').on('click', function() {
        $('.chk-move-status').prop('checked', false);
    });

    // Save Permissions
    $('#btn-save-role-permissions').on('click', function() {
        if (!currentRoleId) return;

        var canViewStatusIds = [];
        $('.chk-view-status:checked').each(function() {
            canViewStatusIds.push($(this).val());
        });

        var canMoveToStatusIds = [];
        $('.chk-move-status:checked').each(function() {
            canMoveToStatusIds.push($(this).val());
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
                can_move_to_status_ids: canMoveToStatusIds,
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
