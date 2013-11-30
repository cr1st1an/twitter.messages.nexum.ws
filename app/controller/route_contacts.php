<?php

class Route_Contacts {

    public static function getSearch() {
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';

        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $get = array();
        $session_followers_ids = array();
        $session_friends_ids = array();
        $pagination_data = array(
            'prev' => null,
            'next' => null
        );
        $profiles_data = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_getGetParams = $Data->getGetParams(array('query', 'page'), array());

            if (!$r_getGetParams['success']) {
                $response = $r_getGetParams;
            } else {
                $get = $r_getGetParams['get'];
            }
        }

        if (empty($response)) {
            $r_getFollowersIds = $MC_Lib_Twitter->getFollowersIds($session['account']['identifier'], $session['account']['credentials']);
            if ($r_getFollowersIds['success']) {
                $session_followers_ids = $r_getFollowersIds['followers_ids'];
            }
        }
        
        if (empty($response)) {
            $r_getFriendsIds = $MC_Lib_Twitter->getFriendsIds($session['account']['identifier'], $session['account']['credentials']);
            if (!$r_getFriendsIds['success']) {
                $response = $r_getFriendsIds;
            } else {
                $session_friends_ids = $r_getFriendsIds['friends_ids'];
            }
        }
        
        if (empty($response)) {
            $r_getUsersSearch = $MC_Lib_Twitter->getUsersSearch($get['query'], $get['page'], $session['account']['credentials']);
            if (!$r_getUsersSearch['success']) {
                $response = $r_getUsersSearch;
            } else {
                foreach ($r_getUsersSearch['twitter_profiles_data'] as $twitter_profile_data) {
                    $profiles_data[] = $Data->profileFromTwitterUser($twitter_profile_data, $session['account']['identifier'], $session_followers_ids, $session_friends_ids);
                }
            }
        }

        if (empty($response)) {
            if (20 <= count($profiles_data)) {
                if (0 != $get['page'])
                    $pagination_data['prev'] = $get['page'] - 1;
                if ((20 * ($get['page'] + 1)) < 1000)
                    $pagination_data['next'] = $get['page'] + 1;
            }

            $response['success'] = true;
            $response['message'] = "Here are the results for the twitter search '" . $get['query'] . "' ";
            $response['pagination'] = $pagination_data;
            $response['profiles_data'] = $profiles_data;
        }

        return $response;
    }

    public static function getFollowing() {
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';

        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $get = array();
        $session_followers_ids = array();
        $session_friends_ids = array();
        $friends_ids = array();
        $pagination_data = array(
            'prev' => null,
            'next' => null
        );
        $profiles_data = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_getGetParams = $Data->getGetParams(array('identifier', 'page'), array());

            if (!$r_getGetParams['success']) {
                $response = $r_getGetParams;
            } else {
                $get = $r_getGetParams['get'];
            }
        }

        if (empty($response)) {
            $r_getFollowersIds = $MC_Lib_Twitter->getFollowersIds($session['account']['identifier'], $session['account']['credentials']);
            if ($r_getFollowersIds['success']) {
                $session_followers_ids = $r_getFollowersIds['followers_ids'];
            }
        }

        if (empty($response)) {
            $r_getFriendsIds = $MC_Lib_Twitter->getFriendsIds($session['account']['identifier'], $session['account']['credentials']);
            if (!$r_getFriendsIds['success']) {
                $response = $r_getFriendsIds;
            } else {
                $session_friends_ids = $r_getFriendsIds['friends_ids'];
            }
        }

        if (empty($response)) {
            $r_getFriendsIds = $MC_Lib_Twitter->getFriendsIds($get['identifier'], $session['account']['credentials']);
            if (!$r_getFriendsIds['success']) {
                $response = $r_getFriendsIds;
            } else {
                $friends_ids = $r_getFriendsIds['friends_ids'];
            }
        }
        
        if (empty($response)) {
            $page_friends_ids = array_slice($friends_ids, ($get['page'] * APP_PAGE_ITEMS), APP_PAGE_ITEMS);

            $r_postUserLookup = $MC_Lib_Twitter->postUserLookup(implode(',', $page_friends_ids), $session['account']['credentials']);
            if (!$r_postUserLookup['success']) {
                $response = $r_postUserLookup;
            } else {
                foreach ($r_postUserLookup['twitter_profiles_data'] as $twitter_profile_data) {
                    $profiles_data[] = $Data->profileFromTwitterUser($twitter_profile_data, $session['account']['identifier'], $session_followers_ids, $session_friends_ids);
                }
            }
        }

        if (empty($response)) {
            if (0 != $get['page'])
                $pagination_data['prev'] = $get['page'] - 1;
            if ((APP_PAGE_ITEMS * ($get['page'] + 1)) < count($friends_ids))
                $pagination_data['next'] = $get['page'] + 1;

            $response['success'] = true;
            $response['message'] = "Here are the followings for the twitter account with identifier '" . $get['identifier'] . "' ";
            $response['pagination'] = $pagination_data;
            $response['profiles_data'] = $profiles_data;
        }

        return $response;
    }

    public static function getFollowers() {
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';

        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $get = array();
        $pagination_data = array(
            'prev' => null,
            'next' => null
        );
        $session_followers_ids = array();
        $session_friends_ids = array();
        $followers_ids = array();
        $profiles_data = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_getGetParams = $Data->getGetParams(array('identifier', 'page'), array());

            if (!$r_getGetParams['success']) {
                $response = $r_getGetParams;
            } else {
                $get = $r_getGetParams['get'];
            }
        }

        if (empty($response)) {
            $r_getFollowersIds = $MC_Lib_Twitter->getFollowersIds($session['account']['identifier'], $session['account']['credentials']);
            if ($r_getFollowersIds['success']) {
                $session_followers_ids = $r_getFollowersIds['followers_ids'];
            }
        }

        if (empty($response)) {
            $r_getFriendsIds = $MC_Lib_Twitter->getFriendsIds($session['account']['identifier'], $session['account']['credentials']);
            if (!$r_getFriendsIds['success']) {
                $response = $r_getFriendsIds;
            } else {
                $session_friends_ids = $r_getFriendsIds['friends_ids'];
            }
        }

        if (empty($response)) {
            $r_getFollowersIds = $MC_Lib_Twitter->getFollowersIds($get['identifier'], $session['account']['credentials']);
            if (!$r_getFollowersIds['success']) {
                $response = $r_getFollowersIds;
            } else {
                $followers_ids = $r_getFollowersIds['followers_ids'];
            }
        }

        if (empty($response)) {
            $page_followers_ids = array_slice($followers_ids, ($get['page'] * APP_PAGE_ITEMS), APP_PAGE_ITEMS);

            $r_postUserLookup = $MC_Lib_Twitter->postUserLookup(implode(',', $page_followers_ids), $session['account']['credentials']);
            if (!$r_postUserLookup['success']) {
                $response = $r_postUserLookup;
            } else {
                foreach ($r_postUserLookup['twitter_profiles_data'] as $twitter_profile_data) {
                    $profiles_data[] = $Data->profileFromTwitterUser($twitter_profile_data, $session['account']['identifier'], $session_followers_ids, $session_friends_ids);
                }
            }
        }

        if (empty($response)) {
            if (0 != $get['page'])
                $pagination_data['prev'] = $get['page'] - 1;
            if ((APP_PAGE_ITEMS * ($get['page'] + 1)) < count($followers_ids))
                $pagination_data['next'] = $get['page'] + 1;

            $response['success'] = true;
            $response['message'] = "Here are the followers for the twitter account with identifier '" . $get['identifier'] . "' ";
            $response['pagination'] = $pagination_data;
            $response['profiles_data'] = $profiles_data;
        }

        return $response;
    }

    public static function getSuggested() {
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';

        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $get = array();
        $pagination_data = array(
            'prev' => null,
            'next' => null
        );
        $session_followers_ids = array();
        $session_friends_ids = array();
        $suggested_ids = array();
        $profiles_data = array();

        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_getGetParams = $Data->getGetParams(array('identifier', 'page'), array());

            if (!$r_getGetParams['success']) {
                $response = $r_getGetParams;
            } else {
                $get = $r_getGetParams['get'];
            }
        }

        if (empty($response)) {
            $r_getFollowersIds = $MC_Lib_Twitter->getFollowersIds($session['account']['identifier'], $session['account']['credentials']);
            if ($r_getFollowersIds['success']) {
                $session_followers_ids = $r_getFollowersIds['followers_ids'];
            }
        }

        if (empty($response)) {
            $r_getFriendsIds = $MC_Lib_Twitter->getFriendsIds($session['account']['identifier'], $session['account']['credentials']);
            if (!$r_getFriendsIds['success']) {
                $response = $r_getFriendsIds;
            } else {
                $session_friends_ids = $r_getFriendsIds['friends_ids'];
            }
        }

        if (empty($response)) {
            $suggested_ids = array_intersect($session_followers_ids, $session_friends_ids);
        }

        if (empty($response)) {
            $page_suggested_ids = array_slice($suggested_ids, ($get['page'] * APP_PAGE_ITEMS), APP_PAGE_ITEMS);

            $r_postUserLookup = $MC_Lib_Twitter->postUserLookup(implode(',', $page_suggested_ids), $session['account']['credentials']);
            if (!$r_postUserLookup['success']) {
                $response = $r_postUserLookup;
            } else {
                foreach ($r_postUserLookup['twitter_profiles_data'] as $twitter_profile_data) {
                    $profiles_data[] = $Data->profileFromTwitterUser($twitter_profile_data, $session['account']['identifier'], $session_followers_ids, $session_friends_ids);
                }
            }
        }

        if (empty($response)) {
            if (0 != $get['page'])
                $pagination_data['prev'] = $get['page'] - 1;
            if ((APP_PAGE_ITEMS * ($get['page'] + 1)) < count($suggested_ids))
                $pagination_data['next'] = $get['page'] + 1;

            $response['success'] = true;
            $response['message'] = "Here are the followers for the twitter account with identifier '" . $get['identifier'] . "' ";
            $response['pagination'] = $pagination_data;
            $response['profiles_data'] = $profiles_data;
        }

        return $response;
    }
    
    public static function postFollow(){
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';
        
        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();
        
        $response = array();
        $session = $Session->get();
        $post = array();
        
        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }
        
        if (empty($response)) {
            $r_getPostParams = $Data->getPostParams(array('identifier'), array());

            if (!$r_getPostParams['success']) {
                $response = $r_getPostParams;
            } else {
                $post = $r_getPostParams['post'];
            }
        }
        
        if(empty($response)){
            $response = $MC_Lib_Twitter->postFriendshipCreate($post['identifier'], $session['account']);
        }
        
        return $response;
    }
    
    public static function postUnfollow(){
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';
        
        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();
        
        $response = array();
        $session = $Session->get();
        $post = array();
        
        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }
        
        if (empty($response)) {
            $r_getPostParams = $Data->getPostParams(array('identifier'), array());

            if (!$r_getPostParams['success']) {
                $response = $r_getPostParams;
            } else {
                $post = $r_getPostParams['post'];
            }
        }
        
        if(empty($response)){
            $response = $MC_Lib_Twitter->postFriendshipDestroy($post['identifier'], $session['account']);
        }
        
        return $response;
    }
    
    
    public static function postBlock(){
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';
        
        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();
        
        $response = array();
        $session = $Session->get();
        $post = array();
        
        if (empty($response) && empty($session['account'])) {
            $response['success'] = false;
            $response['message'] = "You need to login first";
            $response['err'] = 0;
        }
        
        if (empty($response)) {
            $r_getPostParams = $Data->getPostParams(array('identifier'), array());

            if (!$r_getPostParams['success']) {
                $response = $r_getPostParams;
            } else {
                $post = $r_getPostParams['post'];
            }
        }
        
        if(empty($response)){
            $response = $MC_Lib_Twitter->postBlockCreate($post['identifier'], $session['account']);
        }
        
        return $response;
    }
    
}