<?php

class Twitter {

    const API_ENDPOINT_URL = 'https://api.twitter.com/1.1/';
    const API_OAUTH_URL = 'https://api.twitter.com/oauth/';
    const API_CONSUMER_KEY = TWITTER_CONSUMER_KEY;
    const API_CONSUMER_SECRET = TWITTER_CONSUMER_SECRET;
    const API_CALLBACK_URL = TWITTER_CALLBACK_URL;

    protected $_config = array(
    );

    public function oAuthRequestToken() {
        return $this->httpRequest(self::API_OAUTH_URL . 'request_token', 'POST', array(), array('oauth_callback' => self::API_CALLBACK_URL));
    }

    public function oAuthAuthorize($OAUTH_TOKEN) {
        $response = array();
        $response['success'] = true;
        $response['redirect_url'] = self::API_OAUTH_URL . 'authorize?oauth_token=' . $OAUTH_TOKEN;
        return $response;
    }

    public function oAuthAccessToken($OAUTH_VERIFIER, $OAUTH_TOKEN) {
        return $this->httpRequest(self::API_OAUTH_URL . 'access_token', 'POST', array('oauth_verifier' => $OAUTH_VERIFIER), array('oauth_token' => $OAUTH_TOKEN, 'oauth_token_secret' => $OAUTH_TOKEN));
    }

    public function apiGet($PATH, $PARAMS = array(), $CREDENTIALS = array()) {
        return $this->httpRequest(self::API_ENDPOINT_URL . $PATH . '.json', 'GET', $PARAMS, $CREDENTIALS);
    }

    public function apiPost($PATH, $PARAMS = array(), $CREDENTIALS = array()) {
        return $this->httpRequest(self::API_ENDPOINT_URL . $PATH . '.json', 'POST', $PARAMS, $CREDENTIALS);
    }

    private function httpRequest($ENDPOINT, $METHOD = 'GET', $PARAMS = array(), $CREDENTIALS = array()) {
        $response = array();

        $auth_params = array();
        $auth_params['oauth_consumer_key'] = self::API_CONSUMER_KEY;
        $auth_params['oauth_nonce'] = substr(md5(microtime(true)), 0, 32);
        $auth_params['oauth_signature_method'] = 'HMAC-SHA1';
        $auth_params['oauth_timestamp'] = time();
        if (isset($CREDENTIALS['oauth_callback']))
            $auth_params['oauth_callback'] = $CREDENTIALS['oauth_callback'];
        if (isset($CREDENTIALS['oauth_token']))
            $auth_params['oauth_token'] = $CREDENTIALS['oauth_token'];

        $auth_params['oauth_version'] = '1.0';

        $sign_params = array_merge($auth_params, $PARAMS);
        
        $i = 0;
        ksort($sign_params);
        $sign_string = '';
        foreach ($sign_params as $key => $value) {
            $sign_string .= rawurlencode($key);
            $sign_string .= '=';
            $sign_string .= rawurlencode($value);
            if (++$i != count($sign_params))
                $sign_string .= '&';
        }
        $sign_string = strtoupper($METHOD) . '&' . rawurlencode($ENDPOINT) . '&' . rawurlencode($sign_string);
        $sign_key = rawurlencode(self::API_CONSUMER_SECRET) . '&' . rawurlencode(isset($CREDENTIALS['oauth_token_secret']) ? $CREDENTIALS['oauth_token_secret'] : '');
        $auth_params['oauth_signature'] = base64_encode(hash_hmac('SHA1', $sign_string, $sign_key, true));

        $i = 0;
        ksort($auth_params);
        $auth_string = 'OAuth ';
        foreach ($auth_params as $key => $value) {
            $auth_string .= rawurlencode($key);
            $auth_string .= '=';
            $auth_string .= '"';
            $auth_string .= rawurlencode($value);
            $auth_string .= '"';
            if (++$i != count($auth_params))
                $auth_string .= ', ';
        }
        
        $params = http_build_query($PARAMS);
        $headers = array();
        if ('POST' == $METHOD) {
            $headers[] = 'Content-Length: ' . strlen($params);
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        }
        $headers[] = 'Authorization: ' . $auth_string;

        $endpoint = $ENDPOINT . (('GET' == $METHOD && !empty($params)) ? '?' . $params : '' );
        $ch = curl_init($endpoint);
        if ('POST' == $METHOD) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $body = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $api_data = json_decode($body, true);
        if (empty($api_data)) {
            $api_data = array();
            $body_params = explode('&', $body);
            foreach ($body_params as $body_param) {
                $key_value = explode('=', $body_param);
                $api_data[$key_value[0]] = $key_value[1];
            }
        }

        $response['success'] = (200 == $status) ? true : false;
        $response['message'] = "Here's the http response data";
        $response['status'] = $status;
        $response['api_data'] = $api_data;

        return $response;
    }

}