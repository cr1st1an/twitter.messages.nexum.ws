<?php

class MC_Lib_Twitter {

    protected $_name = 'Lib_Twitter_';

    public function getFriendsIds($PROFILE_ID, $CREDENTIALS, $FETCH = true) {
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Twitter = new Twitter();

        $response = array();
        $friends_ids = array();

        $profile_id = (int) $PROFILE_ID;
        if (empty($response) && empty($profile_id)) {
            $response['success'] = false;
            $response['message'] = "Required value PROFILE_ID is missing in MC_Api_Twitter->getFriendsIds()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $key = $this->_name . 'getFriendsIds_' . $profile_id;

            $cached_data = getCache()->get($key);
            if (!$cached_data && $FETCH) {
                $r_apiGet_FI = $Twitter->apiGet('friends/ids', array('user_id' => $profile_id, 'count' => 5000), $CREDENTIALS);
                if (!$r_apiGet_FI['success']) {
                    $response['success'] = false;
                    $response['message'] = "Couldn't complete the request [MC]";
                    $response['err'] = 0;
                } else {
                    $friends_ids = $r_apiGet_FI['api_data']['ids'];
                    getCache()->set($key, $friends_ids);
                }
            } else {
                $friends_ids = $cached_data;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the requested friends ids for the profile with id '$profile_id' [MC]";
            $response['friends_ids'] = $friends_ids;
        }

        return $response;
    }

    public function getFollowersIds($PROFILE_ID, $CREDENTIALS, $FETCH = true) {
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Twitter = new Twitter();

        $response = array();
        $followers_ids = array();

        $profile_id = (int) $PROFILE_ID;
        if (empty($response) && empty($profile_id)) {
            $response['success'] = false;
            $response['message'] = "Required value PROFILE_ID is missing in MC_Api_Twitter->getFollowersIds()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $key = $this->_name . 'getFollowersIds_' . $profile_id;

            $cached_data = getCache()->get($key);
            if (!$cached_data && $FETCH) {
                $r_apiGet_FI = $Twitter->apiGet('followers/ids', array('user_id' => $profile_id, 'count' => 5000), $CREDENTIALS);
                if (!$r_apiGet_FI['success']) {
                    $response['success'] = false;
                    $response['message'] = "Couldn't complete the request [MC]";
                    $response['err'] = 0;
                } else {
                    $followers_ids = $r_apiGet_FI['api_data']['ids'];
                    getCache()->set($key, $followers_ids);
                }
            } else {
                $followers_ids = $cached_data;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the requested followers ids for the profile with id '$profile_id' [MC]";
            $response['followers_ids'] = $followers_ids;
        }

        return $response;
    }

    public function getUsersSearch($QUERY, $PAGE, $CREDENTIALS, $FETCH = true) {
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Twitter = new Twitter();

        $response = array();
        $twitter_profiles_data = array();

        $query = (string) $QUERY;
        if (empty($response) && empty($query)) {
            $response['success'] = false;
            $response['message'] = "Required value QUERY is missing in MC_Api_Twitter->getUsersSearch()";
            $response['err'] = 0;
        }

        $page = (int) $PAGE;
        if (empty($response) && !is_numeric($page)) {
            $response['success'] = false;
            $response['message'] = "Required value PAGE is wrong in MC_Api_Twitter->getUsersSearch()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $key_a = $this->_name . 'getUsersSearch_' . md5($query) . '_' . $page;

            $cached_data = getCache()->get($key_a);
            if (!$cached_data && $FETCH) {
                $r_apiGet_US = $Twitter->apiGet('users/search', array('q' => $query, 'count' => APP_PAGE_ITEMS, 'page' => $page), $CREDENTIALS);
                if (!$r_apiGet_US['success']) {
                    $response['success'] = false;
                    $response['message'] = "Couldn't complete the request [MC]";
                    $response['err'] = 0;
                } else {
                    $twitter_profiles_data = $r_apiGet_US['api_data'];
                    getCache()->set($key_a, $twitter_profiles_data);
                }
            } else {
                $twitter_profiles_data = $cached_data;
            }

            foreach ($twitter_profiles_data as $twitter_profile_data) {
                $key_b = $this->_name . 'getUserShow_' . (int) $twitter_profile_data['id'];
                getCache()->set($key_b, $twitter_profile_data);
            }
        }

        if (empty($response) && empty($twitter_profiles_data)) {
            
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the profiles that match '$query' [MC]";
            $response['twitter_profiles_data'] = $twitter_profiles_data;
        }

        return $response;
    }

    public function getUserShow($PROFILE_ID, $CREDENTIALS, $FETCH = true) {
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Twitter = new Twitter();

        $response = array();
        $twitter_profile_data = array();

        $profile_id = (int) $PROFILE_ID;
        if (empty($response) && empty($profile_id)) {
            $response['success'] = false;
            $response['message'] = "Required value PROFILE_ID is missing in MC_Api_Twitter->getUserShow()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $key = $this->_name . 'getUserShow_' . $profile_id;

            $cached_data = getCache()->get($key);
            if (!$cached_data && $FETCH) {
                $r_apiPost_US = $Twitter->apiGet('users/show', array('user_id' => $profile_id), $CREDENTIALS);
                if (!$r_apiPost_US['success']) {
                    $response['success'] = false;
                    $response['message'] = "Couldn't complete the request [MC]";
                    $response['err'] = 0;
                } else {
                    $twitter_profile_data = $r_apiPost_US['api_data'];
                    getCache()->set($key, $twitter_profile_data);
                }
            } else {
                $twitter_profile_data = $cached_data;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here is the requested profile with id '$profile_id' [MC]";
            $response['twitter_profile_data'] = $twitter_profile_data;
        }

        return $response;
    }

    public function postUserLookup($FOLLOWERS_IDS_STR, $CREDENTIALS, $FETCH = true) {
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Twitter = new Twitter();

        $response = array();
        $twitter_profiles_data = array();

        $followers_ids_str = (string) $FOLLOWERS_IDS_STR;
        if (empty($response) && empty($followers_ids_str)) {
            $response['success'] = false;
            $response['message'] = "Required value FOLLOWERS_IDS_STR is missing in MC_Api_Twitter->postUserLookup()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $key_a = $this->_name . 'postUserLookup_' . md5($followers_ids_str);

            $cached_data = getCache()->get($key_a);
            if (!$cached_data && $FETCH) {
                $r_apiPost_UL = $Twitter->apiGet('users/lookup', array('user_id' => $followers_ids_str), $CREDENTIALS);
                if (!$r_apiPost_UL['success']) {
                    $response['success'] = false;
                    $response['message'] = "Couldn't complete the request [MC]";
                    $response['err'] = 0;
                } else {
                    $twitter_profiles_data = $r_apiPost_UL['api_data'];
                    getCache()->set($key_a, $twitter_profiles_data);
                }
            } else {
                $twitter_profiles_data = $cached_data;
            }

            foreach ($twitter_profiles_data as $twitter_profile_data) {
                $key_b = $this->_name . 'getUserShow_' . (int) $twitter_profile_data['id'];
                getCache()->set($key_b, $twitter_profile_data);
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the requested profiles with ids '$followers_ids_str' [MC]";
            $response['twitter_profiles_data'] = $twitter_profiles_data;
        }

        return $response;
    }

    public function postFriendshipCreate($PROFILE_ID, $ACCOUNT) {
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Twitter = new Twitter();

        $response = array();

        $profile_id = (int) $PROFILE_ID;
        if (empty($response) && empty($profile_id)) {
            $response['success'] = false;
            $response['message'] = "Required value PROFILE_ID is missing in MC_Api_Twitter->postFriendshipCreate()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_apiPost_FC = $Twitter->apiPost('friendships/create', array('user_id' => $profile_id), $ACCOUNT['credentials']);
            if (!$r_apiPost_FC['success']) {
                $response['success'] = false;
                $response['message'] = "Couldn't complete the request [MC]";
                $response['err'] = 0;
            } else {
                $key_a = $this->_name . 'getFriendsIds_' . $ACCOUNT['identifier'];
                getCache()->delete($key_a);
                $key_b = $this->_name . 'getFollowersIds_' . $ACCOUNT['identifier'];
                getCache()->delete($key_b);
                $key_c = $this->_name . 'getFriendsIds_' . $profile_id;
                getCache()->delete($key_c);
                $key_d = $this->_name . 'getFollowersIds_' . $profile_id;
                getCache()->delete($key_d);
            }
        }
        
        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "The relationship has been created [MC]";
        }

        return $response;
    }

    public function postFriendshipDestroy($PROFILE_ID, $ACCOUNT) {
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Twitter = new Twitter();

        $response = array();

        $profile_id = (int) $PROFILE_ID;
        if (empty($response) && empty($profile_id)) {
            $response['success'] = false;
            $response['message'] = "Required value PROFILE_ID is missing in MC_Api_Twitter->postFriendshipDestroy()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_apiPost_FC = $Twitter->apiPost('friendships/destroy', array('user_id' => $profile_id), $ACCOUNT['credentials']);
            if (!$r_apiPost_FC['success']) {
                $response['success'] = false;
                $response['message'] = "Couldn't complete the request [MC]";
                $response['err'] = 0;
            } else {
                $key_a = $this->_name . 'getFriendsIds_' . $ACCOUNT['identifier'];
                getCache()->delete($key_a);
                $key_b = $this->_name . 'getFollowersIds_' . $ACCOUNT['identifier'];
                getCache()->delete($key_b);
                $key_c = $this->_name . 'getFriendsIds_' . $profile_id;
                getCache()->delete($key_c);
                $key_d = $this->_name . 'getFollowersIds_' . $profile_id;
                getCache()->delete($key_d);
            }
        }
        
        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "The relationship has been destroyed [MC]";
        }

        return $response;
    }

    public function postBlockCreate($PROFILE_ID, $ACCOUNT) {
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Twitter = new Twitter();

        $response = array();

        $profile_id = (int) $PROFILE_ID;
        if (empty($response) && empty($profile_id)) {
            $response['success'] = false;
            $response['message'] = "Required value PROFILE_ID is missing in MC_Api_Twitter->postBlockCreate()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_apiPost_FC = $Twitter->apiPost('blocks/create', array('user_id' => $profile_id), $ACCOUNT['credentials']);
            if (!$r_apiPost_FC['success']) {
                $response['success'] = false;
                $response['message'] = "Couldn't complete the request [MC]";
                $response['err'] = 0;
            } else {
                $key_a = $this->_name . 'getFriendsIds_' . $ACCOUNT['identifier'];
                getCache()->delete($key_a);
                $key_b = $this->_name . 'getFollowersIds_' . $ACCOUNT['identifier'];
                getCache()->delete($key_b);
                $key_c = $this->_name . 'getFriendsIds_' . $profile_id;
                getCache()->delete($key_c);
                $key_d = $this->_name . 'getFollowersIds_' . $profile_id;
                getCache()->delete($key_d);
            }
        }
        
        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "The profile has been blocked [MC]";
        }

        return $response;
    }

}