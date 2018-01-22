<?php
return [
    "image_url" => "http://t9-cname.ugchain.com",
    "ug"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
        "ug_host"    => "http://47.74.236.84:22000",
        "ug_sign_url" =>"http://118.190.137.150:10000/ug/defreezeByVote",//ug签名
    ],
    "eth"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
//        "eth_host"   => "https://ropsten.infura.io/5SIQud3rd1716ZjUfO6m",
        "eth_host" => "http://118.190.137.150:8545",
        "eth_sign_url"  => "http://118.190.137.150:10000/eth/defreeze",//eth签名
    ],
];
