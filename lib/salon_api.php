<?php
class Salon_api
{
    private $businessAlias = '';
    private $clientKey;
    private $clientSecret;
    private $apiURL = '';
    private $ch;
    private $last_result  = null;
    private $last_status  = null;
    private $last_error   = null;
    private $log_filename = null;
    private $debug        = false;
    
    public function __construct($clientKey, $clientSecret, $businessAlias = '', $apiURL = '' )
    {
        $this->clientKey    = $clientKey;
        $this->clientSecret = $clientSecret;
        $this->log_filename = __DIR__ . '/log.txt';
        $this->apiURL       = $apiURL;
        
        if (!empty($businessAlias)) {
            $this->businessAlias = $businessAlias;
        }
        
        if ($this->debug) {
            $this->clearLog();
        }

        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->ch, CURLOPT_HEADER, false);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/.cacert');
        
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'ClinicSoftware API PHP-SDK/1.6');
    }

    public function __destruct()
    {
        if (is_resource($this->ch)) {
            curl_close($this->ch);
        }
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;

        if ($this->debug) {
            $this->clearLog();
        }
    }

    public function setURL($url)
    {
        $this->apiURL = $url;
    }

    public function getLastResult()
    {
        return $this->last_result;
    }
    
    public function getLastStatus()
    {
        return $this->last_status;
    }

    public function getLastError()
    {
        return $this->last_error;
    }

    public function call($params = array())
    {
        $this->last_result = null;
        $this->last_status = null;
        $this->last_error  = null;

        $params['business_client_alias'] = $this->businessAlias;
        $params['api_client_key']  = $this->clientKey;
        $params['api_client_time'] = time();
        $params['api_client_salt'] = uniqid(mt_rand(), true);
        $params['api_client_hash'] = hash('sha256', $params['api_client_salt'] . $params['api_client_time'] . $this->clientSecret);

        curl_setopt($this->ch, CURLOPT_URL, $this->apiURL);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($this->ch, CURLOPT_VERBOSE, $this->debug);

        $start = microtime(true);

        if ($this->debug) {
            $this->writeLog("Call to {$this->apiURL}: " . json_encode($params));
            $curl_buffer = fopen('php://temp', 'rw');
            curl_setopt($this->ch, CURLOPT_STDERR, $curl_buffer);
        }

        $response_body = curl_exec($this->ch);
        $info = curl_getinfo($this->ch);
        $time = microtime(true) - $start;

        if ($this->debug) {
            /** @noinspection PhpUndefinedVariableInspection */
            rewind($curl_buffer);
            $this->writeLog(stream_get_contents($curl_buffer));
            fclose($curl_buffer);
            $this->writeLog('Completed in ' . number_format($time * 1000, 2) . ' ms');
            $this->writeLog('Got response: ' . $response_body);
        }

        if (curl_error($this->ch)) {
            $this->last_error = "API call to {$this->apiURL} failed: " . curl_error($this->ch);
            return null;
        }

        if (floor($info['http_code'] / 100) >= 4) {
            $this->last_error = "API call to {$this->apiURL} failed: " . $response_body;
            return null;
        }

        $result = json_decode($response_body, true);
        if (empty($result)) {
            $this->last_error = "Failed decoding JSON response: {$response_body}";
            return null;
        }

        $this->last_result = $result;
        $this->last_status = $result['status'];

        if ($result['status'] == 'error') {
            $this->last_error = "API Error: {$result['message']}";
            return null;
        }

        return empty($result['data'])? null : $result['data'];
    }

    public function add_document(int $client_id, string $base64, string $document_name, string $mime_type, bool $automatically_rename = true) {

        $params = [
            'action'               => 'add_document',
            'client_id'            => $client_id,
            'document_name'        => $document_name,
            'document_b64'         => $base64,
            'mime_type'            => $mime_type,
            'automatically_rename' => $automatically_rename ? 1 : 0,
        ];

        // Return the results of the call with the provided parameters
        return $this->call($params);
    }

    /**
     * Get the relationships of a client
     * @param int $client_id The id of the target client.
     */
    public function getRelationship(int $client_id) {

        $params = [
            'action'    => 'get_relationship',
            'client_id' => $client_id,
        ];

        // Return the results of the call with the provided parameters
        return $this->call($params);
    }

    /**
     * Get a single or multiple services based on their IDs, a last-modified date and/or limit/offset
     * @param null | int | array $id The id of the service(s) you are looking for
     * @param string | null | DateTime $last_modified = null A last modified date for filtering out obsolete data
     * @param int $limit = 10 A limit of objects to return
     * @param int $offset = 0 An offset for the object array return
     */
    public function get_services($id = null, $last_modified = null, int $limit = 10, int $offset = 0) {

        if ( gettype($last_modified) == "string" ) {
            // Parse the last modified to a UNIX timestamp in seconds
            $last_modified = strtotime($last_modified);
            // Check if the conversion failed
            if ( $last_modified === FALSE )
                throw new Exception("Invalid date provided as string");

            // Correctly format last_modified
            $last_modified = date("c", $last_modified);
        } else if ( is_a($last_modified, "DateTime") ) {
            $last_modified = $last_modified->format("c");
        }

        // Check if the id is in array format
        if ( is_array($id) ) {
            foreach( $id as $i ) {
                if ( is_object($i) || is_array($i) ) {
                    throw new Exception("Invalid id provided, please only provide an array of strings or numbers");
                }
            }
        }

        $params = [
            'action'        => 'get_services',
            'id'            => $id,
            'last_modified' => $last_modified,
            'limit'         => $limit,
            'offset'        => $offset,
        ];

        // Return the results of the call with the provided parameters
        return $this->call($params);
    }

    public function getClientNofSessionCourses($client_id, $date_from = null, $date_to = null, $treatment = null)
    {
        $params = array();
        $params['action']    = 'client_get_nof_session_courses';
        $params['client_id'] = $client_id;
        $params['date_from'] = $date_from;
        $params['date_to']   = $date_to;
        $params['treatment'] = $treatment;
        return $this->call($params);
    }

    public function getClientSessionCoursesPag($client_id, $date_from = null, $date_to = null, $treatment = null, $offset = 0, $row_count = 0)
    {
        $params = array();
        $params['action']    = 'client_get_session_courses_pag';
        $params['client_id'] = $client_id;
        $params['date_from'] = $date_from;
        $params['date_to']   = $date_to;
        $params['treatment'] = $treatment;
        $params['offset']    = $offset;
        $params['row_count'] = $row_count;
        return $this->call($params);
    }

    public function getClientNofMinutesCourses($client_id, $date_from = null, $date_to = null, $treatment = null)
    {
        $params = array();
        $params['action']    = 'client_get_nof_minutes_courses';
        $params['client_id'] = $client_id;
        $params['date_from'] = $date_from;
        $params['date_to']   = $date_to;
        $params['treatment'] = $treatment;
        return $this->call($params);
    }

    public function getClientMinutesCoursesPag($client_id, $date_from = null, $date_to = null, $treatment = null, $offset = 0, $row_count = 0)
    {
        $params = array();
        $params['action']    = 'client_get_minutes_courses_pag';
        $params['client_id'] = $client_id;
        $params['date_from'] = $date_from;
        $params['date_to']   = $date_to;
        $params['treatment'] = $treatment;
        $params['offset']    = $offset;
        $params['row_count'] = $row_count;
        return $this->call($params);
    }

    public function getClientConsentFormPDF($client_consent_form_id)
    {
        $params = array();
        $params['action'] = 'get_signed_consent_form_pdf';
        $params['client_consent_form_id'] = $client_consent_form_id;
        return $this->call($params);
    }

    public function getClientSignedConsentFormsList($client_id)
    {
        $params = array();
        $params['action'] = 'get_client_signed_consent_forms_list';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientNofUnreadMessagesGlobal($client_id, $last_message_id = 0)
    {
        $params = array();
        $params['action'] = 'get_client_nof_unread_messages_global';
        $params['client_id'] = $client_id;
        $params['last_message_id'] = $last_message_id;
        return $this->call($params);
    }

    public function getClientNofUnreadMessagesBySalon($client_id, $salon_id, $last_message_id = 0)
    {
        $params = array();
        $params['action'] = 'get_client_nof_unread_messages_by_salon';
        $params['client_id'] = $client_id;
        $params['salon_id'] = $salon_id;
        $params['last_message_id'] = $last_message_id;
        return $this->call($params);
    }

    public function getClientMessagesBySalon($client_id, $salon_id, $date_start = null, $date_end = null, $last_message_id = 0, $mark_messages_as_read = 0)
    {
        $params = array();
        $params['action'] = 'get_client_messages_by_salon';
        $params['salon_id'] = $salon_id;
        $params['client_id'] = $client_id;
        $params['date_start'] = $date_start;
        $params['date_end'] = $date_end;
        $params['last_message_id'] = $last_message_id;
        $params['mark_messages_as_read'] = $mark_messages_as_read;
        return $this->call($params);
    }

    public function addClientMessage($client_id, $salon_id, $message)
    {
        $params = array();
        $params['action'] = 'add_client_message';
        $params['salon_id'] = $salon_id;
        $params['client_id'] = $client_id;
        $params['message'] = $message;
        return $this->call($params);
    }

    public function getSalons()
    {
        $params = array();
        $params['action'] = 'get_salons';
        return $this->call($params);
    }
    
    public function getBarcodeImage($barcode)
    {
        $params = array();
        $params['action'] = 'get_barcode_image';
        $params['barcode'] = $barcode;
        return $this->call($params);
    }

    public function getResources()
    {
        $params = array();
        $params['action'] = 'get_api_client_resources';
        return $this->call($params);
    }

    public function getClients(string $last_modified, int $limit = 10, int $offset = 0) {

        $params = [];
        $params["action"]        = "get_clients";
        $params["last_modified"] = date("c", strtotime($last_modified));
        $params["limit"]         = $limit;
        $params["offset"]        = $offset;

        return $this->call($params);
    }

    public function getClientByID($client_id, $is_online_account = 1)
    {
        $params = array();
        $params['action'] = 'get_client_by_id';
        $params['client_id'] = $client_id;
        $params['is_online_account'] = $is_online_account;
        return $this->call($params);
    }

    public function getClientByEmail($client_email, $is_online_account = 1)
    {
        $params = array();
        $params['action'] = 'get_client_by_email';
        $params['client_email'] = $client_email;
        $params['is_online_account'] = $is_online_account;
        return $this->call($params);
    }

    public function getClientByEmailAndPassword($client_email, $client_password, $is_online_account = 1)
    {
        $params = array();
        $params['action'] = 'get_client_by_email_password';
        $params['client_email'] = $client_email;
        $params['client_password'] = $client_password;
        $params['is_online_account'] = $is_online_account;
        return $this->call($params);
    }

    public function getClientByPhone($client_phone, $is_online_account = 1)
    {
        $params = array();
        $params['action'] = 'get_client_by_phone';
        $params['client_phone'] = $client_phone;
        $params['is_online_account'] = $is_online_account;
        return $this->call($params);
    }

    public function getLeadByPhone($client_phone) {
        $params = array();
        $params['action'] = 'get_lead_by_phone';
        $params['lead_phone'] = $client_phone;
        return $this->call($params);
    }

    public function getClientByName($client_name, $is_online_account = 1)
    {
        $params = array();
        $params['action'] = 'get_client_by_name';
        $params['client_name'] = $client_name;
        $params['is_online_account'] = $is_online_account;
        return $this->call($params);
    }

    public function addClient($data)
    {
        $params = array();
        $params['action'] = 'add_client';
        $params['data'] = json_encode($data);
        return $this->call($params);
    }

    public function updateClient($client_id, $data)
    {
        $params = array();
        $params['action'] = 'update_client';
        $params['client_id'] = $client_id;
        $params['data'] = json_encode($data);
        return $this->call($params);
    }

    public function deleteClient($client_id)
    {
        $params = array();
        $params['action'] = 'delete_client';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function subscribeClientToNewsletter($client_id)
    {
        $params = array();
        $params['action'] = 'client_subscribe_newsletter';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function unsubscribeClientFromNewsletter($client_id)
    {
        $params = array();
        $params['action'] = 'client_unsubscribe_newsletter';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientReceipts($client_id)
    {
        $params = array();
        $params['action'] = 'get_client_receipts';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function emailReceiptToClient($bill_id)
    {
        $params = array();
        $params['action'] = 'client_email_receipt';
        $params['bill_id'] = $bill_id;
        return $this->call($params);
    }

    public function getClientAppointments($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_appointments';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientBalance($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_balance';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientBalanceHistory($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_balance_history';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getVoucherByBarcode($voucher_barcode)
    {
        $params = array();
        $params['action'] = 'get_voucher';
        $params['voucher_barcode'] = $voucher_barcode;
        return $this->call($params);
    }

    public function addVoucher($data)
    {
        $params = array();
        $params['action'] = 'add_voucher';
        $params['data'] = json_encode($data);
        return $this->call($params);
    }

    public function updateVoucherByBarcode($voucher_barcode, $data)
    {
        $params = array();
        $params['action'] = 'update_voucher';
        $params['voucher_barcode'] = $voucher_barcode;
        $params['data'] = json_encode($data);
        return $this->call($params);
    }

    public function deleteVoucherByBarcode($voucher_barcode)
    {
        $params = array();
        $params['action'] = 'delete_voucher';
        $params['voucher_barcode'] = $voucher_barcode;
        return $this->call($params);
    }

    public function assignVoucherBarcodeToClient($voucher_barcode, $client_id)
    {
        $params = array();
        $params['action'] = 'client_assign_voucher';
        $params['voucher_barcode'] = $voucher_barcode;
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientVouchers($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_vouchers';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientTrackSessionsHistory($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_track_sessions_history';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientTrackMinutesHistory($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_track_minutes_history';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientCoursesInstallmentsHistory($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_courses_installments_history';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientPowerPlatesHistory($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_power_plates_history';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientTanningHistory($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_tanning_history';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function getClientTreatmentRecords($client_id)
    {
        $params = array();
        $params['action'] = 'client_get_treatment_records';
        $params['client_id'] = $client_id;
        return $this->call($params);
    }

    public function reqOnlineBookingAuthToken($client_id, $expires = 120)
    {
        $params = array();
        $params['action'] = 'client_req_online_booking_auth';
        $params['client_id'] = $client_id;
        $params['expires'] = $expires;
        return $this->call($params);
    }
    
    public function addLead($data)
    {
        $params = array();
        $params['action'] = 'add_lead';
        $params['data'] = json_encode($data);
        return $this->call($params);
    }

    public function readLog()
    {
        if (!file_exists($this->log_filename)) return '';

        $fh = fopen($this->log_filename, 'r');
        if (false === $fh) return '';

        $contents = fread($fh, filesize($this->log_filename));
        fclose($fh);

        return $contents;
    }

    private function writeLog($message)
    {
        if (!file_exists($this->log_filename)) return;

        $fh = fopen($this->log_filename, 'a');
        if (false === $fh) return;

        fwrite($fh, "{$message}\n\n");
        fclose($fh);
    }

    private function clearLog()
    {
        if (!file_exists($this->log_filename)) return;

        $fh = fopen($this->log_filename, 'w');
        if (false === $fh) return;

        fclose($fh);
    }
}