<?php

class MC_Purchases {

    protected $_name = 'Purchases_';
    
    public function selectRecent() {
        include_once Epi::getPath('data') . 'db_purchases.php';

        $DB_Purchases = new DB_Purchases();

        $response = array();
        $purchases_data = array();

        if (empty($response)) {
            $key = $this->_name . 'Recent';

            $cached_data = getCache()->get($key);
            if (!$cached_data) {
                $r_selectRecent = $DB_Purchases->selectRecent();
                if ($r_selectRecent['success']) {
                    getCache()->set($key, $r_selectRecent['purchases_data']);
                }
                $response = $r_selectRecent;
            } else {
                $purchases_data = $cached_data;
            }
        }

        if (empty($response)) {
            $response['success'] = true;
            $response['message'] = "Here are the recent purchases [MC]";
            $response['purchases_data'] = $purchases_data;
        }

        return $response;
    }

    public function insert($DATA) {
        include_once Epi::getPath('data') . 'db_purchases.php';

        $DB_Purchases = new DB_Purchases();

        $response = array();
        $id_purchase = null;
        
        $id_account = (int) $DATA['id_account'];
        if (empty($response) && empty($id_account)) {
            $response['success'] = false;
            $response['message'] = "Required value ID_ACCOUNT is missing in MC_Purchases->insert()";
            $response['err'] = 0;
        }
        
        if (empty($response)) {
            $r_insert = $DB_Purchases->insert($DATA);
            if (!$r_insert['success']) {
                $response = $r_insert;
            } else {
                $id_purchase = $r_insert['id_purchase'];
            }
        }

        if (empty($response)) {
            $key = $this->_name . 'Recent';
            getCache()->delete($key);

            $response['success'] = true;
            $response['message'] = "A new purchase with id '$id_purchase' has been inserted [MC]";
            $response['id_purchase'] = $id_purchase;
        }

        return $response;
    }
    
}