<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Emulate Consul Server (127.0.0.1:8500)
    |--------------------------------------------------------------------------
    |
    | This option is mainly used for testing, however, you can use it for
    | own purposes, not like there is any reason to do so, but it is here
    | for you.
    |
    */
    'emulate'           =>  env('CONSUL_SERVER_EMULATE', false),

    /*
    |--------------------------------------------------------------------------
    | Default Consul Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the consul connections below you wish
    | to use as your default connection.
    |
    */
    'default'           =>  env('CONSUL_SERVER_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Automatic Consul Server Selection
    |--------------------------------------------------------------------------
    |
    | This options allows you to choose whether you want automatic server
    | selection which will be handled by this package.
    | NOTE: 'use_random' will be set to `false` if this option is set to `true`
    |
    */
    'auto_select'       =>  env('CONSUL_SERVER_AUTO_SELECT', false),

    /*
    |--------------------------------------------------------------------------
    | Use Random Consul Server
    |--------------------------------------------------------------------------
    |
    | This option will allow you to use random consul servers.
    | NOTE: if 'auto_select' is set to `true`, this option will have no effect
    |
    */
    'use_random'        =>  env('CONSUL_SERVER_USE_RANDOM', false),

    /*
    |--------------------------------------------------------------------------
    | Default Datacenter
    |--------------------------------------------------------------------------
    |
    | This datacenter will be used for any connection, which does not specify
    | datacenter to use.
    |
    | If connection has 'datacenter' specified, this parameter will be ignored.
    |
    */
    'datacenter'        =>  env('CONSUL_SERVER_DATACENTER', 'dc0'),

    /*
    |--------------------------------------------------------------------------
    | Default Access Token
    |--------------------------------------------------------------------------
    |
    | This access token will be used for any connection, which does not
    | specify access token to use.
    |
    |
    | If connection has 'access_token' specified, this parameter will be ignored.
    |
    */
    'access_token'      =>  env('CONSUL_SERVER_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Decode Value Retrieved From Consul
    |--------------------------------------------------------------------------
    |
    | Consul returns values from KV as the Base64 encoded strings.
    | This parameter allows you to decode those values.
    |
    */
    'decode_value'      =>  env('CONSUL_DECODE_VALUE', true),

    /*
    |--------------------------------------------------------------------------
    | Consul Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the consul connections setup for your application.
    | You can have as many connections as you want.
    |
    */
    'connections'       =>  [
        'default'       =>  [
            'scheme'        =>  env('CONSUL_SERVER_SCHEME', 'http'),
            'host'          =>  env('CONSUL_SERVER_HOST', '127.0.0.1'),
            'port'          =>  env('CONSUL_SERVER_PORT', 8500),
        ]
    ],

];
