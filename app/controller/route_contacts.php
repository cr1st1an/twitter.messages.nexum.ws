<?php

class Route_Contacts {

    public static function getTwitterSearch() {
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
            $r_getUsersSearch = $MC_Lib_Twitter->getUsersSearch($get['query'], $get['page'], $session['account']['credentials']);
            if (!$r_getUsersSearch['success']) {
                $response = $r_getUsersSearch;
            } else {
                foreach ($r_getUsersSearch['twitter_profiles_data'] as $twitter_profile_data) {
                    $profiles_data[] = $Data->profileFromTwitterUser($twitter_profile_data);
                }
            }
        }

        if (empty($response)) {
            if (0 != $get['page'])
                $pagination_data['prev'] = $get['page'] - 1;
            if ((20 * ($get['page'] + 1)) <= 1000)
                $pagination_data['next'] = $get['page'] + 1;

            $response['success'] = true;
            $response['message'] = "Here are the results for the twitter search '" . $get['query'] . "' ";
            $response['pagination'] = $pagination_data;
            $response['profiles_data'] = $profiles_data;
        }

        return $response;
    }

    public static function getTwitterFollowing() {
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';

        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();
        $Session = new Session();

        $response = array();
        $session = $Session->get();
        $get = array();
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
                    $profiles_data[] = $Data->profileFromTwitterUser($twitter_profile_data);
                }
            }
        }

        if (empty($response)) {
            if (0 != $get['page'])
                $pagination_data['prev'] = $get['page'] - 1;
            if ((APP_PAGE_ITEMS * ($get['page'] + 1)) <= count($friends_ids))
                $pagination_data['next'] = $get['page'] + 1;

            $response['success'] = true;
            $response['message'] = "Here are the followings for the twitter account with identifier '" . $get['identifier'] . "' ";
            $response['pagination'] = $pagination_data;
            $response['profiles_data'] = $profiles_data;
        }

        return $response;
    }

    public static function getTwitterFollowers() {
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
                    $profiles_data[] = $Data->profileFromTwitterUser($twitter_profile_data);
                }
            }
        }

        if (empty($response)) {
            if (0 != $get['page'])
                $pagination_data['prev'] = $get['page'] - 1;
            if ((APP_PAGE_ITEMS * ($get['page'] + 1)) <= count($followers_ids))
                $pagination_data['next'] = $get['page'] + 1;

            $response['success'] = true;
            $response['message'] = "Here are the followers for the twitter account with identifier '" . $get['identifier'] . "' ";
            $response['pagination'] = $pagination_data;
            $response['profiles_data'] = $profiles_data;
        }

        return $response;
    }

}