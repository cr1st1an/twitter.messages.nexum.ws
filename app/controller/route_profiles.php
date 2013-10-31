<?php

class Route_Profiles {
    
    public static function getTwitter() {
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';

        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $get = array();
        $profile_data = array();

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
                $profile_data = $Data->profileFromTwitterUser($r_getUserShow['twitter_profile_data']);
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here is the information for the twitter profile with identifier '" . $get['identifier'] . "' ";
            $response['profile_data'] = $profile_data;
        }

        return $response;
    }

}