<?php

use triagens\ArangoDb\ConnectionOptions;

return [
    ConnectionOptions::OPTION_DATABASE => '_system',
    ConnectionOptions::OPTION_ENDPOINT => 'tcp://localhost:8529',
    ConnectionOptions::OPTION_AUTH_TYPE => 'Basic',
    ConnectionOptions::OPTION_AUTH_USER => 'root',
    ConnectionOptions::OPTION_AUTH_PASSWD => 'password',
    ConnectionOptions::OPTION_CONNECTION => 'Keep-Alive',
    ConnectionOptions::OPTION_TIMEOUT => 3,
    ConnectionOptions::OPTION_RECONNECT => true,
    ConnectionOptions::OPTION_CREATE => true,
    ConnectionOptions::OPTION_UPDATE_POLICY => \triagens\ArangoDb\UpdatePolicy::LAST,
];