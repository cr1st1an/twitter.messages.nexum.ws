<?php

include_once Epi::getPath('controller') . 'route_accounts.php';
include_once Epi::getPath('controller') . 'route_contacts.php';
include_once Epi::getPath('controller') . 'route_messages.php';
include_once Epi::getPath('controller') . 'route_profiles.php';
include_once Epi::getPath('controller') . 'route_sessions.php';
include_once Epi::getPath('controller') . 'route_static.php';
include_once Epi::getPath('controller') . 'route_threads.php';
include_once Epi::getPath('controller') . 'route_workers.php';

/* CLIENT */
getApi()->get('/1.0/contacts/search', array('Route_Contacts', 'getSearch'), EpiApi::external);
getApi()->get('/1.0/contacts/following', array('Route_Contacts', 'getFollowing'), EpiApi::external);
getApi()->get('/1.0/contacts/followers', array('Route_Contacts', 'getFollowers'), EpiApi::external);
getApi()->get('/1.0/contacts/suggested', array('Route_Contacts', 'getSuggested'), EpiApi::external);
getApi()->get('/1.0/messages', array('Route_Messages', 'getRoot'), EpiApi::external);
getApi()->get('/1.0/profiles', array('Route_Profiles', 'getRoot'), EpiApi::external);
getApi()->get('/1.0/sessions/auth', array('Route_Sessions', 'getAuth'), EpiApi::external);
getApi()->get('/1.0/threads', array('Route_Threads', 'getRoot'), EpiApi::external);

getApi()->post('/1.0/accounts/device_token', array('Route_Accounts', 'postDeviceToken'), EpiApi::external);
getApi()->post('/1.0/contacts/follow', array('Route_Contacts', 'postFollow'), EpiApi::external);
getApi()->post('/1.0/messages', array('Route_Messages', 'postRoot'), EpiApi::external);
getApi()->post('/1.0/sessions', array('Route_Sessions', 'postRoot'), EpiApi::external);
getApi()->post('/1.0/sessions/auth', array('Route_Sessions', 'postAuth'), EpiApi::external);

/* WORKERS */
getApi()->post('/1.0/workers/01', array('Route_Workers', 'post01'), EpiApi::external);
getApi()->post('/1.0/workers/02', array('Route_Workers', 'post02'), EpiApi::external);

/* DEFAULT */
getApi()->get('(.*)', array('Route_Static', 'error404'), EpiApi::external);
getApi()->post('(.*)', array('Route_Static', 'error404'), EpiApi::external);
getApi()->delete('(.*)', array('Route_Static', 'error404'), EpiApi::external);
getApi()->put('(.*)', array('Route_Static', 'error404'), EpiApi::external);