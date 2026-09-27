<div class="card bg-white">
    <div class="card-header clearfix">
        <span class="float-start font-16 pt-1"><i data-feather="layers" class="icon-16"></i>&nbsp; <?php echo app_lang('leads'); ?></span>
        <div class="float-end">
            <?php echo modal_anchor(get_uri("leads/modal_form"), "<i data-feather='plus-circle' class='icon-16'></i> " . app_lang('add_lead'), array("class" => "btn btn-default btn-sm me-2", "title" => app_lang('add_lead'))); ?>
            <a href="<?php echo get_uri('leads'); ?>" class="btn btn-default btn-sm"><i data-feather="arrow-right" class="icon-16"></i> <?php echo app_lang('view_all'); ?></a>
        </div>
    </div>
    <div class="table-responsive">
        <table id="lead-table" class="display" cellspacing="0" width="100%">            
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var mobileView = isMobile() ? 1 : 0;

        $("#lead-table").appTable({
            source: '<?php echo_uri("leads/list_data/") ?>' + mobileView,
            serverSide: true,
            smartFilterIdentity: "dashboard_recent_leads",
            order: [[5, "desc"]],
            displayLength: 10,
            columns: [
                {title: "<?php echo app_lang("name") ?>", "class": "all", order_by: "company_name"},
                {title: "<?php echo app_lang("primary_contact") ?>", order_by: "primary_contact"},
                {title: "<?php echo app_lang("phone") ?>"},
                {title: "Preferred Country", order_by: "preferred_country"},
                {title: "<?php echo app_lang("owner") ?>", order_by: "owner_name"},
                {visible: false, searchable: false, order_by: "created_date"},
                {title: "<?php echo app_lang("created_at") ?>", "iDataSort": 5, order_by: "created_date"},
                {title: "<?php echo app_lang("status") ?>", order_by: "status"}
                <?php echo isset($custom_field_headers) ? $custom_field_headers : ""; ?>,
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ]
        });
    });
</script>
<?php echo view("leads/update_lead_status_script"); ?>
