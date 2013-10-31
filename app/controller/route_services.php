<?php

class Route_Services {

    public static function get01() {
        include_once Epi::getPath('data') . 'mc_accounts.php';
        include_once Epi::getPath('data') . 'mc_threads.php';
        include_once Epi::getPath('data') . 'mc_lib_twitter.php';
        include_once Epi::getPath('lib') . 'Data.php';

        $MC_Accounts = new MC_Accounts();
        $MC_Threads = new MC_Threads();
        $MC_Lib_Twitter = new MC_Lib_Twitter();
        $Data = new Data();

        $response = array();
        $account_data = array();
        $treads_data = array();

        if (empty($response)) {
            $r_getGetParams = $Data->getGetParams(array('id_account'), array());

            if (!$r_getGetParams['success']) {
                $response = $r_getGetParams;
            } else {
                $get = $r_getGetParams['get'];
            }
        }

        if (empty($response)) {
            $r_selectOne = $MC_Accounts->selectOne($get['id_account']);
            if (!$r_selectOne['success']) {
                $response = $r_selectOne;
            } else {
                $account_data = $r_selectOne['account_data'];
            }
        }

        if (empty($response)) {
            $r_selectAll = $MC_Threads->selectAll($account_data['id_account']);
            if (!$r_selectAll['success']) {
                $response = $r_selectAll;
            } else {
                $threads_data = $response['threads_data'];
            }
        }

        if (empty($response)) {
            foreach ($threads_data as $thread_data) {
                if ($thread_data['push']) {
                    // SEND PUSH
                }
            }
        }

        if (empty($response)) {
            // RESET THREADS
        }

        if (empty($response)) {
            $r_getDirectMessages = $MC_Lib_Twitter->getDirectMessages($account_data['identifier'], $account_data['credentials'], true);
            if (!$r_getDirectMessages['success']) {
                $response = $r_getDirectMessages;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "The threads are up to date";
        }

        return $response;
    }

}