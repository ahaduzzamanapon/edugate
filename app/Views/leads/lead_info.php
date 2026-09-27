<div class="card">
    <div class="card-header fw-bold lead-info">
        <span class="d-inline-block mt-1">
            <i data-feather="layers" class="icon-16"></i> &nbsp;<?php echo app_lang("lead_info"); ?>
        </span>

        <div class="float-end">
            <div class="action-option" data-bs-toggle="dropdown" aria-expanded="true">
                <i data-feather="more-horizontal" class="icon-16"></i>
            </div>
            <ul class="dropdown-menu" role="menu">
                <li role="presentation"><?php echo modal_anchor(get_uri("leads/modal_form"), "<i data-feather='edit' class='icon-16'></i> " . app_lang('edit'), array("class" => "dropdown-item", "title" => app_lang('edit_lead'), "data-post-id" => $lead_info->id)); ?></li>
            </ul>
        </div>

    </div>

    <div class="card-body">
        <ul class="list-group info-list pt0 border-top-0">
            <li class="list-group-item pt0 border-top-0">
                <span class="mr10" title="<?php echo app_lang("status"); ?>"><i data-feather="check-circle" class="icon-16"></i></span>
                <?php
                $status = "<span class='text-off'>" . app_lang("add") . " " . app_lang("status") . "<span>";

                $lead_status = "<span class='mt0 badge rounded-pill' style='background-color: $lead_info->lead_status_color'>" . $lead_info->lead_status_title . "</span>";
                if (isset($lead_status) && $lead_status) {
                    $status = $lead_status;
                }

                echo modal_anchor(get_uri("leads/transfer_modal_form"), $status, array(
                    'title' => "Transfer Lead / লিড স্থানান্তর",
                    "class" => "",
                    "data-post-lead_id" => $lead_info->id,
                    "data-post-from_context" => "details"
                ));
                ?>
            </li>
            <li class="list-group-item">
                <span class="mr10" title="<?php echo app_lang("source"); ?>"><i data-feather="search" class="icon-16"></i></span>
                <?php
                $source = "<span class='text-off'>" . app_lang("add") . " " . app_lang("source") . "<span>";

                $lead_source = "<span class='mt0 badge rounded-pill text-default b-a'>" . $lead_info->lead_source_title . "</span>";
                if (isset($lead_source) && $lead_source) {
                    $source = $lead_source;
                }

                echo js_anchor($source, array(
                    'title' => "",
                    "class" => "",
                    "data-id" => $lead_info->id,
                    "data-value" => $lead_info->lead_source_id,
                    "data-act" => "lead-modifier",
                    "data-modifier-group" => "lead_info",
                    "data-field" => "source",
                    "data-action-url" => get_uri("leads/update_lead_info/$lead_info->id/lead_source_id")
                ));
                ?>
            </li>
            <li class="list-group-item">
                <span class="mr10" title="<?php echo app_lang("owner"); ?>"><i data-feather="user" class="icon-16"></i></span>
                <?php

                $image_url = get_avatar($lead_info->owner_avatar);
                echo "<span class='avatar avatar-xxs mr5'><img id='lead-owner-avatar' src='$image_url' alt='...'></span>";

                echo js_anchor(
                    $lead_info->owner_name ? $lead_info->owner_name : "<span class='text-off'>" . app_lang("add") . " " . app_lang("owner") . "<span>",
                    array(
                        'title' => "",
                        "class" => "",
                        "data-id" => $lead_info->id,
                        "data-value" => $lead_info->owner_id,
                        "data-act" => "lead-modifier",
                        "data-modifier-group" => "lead_info",
                        "data-field" => "owner_id",
                        "data-action-url" => get_uri("leads/update_lead_info/$lead_info->id/owner_id")
                    )
                );
                ?>
            </li>
            <li class="list-group-item">
                <span class="mr10" title="<?php echo app_lang("managers"); ?>"><i data-feather="users" class="icon-16"></i></span>
                <?php

                echo js_anchor(
                    $lead_info->managers ? $managers : "<span class='text-off'>" . app_lang("add") . " " . app_lang("managers") . "<span>",
                    array(
                        'title' => "",
                        "class" => "",
                        "data-id" => $lead_info->id,
                        "data-value" => $lead_info->managers,
                        "data-act" => "lead-modifier",
                        "data-modifier-group" => "lead_info",
                        "data-field" => "managers",
                        "data-multiple-tags" => "1",
                        "data-action-url" => get_uri("leads/update_lead_info/$lead_info->id/managers")
                    )
                );
                ?>
            </li>
            <?php if ($lead_info->preferred_country) { ?>
                <li class="list-group-item">
                    <span class="mr10" title="Preferred Country"><i data-feather="globe" class="icon-16"></i></span>
                    <strong>Country:</strong> <span class="badge bg-primary text-white"><?php echo $lead_info->preferred_country; ?></span>
                </li>
            <?php } ?>
            <?php if ($lead_info->qualification) { ?>
                <li class="list-group-item">
                    <span class="mr10" title="Academic Qualification"><i data-feather="book-open" class="icon-16"></i></span>
                    <strong>Qualification:</strong> <span><?php echo $lead_info->qualification; ?></span>
                </li>
            <?php } ?>
            <?php if ($lead_info->ielts_status || $lead_info->ielts_score) { ?>
                <li class="list-group-item">
                    <span class="mr10" title="IELTS Status"><i data-feather="award" class="icon-16"></i></span>
                    <strong>IELTS:</strong> <span><?php echo $lead_info->ielts_status; ?><?php echo $lead_info->ielts_score ? " (Score: " . $lead_info->ielts_score . ")" : ""; ?></span>
                </li>
            <?php } ?>
            <?php if ($lead_info->preferred_intake) { ?>
                <li class="list-group-item">
                    <span class="mr10" title="Preferred Intake"><i data-feather="calendar" class="icon-16"></i></span>
                    <strong>Intake:</strong> <span class="badge bg-info text-white"><?php echo $lead_info->preferred_intake; ?></span>
                </li>
            <?php } ?>
            <?php if ($lead_info->phone) { ?>
                <li class="list-group-item">
                    <span class="mr10" title="<?php echo app_lang("phone"); ?>"><i data-feather="phone" class="icon-16"></i></span>
                    <label><a href="tel:<?php echo $lead_info->phone; ?>"><?php echo $lead_info->phone; ?></a></label>
                </li>
            <?php } ?>
            <?php if (!empty($lead_info->email)) { ?>
                <li class="list-group-item">
                    <span class="mr10" title="<?php echo app_lang("email"); ?>"><i data-feather="mail" class="icon-16"></i></span>
                    <label><a href="mailto:<?php echo $lead_info->email; ?>"><?php echo $lead_info->email; ?></a></label>
                </li>
            <?php } ?>
        </ul>
    </div>
</div>