<?php

namespace App\Models;

class Lead_countries_model extends Crud_model {

    protected $table = null;

    function __construct() {
        $this->table = 'lead_countries';
        parent::__construct($this->table);
    }

    function get_details($options = array()) {
        $lead_countries_table = $this->db->prefixTable('lead_countries');

        $where = "";
        $id = $this->_get_clean_value($options, "id");
        if ($id) {
            $where = " AND $lead_countries_table.id=$id";
        }

        $sql = "SELECT $lead_countries_table.*
        FROM $lead_countries_table
        WHERE $lead_countries_table.deleted=0 $where
        ORDER BY $lead_countries_table.sort ASC, $lead_countries_table.title ASC";
        return $this->db->query($sql);
    }

    function get_max_sort_value() {
        $lead_countries_table = $this->db->prefixTable('lead_countries');

        $sql = "SELECT MAX($lead_countries_table.sort) as sort
        FROM $lead_countries_table
        WHERE $lead_countries_table.deleted=0";
        $result = $this->db->query($sql);
        if ($result->resultID->num_rows) {
            return $result->getRow()->sort;
        } else {
            return 0;
        }
    }

    function get_country_dropdown_list() {
        $list = $this->get_details()->getResult();
        $dropdown = array("" => "- " . app_lang("preferred_country") . " -");
        foreach ($list as $item) {
            $dropdown[$item->title] = $item->title;
        }
        return $dropdown;
    }

    function get_country_filter_dropdown() {
        $list = $this->get_details()->getResult();
        $dropdown = array(array("id" => "", "text" => "- Preferred Country -"));
        foreach ($list as $item) {
            $dropdown[] = array("id" => $item->title, "text" => $item->title);
        }
        return $dropdown;
    }

}
