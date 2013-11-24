<?php

class Parse {

    const API_URL = 'https://api.parse.com/1/';
    const APP_ID = PARSE_APP_ID;
    const MASTER_KEY = PARSE_MASTER_KEY;
    const REST_KEY = PARSE_REST_KEY;

    public function apiGet($PATH, $PARAMS = array()) {
        return $this->httpRequest(self::API_URL . $PATH, 'GET', $PARAMS);
    }

    public function apiPost($PATH, $PARAMS = array()) {
        return $this->httpRequest(self::API_URL . $PATH, 'POST', $PARAMS);
    }

    private function httpRequest($ENDPOINT, $METHOD = "GET", $PARAMS = array()) {
        $response = array();

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Parse-Application-Id:' . self::APP_ID;
        $headers[] = 'X-Parse-Master-Key: ' . self::MASTER_KEY;
        $headers[] = 'X-Parse-REST-API-Key: ' . self::REST_KEY;

        if ($METHOD == 'POST') {
            $payload = str_replace('[]', '{}', json_encode($PARAMS));
            if ($payload === '') {
                $payload = '{}';
            }
            $endpoint = $ENDPOINT;
        } else if ($METHOD == 'GET') {
            $params = http_build_query($PARAMS);
            $endpoint = $ENDPOINT . '?' . $params;
        }

        $ch = curl_init($endpoint);
        if ($METHOD == 'POST')
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $METHOD);
        //curl_setopt($ch, CURLOPT_URL, $ENDPOINT);

        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $api_data = json_decode($body, true);

        $response['success'] = (isset($api_data['error'])) ? false : true;
        $response['message'] = "Here's the http response data";
        $response['status'] = $status;
        $response['api_data'] = $api_data;

        return $response;
    }

}