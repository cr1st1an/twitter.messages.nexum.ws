<?php

class DB_Messages {

    protected $_name = 'messages';

    public function selectOne($ID_MESSAGE) {
        $response = array();
        $message_data = array();

        $id_message = (int) $ID_MESSAGE;
        if (empty($response) && empty($id_message)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_MESSAGE is missing in DB_Messages->selectOne()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $select_data = array(
                'id_message' => $id_message
            );
            $message_data = getDatabase()->one(
                    'SELECT * FROM ' . $this->_name . ' WHERE id_message=:id_message', $select_data
            );

            if (empty($message_data)) {
                $response['success'] = false;
                $response['message'] = "The requested message with id '$message_data' was not found [DB]";
                $response['err'] = 0;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here is the message with id '$id_message' [DB]";
            $response['message_data'] = $message_data;
        }

        return $response;
    }

    public function selectAll($ID_ACCOUNT) {
        $response = array();
        $messages_data = array();

        $id_account = (int) $ID_ACCOUNT;
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in DB_Messages->selectAll()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $select_data = array(
                'id_account' => $id_account
            );

            $messages_data = getDatabase()->all(
                    'SELECT * FROM ' . $this->_name . ' WHERE id_account=:id_account', $select_data
            );
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the messages for the account with id '$id_account' [DB]";
            $response['messages_data'] = $messages_data;
        }

        return $response;
    }

    public function insert($DATA) {
        $response = array();
        $id_message = null;

        $id_account = (int) $DATA['id_account'];
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in DB_Messages->insert()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $insert_data = array(
                'id_account' => $id_account,
                'created' => $DATA['created'],
                'identifier' => $DATA['identifier'],
                'sender_id' => $DATA['sender_id'],
                'recipient_id' => $DATA['recipient_id'],
                'text' => $DATA['text']
            );
            $id_message = getDatabase()->execute(
                    'INSERT INTO ' . $this->_name . '(id_account, created, identifier, sender_id, recipient_id, text) VALUES(:id_account, :created, :identifier, :sender_id, :recipient_id, :text)', $insert_data
            );

            $response['success'] = true;
            $response['message'] = "A new message with id '$id_message' has been inserted [DB]";
            $response['id_message'] = $id_message;
        }

        return $response;
    }

}