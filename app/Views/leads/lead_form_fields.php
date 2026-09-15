<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<div class="form-group">
    <div class="row">
        <label for="type" class="<?php echo $label_column; ?>"><?php echo app_lang('type'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_radio(array(
                "id" => "type_organization",
                "name" => "account_type",
                "class" => "form-check-input account_type",
                "data-msg-required" => app_lang("field_required"),
            ), "organization", ($model_info->type === "organization") ? true : (($model_info->type !== "person") ? true : false));
            ?>
            <label for="type_organization" class="mr15"><?php echo app_lang('organization'); ?></label>
            <?php
            echo form_radio(array(
                "id" => "type_person",
                "name" => "account_type",
                "class" => "form-check-input account_type",
                "data-msg-required" => app_lang("field_required"),
            ), "person", ($model_info->type === "person") ? true : false);
            ?>
            <label for="type_person" class=""><?php echo app_lang('person'); ?></label>
        </div>
    </div>
</div>
<?php if ($model_info->id) { ?>
    <div class="form-group">
        <div class="row">
            <?php if ($model_info->type == "person") { ?>
                <label for="name" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('name'); ?></label>
            <?php } else { ?>
                <label for="company_name" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('company_name'); ?></label>
            <?php } ?>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => ($model_info->type == "person") ? "name" : "company_name",
                    "name" => "company_name",
                    "value" => $model_info->company_name,
                    "class" => "form-control company_name_input_section",
                    "placeholder" => app_lang('company_name'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                ));
                ?>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="form-group">
        <div class="row">
            <label for="company_name" class="<?php echo $label_column; ?> company_name_section"><?php echo app_lang('company_name'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "company_name",
                    "name" => "company_name",
                    "value" => $model_info->company_name,
                    "class" => "form-control company_name_input_section",
                    "placeholder" => app_lang('company_name'),
                    "autofocus" => true,
                    "data-rule-required" => true,
                    "data-msg-required" => app_lang("field_required"),
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>
<div class="form-group">
    <div class="row">
        <label for="lead_status_id" class="<?php echo $label_column; ?>"><?php echo app_lang('status'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            foreach ($statuses as $status) {
                $lead_status[$status->id] = $status->title;
            }

            echo form_dropdown("lead_status_id", $lead_status, array($model_info->lead_status_id), "class='select2'");
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="owner_id" class="<?php echo $label_column; ?>"><?php echo app_lang('owner'); ?>
            <span class="help" data-container="body" data-bs-toggle="tooltip" title="<?php echo app_lang('the_person_who_will_manage_this_lead') ?>"><i data-feather="help-circle" class="icon-16"></i></span>
        </label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "owner_id",
                "name" => "owner_id",
                "value" => $model_info->owner_id ? $model_info->owner_id : $login_user->id,
                "class" => "form-control",
                "placeholder" => app_lang('owner')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="managers" class="<?php echo $label_column; ?>"><?php echo app_lang('managers'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "managers",
                "name" => "managers",
                "value" => $model_info->managers,
                "class" => "form-control",
                "placeholder" => app_lang('managers')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="lead_source_id" class="<?php echo $label_column; ?>"><?php echo app_lang('source'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            $lead_source = array();

            foreach ($sources as $source) {
                $lead_source[$source->id] = $source->title;
            }

            echo form_dropdown("lead_source_id", $lead_source, array($model_info->lead_source_id), "class='select2'");
            ?>
        </div>
    </div>
</div>

<!-- Education Consultancy Fields -->
<div class="form-group">
    <div class="row">
        <label for="preferred_country" class="<?php echo $label_column; ?>">Preferred Country</label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "preferred_country",
                "name" => "preferred_country",
                "value" => $model_info->preferred_country ? $model_info->preferred_country : "",
                "class" => "form-control",
                "placeholder" => "e.g. UK, USA, Canada, Australia, Europe"
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="qualification" class="<?php echo $label_column; ?>">Academic Qualification</label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "qualification",
                "name" => "qualification",
                "value" => $model_info->qualification ? $model_info->qualification : "",
                "class" => "form-control",
                "placeholder" => "e.g. HSC / A-Level, Bachelor's, Master's"
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="ielts_status" class="<?php echo $label_column; ?>">IELTS Status & Score</label>
        <div class="<?php echo $field_column; ?>">
            <div class="row">
                <div class="col-md-7">
                    <?php
                    $ielts_options = array(
                        "" => "- Select IELTS Status -",
                        "Not Appeared" => "Not Appeared",
                        "Prepared / Planning" => "Prepared / Planning",
                        "Appeared / Completed" => "Appeared / Completed",
                        "Exempted / MOI" => "Exempted / MOI",
                        "PTE / Duolingo" => "PTE / Duolingo"
                    );
                    echo form_dropdown("ielts_status", $ielts_options, array($model_info->ielts_status), "class='select2'");
                    ?>
                </div>
                <div class="col-md-5">
                    <?php
                    echo form_input(array(
                        "id" => "ielts_score",
                        "name" => "ielts_score",
                        "value" => $model_info->ielts_score ? $model_info->ielts_score : "",
                        "class" => "form-control",
                        "placeholder" => "Score / Band (e.g. 6.5)"
                    ));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="preferred_intake" class="<?php echo $label_column; ?>">Preferred Intake</label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "preferred_intake",
                "name" => "preferred_intake",
                "value" => $model_info->preferred_intake ? $model_info->preferred_intake : "",
                "class" => "form-control",
                "placeholder" => "e.g. September 2026, January 2027"
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="address" class="<?php echo $label_column; ?>"><?php echo app_lang('address'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_textarea(array(
                "id" => "address",
                "name" => "address",
                "value" => $model_info->address ? $model_info->address : "",
                "class" => "form-control",
                "placeholder" => app_lang('address')
            ));
            ?>

        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="city" class="<?php echo $label_column; ?>"><?php echo app_lang('city'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "city",
                "name" => "city",
                "value" => $model_info->city,
                "class" => "form-control",
                "placeholder" => app_lang('city')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="state" class="<?php echo $label_column; ?>"><?php echo app_lang('state'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "state",
                "name" => "state",
                "value" => $model_info->state,
                "class" => "form-control",
                "placeholder" => app_lang('state')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="zip" class="<?php echo $label_column; ?>"><?php echo app_lang('zip'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "zip",
                "name" => "zip",
                "value" => $model_info->zip,
                "class" => "form-control",
                "placeholder" => app_lang('zip')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="country" class="<?php echo $label_column; ?>"><?php echo app_lang('country'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "country",
                "name" => "country",
                "value" => $model_info->country,
                "class" => "form-control",
                "placeholder" => app_lang('country')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="phone" class="<?php echo $label_column; ?>"><?php echo app_lang('phone'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "phone",
                "name" => "phone",
                "value" => $model_info->phone,
                "class" => "form-control",
                "placeholder" => app_lang('phone')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="website" class="<?php echo $label_column; ?>"><?php echo app_lang('website'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "website",
                "name" => "website",
                "value" => $model_info->website,
                "class" => "form-control",
                "placeholder" => app_lang('website')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="vat_number" class="<?php echo $label_column; ?>"><?php echo app_lang('vat_number'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "vat_number",
                "name" => "vat_number",
                "value" => $model_info->vat_number,
                "class" => "form-control",
                "placeholder" => app_lang('vat_number')
            ));
            ?>
        </div>
    </div>
</div>
<div class="form-group">
    <div class="row">
        <label for="gst_number" class="<?php echo $label_column; ?>"><?php echo app_lang('gst_number'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "gst_number",
                "name" => "gst_number",
                "value" => $model_info->gst_number,
                "class" => "form-control",
                "placeholder" => app_lang('gst_number')
            ));
            ?>
        </div>
    </div>
</div>

<?php if ($login_user->is_admin && get_setting("module_invoice")) { ?>
    <div class="form-group">
        <div class="row">
            <label for="currency" class="<?php echo $label_column; ?>"><?php echo app_lang('currency'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "currency",
                    "name" => "currency",
                    "value" => $model_info->currency,
                    "class" => "form-control",
                    "placeholder" => app_lang('keep_it_blank_to_use_default') . " (" . get_setting("default_currency") . ")"
                ));
                ?>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <label for="currency_symbol" class="<?php echo $label_column; ?>"><?php echo app_lang('currency_symbol'); ?></label>
            <div class="<?php echo $field_column; ?>">
                <?php
                echo form_input(array(
                    "id" => "currency_symbol",
                    "name" => "currency_symbol",
                    "value" => $model_info->currency_symbol,
                    "class" => "form-control",
                    "placeholder" => app_lang('keep_it_blank_to_use_default') . " (" . get_setting("currency_symbol") . ")"
                ));
                ?>
            </div>
        </div>
    </div>
<?php } ?>

<div class="form-group">
    <div class="row">
        <label for="lead_labels" class="<?php echo $label_column; ?>"><?php echo app_lang('labels'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "lead_labels",
                "name" => "labels",
                "value" => $model_info->labels,
                "class" => "form-control",
                "placeholder" => app_lang('labels')
            ));
            ?>
        </div>
    </div>
</div>

<?php echo view("custom_fields/form/prepare_context_fields", array("custom_fields" => $custom_fields, "label_column" => $label_column, "field_column" => $field_column)); ?>

<script type="text/javascript">
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $("#lead-form .select2").select2();

        <?php if (isset($currency_dropdown)) { ?>
            if ($('#currency').length) {
                $('#currency').select2({
                    data: <?php echo json_encode($currency_dropdown); ?>
                });
            }
        <?php } ?>

        $('#owner_id').select2({
            data: <?php echo json_encode($owners_dropdown); ?>
        });

        $('#managers').select2({
            multiple: true,
            data: <?php echo $team_members_dropdown; ?>
        });

        $("#lead_labels").select2({
            multiple: true,
            data: <?php echo json_encode($label_suggestions); ?>
        });

        $('.account_type').click(function() {
            var inputValue = $(this).attr("value");
            if (inputValue === "person") {
                $(".company_name_section").html("<?php echo app_lang('name'); ?>");
                $(".company_name_input_section").attr("placeholder", "<?php echo app_lang('name'); ?>");
            } else {
                $(".company_name_section").html("<?php echo app_lang('company_name'); ?>");
                $(".company_name_input_section").attr("placeholder", "<?php echo app_lang('company_name'); ?>");
            }
        });
    });
</script>