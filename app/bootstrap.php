<?php

include_once '../vendors/epi/Epi.php';

setlocale(LC_MONETARY, 'en_US');

Epi::setPath('root', dirname(__DIR__));
Epi::setPath('base', Epi::getPath('root') . '/vendors/epi/');
Epi::setPath('config', Epi::getPath('root') . '/app/config/');
Epi::setPath('controller', Epi::getPath('root') . '/app/controller/');
Epi::setPath('data', Epi::getPath('root') . '/app/data/');
Epi::setPath('lib', Epi::getPath('root') . '/app/lib/');

Epi::setSetting('debug', true);
Epi::setSetting('exceptions', true);

Epi::init('api', 'cache', 'config', 'database', 'route', 'session', 'debug');

getConfig()->load('default.ini', 'secure.ini');

if (strpos($_SERVER['SERVER_NAME'], 'dev.') !== false) {
    getConfig()->set('db', getConfig()->get('db-dev'));
    getConfig()->set('memcached', getConfig()->get('memcached-dev'));
}

EpiDatabase::employ(
        getConfig()->get('db')->type, getConfig()->get('db')->name, getConfig()->get('db')->host, getConfig()->get('db')->username, getConfig()->get('db')->password
);
getDatabase()->execute('SET NAMES utf8mb4 COLLATE utf8mb4_bin;');

EpiCache::employ(EpiCache::MEMCACHED);
EpiSession::employ(
        array(
            EpiSession::MEMCACHED,
            getConfig()->get('memcached')->host,
            getConfig()->get('memcached')->port,
            getConfig()->get('memcached')->compress,
            getConfig()->get('memcached')->expiry
        )
);

define('APP_PAGE_ITEMS', getConfig()->get('app')->page_items);

define('TWITTER_CONSUMER_KEY', getConfig()->get('twitter')->consumer_key);
define('TWITTER_CONSUMER_SECRET', getConfig()->get('twitter')->consumer_secret);
define('TWITTER_CALLBACK_URL', getConfig()->get('twitter')->callback_url);

define('PARSE_APP_ID', getConfig()->get('parse')->app_id);
define('PARSE_MASTER_KEY', getConfig()->get('parse')->master_key);
define('PARSE_REST_KEY', getConfig()->get('parse')->rest_key);

include_once 'controller/router.php';

getRoute()->run();