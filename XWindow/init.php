<?php
date_default_timezone_set('PRC');
define('TIME', time());
!defined('ROOT') && define('ROOT', str_replace("\\", "/", dirname(__FILE__)) . '/');

function showError() {
    ini_set("display_errors", "On"); //打开错误提示
    ini_set("error_reporting", E_ALL&~E_NOTICE); //显示所有错误
}

//__autoload方法
function i_autoload($className) {
    if (is_int(strripos($className, '..'))) {
        return;
    }
    $file = ROOT . 'lib/' . $className . '.php';
    if (file_exists($file)) {
        include $file;
    }
}
spl_autoload_register('i_autoload');

!defined('FILE_FLAGS') && define('FILE_FLAGS', LOCK_EX);
/**
* config('name');
* config('name@file');
* config('@file');
*/
if (!function_exists('config')) {
    !defined('CONFIG_PATH') && define('CONFIG_PATH', ROOT . 'config/');
    function config($key) {
        static $configs = array();
        list($key, $file) = explode('@', $key, 2);
        $file = empty($file) ? 'base' : $file;

        $file_name = CONFIG_PATH . $file . '.php';
        //读取配置
        if (empty($configs[$file]) AND file_exists($file_name)) {
            $configs[$file] = @include $file_name;

            // foreach ($configs[$file] as $key => $value) {
            //     !defined('CONFIG_') && define('CONFIG_PATH', ROOT . 'config/');
            // }
        }
        if (func_num_args() === 2) {
            $value = func_get_arg(1);
            //写入配置
            if (!empty($key)) {
                $configs[$file] = (array) $configs[$file];
                if (is_null($value)) {
                    unset($configs[$file][$key]);
                } else {
                    $configs[$file][$key] = $value;
                }
            } else {
                if (is_null($value)) {
                    return unlink($file_name);
                } else {
                    $configs[$file] = $value;
                }
            }
            file_put_contents($file_name, "<?php return " . var_export($configs[$file], true) . ";", FILE_FLAGS);
        } else {
            //返回结果
            if (!empty($key)) {
                return $configs[$file][$key];
            }

            return $configs[$file];
        }
    }

    $config = config;
    global $config; // 可以通过 $GLOBALS['config']() 调用

    !defined('ROOT_PATH') && define('ROOT_PATH', config("site_root_path"));
}

if (!function_exists('db')) {
    function db($table) {
        return db::table($table);
    }
}

if (!function_exists('view')) {
    function view($file, $set = null) {
        return view::load($file, $set = null);
    }
}

if (!function_exists('_')) {
    function _($str) {
        return htmlspecialchars($str);
    }
}

if (!function_exists('e')) {
    function e($str) {
        echo $str;
    }
}

if (!function_exists('str_is')) {
    function str_is($pattern, $value) {
        if (is_null($pattern)) {
            $patterns = [];
        }
        $patterns = ! is_array($pattern) ? [$pattern] : $pattern;
        if (empty($patterns)) {
            return false;
        }
        foreach ($patterns as $pattern) {
            if ($pattern == $value) {
                return true;
            }
            $pattern = preg_quote($pattern, '#');
            $pattern = str_replace('\*', '.*', $pattern);
            if (preg_match('#^'.$pattern.'\z#u', $value) === 1) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('get_domain')) {
    function get_domain($url = null) {
        if (is_null($url)) {
            return $_SERVER['HTTP_HOST'];
        }
        return strstr(ltrim(strstr($url, '://'), '://'), '/', true);
    }
}

function get_absolute_path($path) {
    $path = str_replace(array('/', '\\', '//'), '/', $path);
    $parts = array_filter(explode('/', $path), 'strlen');
    $absolutes = array();
    foreach ($parts as $part) {
        if ('.' == $part) continue;
        if ('..' == $part) {
            array_pop($absolutes);
        } else {
            $absolutes[] = $part;
        }
    }
    return str_replace('//', '/', '/'.implode('/', $absolutes).'/');
}

function checkLogin($name, $key, $if_md5) {
    if (empty($name) || empty($key)) return false;
    // 重设密码
    // sqlite::load('users')->update('password',md5($key),'username','test');
    $hhh = sqlite::load('users')->getOnce('username', $name);
    if ($if_md5) $key = md5($key);
    // echo $hhh['password'] . ' ' . $hhh['username'] . '<br>' . md5($key) . '<br>';
    if ($hhh['password'] == $key) return $hhh;
    else return false;
}

function routeForbidden($first) {
    if (strstr($first, '/api/')) {
        return route::any($first, 'IndexController@Forbidden');
    } else {
        return route::any($first, 'LoginController@index');
    }
}

function route($first, $second, $role = 0) {
    $login = checkLogin($_COOKIE['user'], $_COOKIE['password'], false);
    if ($login == false) {
        return routeForbidden($first);
    }
    if ($login['role'] == 1 || $role == 0) {
        return route::any($first, $second);
    } else {
        return route::any($first, 'IndexController@Forbidden');
    }
}

function clientRoute($callback) {
    if (strstr($_SERVER['HTTP_XWINDOW_CLIENT'], "XWindowRequest") or strstr($_POST['XWindow-Client'], "XWindowRequest")) {
        return $callback();
    }
}

!defined('CONTROLLER_PATH') && define('CONTROLLER_PATH', ROOT_PATH.'/controller/');
define('VIEW_PATH', ROOT_PATH.'/view/themes/');

if(sqlite::load('settings')->getOnce('key', 'develop')['value']) showError();

// Route Area.
route::any('/jump', function(){
    return view::load('jump', null, 'XWindow/view/themes/')->with("_GET", $_GET);
});
route::any('/', function(){
    return view::load('layout', null, 'XWindow/view/themes/')->with('title', '首页');
});
route::any("/XWindow/api/dialog/settings", function(){
    return view::load('dialog/settingsDialog', null, 'XWindow/view/themes/');
});
