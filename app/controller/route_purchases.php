<?php

class Route_Purchases {
    
    public static function getRecent(){
        include_once Epi::getPath('data') . 'mc_accounts.php';
        include_once Epi::getPath('data') . 'mc_purchases.php';
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';
        
        $MC_Accounts = new MC_Accounts();
        $MC_Purchases = new MC_Purchases();
        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $purchases_data = array();
        $buyers_ids = array();
        $twitter_profiles_data = array();
        $profiles_data = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_selectRecent = $MC_Purchases->selectRecent();
            if(!$r_selectRecent['success']){
                $response = $r_selectRecent;
            } else {
                $purchases_data = $r_selectRecent['purchases_data'];
            }
        }
        
        if(empty($response)) {
            foreach($purchases_data as $purchase_data){
                $r_selectOne = $MC_Accounts->selectOne($purchase_data['id_account']);
                if($r_selectOne['success']){
                    $buyers_ids[] = $r_selectOne['account_data']['identifier'];
                }
            }
        }
        
        if (empty($response)) {
            $r_postUserLookup = $MC_Lib_Twitter->postUserLookup(implode(',', $buyers_ids), $session['account']['credentials']);
            if (!$r_postUserLookup['success']) {
                $response = $r_postUserLookup;
            } else {
                $twitter_profiles_data = $Data->profilesFromTwitterUsers($r_postUserLookup['twitter_profiles_data'], $session['account']);
                foreach($twitter_profiles_data as $twitter_profile_data){
                    $twitter_profiles_data[$twitter_profile_data['identifier']] = $twitter_profile_data;
                }
            }
        }
        
        if(empty($response)){
            foreach($buyers_ids as $buyer_id){
                $profiles_data[] = $twitter_profiles_data[$buyer_id];
            }
        }
        
        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the recent purchases";
            $response['profiles_data'] = $profiles_data;
        }

        return $response;
    }
    
    public static function postRoot() {
        include_once Epi::getPath('data') . 'mc_accounts.php';
        include_once Epi::getPath('data') . 'mc_purchases.php';
        //include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';
        
        $MC_Accounts = new MC_Accounts();
        $MC_Purchases = new MC_Purchases();
        //$Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        //$post = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

//        if (empty($response)) {
//            $r_getPostParams = $Data->getPostParams(array('identifier', 'text'), array());
//
//            if (!$r_getPostParams['success']) {
//                $response = $r_getPostParams;
//            } else {
//                $post = $r_getPostParams['post'];
//            }
//        }

        if (empty($response)) {
            $new_purchase_data = array(
                'id_account' => $session['account']['id_account']
            );
            $r_insert = $MC_Purchases->insert($new_purchase_data);
            if(!$r_insert['success']){
                $response = $r_insert;
            }
        }
        
        if(empty($response)){
            $r_update = $MC_Accounts->updateFeatured($session['account']['id_account'], true);
            if(!$r_update['success']){
                $response = $r_update;
            }
        }
        
        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "The purchase has been recorded";
        }

        return $response;
    }

}