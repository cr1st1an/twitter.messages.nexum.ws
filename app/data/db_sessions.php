<?php

class DB_Sessions {

    protected $_name = 'sessions';

    public function selectOne($ID_SESSION) {
        $response = array();
        $session_data = array();

        $id_session = (int) $ID_SESSION;
        if (empty($response) && empty($id_session)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_SESSION is missing in DB_Sessions->selectOne()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $select_data = array(
                'id_session' => $id_session
            );
            $session_data = getDatabase()->one(
                    'SELECT * FROM ' . $this->_name . ' WHERE id_session=:id_session', $select_data
            );

            if (empty($session_data)) {
                $response['success'] = false;
                $response['message'] = "The requested session with id '$id_session' was not found [DB]";
                $response['err'] = 0;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here is the session with id '$id_session' [DB]";
            $response['session_data'] = $session_data;
        }

        return $response;
    }

    public function insert($DATA) {
        $response = array();
        $id_session = null;

        $id_account = (int) $DATA['id_account'];
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in DB_Sessions->insert()";
            $response['err'] = 0;
        }

        $uiid = (string) $DATA['uiid'];
        if (empty($response) && empty($uiid)) {
            $response['success'] = false;
            $response['message'] = "Required value UIID is missing in DB_Accounts->insert()";
            $response['err'] = 0;
        }

        $client = (string) $DATA['client'];
        if (empty($response) && empty($client)) {
            $response['success'] = false;
            $response['message'] = "Required value CLIENT is missing in DB_Accounts->insert()";
            $response['err'] = 0;
        }

        $version = (string) $DATA['version'];
        if (empty($response) && empty($client)) {
            $response['success'] = false;
            $response['message'] = "Required value VERSION is missing in DB_Accounts->insert()";
            $response['err'] = 0;
        }

        $created = date("Y-m-d H:i:s");

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else
            $ip = $_SERVER['REMOTE_ADDR'];

        if (empty($response)) {
            $insert_data = array(
                'id_account' => $id_account,
                'created' => $created,
                'uiid' => $uiid,
                'client' => $client,
                'version' => $version,
                'ip' => $ip
            );
            $id_session = getDatabase()->execute(
                    'INSERT INTO ' . $this->_name . '(id_account, created, uiid, client, version, ip) VALUES(:id_account, :created, :uiid, :client, :version, :ip)', $insert_data
            );

            $response['success'] = true;
            $response['message'] = "A new session with id '$id_session' has been inserted [DB]";
            $response['id_session'] = $id_session;
        }

        return $response;
    }

}