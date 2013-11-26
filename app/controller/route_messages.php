<?php

class Route_Messages {

    public static function getRoot() {
        include_once Epi::getPath('data') . 'mc_messages.php';
        include_once Epi::getPath('data') . 'mc_threads.php';
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';

        $MC_Messages = new MC_Messages();
        $MC_Threads = new MC_Threads();
        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $profile_data = array();
        $messages_data = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_getGetParams = $Data->getGetParams(array('identifier'), array());

            if (!$r_getGetParams['success']) {
                $response = $r_getGetParams;
            } else {
                $get = $r_getGetParams['get'];
            }
        }

        if (empty($response)) {
            $r_getUserShow = $MC_Lib_Twitter->getUserShow($get['identifier'], $session['account']['credentials']);
            if (!$r_getUserShow['success']) {
                $response = $r_getUserShow;
            } else {
                $profile_data = $Data->profileFromTwitterUser($r_getUserShow['twitter_profile_data'], $session['account']['identifier']);
            }
        }

        if (empty($response)) {
            $r_selectAll = $MC_Messages->selectAll($session['account']['id_account']);
            if (!$r_selectAll['success']) {
                $response = $r_selectAll;
            } else {
                $messages_data = $Data->threadMessagesFromMessages($r_selectAll['messages_data'], $session['account'], $get['identifier']);
            }
        }

        if (empty($response)) {
            $update_thread_data = array(
                'id_account' => $session['account']['id_account'],
                'identifier' => $get['identifier'],
                'opened' => true
            );
            $r_insert = $MC_Threads->insert($update_thread_data);
            if (!$r_insert['success']) {
                $response = $r_insert;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are your threads";
            //$response['account_data'] = $Data->publicAccount($session['account'], $session['account']);
            $response['profile_data'] = $profile_data;
            $response['messages_data'] = $messages_data;
        }

        return $response;
    }

    public static function postRoot() {
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Data = new Data();
        $Session = new Session();
        $Twitter = new Twitter();

        $response = array();
        $session = $Session->get();
        $post = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_getPostParams = $Data->getPostParams(array('identifier', 'text'), array());

            if (!$r_getPostParams['success']) {
                $response = $r_getPostParams;
            } else {
                $post = $r_getPostParams['post'];
            }
        }

        if (empty($response)) {
            $r_apiPost_DMN = $Twitter->apiPost('direct_messages/new', array('user_id' => $post['identifier'], 'text' => $post['text']), $session['account']['credentials']);
            if (!$r_apiPost_DMN['success']) {
                $response = $r_apiPost_DMN;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "The new message was sent to twitter";
        }

        return $response;
    }

}