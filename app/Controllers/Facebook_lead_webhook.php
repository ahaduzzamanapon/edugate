<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Facebook_lead_webhook extends Controller {

    protected $Users_model;
    protected $Clients_model;
    protected $Lead_status_model;
    protected $Lead_source_model;
    protected $Notes_model;
    protected $Settings_model;

    function __construct() {
        helper(array('url', 'file', 'form', 'language', 'general', 'date_time', 'app_files', 'currency', 'reports'));
        $this->Users_model = model("App\Models\Users_model");
        $this->Clients_model = model("App\Models\Clients_model");
        $this->Lead_status_model = model("App\Models\Lead_status_model");
        $this->Lead_source_model = model("App\Models\Lead_source_model");
        $this->Notes_model = model("App\Models\Notes_model");
        $this->Settings_model = model("App\Models\Settings_model");

        $settings = $this->Settings_model->get_all_required_settings()->getResult();
        foreach ($settings as $setting) {
            config('Rise')->app_settings_array[$setting->setting_name] = $setting->setting_value;
        }
    }

    public function index() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');

        $method = $this->request->getMethod();

        if ($method === 'options') {
            exit;
        }

        // 1. Webhook Verification from Meta (GET request)
        if ($method === 'get') {
            $mode = $this->request->getGet('hub_mode');
            if (!$mode) $mode = $this->request->getGet('hub.mode');

            $token = $this->request->getGet('hub_verify_token');
            if (!$token) $token = $this->request->getGet('hub.verify_token');

            $challenge = $this->request->getGet('hub_challenge');
            if (!$challenge) $challenge = $this->request->getGet('hub.challenge');

            $expected_token = get_setting('facebook_lead_verify_token');
            if (!$expected_token) {
                $expected_token = 'eduget_fb_verify_token_2026'; // Default fallback
            }

            if ($mode === 'subscribe' && $token === $expected_token) {
                // Verification challenge successful
                echo $challenge;
                exit;
            } else {
                return $this->response->setStatusCode(403)->setBody('Verification token mismatch.');
            }
        }

        // 2. Incoming Lead Data from Meta / Webhook (POST request)
        if ($method === 'post') {
            try {
                $raw_body = $this->request->getBody();
                $payload = json_decode($raw_body, true);

                if (!is_array($payload)) {
                    $payload = $this->request->getPost();
                }

                // Check if this is a standard Meta LeadGen Webhook event
                if (isset($payload['entry']) && is_array($payload['entry'])) {
                    foreach ($payload['entry'] as $entry) {
                        if (isset($entry['changes']) && is_array($entry['changes'])) {
                            foreach ($entry['changes'] as $change) {
                                if (isset($change['value']['leadgen_id'])) {
                                    $leadgen_id = $change['value']['leadgen_id'];
                                    $this->_process_meta_leadgen_id($leadgen_id, $change['value']);
                                }
                            }
                        }
                    }
                    return $this->response->setJSON(array("success" => true, "message" => "Meta event processed"));
                }

                // Or Direct JSON payload (e.g. from Zapier / Make / direct form)
                $result = $this->_create_or_update_lead($payload);
                return $this->response->setJSON($result);

            } catch (\Throwable $e) {
                log_message('error', '[FB_WEBHOOK_ERROR] ' . $e->getMessage());
                return $this->response->setStatusCode(500)->setJSON(array("success" => false, "message" => $e->getMessage()));
            }
        }
    }

    /**
     * Fetch lead data from Meta Graph API using leadgen_id
     */
    private function _process_meta_leadgen_id($leadgen_id, $meta_context = array()) {
        $access_token = get_setting('facebook_page_access_token');
        if (!$access_token) {
            log_message('error', '[FB_WEBHOOK] No Facebook Page Access Token configured.');
            return false;
        }

        $url = "https://graph.facebook.com/v19.0/{$leadgen_id}?access_token=" . urlencode($access_token);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (!is_array($data) || isset($data['error'])) {
            log_message('error', '[FB_WEBHOOK] Graph API Error: ' . json_encode($data['error'] ?? 'Unknown error'));
            return false;
        }

        // Parse field_data array from Meta
        $mapped = array();
        if (isset($data['field_data']) && is_array($data['field_data'])) {
            foreach ($data['field_data'] as $field) {
                $fname = strtolower(trim($field['name']));
                $fval = isset($field['values'][0]) ? trim($field['values'][0]) : '';

                if (in_array($fname, ['full_name', 'name', 'first_name', 'student_name'])) {
                    $mapped['name'] = $fval;
                } else if (in_array($fname, ['phone_number', 'phone', 'contact_number', 'mobile'])) {
                    $mapped['phone'] = $fval;
                } else if (in_array($fname, ['email', 'email_address'])) {
                    $mapped['email'] = $fval;
                } else if (strpos($fname, 'country') !== false) {
                    $mapped['preferred_country'] = $fval;
                } else if (strpos($fname, 'ielts') !== false || strpos($fname, 'english') !== false) {
                    $mapped['ielts_status'] = $fval;
                } else if (strpos($fname, 'qualification') !== false || strpos($fname, 'education') !== false || strpos($fname, 'degree') !== false) {
                    $mapped['qualification'] = $fval;
                } else if (strpos($fname, 'intake') !== false || strpos($fname, 'session') !== false) {
                    $mapped['preferred_intake'] = $fval;
                } else {
                    $mapped['extra_fields'][$fname] = $fval;
                }
            }
        }

        $mapped['meta_leadgen_id'] = $leadgen_id;
        $mapped['meta_form_id'] = $meta_context['form_id'] ?? '';
        $mapped['meta_ad_id'] = $meta_context['ad_id'] ?? '';

        return $this->_create_or_update_lead($mapped);
    }

    /**
     * Core function to create lead with duplicate checking and auto assignment
     */
    private function _create_or_update_lead($data) {
        $name = $data['name'] ?? $data['full_name'] ?? $data['client_name'] ?? 'Facebook Lead';
        $phone = $data['phone'] ?? $data['phone_number'] ?? '';
        $email = $data['email'] ?? $data['contact_email'] ?? '';
        $country = $data['preferred_country'] ?? '';
        $ielts_status = $data['ielts_status'] ?? '';
        $ielts_score = $data['ielts_score'] ?? '';
        $qualification = $data['qualification'] ?? '';
        $preferred_intake = $data['preferred_intake'] ?? '';

        // Clean phone for duplicate check
        $clean_phone = preg_replace('/[^0-9]/', '', $phone);

        // 1. Duplicate Check
        $existing_lead_id = 0;
        $db = \Config\Database::connect();
        $clients_table = $db->prefixTable('clients');
        $users_table = $db->prefixTable('users');

        if ($clean_phone && strlen($clean_phone) >= 7) {
            $last_7 = substr($clean_phone, -7);
            $check_sql = "SELECT c.id FROM $clients_table c 
                          LEFT JOIN $users_table u ON u.client_id = c.id
                          WHERE c.deleted = 0 AND c.is_lead = 1 
                            AND (c.phone LIKE '%$last_7%' OR u.phone LIKE '%$last_7%') 
                          LIMIT 1";
            $row = $db->query($check_sql)->getRow();
            if ($row && $row->id) {
                $existing_lead_id = $row->id;
            }
        }

        if (!$existing_lead_id && $email) {
            $check_email_sql = "SELECT c.id FROM $clients_table c 
                                LEFT JOIN $users_table u ON u.client_id = c.id
                                WHERE c.deleted = 0 AND c.is_lead = 1 
                                  AND (u.email = " . $db->escape(trim($email)) . ") 
                                LIMIT 1";
            $row = $db->query($check_email_sql)->getRow();
            if ($row && $row->id) {
                $existing_lead_id = $row->id;
            }
        }

        // If duplicate found, add a note/activity and return
        if ($existing_lead_id) {
            $note_body = "Duplicate inquiry received from Facebook on " . format_to_datetime(get_current_utc_time()) . ".\n"
                       . "Inquiry Details:\n"
                       . "Name: $name\n"
                       . "Phone: $phone\n"
                       . "Email: $email\n"
                       . "Country: $country\n"
                       . "Qualification: $qualification\n"
                       . "IELTS: $ielts_status\n"
                       . "Intake: $preferred_intake";

            $note_data = array(
                "title" => "Facebook Re-Inquiry (Duplicate Detected)",
                "description" => $note_body,
                "created_by" => 0,
                "created_at" => get_current_utc_time(),
                "client_id" => $existing_lead_id,
                "is_public" => 0
            );
            $this->Notes_model->ci_save($note_data);

            return array(
                "success" => true,
                "status" => "duplicate_updated",
                "message" => "Duplicate lead found. Logged new inquiry note.",
                "lead_id" => $existing_lead_id
            );
        }

        // 2. Identify Source ID (Facebook)
        $source_id = 0;
        $fb_source = $this->Lead_source_model->get_one_where(array("title" => "Facebook", "deleted" => 0));
        if ($fb_source && $fb_source->id) {
            $source_id = $fb_source->id;
        }

        // 3. Identify Default Status (Cold Lead - Tier 1)
        $status_id = 0;
        $cold_status = $this->Lead_status_model->get_one_where(array("title" => "Cold Lead", "deleted" => 0));
        if ($cold_status && $cold_status->id) {
            $status_id = $cold_status->id;
        } else {
            $status_id = $this->Lead_status_model->get_first_status();
        }

        // 4. Assign Owner via Round-Robin among Tier 2 / Screening Staff
        $owner_id = $this->_get_round_robin_owner();

        // 5. Create New Lead
        $lead_data = array(
            "company_name" => $name,
            "type" => "person",
            "phone" => $phone,
            "is_lead" => 1,
            "lead_status_id" => $status_id,
            "lead_source_id" => $source_id,
            "owner_id" => $owner_id,
            "created_date" => get_current_utc_time(),
            "data_logger_id" => 0, // 0 denotes System / Automated Webhook
            "preferred_country" => $country,
            "ielts_status" => $ielts_status,
            "ielts_score" => $ielts_score,
            "qualification" => $qualification,
            "preferred_intake" => $preferred_intake
        );

        $new_lead_id = $this->Clients_model->ci_save($lead_data);

        if ($new_lead_id) {
            // Create Primary Contact
            $name_parts = explode(" ", $name, 2);
            $first_name = $name_parts[0];
            $last_name = $name_parts[1] ?? '';

            $contact_data = array(
                "first_name" => $first_name,
                "last_name" => $last_name,
                "client_id" => $new_lead_id,
                "user_type" => "lead",
                "email" => trim($email),
                "phone" => $phone,
                "created_at" => get_current_utc_time(),
                "is_primary_contact" => 1
            );
            $contact_id = $this->Users_model->ci_save($contact_data);

            // Add initial note with any extra payload details
            if (!empty($data['extra_fields']) && is_array($data['extra_fields'])) {
                $extra_text = "Additional Facebook Form Fields:\n";
                foreach ($data['extra_fields'] as $k => $v) {
                    $extra_text .= "$k: $v\n";
                }
                $meta_note_data = array(
                    "title" => "Facebook Lead Ad Metadata",
                    "description" => $extra_text,
                    "created_by" => $owner_id,
                    "created_at" => get_current_utc_time(),
                    "client_id" => $new_lead_id,
                    "is_public" => 0
                );
                $this->Notes_model->ci_save($meta_note_data);
            }

            log_notification("lead_created", array("lead_id" => $new_lead_id), $owner_id ? $owner_id : 0);

            return array(
                "success" => true,
                "status" => "created",
                "message" => "Lead created successfully from Facebook Lead Ads",
                "lead_id" => $new_lead_id,
                "contact_id" => $contact_id
            );
        }

        return array("success" => false, "message" => "Failed to save lead record.");
    }

    /**
     * Get next owner via Round-Robin among eligible screening staff
     */
    private function _get_round_robin_owner() {
        // Check if a specific team is designated for Tier 2 intake
        $team_id = (int)get_setting('facebook_lead_assigned_team_id');
        $db = \Config\Database::connect();
        $users_table = $db->prefixTable('users');
        $team_table = $db->prefixTable('team');

        $eligible_user_ids = array();

        if ($team_id) {
            $team = $db->query("SELECT members FROM $team_table WHERE id=$team_id AND deleted=0")->getRow();
            if ($team && !empty($team->members)) {
                $eligible_user_ids = array_filter(explode(',', $team->members));
            }
        }

        // If no team, select all active staff
        if (empty($eligible_user_ids)) {
            $staff = $db->query("SELECT id FROM $users_table WHERE deleted=0 AND status='active' AND user_type='staff' ORDER BY id ASC LIMIT 20")->getResult();
            foreach ($staff as $s) {
                $eligible_user_ids[] = $s->id;
            }
        }

        if (empty($eligible_user_ids)) {
            return 0;
        }

        // Round Robin index pointer from settings
        $last_index = (int)get_setting('facebook_lead_round_robin_last_index');
        $eligible_count = count($eligible_user_ids);
        $next_index = ($last_index + 1) % $eligible_count;

        $assigned_owner_id = (int)$eligible_user_ids[$next_index];
        $this->Settings_model->save_setting('facebook_lead_round_robin_last_index', $next_index);

        return $assigned_owner_id;
    }
}
