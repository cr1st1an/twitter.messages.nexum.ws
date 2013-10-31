<?php

class Route_Threads {

    public static function getTwitter() {
        include_once Epi::getPath('data') . 'mc_threads.php';
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';

        $MC_Threads = new MC_Threads();
        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $twitter_threads_data = array();
        $threads_data = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_getDirectMessages = $MC_Lib_Twitter->getDirectMessages($session['account']['identifier'], $session['account']['credentials'], false);
            if (!$r_getDirectMessages['success']) {
                $response = $r_getDirectMessages;
            } else {
                $twitter_threads_data = $Data->threadsFromTwitterDDMM($r_getDirectMessages['twitter_ddmm_data'], $session['account']['identifier']);
            }
        }

        if (empty($response)) {
            $r_selectAll = $MC_Threads->selectAll($session['account']['id_account']);
            if (!$r_selectAll['success']) {
                $response = $r_selectAll;
            } else {
                $threads_data = $Data->threadsTwitterExtend($twitter_threads_data, $r_selectAll['threads_data']);
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = json_encode($r_selectAll['threads_data']); //"Here are your threads";
            $response['threads_data'] = $threads_data;
        }

        return $response;
    }

}