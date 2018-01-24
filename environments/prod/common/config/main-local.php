<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=rm-m5eoyj8a0to06l399.mysql.rds.aliyuncs.com;dbname=ugc',
            'username' => 'ugc',
            'password' => 'p7K48vbDkv72hEk272PM73',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
