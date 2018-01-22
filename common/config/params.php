<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    "ug"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
        "ug_host"    => "http://47.74.236.84:22000",
        "ug_sign_url" =>"http://10.10.10.13:10000/ug/defreezeByVote",//ug签名
    ],
    "eth"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
        "eth_host"   => "http://127.0.0.1:7545",
        "eth_sign_url"  => "http://127.0.0.1:12000/eth/defreeze",//eth签名
    ],
];
