<div class="table-responsive">
    <table id="country-settings-table" class="display no-thead b-b-only no-hover" cellspacing="0" width="100%">         
    </table>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#country-settings-table").appTable({
            source: '<?php echo_uri("country_settings/list_data") ?>',
            order: [[0, "asc"]],
            hideTools: true,
            displayLength: 100,
            columns: [
                {visible: false},
                {title: 'Country'},
                {title: '<i data-feather="menu" class="icon-16"></i>', "class": "text-center option w100"}
            ],
            onInitComplete: function () {
                $("#country-settings-table").find("tbody").attr("id", "country-settings-table-sortable");
                var $selector = $("#country-settings-table-sortable");

                Sortable.create($selector[0], {
                    animation: 150,
                    chosenClass: "sortable-chosen",
                    ghostClass: "sortable-ghost",
                    onUpdate: function (e) {
                        appLoader.show();
                        var data = "";
                        $.each($selector.find(".field-row"), function (index, ele) {
                            if (data) {
                                data += ",";
                            }
                            data += $(ele).attr("data-id") + "-" + index;
                        });

                        appAjaxRequest({
                            url: '<?php echo_uri("country_settings/update_field_sort_values") ?>',
                            type: "POST",
                            data: {sort_values: data},
                            success: function () {
                                appLoader.hide();
                            }
                        });
                    }
                });
            }
        });
    });
</script>
