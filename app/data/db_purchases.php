<?php

class DB_Purchases {

    protected $_name = 'purchases';

    public function selectRecent() {
        $response = array();
        $purchases_data = array();

        if (empty($response)) {
            $purchases_data = getDatabase()->all(
                    'SELECT * FROM ' . $this->_name . ' WHERE 1 ORDER BY created DESC LIMIT 100'
            );
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the recent purchases [DB]";
            $response['purchases_data'] = $purchases_data;
        }

        return $response;
    }

    public function insert($DATA) {
        $response = array();
        $id_purchase = null;

        $id_account = (int) $DATA['id_account'];
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in DB_Purchases->insert()";
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
                'ip' => $ip
            );
            $id_purchase = getDatabase()->execute(
                    'INSERT INTO ' . $this->_name . '(id_account, created, ip) VALUES(:id_account, :created, :ip)', $insert_data
            );

            $response['success'] = true;
            $response['message'] = "A new purchase with id '$id_purchase' has been inserted [DB]";
            $response['id_purchase'] = $id_purchase;
        }

        return $response;
    }

}