<?php

class DB_Accounts {

    protected $_name = 'accounts';

    public function selectOne($ID_ACCOUNT) {
        $response = array();
        $account_data = array();

        $id_account = (int) $ID_ACCOUNT;
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in DB_Accounts->selectOne()";
            $response['err'] = 0;
        }

        if (empty($response)) {
            $select_data = array(
                'id_account' => $id_account
            );
            $account_data = getDatabase()->one(
                    'SELECT * FROM ' . $this->_name . ' WHERE id_account=:id_account', $select_data
            );

            if (empty($account_data)) {
                $response['success'] = false;
                $response['message'] = "The requested account with id '$id_account' was not found [DB]";
                $response['err'] = 0;
            } else {
                $account_data['credentials'] = json_decode($account_data['credentials'], true);
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here is the account with id '$id_account' [DB]";
            $response['account_data'] = $account_data;
        }

        return $response;
    }

    public function selectFeaturedIds() {
        $response = array();
        $featured_ids = array();

        if (empty($response)) {
            $featured_ids_arr = getDatabase()->all(
                    'SELECT identifier FROM ' . $this->_name . ' WHERE featured=1'
            );
            if($featured_ids_arr){
                foreach($featured_ids_arr as $featured_id){
                    $featured_ids[] = $featured_id['identifier'];
                }
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the featured ids [DB]";
            $response['featured_ids'] = $featured_ids;
        }
        
        return $response;
    }

    public function insert($DATA) {
        $response = array();
        $id_account = null;
        $message = "";

        $identifier = (int) $DATA['identifier'];
        if (empty($response) && empty($identifier)) {
            $response['success'] = false;
            $response['message'] = "Required value IDENTIFIER is missing in DB_Accounts->insert()";
            $response['err'] = 0;
        }

        $credentials = $DATA['credentials'];
        if (empty($response) && (empty($credentials) || !is_array($credentials))) {
            $response['success'] = false;
            $response['message'] = "Required value CREDENTIALS is missing in DB_Accounts->insert()";
            $response['err'] = 0;
        }

        $created = date("Y-m-d H:i:s");
        $updated = date("Y-m-d H:i:s");

        if (empty($response)) {
            $select_data = array(
                'identifier' => $identifier
            );
            $account_data = getDatabase()->one('SELECT * FROM ' . $this->_name . ' WHERE identifier=:identifier', $select_data);

            if (empty($account_data)) {
                $insert_data = array(
                    'created' => $created,
                    'identifier' => $identifier,
                    'fullname' => $DATA['fullname'],
                    'username' => $DATA['username'],
                    'picture' => $DATA['picture'],
                    'credentials' => json_encode($credentials),
                    'status' => true
                );
                $id_account = getDatabase()->execute(
                        'INSERT INTO ' . $this->_name . '(created, identifier, fullname, username, picture, credentials, status) VALUES(:created, :identifier, :fullname, :username, :picture, :credentials, :status)', $insert_data
                );
                $message = "A new account with id '$id_account' has been inserted [DB]";
            } else {
                $id_account = $account_data['id_account'];
                $update_data = array(
                    'id_account' => $id_account,
                    'updated' => $updated,
                    'fullname' => $DATA['fullname'],
                    'username' => $DATA['username'],
                    'picture' => $DATA['picture'],
                    'credentials' => json_encode($credentials)
                );
                getDatabase()->execute('UPDATE ' . $this->_name . ' SET updated=:updated, fullname=:fullname, username=:username, picture=:picture, credentials=:credentials WHERE id_account=:id_account', $update_data);
                $message = "The account with id '$id_account' has been updated [DB]";
            }

            $response['success'] = true;
            $response['message'] = $message;
            $response['id_account'] = $id_account;
        }

        return $response;
    }

    public function updateFeatured($ID_ACCOUNT, $FEATURED) {
        $response = array();

        $id_account = (int) $ID_ACCOUNT;
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in DB_Accounts->updateFeatured()";
            $response['err'] = 0;
        }

        $featured = (int) $FEATURED;

        if (empty($response)) {
            $update_data = array(
                'id_account' => $id_account,
                'featured' => $featured
            );
            getDatabase()->execute('UPDATE ' . $this->_name . ' SET featured=:featured WHERE id_account=:id_account', $update_data);

            $response['success'] = true;
            $response['message'] = "The account with id '$id_account' has been updated [DB]";
        }

        return $response;
    }

}