<?php

class Session {

    public function get() {
        $session_data = array();
        $session_data['account'] = json_decode(getSession()->get('account'), true);
        return $session_data;
    }

    public function setAccount($ACCOUNT_DATA) {
        getSession()->set('account', json_encode($ACCOUNT_DATA));
    }

}