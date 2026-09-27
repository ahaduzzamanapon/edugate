<?php echo form_open(get_uri("leads/save_transfer"), array("id" => "lead-transfer-form", "class" => "general-form", "role" => "form")); ?>
<div class="modal-body clearfix">
    <div class="container-fluid">
        <input type="hidden" name="lead_id" value="<?php echo $lead_info->id; ?>" />
        <input type="hidden" name="from_context" value="<?php echo $from_context; ?>" />
        <?php if (isset($sort) && $sort) { ?>
            <input type="hidden" name="sort" value="<?php echo $sort; ?>" />
        <?php } ?>

        <!-- Lead Summary Card -->
        <div class="bg-light p15 rounded mb15 border">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small d-block">Lead Name:</span>
                    <h5 class="fw-bold mb0 text-primary"><?php echo $lead_info->company_name; ?></h5>
                </div>
                <div>
                    <span class="text-muted small d-block text-end">Current Status:</span>
                    <span class="badge rounded-pill px-3 py-1" style="background-color: <?php echo $current_status ? $current_status->color : '#6c757d'; ?>;">
                        <?php echo $current_status ? $current_status->title : 'Unknown'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Target Status Selection -->
        <div class="form-group mb15">
            <?php if ($from_context === 'kanban' && $target_status) { ?>
                <label class="form-label fw-bold">Target Status / গন্তব্য স্ট্যাটাস:</label>
                <div>
                    <span class="badge rounded-pill px-3 py-2 fs-6 text-white" style="background-color: <?php echo $target_status->color; ?>;">
                        <i data-feather="arrow-right-circle" class="icon-14 mr5"></i> <?php echo $target_status->title; ?>
                    </span>
                </div>
                <input type="hidden" name="to_status_id" id="transfer_to_status_id" value="<?php echo $target_status->id; ?>" />
            <?php } else { ?>
                <label for="transfer_to_status_id" class="form-label fw-bold">New Status / নতুন স্ট্যাটাস:</label>
                <select name="to_status_id" id="transfer_to_status_id" class="form-select select2" required>
                    <option value="">- Select Status / স্ট্যাটাস নির্বাচন করুন -</option>
                    <?php foreach ($allowed_statuses as $st) { ?>
                        <option value="<?php echo $st->id; ?>" <?php echo ((int)$st->id === (int)$to_status_id) ? "selected" : ""; ?>>
                            <?php echo $st->title; ?>
                        </option>
                    <?php } ?>
                </select>
            <?php } ?>
        </div>

        <!-- Assign Person Selection -->
        <div class="form-group mb15">
            <label for="transfer_owner_id" class="form-label fw-bold">
                <i data-feather="user-check" class="icon-14 text-success mr5"></i> Assign To / দায়িত্বপ্রাপ্ত কর্মকর্তা:
            </label>
            <select name="owner_id" id="transfer_owner_id" class="form-select select2" required>
                <option value="">- Select Assignee / কর্মকর্তা নির্বাচন করুন -</option>
                <?php foreach ($qualified_staff as $staff) { ?>
                    <?php 
                        $role_text = $staff->is_admin ? "Admin" : ($staff->role_title ? $staff->role_title : "Staff");
                        $is_current = ((int)$staff->id === (int)$lead_info->owner_id);
                    ?>
                    <option value="<?php echo $staff->id; ?>" <?php echo $is_current ? "selected" : ""; ?>>
                        <?php echo $staff->first_name . " " . $staff->last_name . " (" . $role_text . ")" . ($is_current ? " [Current]" : ""); ?>
                    </option>
                <?php } ?>
            </select>
            <small class="text-muted d-block mt5">
                <i data-feather="info" class="icon-12 mr5"></i> এই স্ট্যাটাসে যেসব কর্মকর্তার এক্সেস রয়েছে শুধুমাত্র তাদের তালিকা প্রদর্শিত হচ্ছে।
            </small>
        </div>

        <!-- Optional Transfer Note -->
        <div class="form-group mb10">
            <label for="transfer_note" class="form-label">
                <i data-feather="file-text" class="icon-14 mr5"></i> Internal Note / মন্তব্য (Optional):
            </label>
            <textarea name="note" id="transfer_note" class="form-control" rows="2" placeholder="কেন এই স্ট্যাটাসে বা কর্মকর্তার কাছে পাঠানো হচ্ছে..."></textarea>
        </div>

    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-default" data-bs-dismiss="modal">
        <span data-feather="x" class="icon-16"></span> Cancel
    </button>
    <button type="submit" class="btn btn-primary" id="btn-submit-transfer">
        <span data-feather="check-circle" class="icon-16 mr5"></span> Confirm & Transfer
    </button>
</div>
<?php echo form_close(); ?>

<script type="text/javascript">
    $(document).ready(function () {
        feather.replace();
        $("#lead-transfer-form select.select2").select2();

        $("#lead-transfer-form").appForm({
            onSuccess: function (result) {
                if (result.from_context === "kanban") {
                    window.kanbanTransferCompleted = true;
                    $("#reload-kanban-button:visible").trigger("click");
                    appAlert.success(result.message);
                } else if (result.from_context === "details") {
                    appAlert.success(result.message, {duration: 10000});
                    setTimeout(function () {
                        location.reload();
                    }, 500);
                } else {
                    $("#lead-table").appTable({newData: result.data, dataId: result.id});
                    appAlert.success(result.message);
                }
            }
        });

        // Dynamic status change handler if from table or details
        $("#transfer_to_status_id").on("change", function () {
            var newStatusId = $(this).val();
            if (!newStatusId) return;

            $("#transfer_owner_id").prop("disabled", true);
            $.ajax({
                url: '<?php echo get_uri("leads/get_staff_for_status"); ?>/' + newStatusId,
                type: 'GET',
                dataType: 'json',
                success: function (res) {
                    $("#transfer_owner_id").empty();
                    if (res.success && res.staff && res.staff.length > 0) {
                        $("#transfer_owner_id").append('<option value="">- Select Assignee / কর্মকর্তা নির্বাচন করুন -</option>');
                        res.staff.forEach(function (s) {
                            var isCurrent = (s.id == "<?php echo $lead_info->owner_id; ?>");
                            $("#transfer_owner_id").append('<option value="' + s.id + '" ' + (isCurrent ? 'selected' : '') + '>' + s.text + (isCurrent ? ' [Current]' : '') + '</option>');
                        });
                    } else {
                        $("#transfer_owner_id").append('<option value="">No staff with access to this status</option>');
                    }
                    $("#transfer_owner_id").prop("disabled", false);
                    $("#transfer_owner_id").select2("destroy").select2();
                }
            });
        });
    });
</script>
