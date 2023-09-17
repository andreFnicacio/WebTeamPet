<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAADIQI3B0:APA91bFH7lYu02W0drTnCR_PWj122dVS9gBSgUYtzCIxPWXu10Z2nWNM9C5gOy5bWVfppOs2pEJuqhxPT4YS5AmFK2zRJi4sc1anqqK_B_7gBFjRnBHNRwEZpQTnbozhDNaCmLzChiv2'),
        'sender_id' => env('FCM_SENDER_ID', '53754780701'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
