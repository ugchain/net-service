<?php
return [
    "image_url" => "http://t9-cname.ugchain.com/",
    "host"=>"http://t9-cname.ugchain.com",
    "ug"=>[
        "gas_price"=>0,
        "gas_limit"=>150000,
        "ug_host"    => "http://47.104.166.51:22000",
        "ug_sign_url" =>"http://118.190.137.150:10000/ug/defreeze",//ug签名
        "owner_address" => "0xed9d02e382b34818e88b88a309c7fe71e65f419d",
        "red_packet_address" => "0x9d431068dbdffdbc0ef7d431a7f14665c1e00674",//红包平台地址
        "ug_sign_red_packet" => "http://118.190.137.150:10000/ug/transfer",//ug红包签名
    ],
    "eth"=>[
        "gas_price"=>0,
        "gas_limit"=>"90000",
//        "eth_host"   => "https://ropsten.infura.io/5SIQud3rd1716ZjUfO6m",
        "eth_host" => "http://118.190.115.77:8545",
        "eth_sign_url"  => "http://118.190.137.150:10000/eth/defreeze",//eth签名
        "owner_address" => "0x0e10d1b1ae10ae124939ff657f96836c34b42f10",
    ],
    "rsa"=>[
        "privatekey"=>"-----BEGIN RSA PRIVATE KEY-----
MIIEpAIBAAKCAQEAzY0lbrHpwvquLY+rHwU8p+6zcxSk7Z7NA1yks+lZIoYmo6Xi
7bfut72y7QleAFmRDmcPufvW6qI1gfBGAQ/UI+z/a5VWqFgi3yu9V40byBOQQ6So
wAejX6vvyOqvh+ZnhRmCNBanljoNYawWJCLfxNJkpN1vipJ7HnFFojYYBKT7Hz5T
KtAsfSBXMaZJZT68lQX8xD1Du47GqWWpQfW7yyJCM6vyyMFip54YuYGzOWzMLl31
fFBlxrnyNukjTGGRdfzEEvSxxFrqcczJNEFUkKQBUXD9Nq+EU1Krnk2COI9LzM6G
txshlE2KqdH7djKn90ji6nxEUmqJ6WJMooNQjQIDAQABAoIBAHQTXe0Z2Sk7SLr/
46F74pnuyTWWleB+CcX5PiU/BA/j89P8LJ8TfGIUZQNIg+XsrmaUuqPLXL1ZZp//
IHkfCCxVfJOzXKFqTB7840qaq/KbYZ9hT85JQfAX9yvdo8w5x4G5OvbRSEgkdkQO
2t4DN5w9N4qGZaO6Pn6ddrY4l+2efffPIBeNpdn4xjGs5gSJ+Oy45RoGyRGxqeiw
zatx/LWyN187uXxjgnKTrb/eJnw9KL5OEafYOUuK3NtyWzvDR6H6V+UyERnx41dn
ENFOQOmZqCW/5Z3TPKgBVDSar6+93s+zqLHCmUkuWrkFFSE4Z9rG85G5zrbXoe4L
6F7N0YECgYEA6RaP50JwaNN/7expDHltHRW5E+ADx16omWhcrwyjs+5K/KWWto7x
sszO+4tkmtlV4474ghEhOsm2gxJWIlCYDURvGcmhEXWSLpXXPJNqiB3DFb7UeZsF
Igw89J5Kb5GD97oSi9nVd9oEfuY/KeUhXup0/GXOesRmb9uc+El5vSECgYEA4cGl
yFaKL1bEgtjoYSMEuXBjW31qGXa+Z8kF48mtQRBAFMkPAv3odWyaTjg041hZ/8O2
jH6IWA5FVPdFkxMi3tldnnKDHGjpHIaMmz3ogxz7u7BT2JMMNgbEufREbMYz2kjL
0VLNBOfBO800ImuO/eSgSBoaNkL0ZrZ7u+93Ge0CgYBmsHr7rqipdiyRXKs3RLPO
sYhVekcP6eMrmu/iaxYgKma9AhLxIO7ZECRMbDAJjKCrXYyceQGDzeRrwINUIN+s
UK8F1G/yqjKZ9Yfa9zNi/oG5LdacMLDFPFEKkEZI+voCBOcw1+qVH+cFJVlEkt7t
2ytpG00phyd+NmnkdTJ+IQKBgQCwTBRV1dcFzuGZOSCHstweoHjG2rK/fe6FiAOU
dktZUwJn+PdDI9ujz5LU4KnUnItz43esUafR9BsKlit8Bmal1uN4N/7RcdARWbV2
CiuIMFsZoEJqD7NqgXChsvK4azPVFCIurlWyrfVF6SL8ejhpZ2APzmqH01Oe3oTU
J9Y/pQKBgQDHLIPFg3cb2DIGgZez+X45OgaH0dAuiWfIzrqpBSwGAUaLap8vdNGv
VFue1/BJ9u0+lEwjftb1KUc9nUUf8PNjo+OVx+yJsRuGsHc+65wFtSHEQmb5iaNT
U0BiQ/0TcV8eXa1LnaXlr3gLvk0JUK5Q/BalyxgxCrBnMgGVG/CbAA==
-----END RSA PRIVATE KEY-----",
        "publickey"=>"-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzY0lbrHpwvquLY+rHwU8
p+6zcxSk7Z7NA1yks+lZIoYmo6Xi7bfut72y7QleAFmRDmcPufvW6qI1gfBGAQ/U
I+z/a5VWqFgi3yu9V40byBOQQ6SowAejX6vvyOqvh+ZnhRmCNBanljoNYawWJCLf
xNJkpN1vipJ7HnFFojYYBKT7Hz5TKtAsfSBXMaZJZT68lQX8xD1Du47GqWWpQfW7
yyJCM6vyyMFip54YuYGzOWzMLl31fFBlxrnyNukjTGGRdfzEEvSxxFrqcczJNEFU
kKQBUXD9Nq+EU1Krnk2COI9LzM6GtxshlE2KqdH7djKn90ji6nxEUmqJ6WJMooNQ
jQIDAQAB
-----END PUBLIC KEY-----",
    ],
];
