<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'github' => [
        'client_id' => env('GITHUB_CLIENT_ID'),
        'client_secret' => env('GITHUB_CLIENT_SECRET'),
        'redirect' => env('GITHUB_REDIRECT_URI'),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'ai' => [
        'provider' => env('AI_PROVIDER', 'gemini'),
        'providers' => [
            'gemini' => [
                'key' => env('GEMINI_API_KEY'),
                'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
            ],
            'openai' => [
                'key' => env('OPENAI_API_KEY'),
                'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            ],
            'anthropic' => [
                'key' => env('ANTHROPIC_API_KEY'),
                'model' => env('ANTHROPIC_MODEL', 'claude-3-5-sonnet-20241022'),
            ],
            'xai' => [
                'key' => env('XAI_API_KEY'),
                'model' => env('XAI_MODEL', 'grok-2-1212'),
            ],
            'deepseek' => [
                'key' => env('DEEPSEEK_API_KEY'),
                'model' => env('DEEPSEEK_MODEL', 'deepseek-chat'),
            ],
            'openrouter' => [
                'key' => env('OPENROUTER_API_KEY'),
                'model' => env('OPENROUTER_MODEL', 'anthropic/claude-3.5-sonnet'),
            ],
            'iyh' => [
                'key' => env('IYH_API_KEY'),
                'model' => env('IYH_MODEL', 'claude-3-5-sonnet'),
            ],
            'ninerouter' => [
                'key' => env('NINEROUTER_API_KEY'),
                'url' => env('NINEROUTER_BASE_URL', 'https://api.9router.com/v1'),
                'model' => env('NINEROUTER_MODEL', 'gpt-4o-mini'),
            ],
        ],
    ],

];
