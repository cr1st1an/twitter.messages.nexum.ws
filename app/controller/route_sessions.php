<?php

class Route_Sessions {

    public static function postRoot() {
        include_once Epi::getPath('data') . 'mc_accounts.php';
        include_once Epi::getPath('data') . 'mc_sessions.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Session.php';
        include_once Epi::getPath('lib') . 'Twitter.php';

        $MC_Accounts = new MC_Accounts();
        $MC_Sessions = new MC_Sessions();
        $Data = new Data();
        $Session = new Session();
        $Twitter = new Twitter();

        $response = array();
        $post = array();
        $session_data = array();
        $account_data = array();

        if (empty($response)) {
            $r_getPostParams = $Data->getPostParams(array('id_session', 'uiid'), array());

            if (!$r_getPostParams['success']) {
                $response = $r_getPostParams;
            } else {
                $post = $r_getPostParams['post'];
            }
        }

        if (empty($response)) {
            $r_selectOne_1 = $MC_Sessions->selectOne($post['id_session']);
            if (!$r_selectOne_1['success']) {
                $response = $r_selectOne_1;
            } else {
                $session_data = $r_selectOne_1['session_data'];
            }
        }

        if (empty($response)) {
            if ($post['uiid'] != $session_data['uiid']) {
                $response['success'] = false;
                $response['message'] = "Please login again";
            }
        }

        if (empty($response)) {
            $r_selectOne_2 = $MC_Accounts->selectOne($session_data['id_account']);
            if (!$r_selectOne_2['success']) {
                $response = $r_selectOne_2;
            } else {
                $account_data = $r_selectOne_2['account_data'];
            }
        }

        if (empty($response)) {
            switch ($account_data['network']) {
                case 'twitter':
                    $r_apiGet_AVC = $Twitter->apiGet('account/verify_credentials', array(), $account_data['credentials']);
                    if (!$r_apiGet_AVC['success']) {
                        $response = $r_apiGet_AVC;
                    } else {
                        $update_account_data = array(
                            'identifier' => $account_data['identifier'],
                            'network' => $account_data['network'],
                            'fullname' => $r_apiGet_AVC['api_data']['name'],
                            'username' => $r_apiGet_AVC['api_data']['screen_name'],
                            'picture' => str_ireplace('_normal', '', $r_apiGet_AVC['api_data']['profile_image_url_https']),
                            'credentials' => $account_data['credentials']
                        );
                        $r_insert = $MC_Accounts->insert($update_account_data);
                        if (!$r_insert['success']) {
                            $response = $r_insert;
                        } else {
                            $id_account = $r_insert['id_account'];
                            $r_selectOne_3 = $MC_Accounts->selectOne($id_account);
                            if (!$r_selectOne_3['success']) {
                                $response = $r_selectOne_3;
                            } else {
                                $account_data = $r_selectOne_3['account_data'];
                            }
                        }
                    }
                    break;
            }
        }

        if (empty($response)) {
            $Session->setAccount($account_data);

            $response['success'] = true;
            $response['message'] = "Welcome back";
            $response['account_data'] = $Data->publicAccount($account_data, $account_data);
        }

        return $response;
    }

    public static function getTwitter() {
        include_once Epi::getPath('lib') . 'Twitter.php';

        $Twitter = new Twitter();

        $response = array();
        $oauth = array();
        $redirect_url = '';

        if (empty($response)) {
            $r_oAuthRequestToken = $Twitter->oAuthRequestToken();
            if (!$r_oAuthRequestToken['success']) {
                $response = $r_oAuthRequestToken;
            } else {
                $oauth = $r_oAuthRequestToken['api_data'];
            }
        }

        if (empty($response)) {
            $r_oAuthAuthorize = $Twitter->oAuthAuthorize($oauth['oauth_token']);
            if (!$r_oAuthAuthorize['success']) {
                $response = $r_oAuthAuthorize;
            } else {
                $redirect_url = $r_oAuthAuthorize['redirect_url'];
            }
        }

        if (empty($response)) {
            getRoute()->redirect($redirect_url, 302, true);
        }

        return $response;
    }

    public static function postTwitter() {
        include_once Epi::getPath('data') . 'mc_sessions.php';
        include_once Epi::getPath('data') . 'mc_accounts.php';
        include_once Epi::getPath('lib') . 'Data.php';
        include_once Epi::getPath('lib') . 'Twitter.php';

        $MC_Sessions = new MC_Sessions();
        $MC_Accounts = new MC_Accounts();
        $Data = new Data();
        $Twitter = new Twitter();

        $response = array();
        $post = array();
        $identifier = null;
        $credentials_data = array();
        $new_account_data = array();
        $id_account = null;
        $account_data = array();
        $id_session = null;
        $session_data = array();

        if (empty($response)) {
            $r_getPostParams = $Data->getPostParams(array('uiid', 'client', 'version', 'oauth_token', 'oauth_verifier'), array());

            if ($r_getPostParams['success']) {
                $post = $r_getPostParams['post'];
            }
        }

        if (empty($response)) {
            $r_oAuthAccessToken = $Twitter->oAuthAccessToken($post['oauth_verifier'], $post['oauth_token']);
            if (!$r_oAuthAccessToken['success']) {
                $response = $r_oAuthAccessToken;
            } else {
                $identifier = $r_oAuthAccessToken['api_data']['user_id'];
                $credentials_data['oauth_token'] = $r_oAuthAccessToken['api_data']['oauth_token'];
                $credentials_data['oauth_token_secret'] = $r_oAuthAccessToken['api_data']['oauth_token_secret'];
            }
        }

        if (empty($response)) {
            $r_apiGet_AVC = $Twitter->apiGet('account/verify_credentials', array(), $credentials_data);
            if (!$r_apiGet_AVC['success']) {
                $response = $r_apiGet_AVC;
            } else {
                $new_account_data = array(
                    'identifier' => $identifier,
                    'network' => 'twitter',
                    'fullname' => $r_apiGet_AVC['api_data']['name'],
                    'username' => $r_apiGet_AVC['api_data']['screen_name'],
                    'picture' => str_ireplace('_normal', '', $r_apiGet_AVC['api_data']['profile_image_url_https']),
                    'credentials' => $credentials_data
                );
            }
        }

        if (empty($response)) {
            $r_insert_1 = $MC_Accounts->insert($new_account_data);
            if (!$r_insert_1['success']) {
                $response = $r_insert_1;
            } else {
                $id_account = $r_insert_1['id_account'];
                $r_selectOne_1 = $MC_Accounts->selectOne($id_account);
                if (!$r_selectOne_1['success']) {
                    $response = $r_selectOne_1;
                } else {
                    $account_data = $r_selectOne_1['account_data'];
                }
            }
        }

        if (empty($response)) {
            $new_session_data = array(
                'id_account' => $id_account,
                'uiid' => $post['uiid'],
                'client' => $post['client'],
                'version' => $post['version']
            );

            $r_insert_2 = $MC_Sessions->insert($new_session_data);
            if (!$r_insert_2['success']) {
                $response = $r_insert_2;
            } else {
                $id_session = $r_insert_2['id_session'];
                $r_selectOne_2 = $MC_Sessions->selectOne($id_session);
                if (!$r_selectOne_2['success']) {
                    $response = $r_selectOne_2;
                } else {
                    $session_data = $r_selectOne_2['session_data'];
                }
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Twitter login was successful";
            $response['id_session'] = $id_session;
            $response['account_data'] = $Data->publicAccount($account_data, $account_data);
        }

        return $response;
    }

}