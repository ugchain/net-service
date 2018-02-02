<?php
return [
    "image_url" => "http://ugc.ugchain.com",
    "host" => "http://ugc.ugchain.com",
    "ug"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
        "ug_host"    => "http://chain.ugchain.org",
        "ug_sign_url" =>"http://118.190.137.150:10000/ug/defreeze",//ug签名
        "owner_address" => "0x3a96700a6cce699c8219332202eca67b1442fbe1",
        "red_packet_address" => "0x3a96700a6cce699c8219332202eca67b1442fbe1",//红包平台地址
        "ug_sign_red_packet" => "http://118.190.137.150:10000/ug/transfer",//ug红包签名
    ],
    "eth"=>[
        "gas_price"=>0,
        "gas_limit"=>"90000",
        "eth_host" => "http://mainnet.ugchain.org",
        "eth_sign_url"  => "http://118.190.137.150:10000/eth/defreeze",//eth签名
        "owner_address" => "0x0E10d1B1AE10AE124939ff657F96836c34b42f10",
    ],
];
