<?php
return [
    "image_url" => "http://wallet-pro.dev:8088",
    "ug"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
        "ug_host"    => "http://47.104.166.51:22000",
        "ug_sign_url" =>"http://118.190.137.150:10000/ug/defreezeByVote",//ug签名
        "owner_address" => "0x3a96700a6cce699c8219332202eca67b1442fbe1",
    ],
    "eth"=>[
        "gas_price"=>0,
        "gas_limit"=>"90000",
        "eth_host"   => "http://127.0.0.1:7545",
        "eth_sign_url"  => "http://118.190.137.150:10000/eth/defreeze",//eth签名
        "owner_address" => "0x3a96700a6cce699c8219332202eca67b1442fbe1",
    ],
];
