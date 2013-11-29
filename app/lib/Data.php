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

    public function publicAccount($ACCOUNT_DATA, $PROFILE_DATA = array()) {
        $account_data = $PROFILE_DATA;

        $account_data['id_account'] = $ACCOUNT_DATA['id_account'];
        $account_data['identifier'] = $ACCOUNT_DATA['identifier'];
        $account_data['fullname'] = $ACCOUNT_DATA['fullname'];
        $account_data['username'] = $ACCOUNT_DATA['username'];
        $account_data['picture'] = $ACCOUNT_DATA['picture'];

        return $account_data;
    }

    public function profileFromTwitterUser($TWITTER_USER_OBJECT, $ACCOUNT_IDENTIFIER = NULL, $FOLLOWERS_IDS = array(), $FRIENDS_IDS = array()) {
        $profile_data = array();

        $profile_data['identifier'] = $TWITTER_USER_OBJECT['id_str'];
        $profile_data['fullname'] = isset($TWITTER_USER_OBJECT['name']) ? $TWITTER_USER_OBJECT['name'] : '';
        $profile_data['username'] = $TWITTER_USER_OBJECT['screen_name'];
        $profile_data['picture'] = str_ireplace('_normal', '_bigger', $TWITTER_USER_OBJECT['profile_image_url_https']);
        $profile_data['description'] = isset($TWITTER_USER_OBJECT['description']) ? $TWITTER_USER_OBJECT['description'] : '';
        $profile_data['back'] = isset($TWITTER_USER_OBJECT['profile_banner_url']) ? $TWITTER_USER_OBJECT['profile_banner_url'] . '/mobile_retina' : '';
        $profile_data['count_following'] = $TWITTER_USER_OBJECT['friends_count'];
        $profile_data['count_followers'] = $TWITTER_USER_OBJECT['followers_count'];
        $profile_data['follower'] = (in_array($profile_data['identifier'], $FOLLOWERS_IDS) ? true : false);
        $profile_data['following'] = (in_array($profile_data['identifier'], $FRIENDS_IDS) ? true : false);
        $profile_data['own'] = ($profile_data['identifier'] == $ACCOUNT_IDENTIFIER ? true : false);
        
        return $profile_data;
    }

    public function threadMessagesFromMessages($MESSAGES_DATA, $ACCOUNT, $IDENTIFIER) {
        $messages_data = array();

        foreach ($MESSAGES_DATA as $message_data) {
            $sender_id = $message_data['sender_id'];
            $recipient_id = $message_data['recipient_id'];
            $profile_id = ($sender_id == $ACCOUNT['identifier']) ? $recipient_id : $sender_id;

            if ($IDENTIFIER == $profile_id) {
                $message_data['timeago'] = $this->getTimeAgo(strtotime($message_data['created']));
                $message_data['sent'] = ($sender_id == $ACCOUNT['identifier']) ? true : false;
                $messages_data[] = $message_data;
            }
        }

        usort($messages_data, 'Data::sortMessages');
        return $messages_data;
    }

    public function threadsFromMessages($MESSAGES_DATA, $ACCOUNT) {
        $messages_threads_data = array();
        $thread_identifiers = array();

        usort($MESSAGES_DATA, 'Data::sortMessagesReverse');
        foreach ($MESSAGES_DATA as $message_data) {
            $id_sender = $message_data['sender_id'];
            $id_recipient = $message_data['recipient_id'];
            $identifier = ($id_sender == $ACCOUNT['identifier']) ? $id_recipient : $id_sender;

            if (!in_array($identifier, $thread_identifiers)) {
                $thread_identifiers[] = $identifier;

                $message_thread_data = array();
                $message_thread_data['date'] = $message_data['created'];
                $message_thread_data['timeago'] = $this->getTimeAgo(strtotime($message_data['created']));
                $message_thread_data['identifier'] = $identifier;
                $message_thread_data['preview'] = $message_data['text'];
                $message_thread_data['opened'] = false;
                $message_thread_data['folder'] = 0;

                $messages_threads_data[] = $message_thread_data;
            }
        }

        return $messages_threads_data;
    }

    public function threadsMerge($MESSAGES_THREADS_DATA, $THREADS_DATA) {
        $threads_data = array();
        $match_keys = array();
        
        foreach ($THREADS_DATA as $id => $thread_data) {
            $match_keys[$thread_data['identifier']] = $id;
        }

        foreach ($MESSAGES_THREADS_DATA as $id => $message_thread_data) {
            $thread_data = $message_thread_data;

            if (isset($match_keys[$message_thread_data['identifier']])) {
                $match_key = $match_keys[$message_thread_data['identifier']];
                $thread_data['opened'] = ($THREADS_DATA[$match_key]['opened']) ? true : false;
                $thread_data['folder'] = $THREADS_DATA[$match_key]['folder'];
            }

            $threads_data[] = $thread_data;
        }

        usort($threads_data, 'Data::sortThreads');
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
    
    private function sortMessages($thread_a, $thread_b) {
        if ($thread_a['created'] == $thread_b['created']) {
            return 0;
        }
        return ($thread_a['created'] < $thread_b['created']) ? -1 : 1;
    }
    
    private function sortMessagesReverse($thread_a, $thread_b) {
        if ($thread_a['created'] == $thread_b['created']) {
            return 0;
        }
        return ($thread_a['created'] > $thread_b['created']) ? -1 : 1;
    }

    private function sortThreads($thread_a, $thread_b) {
        if ($thread_a['date'] == $thread_b['date']) {
            return 0;
        }
        return ($thread_a['date'] > $thread_b['date']) ? -1 : 1;
    }

}