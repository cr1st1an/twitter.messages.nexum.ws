<?php

class Route_Threads {

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
        $messages_threads_data = array();
        $threads_data = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_selectAll_1 = $MC_Messages->selectAll($session['account']['id_account']);
            if (!$r_selectAll_1['success']) {
                $response = $r_selectAll_1;
            } else {
                $messages_threads_data = $Data->threadsFromMessages($r_selectAll_1['messages_data'], $session['account']);
            }
        }

        if (empty($response)) {
            $r_selectAll_2 = $MC_Threads->selectAll($session['account']['id_account']);
            if (!$r_selectAll_2['success']) {
                $response = $r_selectAll_2;
            } else {
                $threads_data = $Data->threadsMerge($messages_threads_data, $r_selectAll_2['threads_data']);
            }
        }

        if (empty($response)) {
            foreach ($threads_data as $id => $thread_data) {
                $r_getUserShow = $MC_Lib_Twitter->getUserShow($thread_data['identifier'], $session['account']['credentials']);
                if ($r_getUserShow['success']) {
                    $profiles_data = $Data->profilesFromTwitterUsers(array($r_getUserShow['twitter_profile_data']), $session['account']);
                    $profile_data = array_pop($profiles_data);
                    
                    $threads_data[$id]['picture'] = $profile_data['picture'];
                    $threads_data[$id]['title'] = $profile_data['fullname'];
                    $threads_data[$id]['subtitle'] = '@' . $profile_data['username'];
                    $threads_data[$id]['profile_data'] = $profile_data;
                }
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are your threads";
            $response['threads_data'] = $threads_data;
        }

        return $response;
    }

}