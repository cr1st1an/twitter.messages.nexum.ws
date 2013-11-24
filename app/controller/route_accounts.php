<?php

class Route_Accounts {

    public static function postDeviceToken() {
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Parse.php';
        include_once Epi::getPath('lib') . 'Session.php';

        $Data = new Data();
        $Parse = new Parse();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $post = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_getPostParams = $Data->getPostParams(array('device_token'), array());

            if (!$r_getPostParams['success']) {
                $response = $r_getPostParams;
            } else {
                $post = $r_getPostParams['post'];
            }
        }

        if (empty($response)) {
            $channels = array();

            $params = array();
            $params['where'] = array(
                'deviceToken' => $post['device_token']
            );
            $params['limit'] = '1';

            $r_getApi_I = $Parse->apiGet('installations', $params);

            if (!$r_getApi_I['success']) {
                $response = $r_getApi_I;
            } else {
                if (!empty($r_getApi_I['results']))
                    $channels = $r_getApi_I['api_data'][0]['channels'];
            }

            $channels[] = 'TW-' . $session['account']['identifier'];

            $params = array();
            $params['deviceType'] = 'ios';
            $params['deviceToken'] = $post['device_token'];
            $params['channels'] = $channels;

            $r_postApi_I = $Parse->apiPost('installations', $params);

            if (!$r_postApi_I['success']) {
                $response = $r_postApi_I;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "The device token has been updated";
        }

        return $response;
    }

}