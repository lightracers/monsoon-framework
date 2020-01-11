<?php
/*
 * Security class
 *
 * Credits
 * @link      https://github.com/marcocesarato/PHP-AIO-Security-Class
 */

namespace Framework;


class Security
{

    /**
     * @return bool
     */
    public static function isHttps()
    {
        if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
            return true;
        }

        return false;
    }

    /**
     * Redirect to HTTPS URL
     */
    public static function forceHttps()
    {
        if (!self::isHttps()) {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $redirect);
            die();
        }
    }

    /**
     * Block bots
     */
    public static function blockBots()
    {
        // Block bots
        if (preg_match("/(spider|crawler|slurp|teoma|archive|track|snoopy|lwp|client|libwww)/i", $_SERVER['HTTP_USER_AGENT']) ||
            preg_match("/(havij|libwww-perl|wget|python|nikto|curl|scan|java|winhttp|clshttp|loader)/i", $_SERVER['HTTP_USER_AGENT']) ||
            preg_match("/(%0A|%0D|%27|%3C|%3E|%00)/i", $_SERVER['HTTP_USER_AGENT']) ||
            preg_match("/(;|<|>|'|\"|\)|\(|%0A|%0D|%22|%27|%28|%3C|%3E|%00).*(libwww-perl|wget|python|nikto|curl|scan|java|winhttp|HTTrack|clshttp|archiver|loader|email|harvest|extract|grab|miner)/i", $_SERVER['HTTP_USER_AGENT'])) {
            self::error(403, 'Permission denied!');
        }

        // Block Fake google bot
        $userAgent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        if (preg_match('/googlebot/i', $userAgent, $matches)) {
            $ip     = self::getClientIP();
            $name   = gethostbyaddr($ip);
            $hostIp = gethostbyname($name);
            if (preg_match('/googlebot/i', $name, $matches)) {
                if ($hostIp != $ip) {
                    self::error(403, 'Permission denied!');
                }
            } else {
                self::error(403, 'Permission denied!');
            }
        }
    }

    /**
     * Security Headers
     * @param bool $api
     */
    public static function setSecureHeaders($api = false)
    {
        // Headers
        @header("Accept-Encoding: gzip, deflate");
        @header("Strict-Transport-Security: max-age=16070400; includeSubDomains; preload");
        @header("X-UA-Compatible: IE=edge,chrome=1");
        @header("X-XSS-Protection: 1; mode=block");
        @header("X-Frame-Options: sameorigin");
        @header("X-Content-Type-Options: nosniff");
        @header("X-Permitted-Cross-Domain-Policies: master-only");
        @header("Referer-Policy: origin");
        @header("X-Download-Options: noopen");
        if (!$api) {
            @header("Access-Control-Allow-Methods: GET, POST");
        } else {
            @header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
        }

        header_remove("X-Powered-By");
        header_remove("Server");

        // Php settings
        ini_set('expose_php', 0);
        ini_set('allow_url_fopen', 0);
        ini_set('magic_quotes_gpc', 0);
        ini_set('register_globals', 0);
    }

    /**
     * Get Real IP Address
     * @return string
     */
    public static function getClientIP()
    {
        $headersToLook = [
            'GD_PHP_HANDLER',
            'HTTP_AKAMAI_ORIGIN_HOP',
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_FASTLY_CLIENT_IP',
            'HTTP_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_INCAP_CLIENT_IP',
            'HTTP_TRUE_CLIENT_IP',
            'HTTP_X_CLIENTIP',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_X_FORWARDED',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_IP_TRAIL',
            'HTTP_X_REAL_IP',
            'HTTP_X_VARNISH',
            'HTTP_VIA',
            'REMOTE_ADDR',
        ];
        foreach ($headersToLook as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    // Check for IPv4 IP cast as IPv6
                    if (preg_match('/^::ffff:(\d+\.\d+\.\d+\.\d+)$/', $ip, $matches)) {
                        $ip = $matches[1];
                    }

                    if ($ip == "::1") {
                        $ip = "127.0.0.1";
                    }

                    if ($ip == '127.0.0.1' || self::isPrivateIP($ip)) {
                        $ip = $_SERVER['REMOTE_ADDR'];
                        if ($ip == "::1") {
                            $ip = "127.0.0.1";
                        }

                        return $ip;
                    }

                    if (self::validateIPAddress($ip)) {
                        return $ip;
                    }
                }
            }
        }

        return "0.0.0.0";
    }

    /**
     * Detect if is private IP
     * @param $ip
     * @return bool
     */
    public static function isPrivateIP($ip)
    {
        // Dealing with ipv6, so we can simply rely on filter_var
        if (false === strpos($ip, '.')) {
            return !@filter_var($ip, FILTER_VALIDATE_IP, (FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE));
        }

        $longIp = ip2long($ip);
        // Dealing with ipv4
        $privateIp4Addresses = [
            // single class A network
            '10.0.0.0|10.255.255.255',
            // 16 contiguous class B network
            '172.16.0.0|172.31.255.255',
            // 256 contiguous class C network
            '192.168.0.0|192.168.255.255',
            // Link-local address also referred to as Automatic Private IP Addressing
            '169.254.0.0|169.254.255.255',
            // localhost
            '127.0.0.0|127.255.255.255',
        ];
        if (-1 != $longIp) {
            foreach ($privateIp4Addresses as $priAddr) {
                list ($start, $end) = explode('|', $priAddr);
                if ($longIp >= ip2long($start) && $longIp <= ip2long($end)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Ensures an ip address is both a valid IP and does not fall within
     * a private network range.
     * @param $ip
     * @return bool
     */
    public static function validateIPAddress($ip)
    {
        if ($ip == '' || strtolower($ip) === 'unknown') {
            return false;
        }

        // generate ipv4 network address
        $ip = ip2long($ip);
        // if the ip is set and not equivalent to 255.255.255.255
        if ($ip !== false && $ip !== -1) {
            // make sure to get unsigned long representation of ip
            // due to discrepancies between 32 and 64 bit OSes and
            // signed numbers (ints default to signed in PHP)
            $ip = sprintf('%u', $ip);
            // do private network range checking
            if ($ip >= 0 && $ip <= 50331647) {
                return false;
            }

            if ($ip >= 167772160 && $ip <= 184549375) {
                return false;
            }

            if ($ip >= 2130706432 && $ip <= 2147483647) {
                return false;
            }

            if ($ip >= 2851995648 && $ip <= 2852061183) {
                return false;
            }

            if ($ip >= 2886729728 && $ip <= 2887778303) {
                return false;
            }

            if ($ip >= 3221225984 && $ip <= 3221226239) {
                return false;
            }

            if ($ip >= 3232235520 && $ip <= 3232301055) {
                return false;
            }

            if ($ip >= 4294967040) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param int $saltLength
     * @return string
     * @throws \Exception
     */
    public static function generateSalt($saltLength = 22)
    {
        $base64Digits   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
        $bcrypt64Digits = './ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $base64String   = base64_encode(random_bytes($saltLength));
        $salt           = strtr(rtrim($base64String, '='), $base64Digits, $bcrypt64Digits);
        return $salt;
    }

    /**
     * get CSRF token and generate a new one if expired
     *
     * @access public
     * @static static method
     * @return string
     */
    public static function generateCSRFToken()
    {
        // token is valid for 1 hour
        $maxTime    = (60 * 60 * 1);
        $csrfToken  = Session::get('csrf_token');
        $storedTime = Session::get('csrf_token_time');

        if (($maxTime + $storedTime) <= time() || empty($csrfToken)) {
            Session::set('csrf_token', md5(uniqid(rand(), true)));
            Session::set('csrf_token_time', time());
        }

        return Session::get('csrf_token');
    }

    /**
     * checks if CSRF token in session is same as in the form submitted
     *
     * @access public
     * @static static method
     * @return bool
     */
    public static function isCSRFTokenValid()
    {
        return $_POST['csrf_token'] === Session::get('csrf_token');
    }

    /**
     * Set Cookie
     * @param $name
     * @param $value
     * @param int $expires
     * @param string $path
     * @param null $domain
     * @param bool $secure
     * @param bool $httponly
     * @return bool
     */
    public static function setCookieEncrypted($name, $value, $expires = 2592000, $path = "/", $domain = "", $secure = false, $httponly = false)
    {
        $name        = 'SEC_' . $name;
        $secure      = self::isHttps();
        $cookieValue = Cipher::encrypt($value);

        if (!setcookie($name, $cookieValue, (time() + $expires), $path . "; SameSite=Strict", $domain, $secure, $httponly)) {
            return false;
        }

        $_COOKIE[$name] = $value;
        return true;
    }

    /**
     * Unset Cookie
     * @param $name
     * @return null
     */
    public static function unsetCookie($name)
    {
        if (isset($_COOKIE[$name])) {
            unset($_COOKIE[$name]);
            setcookie($name, null, -1);
        }

        return null;
    }

    /**
     * Get Cookie
     * @param $name
     */
    public static function getCookieDecrypted($name)
    {
        $name = 'SEC_' . $name;
        if (isset($_COOKIE[$name])) {
            return Cipher::decrypt($_COOKIE[$name]);
        }

        return null;
    }

    /**
     * String escape
     * @param $data
     * @return mixed
     */
    public static function escapeSQL($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::escapeSQL($v);
            }
        } else {
            if (!empty($data) && is_string($data)) {
                $search  = [
                    "\\",
                    "\x00",
                    "\n",
                    "\r",
                    "'",
                    '"',
                    "\x1a",
                ];
                $replace = [
                    "\\\\",
                    "\\0",
                    "\\n",
                    "\\r",
                    "\'",
                    '\"',
                    "\\Z",
                ];
                $data    = str_replace($search, $replace, $data);
            }
        }

        return $data;
    }

    /**
     * Attribute escape
     * @param $data
     * @return mixed
     */
    public static function escapeAttr($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::escapeAttr($v);
            }
        } else {
            if (!empty($data) && is_string($data)) {
                $data = htmlentities($data, ENT_QUOTES, ini_get('default_charset'), false);
            }
        }

        return $data;
    }

    /**
     * XSS escape
     * @param $data
     * @return mixed
     */
    public static function escapeXSS($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::escapeXSS($v);
            }
        } else {
            $data = str_replace(["&amp;", "&lt;", "&gt;"], ["&amp;amp;", "&amp;lt;", "&amp;gt;"], $data);
            $data = preg_replace("/(&#*\w+)[- ]+;/u", "$1;", $data);
            $data = preg_replace("/(&#x*[0-9A-F]+);*/iu", "$1;", $data);
            $data = html_entity_decode($data, ENT_COMPAT, "UTF-8");
            $data = preg_replace('#(<[^>]+?[- "\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);
            $data = preg_replace('#([a-z]*)[- ]*=[- ]*([`\'"]*)[- ]*j[- ]*a[- ]*v[- ]*a[- ]*s[- ]*c[- ]*r[- ]*i[- ]*p[- ]*t[- ]*:#iu', '$1=$2nojavascript', $data);
            $data = preg_replace('#([a-z]*)[- ]*=([\'"]*)[- ]*v[- ]*b[- ]*s[- ]*c[- ]*r[- ]*i[- ]*p[- ]*t[- ]*:#iu', '$1=$2novbscript', $data);
            $data = preg_replace('#([a-z]*)[- ]*=([\'"]*)[- ]*-moz-binding[- ]*:#u', '$1=$2nomozbinding', $data);
            $data = preg_replace('#(<[^>]+?)style[- ]*=[- ]*[`\'"]*.*?expression[- ]*\([^>]*+>#i', '$1>', $data);
            $data = preg_replace('#(<[^>]+?)style[- ]*=[- ]*[`\'"]*.*?behaviour[- ]*\([^>]*+>#i', '$1>', $data);
            $data = preg_replace('#(<[^>]+?)style[- ]*=[- ]*[`\'"]*.*?s[- ]*c[- ]*r[- ]*i[- ]*p[- ]*t[- ]*:*[^>]*+>#iu', '$1>', $data);
            $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);
            do {
                $oldData = $data;
                $data    = preg_replace("#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml|eval|svg|video|math|keygen)[^>]*+>#i", "", $data);
            } while ($oldData !== $data);
            $data = str_replace(chr(0), '', $data);
            $data = preg_replace('%&\s*\{[^}]*(\}\s*;?|$)%', '', $data);
            //$data = str_replace('&', '&amp;', $data);
            $data = preg_replace('/&amp;#([0-9]+;)/', '&#\1', $data);
            $data = preg_replace('/&amp;#[Xx]0*((?:[0-9A-Fa-f]{2})+;)/', '&#x\1', $data);
            $data = preg_replace('/&amp;([A-Za-z][A-Za-z0-9]*;)/', '&\1', $data);
        }

        return $data;
    }

    /**
     * Escape slashes and new lines
     *
     * @param $text
     * @param bool $escapeHtml
     * @param bool $allowBasicFormatTags
     * @param null $allowedTagsArray
     * @return int|mixed|string
     */
    public static function escapeString($text, $escapeHtml = true, $allowBasicFormatTags = false, $allowedTagsArray = null)
    {
        if (!is_string($text)) {
            return $text;
        }

        $text = trim($text);

        if ($escapeHtml) {
            if (!is_array($allowedTagsArray)) {
                $allowedTagsArray = [];
            }

            // if asking to allow basic formating tags
            $customArray = [];
            if ($allowBasicFormatTags) {
                $customArray      = [
                    '<p>',
                    '<span>',
                    '<strong>',
                    '<br>',
                    '<br />',
                    '<hr>',
                    '<hr />',
                    '<em>',
                    '<a>',
                    '<u>',
                    '<ul>',
                    '<ol>',
                    '<li>',
                    '<table>',
                    '<tr>',
                    '<td>',
                    '<tbody>',
                    '<caption>',
                    '<h1>',
                    '<h2>',
                    '<h3>',
                    '<h4>',
                    '<h5>',
                    '<h6>',
                    '<blockquote>',
                    '<pre>',
                    '<address>',
                    '<div>',
                ];
                $allowedTagsArray = array_merge($allowedTagsArray, $customArray);
            }

            // if any tags are to be allowed

            if (is_array($allowedTagsArray)) {
                $allowedTags = implode('', $allowedTagsArray);
            }

            // custom filters

            if (strstr(strtolower($text), '"-->')) {
                $text = stripos($text, '"-->');
            }

            $text = str_replace(
                "<p>
	&nbsp;</p>",
                '',
                $text
            );
            // text coming from ck editor if any.
            // strip tags

            if ($allowedTags != '') {
                $text = addslashes(strip_tags($text, $allowedTags));
            } else {
                $text = nl2br(htmlspecialchars(strip_tags($text), ENT_QUOTES));
            }

            return $text;
        } else {
            return addslashes(nl2br($text));
        }
    }

    /**
     * Restores string escaped with escapeString
     *
     * @param $text
     * @param bool $replaceHtml
     * @param bool $preserveBreakTags
     * @return string
     */
    public static function restoreString($text, $replaceHtml = false, $preserveBreakTags = false)
    {
        if (!is_string($text)) {
            return '';
        }

        if ($replaceHtml && $preserveBreakTags) {
            return stripslashes(html_entity_decode($text, ENT_QUOTES));
        } else if ($replaceHtml && !$preserveBreakTags) {
            return stripslashes(self::br2nl(html_entity_decode($text, ENT_QUOTES)));
        } else {
            return stripslashes(self::br2nl($text));
        }
    }

    /**
     * Strip tags recursive
     * @param $data
     * @return mixed
     */
    public static function stripTags($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::stripTags($v);
            }
        } else {
            $data = trim(strip_tags($data));
        }

        return $data;
    }

    /**
     * Trim recursive
     * @param $data
     * @return mixed
     */
    public static function trim($data)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::trim($v);
            }
        } else {
            $data = trim($data);
        }

        return $data;
    }

    /**
     * Strip tags and contents recursive
     * @param $data
     * @param string $tags
     * @param bool $invert
     * @return array|string
     */
    public static function stripTagsContent($data, $tags = '', $invert = false)
    {
        if (is_array($data)) {
            foreach ($data as $k => $v) {
                $data[$k] = self::stripTagsContent($v, $tags, $invert);
            }
        } else {
            preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
            $tags = array_unique($tags[1]);
            if (is_array($tags) and count($tags) > 0) {
                if ($invert == false) {
                    $data = preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $data);
                } else {
                    $data = preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $data);
                }
            } else if ($invert == false) {
                $data = preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $data);
            }
        }

        return self::stripTags($data);
    }

    /**
     * Generate strong password
     * @param int $length
     * @param string $availableSets
     * @return bool|string
     */
    public static function generatePassword($length = 8, $availableSets = 'luns')
    {
        $sets = [];
        // lowercase
        if (strpos($availableSets, 'l') !== false) {
            $sets[] = 'abcdefghjkmnpqrstuvwxyz';
        }

        // uppercase
        if (strpos($availableSets, 'u') !== false) {
            $sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
        }

        // numbers
        if (strpos($availableSets, 'n') !== false) {
            $sets[] = '0123456789';
        }

        // special chars
        if (strpos($availableSets, 's') !== false) {
            $sets[] = '_-=+!@#$%&*?/';
        }

        $all      = '';
        $password = '';
        foreach ($sets as $set) {
            $password .= $set[array_rand(str_split($set))];
            $all      .= $set;
        }

        $all = str_split($all);
        for ($i = 0; $i < ($length - count($sets)); $i++) {
            $password .= $all[array_rand($all)];
        }

        $password = str_shuffle($password);
        return $password;
    }

    /**
     * Return password strength score from 0 to 10 (under 6 is a bad score)
     * @param $password
     * @return int
     */
    public static function passwordStrength($password)
    {
        $score     = 0;
        $maxScore  = 10;
        $uppercase = preg_match('/[A-Z]/', $password);
        $lowercase = preg_match('/[a-z]/', $password);
        $number    = preg_match('/[0-9]/', $password);
        $special1  = preg_match('/[\-\_\=\+\&\!\?\;\.\,]/', $password);
        $special2  = preg_match('/[\#\%\@\*\\\'\>\>\\\\\/\$\[\]\(\)\{\}\|]/', $password);
        $special3  = preg_match('/[\^\`\~\±\§]/', $password);
        // Length
        if (strlen($password) >= 6) {
            $score++;
        }

        if (strlen($password) >= 8) {
            $score++;
        }

        if (strlen($password) >= 10) {
            $score++;
        }

        if (strlen($password) >= 12) {
            $score++;
        }

        // Chars
        if (strlen(count_chars($password, 3)) == strlen($password)) {
            $score += 2;
        } else if (strlen(count_chars($password, 3)) > (strlen($password) / 1.5)) {
            $score += 1;
        }

        if (strlen(count_chars($password, 3)) == 1) {
            $score = 1;
        }

        // Chars case and type
        if ($uppercase) {
            $score++;
        } else if ($score > 3) {
            $score -= 2;
        } else if ($score > 2) {
            $score--;
        }

        if ($lowercase) {
            $score++;
        } else if ($score > 3) {
            $score -= 2;
        } else if ($score > 2) {
            $score--;
        }

        if ($number) {
            $score++;
        } else if ($score > 3) {
            $score -= 2;
        } else if ($score > 2) {
            $score--;
        }

        if ($special2) {
            $score += 2;
        } else if ($special1) {
            $score++;
        }

        if ($special3) {
            $score += 3;
        }

        // Special cases
        if ($score > 6 && strlen($password) < 4) {
            return 2;
        } else if ($score > 6 && strlen($password) < 5) {
            return 3;
        } else if ($score > 6 && strlen($password) < 6) {
            return 5;
        } else if ($score > $maxScore) {
            return $maxScore;
        }

        if ($score < 0) {
            return 0;
        }

        return $score;
    }

    /**
     * Hash password
     * @param $password
     * @param $cost (4-30)
     * @return bool|null|string
     */
    public static function passwordHash($password, $cost = 10)
    {
        if (!function_exists('crypt')) {
            return false;
        }

        if ($password == '' || is_int($password)) {
            $password = (string) $password;
        }

        if ($cost < 4 || $cost > 31) {
            trigger_error(sprintf("Invalid bcrypt cost parameter specified: %d", $cost), E_USER_WARNING);
            return null;
        }

        $hashFormat   = sprintf("$2y$%02d$", $cost);
        $resultLength = 60;
        $salt         = Cipher::generateSalt();
        $hash         = $hashFormat . $salt;
        $ret          = crypt($password, $hash);
        if (!is_string($ret) || strlen($ret) != $resultLength) {
            return false;
        }

        return $ret;
    }

    /**
     * Verify password
     * @param $password
     * @param $hash
     * @return bool
     */
    public static function passwordVerify($password, $hash)
    {
        if (!function_exists('crypt')) {
            return false;
        }

        $ret = crypt($password, $hash);
        if (!is_string($ret) || strlen($ret) != strlen($hash) || strlen($ret) <= 13) {
            return false;
        }

        $status = 0;
        for ($i = 0; $i < strlen($ret); $i++) {
            $status |= (ord($ret[$i]) ^ ord($hash[$i]));
        }

        return ($status === 0);
    }

    /**
     * Create a GUID
     * @return string
     */
    public static function generateGUID()
    {
        $microtime       = microtime();
        list($dec, $sec) = explode(' ', $microtime);
        $decHex          = dechex($dec * 1000000);
        $secHex          = dechex($sec);
        $decHex          = (strlen($decHex) <= 5) ? str_pad($decHex, 5, '0') : substr($decHex, 0, 5);
        $secHex          = (strlen($secHex) <= 6) ? str_pad($secHex, 6, '0') : substr($secHex, 0, 6);
        // Section 1 (length 8)
        $guid = $decHex;
        for ($i = 0; $i < 3; ++$i) {
            $guid .= dechex(mt_rand(0, 15));
        }

        $guid .= '-';
        // Section 2 (length 4)
        for ($i = 0; $i < 4; ++$i) {
            $guid .= dechex(mt_rand(0, 15));
        }

        $guid .= '-';
        // Section 3 (length 4)
        for ($i = 0; $i < 4; ++$i) {
            $guid .= dechex(mt_rand(0, 15));
        }

        $guid .= '-';
        // Section 4 (length 4)
        for ($i = 0; $i < 4; ++$i) {
            $guid .= dechex(mt_rand(0, 15));
        }

        $guid .= '-';
        // Section 5 (length 12)
        $guid .= $secHex;
        for ($i = 0; $i < 6; ++$i) {
            $guid .= dechex(mt_rand(0, 15));
        }

        return $guid;
    }

    /**
     * Generate short GUID
     *
     * @param string $passKey
     * @return string
     */
    public static function generateShortGUID($passKey = '8HU9BBSUIW82N')
    {
        $in    = (str_replace('.', '', microtime(true)) * rand(0, 9999));
        $out   = '';
        $index = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base  = strlen($index);
        $i     = $p = [];
        $padUp = $toNum = '';

        if ($passKey != '') {
            for ($n = 0; $n < strlen($index); $n++) {
                $i[] = substr($index, $n, 1);
            }

            $passHash = hash('sha256', $passKey);
            $passHash = (strlen($passHash) < strlen($index) ? hash('sha512', $passKey) : $passHash);

            for ($n = 0; $n < strlen($index); $n++) {
                $p[] = substr($passHash, $n, 1);
            }

            array_multisort($p, SORT_DESC, $i);
            $index = implode($i);
        }

        if ($toNum) {
            // Digital number <<-- alphabet letter code
            $len = (strlen($in) - 1);

            for ($t = $len; $t >= 0; $t--) {
                $bcp = \bcpow($base, ($len - $t));
                $out = ($out + strpos($index, substr($in, $t, 1)) * $bcp);
            }

            if (is_numeric($padUp)) {
                $padUp--;

                if ($padUp > 0) {
                    $out -= pow($base, $padUp);
                }
            }
        } else {
            // Digital number -->> alphabet letter code
            if (isset($padUp) && is_numeric($padUp)) {
                $padUp--;

                if ($padUp > 0) {
                    $in += pow($base, $padUp);
                }
            }

            for ($t = ($in != 0 ? ceil(log($in, $base)) : 0); $t >= 0; $t--) {
                $bcp = \bcpow($base, $t);
                $a   = (floor($in / $bcp) % $base);
                $out = $out . substr($index, $a, 1);
                $in  = ($in - ($a * $bcp));
            }
        }

        return $out . rand(0, 9999);
    }

    /**
     * Custom Error
     * @param integer $code
     * @param string $message
     * @param string $title
     * @return bool
     */
    public static function error($code = 404, $message = "Not found!", $title = 'Error')
    {
        if (empty(self::$errorCallback)) {
            ob_clean();
            http_response_code($code);
            $output = str_replace('${ERROR_TITLE}', $title, self::$errorTemplate);
            $output = str_replace('${ERROR_BODY}', $message, $output);
            die($output);
        }

        call_user_func(self::$errorCallback, $code, $message, $title);
        return true;
    }
}
