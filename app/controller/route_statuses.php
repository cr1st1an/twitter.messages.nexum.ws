<?php

class Route_Statuses {

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
            $r_getPostParams = $Data->getPostParams(array('status'), array());

            if (!$r_getPostParams['success']) {
                $response = $r_getPostParams;
            } else {
                $post = $r_getPostParams['post'];
            }
        }
        
        if(empty($response)){
            $r_apiPost_SU = $Twitter->apiPost('statuses/update', array('status' => $post['status']), $session['account']['credentials']);
            if (!$r_apiPost_SU['success']) {
                $response['success'] = false;
                $response['message'] = "Couldn't complete the request [MC]";
                $response['err'] = 0;
            }
        }
        
        if(empty($response)){
            $response['success'] = true;
            $response['message'] = "The status has been updated.";
        }
        
        return $response;
    }

}