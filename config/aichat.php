<?php
return [
    'qian_fan' => [
        'domain' => 'https://aip.baidubce.com/',
        'client_id' => env('qian.CLIENT_ID', ''),
        'client_secret' =>  env('qian.CLIENT_SECRET', ''),
        'grant_type'=> 'client_credentials',
        'oauth' => 'oauth/2.0/token?',
        'chatglm2_6b_32k' => 'rpc/2.0/ai_custom/v1/wenxinworkshop/chat/chatglm2_6b_32k?access_token='
    ],
    'zhipu' => [
        'domain' => 'https://open.bigmodel.cn/',
        'client_id' => env('zhipu.CLIENT_ID', ''),
        'client_secret' =>  env('zhipu.CLIENT_SECRET', ''),
        'characterglm'       => '/api/paas/v3/model-api/characterglm/invoke'
    ]

];
