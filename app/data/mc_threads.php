<?php

class MC_Threads {

    protected $_name = 'Threads_';

    public function selectAll($ID_ACCOUNT) {
        include_once Epi::getPath('data') . 'db_threads.php';

        $DB_Threads = new DB_Threads();

        $response = array();
        $threads_data = array();

        $id_account = (int) $ID_ACCOUNT;
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in MC_Threads->selectAll()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $key = $this->_name . $id_account;

            $cached_data = getCache()->get($key);
            if (!$cached_data) {
                $r_selectAll = $DB_Threads->selectAll($id_account);
                if ($r_selectAll['success']) {
                    getCache()->set($key, $r_selectAll['threads_data']);
                }
                $response = $r_selectAll;
            } else {
                $threads_data = $cached_data;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the threads for the account with id '$id_account' [MC]";
            $response['threads_data'] = $threads_data;
        }

        return $response;
    }

    public function insert($DATA) {
        include_once Epi::getPath('data') . 'db_threads.php';

        $DB_Threads = new DB_Threads();

        $response = array();
        $id_thread = null;
        
        $id_account = (int) $DATA['id_account'];
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in MC_Threads->insert()";
            $response['err'] = 0;
        }
        
        if (empty($response)) {
            $r_insert = $DB_Threads->insert($DATA);
            if (!$r_insert['success']) {
                $response = $r_insert;
            } else {
                $id_thread = $r_insert['id_thread'];
            }
        }

        if (empty($response)) {
            $key = $this->_name . $id_account;
            getCache()->delete($key);

            $response['success'] = true;
            $response['message'] = "A new thread with id '$id_thread' has been inserted [MC]";
            $response['id_thread'] = $id_thread;
        }

        return $response;
    }
    
}