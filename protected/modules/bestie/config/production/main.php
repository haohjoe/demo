<?php
return CMap::mergeArray(require(__DIR__ . '/../base.php'), array(
    'components' => array(
        'db.bestie' => array(
            'class' => 'MongoConnection',
            'server' => $GLOBALS['mongo_servers'],
            'options' => array(
                'connect' => false,
                'readPreference' => MongoClient::RP_SECONDARY_PREFERRED,//,RP_NEAREST,MongoClient::RP_PRIMARY
                'connectTimeoutMS' => 1000,
                'replicaSet' => 'member_rs1'
            )
        )
    )
));
