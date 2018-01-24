<?php
return [
    "image_url" => "http://ugc.ugchain.com",
    "ug"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
        "ug_host"    => "http://47.104.166.51:22000",
        "ug_sign_url" =>"http://118.190.137.150:10000/ug/defreeze",//ug签名
        "owner_address" => "0x3a96700a6cce699c8219332202eca67b1442fbe1",
    ],
    "eth"=>[
        "gas_price"=>0,
        "gas_limit"=>"90000",
        "eth_host" => "http://118.190.115.77:8545",
        "eth_sign_url"  => "http://118.190.137.150:10000/eth/defreeze",//eth签名
        "owner_address" => "0x0E10d1B1AE10AE124939ff657F96836c34b42f10",
    ],
];
