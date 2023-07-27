<?php
class cache {
	static function load($prefix, $host = null, $port = null) {
		extension_loaded('memcached') or die('memcached扩展未安装！');

		if ($host == null) $host = $GLOBALS['config']("memcached_host");
		if ($port == null) $port = $GLOBALS['config']("memcached_port");

        $cache = new Memcached();
        $cache->addServer($host, $port) or die('memcached连接失败！');
		$cache->setOption(Memcached::OPT_PREFIX_KEY, "tl-".$prefix);
		return $cache;
	}
}