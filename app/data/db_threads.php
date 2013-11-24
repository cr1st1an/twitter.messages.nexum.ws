<?php

class DB_Threads {

    protected $_name = 'threads';

    public function selectAll($ID_ACCOUNT) {
        $response = array();
        $threads_data = array();

        $id_account = (int) $ID_ACCOUNT;
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in DB_Threads->selectAll()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $select_data = array(
                'id_account' => $id_account
            );
            
            $threads_data = getDatabase()->all(
                    'SELECT * FROM ' . $this->_name . ' WHERE id_account=:id_account', $select_data
            );
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the threads for the account with id '$id_account' [DB]";
            $response['threads_data'] = $threads_data;
        }

        return $response;
    }

    public function insert($DATA) {
        $response = array();
        $id_thread = null;
        $message = "";

        $id_account = (int) $DATA['id_account'];
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in DB_Threads->insert()";
            $response['err'] = 0;
        }

        $identifier = (int) $DATA['identifier'];
        if (empty($response) && empty($identifier)) {
            $response['success'] = false;
            $response['message'] = "Required value IDENTIFIER is missing in DB_Threads->insert()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $select_data = array(
                'id_account' => $id_account,
                'identifier' => $identifier
            );
            $thread_data = getDatabase()->one('SELECT * FROM ' . $this->_name . ' WHERE id_account=:id_account AND identifier=:identifier', $select_data);

            if (empty($thread_data)) {
                $insert_data = array(
                    'id_account' => $id_account,
                    'identifier' => $identifier,
                    'opened' => $DATA['opened']
                );
                $id_thread = getDatabase()->execute(
                        'INSERT INTO ' . $this->_name . '(id_account, identifier, opened) VALUES(:id_account, :identifier, :opened)', $insert_data
                );
                $message = "A new thread with id '$id_thread' has been inserted [DB]";
            } else {
                $id_thread = $thread_data['id_thread'];
                $update_data = array(
                    'id_thread' => $id_thread,
                    'opened' => $DATA['opened']
                );
                getDatabase()->execute('UPDATE ' . $this->_name . ' SET opened=:opened WHERE id_thread=:id_thread', $update_data);
                $message = "The thread with id '$id_thread' has been updated [DB]";
            }

            $response['success'] = true;
            $response['message'] = $message;
            $response['id_thread'] = $id_thread;
        }

        return $response;
    }

}