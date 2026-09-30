<input type="hidden" name="id" value="<?php echo $model_info->id; ?>" />
<input type="hidden" name="view" value="<?php echo isset($view) ? $view : ""; ?>" />
<input type="hidden" name="account_type" value="person" />

<div class="form-group">
    <div class="row">
        <label for="company_name" class="<?php echo $label_column; ?>"><?php echo app_lang('name'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "company_name",
                "name" => "company_name",
                "value" => $model_info->company_name,
                "class" => "form-control",
                "placeholder" => app_lang('name'),
                "autofocus" => true,
                "data-rule-required" => true,
                "data-msg-required" => app_lang("field_required"),
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="lead_status_id" class="<?php echo $label_column; ?>"><?php echo app_lang('status'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            $lead_status = array();
            if (isset($statuses) && is_array($statuses)) {
                foreach ($statuses as $status) {
                    $lead_status[$status->id] = $status->title;
                }
            }

            echo form_dropdown("lead_status_id", $lead_status, array($model_info->lead_status_id), "class='select2' id='lead_status_id'");
            ?>
        </div>
    </div>
</div>

<?php if (isset($owners_dropdown) && is_array($owners_dropdown)) { ?>
<div class="form-group">
    <div class="row">
        <label for="owner_id" class="<?php echo $label_column; ?>"><?php echo app_lang('owner'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            $owners_options = array("" => "- " . app_lang("owner") . " -");
            foreach ($owners_dropdown as $owner) {
                $o_id = is_array($owner) ? (isset($owner['id']) ? $owner['id'] : '') : (isset($owner->id) ? $owner->id : '');
                $o_text = is_array($owner) ? (isset($owner['text']) ? $owner['text'] : '') : (isset($owner->text) ? $owner->text : '');
                if ($o_id) {
                    $owners_options[$o_id] = $o_text;
                }
            }
            echo form_dropdown("owner_id", $owners_options, array($model_info->owner_id), "class='select2' id='owner_id'");
            ?>
        </div>
    </div>
</div>
<?php } ?>

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
        <label for="email" class="<?php echo $label_column; ?>"><?php echo app_lang('email'); ?></label>
        <div class="<?php echo $field_column; ?>">
            <?php
            echo form_input(array(
                "id" => "email",
                "name" => "email",
                "value" => isset($model_info->email) ? $model_info->email : "",
                "class" => "form-control",
                "placeholder" => app_lang('email'),
                "data-rule-email" => true,
                "data-msg-email" => app_lang("enter_valid_email")
            ));
            ?>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="row">
        <label for="preferred_country" class="<?php echo $label_column; ?>">Preferred Country</label>
        <div class="<?php echo $field_column; ?>">
            <?php
            $countries_options = isset($countries_dropdown) && is_array($countries_dropdown) ? $countries_dropdown : array("" => "- Select Preferred Country -");
            if ($model_info->preferred_country && !isset($countries_options[$model_info->preferred_country])) {
                $countries_options[$model_info->preferred_country] = $model_info->preferred_country;
            }
            echo form_dropdown("preferred_country", $countries_options, array($model_info->preferred_country), "class='select2' id='preferred_country'");
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
                    echo form_dropdown("ielts_status", $ielts_options, array($model_info->ielts_status), "class='select2' id='ielts_status'");
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

<script type="text/javascript">
    $(document).ready(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
        $("#lead-form .select2, #company-form .select2, .select2").select2();
    });
    setTimeout(function() {
        $("#lead-form .select2, #company-form .select2, .select2").select2();
    }, 100);
</script>