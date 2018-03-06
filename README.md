# RapidFire
curl_multiを使った並列リクエストライブラリです。

## 簡易的な使い方

簡易的な使い方としては、下記です。

### 例：

```php
<?php
    //リクエストしたいURLを設定
    $urlList = array(
        "https://xx.xx.xx”,
        "https://xx.xx.xx”,
        "https://xx.xx.xx”,
        "https://xx.xx.xx”,
        "https://xx.xx.xx”
    );

    //タイムアウトする秒数を指定
    $timeout = 10;

    //並列リクエストを行う
    $fire = new RapidFire($timeout);
    $response = $fire->shot($urlList);
```

## CURLオプションを指定する使い方

CURLのオプションを指定する場合は、配列に設定します。

パラメータは下記です。


url：リクエストするURL
curl_option: curl_set_optのオプション  
http://php.net/manual/ja/function.curl-setopt.php

### 例：

```php
<?php
    //リクエストしたいURLを設定
    $urlList = array(
        "https://xx.xx.xx”,
        array(
            "url" => "https://xx.xx.xx”,
            "curl_option" => array(
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36'
            )
        ),
        "https://xx.xx.xx”,
        "https://xx.xx.xx”,
        "https://xx.xx.xx”
    );

    //タイムアウトする秒数を指定
    $timeout = 10;

    //並列リクエストを行う
    $fire = new RapidFire($timeout);
    $response = $fire->shot($urlList);
```