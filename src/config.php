<?php

return array(
    'error_reporting' => E_RECOVERABLE_ERROR | E_USER_ERROR | E_ERROR,
    //'error_reporting' => -1,
    'memory_limit' => '256M',

    'blacklist' => array(
        '.svn/',
    ),
    'directories' => array(
        (isset($argv[1])) ? $argv[1]: '',
        (isset($argv[2])) ? $argv[2]: '',
    )
);
