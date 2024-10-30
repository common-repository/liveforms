<?php
/**
 * User: shahnuralam
 * Date: 01/11/18
 * Time: 7:08 PM
 * From v4.7.9
 * Last Updated: 10/11/2018
 */

namespace LiveForms\__;

class Session
{
    static $data;
    static $deviceID;
    static $store = 'file';

    function __construct()
    {

        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $deviceID = md5(self::clientIP() . $agent);
        self::$deviceID = $deviceID;

        if (self::$store === 'file') {
            if (file_exists(LF_BASE_DIR . "cache/session-{$deviceID}.txt")) {
                $data = file_get_contents(LF_BASE_DIR . "cache/session-{$deviceID}.txt");
                $data = Crypt::decrypt($data, true);
                if (!is_array($data)) $data = array();
            } else {
                $data = array();
            }

            self::$data = $data;

            register_shutdown_function(array($this, 'saveSession'));
        }
    }

    static function clientIP()
    {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

    static function deviceID($deviceID)
    {
        self::$deviceID = $deviceID;
    }

    static function set($name, $value, $expire = 1800)
    {
        global $wpdb;
        //if(self::$store === 'cookie') setcookie($name, Crypt::encrypt($value), time() + $expire, '/');
        if (self::$store === 'file') self::$data[$name] = array('value' => $value, 'expire' => time() + $expire);
        if (self::$store === 'db') $wpdb->insert("{$wpdb->prefix}liveforms_sessions", array('deviceID' => self::$deviceID, 'name' => $name, 'value' => maybe_serialize($value), 'expire' => time() + $expire));
    }

    static function get($name)
    {
        /*if(self::$store === 'cookie') {
            $value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : '';
            $value = Crypt::decrypt($value);
        }*/
        if (self::$store === 'file') {
            if (!isset(self::$data[$name])) return null;
            $_value = self::$data[$name];
            if (count($_value) == 0) return null;
            extract($_value);
            if (isset($expire) && $expire < time()) {
                unset(self::$data[$name]);
                $value = null;
            }
        }
        if (self::$store === 'db') {
            global $wpdb;
            $deviceID = self::$deviceID;
            $value = $wpdb->get_var("select `value` from {$wpdb->prefix}liveforms_sessions where deviceID = '{$deviceID}' and `name` = '{$name}'");
        }
        return maybe_unserialize($value);

    }

    static function clear($name = '')
    {
        global $wpdb;
        if ($name == '') {
            if (self::$store === 'file') self::$data = array();
            if (self::$store === 'db') $wpdb->delete("{$wpdb->prefix}liveforms_sessions", array('deviceID' => self::$deviceID));
        } else {
            //if(self::$store === 'cookie') setcookie($name, null, '/', time() - 3600);
            if (self::$store === 'file' && isset(self::$data[$name])) unset(self::$data[$name]);
            if (self::$store === 'db') $wpdb->delete("{$wpdb->prefix}liveforms_sessions", array('deviceID' => self::$deviceID, 'name' => $name));
        }
    }

    static function show()
    {
        echo "<pre>";
        print_r(self::$data);
        echo "</pre>";
    }

    static function saveSession()
    {
        if (self::$store === 'file' && is_array(self::$data)) {
            $data = Crypt::encrypt(self::$data);
            if (!file_exists(LF_BASE_DIR . 'cache')) {
                mkdir(LF_BASE_DIR . 'cache', 0755);
            }
            file_put_contents(LF_BASE_DIR . 'cache/session-' . self::$deviceID . '.txt', $data);
        }

    }

}

new Session();

