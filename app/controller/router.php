<?php

include_once Epi::getPath('controller') . 'route_contacts.php';
include_once Epi::getPath('controller') . 'route_profiles.php';
include_once Epi::getPath('controller') . 'route_sessions.php';
include_once Epi::getPath('controller') . 'route_static.php';

getApi()->get('/1.0/contacts/twitter/search', array('Route_Contacts', 'getTwitterSearch'), EpiApi::external);
getApi()->get('/1.0/contacts/twitter/following', array('Route_Contacts', 'getTwitterFollowing'), EpiApi::external);
getApi()->get('/1.0/contacts/twitter/followers', array('Route_Contacts', 'getTwitterFollowers'), EpiApi::external);
getApi()->get('/1.0/profiles/twitter', array('Route_Profiles', 'getTwitterProfile'), EpiApi::external);
getApi()->get('/1.0/sessions/twitter', array('Route_Sessions', 'getTwitter'), EpiApi::external);

getApi()->post('/1.0/sessions', array('Route_Sessions', 'postRoot'), EpiApi::external);
getApi()->post('/1.0/sessions/twitter', array('Route_Sessions', 'postTwitter'), EpiApi::external);

/* DEFAULT */
getApi()->get('(.*)', array('Route_Static', 'error404'), EpiApi::external);
getApi()->post('(.*)', array('Route_Static', 'error404'), EpiApi::external);
getApi()->delete('(.*)', array('Route_Static', 'error404'), EpiApi::external);
getApi()->put('(.*)', array('Route_Static', 'error404'), EpiApi::external);