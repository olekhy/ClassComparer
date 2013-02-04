<?php

return array(
    'memory_limit' => '4G',

    'blacklist' => array(
        '.svn/',
        'alice/public',
        'bob/public',
        'bob/data',
        'conny/public',
        'conny/data',
        'log',
        'logs',
        'library/Apache',
        'library/PHPExcel',
        'library/Yii',
        'library/Zend',
        'unit/tests',
        'alice/vendor/tests'
    ),
    'directories' => array(
        array(
            //
            '/home/al/P/a4b/CompareTest/lazada_4_0/local',
            '/home/al/P/a4b/CompareTest/trunk_4_0/vendor',
            '/home/al/P/a4b/CompareTest/aubig_4_0/local',
        ),
    //'/home/al/P/a4b/trunk/alice',
    //'/home/al/P/a4b/trunk-implements-consumer-class-loader/alice'
    //'/home/al/P/a4b/trunk/bob/application/vendor/Bob',
    //'/home/al/P/a4b/vendor/Bob'
        /*
        array(
            '/home/al/P/a4b/CompareTest/trunk_4_0/bob/application/vendor',
            '/home/al/P/a4b/CompareTest/lazada_4_0/bob/application/local',
            '/home/al/P/a4b/CompareTest/aubig_4_0/bob/application/local',
        ),
        array(
            '/home/al/P/a4b/CompareTest/trunk_4_0/conny/application/vendor',
            '/home/al/P/a4b/CompareTest/lazada_4_0/conny/application/vendor',
            '/home/al/P/a4b/CompareTest/aubig_4_0/conny/application/vendor',
        ),
        array(
            '/home/al/P/a4b/CompareTest/trunk_4_0/alice/vendor',
            '/home/al/P/a4b/CompareTest/lazada_4_0/alice/local',
            '/home/al/P/a4b/CompareTest/aubig_4_0/alice/local',
        )
        */
    //'/home/al/P/a4b/CompareTest/trunk_4_0',
    //'/home/al/P/a4b/CompareTest/lazada_4_0',
    )
);
