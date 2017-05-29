<?php

return [
    // database name
    \triagens\ArangoDb\ConnectionOptions::OPTION_DATABASE => 'arango-development',
    // server endpoint to connect to
    \triagens\ArangoDb\ConnectionOptions::OPTION_ENDPOINT => 'tcp://localhost:8529',
    // authorization type to use (currently supported: 'Basic')
    \triagens\ArangoDb\ConnectionOptions::OPTION_AUTH_TYPE => 'Basic',
    // user for basic authorization
    \triagens\ArangoDb\ConnectionOptions::OPTION_AUTH_USER => 'root',
    // password for basic authorization
    \triagens\ArangoDb\ConnectionOptions::OPTION_AUTH_PASSWD => '',
    // connection persistence on server. can use either 'Close' (one-time connections) or 'Keep-Alive' (re-used connections)
    \triagens\ArangoDb\ConnectionOptions::OPTION_CONNECTION => 'Keep-Alive',
    // connect timeout in seconds
    \triagens\ArangoDb\ConnectionOptions::OPTION_TIMEOUT => 3,
    // whether or not to reconnect when a keep-alive connection has timed out on server
    \triagens\ArangoDb\ConnectionOptions::OPTION_RECONNECT => true,
    // optionally create new collections when inserting documents
    \triagens\ArangoDb\ConnectionOptions::OPTION_CREATE => true,
    // optionally create new collections when inserting documents
    \triagens\ArangoDb\ConnectionOptions::OPTION_UPDATE_POLICY => \triagens\ArangoDb\UpdatePolicy::LAST,
];