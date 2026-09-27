<?php echo get_reports_topbar(); ?>

<div id="page-content" class="page-wrapper clearfix grid-button">
    <div class="card clearfix">
        <ul id="leads-reports-tabs" data-bs-toggle="ajax-tab" class="nav nav-tabs bg-white inner scrollable-tabs" role="tablist">
            <li class="title-tab"><h4 class="pl15 pt10 pr15"><?php echo app_lang("leads"); ?></h4></li>
            <li><a role="presentation" data-bs-toggle="tab" href="javascript:;" data-bs-target="#team-members-summary-tab"><?php echo app_lang('team_members_summary'); ?></a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade" id="team-members-summary-tab">
                <?php echo view("leads/reports/team_members_summary"); ?>
            </div>
        </div>
    </div>
</div>