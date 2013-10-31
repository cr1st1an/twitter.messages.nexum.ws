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

    public function profileFromTwitterUser($TWITTER_USER_OBJECT) {
        $profile_data = array();

        $profile_data['identifier'] = $TWITTER_USER_OBJECT['id'];
        $profile_data['network'] = 'twitter';
        $profile_data['fullname'] = isset($TWITTER_USER_OBJECT['name']) ? $TWITTER_USER_OBJECT['name'] : '';
        $profile_data['username'] = $TWITTER_USER_OBJECT['screen_name'];
        $profile_data['picture'] = str_ireplace('_normal', '_bigger', $TWITTER_USER_OBJECT['profile_image_url_https']);
        $profile_data['status'] = isset($TWITTER_USER_OBJECT['status']['text']) ? $TWITTER_USER_OBJECT['status']['text'] : '';
        $profile_data['description'] = isset($TWITTER_USER_OBJECT['description']) ? $TWITTER_USER_OBJECT['description'] : '';
        $profile_data['back'] = isset($TWITTER_USER_OBJECT['profile_banner_url']) ? $TWITTER_USER_OBJECT['profile_banner_url'] . '/mobile_retina' : '';
        $profile_data['count_following'] = $TWITTER_USER_OBJECT['friends_count'];
        $profile_data['count_followers'] = $TWITTER_USER_OBJECT['followers_count'];

        return $profile_data;
    }

    public function threadsFromTwitterDDMM($TWITTER_DDMM_OBJECT, $ACCOUNT_PROFILE_ID) {
        $twitter_threads_data = array();
        $thread_identifiers = array();

        krsort($TWITTER_DDMM_OBJECT);
        foreach ($TWITTER_DDMM_OBJECT as $twitter_dm_object) {
            $id_sender = $twitter_dm_object['sender']['id'];
            $id_recipient = $twitter_dm_object['recipient']['id'];
            $identifier = ($id_sender == $ACCOUNT_PROFILE_ID) ? $id_recipient : $id_sender;

            if (!in_array($identifier, $thread_identifiers)) {
                $thread_identifiers[] = $identifier;

                $twitter_tread_data = array();
                $key_profile = ($id_sender == $ACCOUNT_PROFILE_ID) ? 'recipient' : 'sender';
                $profile_data = $this->profileFromTwitterUser($twitter_dm_object[$key_profile]);

                $twitter_tread_data['date'] = $twitter_dm_object['created_at'];
                $twitter_tread_data['timeago'] = $this->getTimeAgo(strtotime($twitter_dm_object['created_at']));
                $twitter_tread_data['identifier'] = $identifier;
                $twitter_tread_data['network'] = 'twitter';
                $twitter_tread_data['picture'] = $profile_data['picture'];
                $twitter_tread_data['title'] = $profile_data['fullname'];
                $twitter_tread_data['subtitle'] = '@' . $profile_data['username'];
                $twitter_tread_data['preview'] = $twitter_dm_object['text'];
                $twitter_tread_data['opened'] = false;
                $twitter_tread_data['folder'] = 0;

                $twitter_threads_data[] = $twitter_tread_data;
            }
        }

        return $twitter_threads_data;
    }

    public function messagesFromTwitterDDMM($TWITTER_DDMM_OBJECT, $ACCOUNT_PROFILE_ID, $PROFILE_ID) {
        $messages_data = array();

        ksort($TWITTER_DDMM_OBJECT);
        foreach ($TWITTER_DDMM_OBJECT as $twitter_dm_object) {
            $sender_id = $twitter_dm_object['sender']['id'];
            $recipient_id = $twitter_dm_object['recipient']['id'];
            $profile_id = ($sender_id == $ACCOUNT_PROFILE_ID) ? $recipient_id : $sender_id;

            if ($PROFILE_ID == $profile_id) {
                $message_data = array();

                $message_data['identifier'] = $twitter_dm_object['id'];
                $message_data['network'] = 'twitter';
                $message_data['sender_id'] = $sender_id;
                $message_data['recipient_id'] = $recipient_id;
                $message_data['created'] = $twitter_dm_object['created_at'];
                $message_data['timeago'] = $this->getTimeAgo(strtotime($twitter_dm_object['created_at']));
                $message_data['text'] = $twitter_dm_object['text'];
                $message_data['sent'] = ($sender_id == $ACCOUNT_PROFILE_ID) ? true : false;

                $messages_data[] = $message_data;
            }
        }

        return $messages_data;
    }

    public function threadsTwitterExtend($TWITTER_THREADS_DATA, $THREADS_DATA) {
        $threads_data = array();
        $match_keys = array();

        foreach ($THREADS_DATA as $id => $thread_data) {
            $match_keys[$thread_data['network'] . '_' . $thread_data['identifier']] = $id;
        }

        foreach ($TWITTER_THREADS_DATA as $id => $twitter_thread_data) {
            $match_key = $match_keys[$twitter_thread_data['network'] . '_' . $twitter_thread_data['identifier']];
            $thread_data = $twitter_thread_data;

            if (isset($THREADS_DATA[$match_key])) {
                $thread_data['opened'] = ($THREADS_DATA[$match_key]['opened'])? true : false;
                $thread_data['folder'] = $THREADS_DATA[$match_key]['folder'];
            }

            $threads_data[] = $thread_data;
        }

        return $threads_data;
    }

    public function getTimeAgo($TIME) {
        $periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
        $lengths = array("60", "60", "24", "7", "4.35", "12", "10");

        $now = time();

        $difference = $now - $TIME;

        for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths) - 1; $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if ($difference != 1) {
            $periods[$j].= "s";
        }

        if ('0 seconds' === "$difference $periods[$j]")
            return "now";

        return "$difference $periods[$j]";
    }

}