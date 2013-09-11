#Yii PushoverLogRoute extension
Send logs to your mobile device by [Pushover.net](http://pushover.net/api).
Recommended for alarm reports or non-frequent messages.

- Immediate log dilevery
- Customized notification diliverying (silent hours, level, sounds) via pushover service API.

##Requirements
- Yii Framework 1.1.x (tested on 1.1.14)
- php.ini directive `allow_url_fopen` enabled

##Installation
- **Step 1:** Put directory PushoverLogRoute (or only PushoverLogRoute.php) into your framework extensions directory.
- **Step 2:** Create account on http://pushover.net/ and register your application (`yii-logo.png` for application icon included).
- **Step 3:** Add PushoverLogRoute as new log route on framework config:

```php
'components' => array(
    'log' => array(
        'class' => 'CLogRouter',
        'routes' => array(
            array(
                'class'   => 'ext.PushoverLogRoute.PushoverLogRoute',
                'token'   => '*your-token-on-pushover*',
                'userKey' => '*your-user-key-on-pushover*',
            ),
        ),
    ),
),
```

##Additional config params

```php
array(
    'class'   => 'ext.PushoverLogRoute.PushoverLogRoute',
    'token'   => '*your-token-on-pushover*',
    'userKey' => '*your-user-key-on-pushover*',

    //Customize logging levels
    'levels' => 'warning, error', //Important: by default level set to 'error' only

    //Customize API params for all notify
    'options' => array(
        'url' => 'http://example.com/',
        'url_title' => 'Go to app',
    ),

    //Cutomize API params by level
    'levelOptions' => array(
        //Set normal priority and silent notify for error level
        'error' => array(
            'priority' => 0,
            'sound' => 'none',
        ),
        //Set sound for info level
        'info' => array(
            'sound' => 'bike',
        ),
    ),
)
```

##Resources
- [Pushover API](http://pushover.net/api)
- [GitHub Repo](https://github.com/Yiivgeny/Yii-PushoverLogRoute/)
