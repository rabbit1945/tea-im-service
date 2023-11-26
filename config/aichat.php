<?php
return [
    'qian_fan' => [
        'domain' => 'https://aip.baidubce.com/',
        'client_id' => env('aichat.CLIENT_ID', ''),
        'client_secret' =>  env('aichat.CLIENT_SECRET', ''),
        'grant_type'=> 'client_credentials',
        'oauth' => 'oauth/2.0/token?',
        'chatglm2_6b_32k' => 'rpc/2.0/ai_custom/v1/wenxinworkshop/chat/chatglm2_6b_32k?access_token='
    ]

];
