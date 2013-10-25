<?php

class MC_Sessions {

    protected $_name = 'Sessions_';

    public function selectOne($ID_SESSION) {
        include_once Epi::getPath('data') . 'db_sessions.php';

        $DB_Sessions = new DB_Sessions();

        $response = array();
        $session_data = array();

        $id_session = (int) $ID_SESSION;
        if (empty($response) && empty($id_session)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_SESSION is missing in MC_Sessions->selectOne()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $key = $this->_name . $id_session;

            $cached_data = getCache()->get($key);
            if (!$cached_data) {
                $r_selectOne = $DB_Sessions->selectOne($id_session);
                if ($r_selectOne['success']) {
                    getCache()->set($key, $r_selectOne['session_data']);
                }
                $response = $r_selectOne;
            } else {
                $session_data = $cached_data;
            }
        }

        if (empty($response) && empty($session_data)) {
            $response['success'] = false;
            $response['message'] = "The requested session with id '$id_session' was not found [MC]";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here is the session with id '$id_session' [MC]";
            $response['session_data'] = $session_data;
        }

        return $response;
    }

    public function insert($DATA) {
        include_once Epi::getPath('data') . 'db_sessions.php';

        $DB_Sessions = new DB_Sessions();

        $response = array();
        $id_session = null;

        if (empty($response)) {
            $r_insert = $DB_Sessions->insert($DATA);
            if (!$r_insert['success']) {
                $response = $r_insert;
            } else {
                $id_session = $r_insert['id_session'];
            }
        }

        if (empty($response)) {
            $key = $this->_name . $id_session;
            getCache()->delete($key);

            $response['success'] = true;
            $response['message'] = "A new session with id '$id_session' has been inserted [MC]";
            $response['id_session'] = $id_session;
        }

        return $response;
    }
    
}