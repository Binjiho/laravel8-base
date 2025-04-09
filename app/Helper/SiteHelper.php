<?php

// check Url
if (!function_exists('checkUrl')) {
    function checkUrl(): string
    {
        $uri = str_replace('://www.', '://', request()->getUri());

        if (strpos($uri, config('site.app.api.url')) !== false) {
            return 'api';
        }

        if (strpos($uri, config('site.app.admin.url')) !== false) {
            return 'admin';
        }

        return 'web';
    }
}

// global auth
if (!function_exists('thisAuth')) {
    function thisAuth()
    {
        if (checkUrl() == 'admin') {
            return auth('admin');
        }

        return auth('web');
    }
}

// get App Name
if (!function_exists('getAppName')) {
    function getAppName(): string
    {
        return config('site.app.' . checkUrl() . '.app_name');
    }
}

// get default url
if (!function_exists('getDefaultUrl')) {
    function getDefaultUrl($auth = false): string
    {
        if ($auth) {
            if (checkUrl() == 'admin') {
                return thisAuth()->check()
                    ? getDefaultUrl()
                    : env('APP_URL') . '/auth/login';
            }

            return thisAuth()->check()
                ? getDefaultUrl()
                : route('login');
        }

        return route('main');
    }
}

// thisLevel
if (!function_exists('thisLevel')) {
    function thisLevel(): string
    {
        return thisUser()->level ?? '';
    }
}

// isAdmin
if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return ((thisUser()->is_admin ?? '') === 'Y');
    }
}

if (!function_exists('maskEvenStr')) {
    function maskEvenStr($string)
    {
        $length = mb_strlen($string);
        $maskedId = '';

        for ($i = 0; $i < $length; $i++) {
            // 0-based index이므로 1-based 기준 짝수 번째 문자는 $i가 홀수일 때 변경
            if ($i % 2 == 1) {
                $maskedId .= '*';
            } else {
                $maskedId .= mb_substr($string, $i, 1);
            }
        }
        return $maskedId;
    }
}

if (!function_exists('wiseuConnection')) {
    function wiseuConnection()
    {
        $host = env('DB_HOST_WISEU');
        $port = env('DB_PORT_WISEU', '1433');
        $dbname = env('DB_DATABASE_WISEU');
        $username = env('DB_USERNAME_WISEU');
        $password = env('DB_PASSWORD_WISEU');

        try {
            $conn = new \PDO(
                "dblib:host={$host}:{$port};dbname={$dbname};TrustServerCertificate=True",
                $username,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::SQLSRV_ATTR_ENCODING => \PDO::SQLSRV_ENCODING_UTF8
                ]
            );

            return $conn;
        } catch (\PDOException $e) {
            // Log or handle the connection error
            throw $e;
        }
    }
}

// 금액 표기
if (!function_exists('priceKo')) {
    function priceKo($price = 0)
    {
        $price = unComma($price);

        // 값이 0이거나 10억 이상일 때
        if ($price <= 0 || $price >= 1000000000) {
            return $price;
        }

        // 숫자에 해당하는 한글 표기
        $numKo = ['', '일', '이', '삼', '사', '오', '육', '칠', '팔', '구'];
        // 단위 표기 (1의 자리, 10의 자리, 100의 자리, 1000의 자리)
        $unitKo = ['', '십', '백', '천'];
        // 만 단위 표기 (없음, 만, 억)
        $manKo = ['', '만', '억'];

        $result = '';
        $strPrice = (string)$price;
        $len = strlen($strPrice);

        // 각 자리 숫자를 처리
        for ($i = 0; $i < $len; $i++) {
            $digit = (int)$strPrice[$i];
            $digitPos = $len - $i - 1; // 자릿수 위치 (0부터 시작)

            // 현재 숫자가 0이 아닐 때만 처리
            if ($digit > 0) {
                // 만, 억 단위 처리
                $manUnit = floor($digitPos / 4);
                // 천, 백, 십, 일 단위 처리
                $unitPos = $digitPos % 4;

                // 숫자 + 단위 추가
                // 1인 경우, 1의 자리가 아니라면 '일'을 생략 (예: 일십 -> 십)
                if ($digit == 1 && $unitPos > 0) {
                    $result .= $unitKo[$unitPos];
                } else {
                    $result .= $numKo[$digit] . $unitKo[$unitPos];
                }

                // 만, 억 단위 추가 (해당 단위의 마지막 숫자일 때)
                if ($unitPos == 0 && $manUnit > 0) {
                    $result .= $manKo[$manUnit];
                }
            }
        }

        return $result;
    }
}

// check Timestamp
if (!function_exists('isValidTimestamp')) {
    function isValidTimestamp($timestamp)
    {
        try {
            $date = new DateTime($timestamp);
            return $date && $date->format('Y-m-d') !== '-0001-11-30';
        } catch (Exception $e) {
            return false;
        }
    }
}