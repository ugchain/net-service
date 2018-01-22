<?php
return [
    "image_url" => "http://wallet-pro.dev:8088",
    "ug"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
        "ug_host"    => "http://47.104.166.51:22000",
        "ug_sign_url" =>"http://118.190.137.150:10000/ug/defreezeByVote",//ug签名
    ],
    "eth"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
        "eth_host"   => "http://127.0.0.1:7545",
        "eth_sign_url"  => "http://118.190.137.150:10000/eth/defreeze",//eth签名
    ],
];
