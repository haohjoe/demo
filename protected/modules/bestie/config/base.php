<?php

return array(
    'import' => array(
        'application.modules.bestie.components.*',
        'application.modules.bestie.models.dao.*',
        'application.modules.bestie.models.data.*',
        'application.modules.bestie.models.logic.*',
    ),

    'components' => array(
        'db.bestie' => array(
            'class' => 'MongoConnection',
            'server' => $GLOBALS['mongo_servers'],
            'options' => array(
                'connect' => false,
                'connectTimeoutMS' => 1000,
            ),
        ),
    ),

    'params' => array(),
);
