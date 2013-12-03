<?php

class Route_Workers {

    public static function post01() {
        include_once Epi::getPath('data') . 'mc_messages.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';
        include_once Epi::getPath('lib') . 'Twitter.php';

        $MC_Messages = new MC_Messages();
        $Session = new Session();
        $Twitter = new Twitter();

        $response = array();
        $session = $Session->get();
        $twitter_ddmm_data = array();
        $identifiers = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $params = array();
            $params['count'] = 200;
            do {
                $twitter_ddmm_in_data = array();
                $r_apiGet_DM = $Twitter->apiGet('direct_messages', $params, $session['account']['credentials']);
                if ($r_apiGet_DM['success']) {
                    $twitter_ddmm_in_data = $r_apiGet_DM['api_data'];
                    foreach ($twitter_ddmm_in_data as $twitter_dm_data) {
                        if (!empty($twitter_dm_data['id'])) {
                            $twitter_ddmm_data[$twitter_dm_data['id']] = $twitter_dm_data;
                            if (!isset($params['max_id']) || $params['max_id'] > $twitter_dm_data['id'])
                                $params['max_id'] = $twitter_dm_data['id'];
                        }
                    }
                }
            } while (APP_PAGE_ITEMS <= count($twitter_ddmm_in_data));

            $params = array();
            $params['count'] = 200;
            do {
                $twitter_ddmm_out_data = array();
                $r_apiGet_DMS = $Twitter->apiGet('direct_messages/sent', $params, $session['account']['credentials']);
                if ($r_apiGet_DMS['success']) {
                    $twitter_ddmm_out_data = $r_apiGet_DMS['api_data'];
                    foreach ($twitter_ddmm_out_data as $twitter_dm_data) {
                        if (!empty($twitter_dm_data['id'])) {
                            $twitter_ddmm_data[$twitter_dm_data['id']] = $twitter_dm_data;
                            if (!isset($params['max_id']) || $params['max_id'] > $twitter_dm_data['id'])
                                $params['max_id'] = $twitter_dm_data['id'];
                        }
                    }
                }
            } while (APP_PAGE_ITEMS <= count($twitter_ddmm_out_data));
        }

        if (empty($response)) {
            $r_selectAll = $MC_Messages->selectAll($session['account']['id_account']);
            if (!$r_selectAll['success']) {
                $response = $r_selectAll;
            } else {
                foreach ($r_selectAll['messages_data'] as $message_data) {
                    $identifiers[] = $message_data['identifier'];
                }
            }
        }

        if (empty($response)) {
            foreach ($twitter_ddmm_data as $twitter_dm_data) {
                if (!in_array($twitter_dm_data['id'], $identifiers)) {
                    $message_data = array(
                        'id_account' => $session['account']['id_account'],
                        'created' => date("Y-m-d H:i:s", strtotime($twitter_dm_data['created_at'])),
                        'identifier' => $twitter_dm_data['id'],
                        'sender_id' => $twitter_dm_data['sender_id'],
                        'recipient_id' => $twitter_dm_data['recipient_id'],
                        'text' => $twitter_dm_data['text']
                    );
                    $MC_Messages->insert($message_data);
                }
            }

            file_get_contents('http://dev.streaming.messages.nexum.ws/load.php?i=' . $session['account']['id_account']);

            $response['success'] = true;
            $response['message'] = "The messages have been synced";
        }
        return $response;
    }

    public static function post02() {
        include_once Epi::getPath('data') . 'mc_messages.php';
        include_once Epi::getPath('data') . 'mc_threads.php';
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Parse.php';

        $MC_Messages = new MC_Messages();
        $MC_Threads = new MC_Threads();
        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Parse = new Parse();

        $response = array();
        $post = array();
        $message_data = array();
        $profile_data = array();

        if (empty($response)) {
            $r_getPostParams = $Data->getPostParams(array('i01', 'i02'));

            if (!$r_getPostParams['success']) {
                $response = $r_getPostParams;
            } else {
                $post = $r_getPostParams['post'];
            }
        }

        if (empty($response)) {
            $r_selectOne = $MC_Messages->selectOne($post['i01']);
            if (!$r_selectOne['success']) {
                $response = $r_selectOne;
            } else {
                $message_data = $r_selectOne['message_data'];
            }
        }

        if (empty($response) && ($post['i02'] != md5($message_data['id_message'] . $message_data['identifier']))) {
            $response['success'] = false;
            $response['message'] = "The admins have been notified.";
        }

        if (empty($response)) {
            $update_thread_data = array(
                'id_account' => $message_data['id_account'],
                'identifier' => $message_data['sender_id'],
                'opened' => false
            );
            $r_insert = $MC_Threads->insert($update_thread_data);
            if (!$r_insert['success']) {
                $response = $r_insert;
            }
        }

        if (empty($response)) {
            $r_getUserShow = $MC_Lib_Twitter->getUserShow($message_data['sender_id'], array(), false);
            if ($r_getUserShow['success']) {
                $profile_data = $Data->profileFromTwitterUser($r_getUserShow['twitter_profile_data']);
            }
        }

        if (empty($response)) {
            $alert = (isset($profile_data['username']) ? '@' . $profile_data['username'] . ': ' : '') . $message_data['text'];

            $params = array();
            $params['channel'] = 'TW-' . $message_data['recipient_id'];
            $params['type'] = 'ios';
            $params['data'] = array();
            $params['data']['alert'] = $alert;
            $params['data']['badge'] = 'Increment';
            $params['data']['sound'] = 'newdm.aif';
            $params['data']['recipient'] = $message_data['recipient_id'];
            $params['data']['sender'] = $profile_data['identifier'];
            $Parse->apiPost('push', $params);
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "The notification has been sent.";
        }

        return $response;
    }

}