<?php

class Data {

    public function getGetParams($REQUIRED, $OPTIONAL = array()) {
        $response = array();
        $get = array();

        foreach ($REQUIRED as $key) {
            if (empty($response)) {
                if (isset($_GET[$key])) {
                    $get[$key] = $_GET[$key];
                } else {
                    $response['success'] = false;
                    $response['message'] = "The required GET parameter '$key' was not found";
                    $response['err'] = 0;
                }
            }
        }

        foreach ($OPTIONAL as $key) {
            if (isset($_GET[$key])) {
                $get[$key] = $_GET[$key];
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "All required GET parameters are present";
            $response['get'] = $get;
        }

        return $response;
    }

    public function getPostParams($REQUIRED, $OPTIONAL = array()) {
        $response = array();
        $post = array();

        foreach ($REQUIRED as $key) {
            if (empty($response)) {
                if (isset($_POST[$key])) {
                    $post[$key] = $_POST[$key];
                } else {
                    $response['success'] = false;
                    $response['message'] = "The required POST parameter '$key' was not found";
                    $response['err'] = 0;
                }
            }
        }

        foreach ($OPTIONAL as $key) {
            if (isset($_POST[$key])) {
                $post[$key] = $_POST[$key];
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "All required POST parameters are present";
            $response['post'] = $post;
        }

        return $response;
    }

    public function getPutParams($REQUIRED, $OPTIONAL = array()) {
        $response = array();
        $put = array();

        parse_str(file_get_contents("php://input"), $_PUT);

        foreach ($REQUIRED as $key) {
            if (empty($response)) {
                if (isset($_PUT[$key])) {
                    $put[$key] = $_PUT[$key];
                } else {
                    $response['success'] = false;
                    $response['message'] = "The required PUT parameter '$key' was not found";
                    $response['err'] = 0;
                }
            }
        }

        foreach ($OPTIONAL as $key) {
            if (isset($_PUT[$key])) {
                $put[$key] = $_PUT[$key];
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "All required PUT parameters are present";
            $response['put'] = $put;
        }

        return $response;
    }

    public function publicAccount($ACCOUNT_DATA, $ACTIVE_ACCOUNT = array()) {
        $account_data = array();

        $account_data['id_account'] = $ACCOUNT_DATA['id_account'];
        $account_data['identifier'] = $ACCOUNT_DATA['identifier'];
        $account_data['network'] = $ACCOUNT_DATA['network'];
        $account_data['fullname'] = $ACCOUNT_DATA['fullname'];
        $account_data['username'] = $ACCOUNT_DATA['username'];
        $account_data['picture'] = $ACCOUNT_DATA['picture'];
        $account_data['self'] = ((isset($ACTIVE_ACCOUNT['id_account']) && ($ACTIVE_ACCOUNT['id_account'] = $ACCOUNT_DATA['id_account'])) ? true : false);

        return $account_data;
    }
    
    public function profileFromTwitterUser($TWITTER_USER_OBJECT){
        $profile_data = array();
        
        $profile_data['identifier'] = $TWITTER_USER_OBJECT['id'];
        $profile_data['network'] = 'twitter';
        $profile_data['fullname'] = isset($TWITTER_USER_OBJECT['name'])? $TWITTER_USER_OBJECT['name'] : '';
        $profile_data['username'] = $TWITTER_USER_OBJECT['screen_name'];
        $profile_data['picture'] = str_ireplace('_normal', '_bigger', $TWITTER_USER_OBJECT['profile_image_url_https']);
        $profile_data['status'] = isset($TWITTER_USER_OBJECT['status']['text'])? $TWITTER_USER_OBJECT['status']['text'] : '';
        $profile_data['description'] = isset($TWITTER_USER_OBJECT['description'])? $TWITTER_USER_OBJECT['description'] : '';
        $profile_data['back'] = isset($TWITTER_USER_OBJECT['profile_banner_url'])? $TWITTER_USER_OBJECT['profile_banner_url'].'/mobile_retina' : '';
        $profile_data['following_count'] = $TWITTER_USER_OBJECT['friends_count'];
        $profile_data['followers_count'] = $TWITTER_USER_OBJECT['followers_count'];
        
        return $profile_data;
    }
    
}