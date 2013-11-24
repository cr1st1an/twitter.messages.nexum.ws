<?php

class MC_Messages {

    protected $_name = 'Messages_';

    public function selectOne($ID_MESSAGE) {
        include_once Epi::getPath('data') . 'db_messages.php';

        $DB_Messages = new DB_Messages();

        $response = array();
        $message_data = array();

        $id_message = (int) $ID_MESSAGE;
        if (empty($response) && empty($id_message)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_MESSAGE is missing in MC_Messages->selectOne()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $key = $this->_name . $id_message;

            $cached_data = getCache()->get($key);
            if (!$cached_data) {
                $r_selectOne = $DB_Messages->selectOne($id_message);
                if ($r_selectOne['success']) {
                    getCache()->set($key, $r_selectOne['message_data']);
                }
                $response = $r_selectOne;
            } else {
                $message_data = $cached_data;
            }
        }

        if (empty($response) && empty($message_data)) {
            $response['success'] = false;
            $response['message'] = "The requested message with id '$id_message' was not found [MC]";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here is the message with id '$id_message' [MC]";
            $response['message_data'] = $message_data;
        }

        return $response;
    }

    public function selectAll($ID_ACCOUNT) {
        include_once Epi::getPath('data') . 'db_messages.php';

        $DB_Messages = new DB_Messages();

        $response = array();
        $messages_data = array();

        $id_account = (int) $ID_ACCOUNT;
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in MC_Messages->selectAll()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $key = $this->_name . $id_account;

            $cached_data = getCache()->get($key);
            if (!$cached_data) {
                $r_selectAll = $DB_Messages->selectAll($id_account);
                if ($r_selectAll['success']) {
                    getCache()->set($key, $r_selectAll['messages_data']);
                }
                $response = $r_selectAll;
            } else {
                $messages_data = $cached_data;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the messages for the account with id '$id_account' [MC]";
            $response['messages_data'] = $messages_data;
        }

        return $response;
    }

    public function insert($DATA) {
        include_once Epi::getPath('data') . 'db_messages.php';

        $DB_Messages = new DB_Messages();

        $response = array();
        $id_message = null;

        $id_account = (int) $DATA['id_account'];
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in MC_Messages->insert()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $r_insert = $DB_Messages->insert($DATA);
            if (!$r_insert['success']) {
                $response = $r_insert;
            } else {
                $id_message = $r_insert['id_message'];
            }
        }

        if (empty($response)) {
            $key = $this->_name . $id_account;
            getCache()->delete($key);

            $response['success'] = true;
            $response['message'] = "A new message with id '$id_message' has been inserted [MC]";
            $response['id_message'] = $id_message;
        }

        return $response;
    }

}