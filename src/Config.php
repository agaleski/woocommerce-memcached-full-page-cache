<?php
/*
    WooCommerce Memcached Full Page Cache - FPC the WooCommerece way via PHP-Memcached.
    Copyright (C)  2019 Achim Galeski ( achim@invinciblebrands.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 3, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA02110-1301USA
*/

namespace InvincibleBrands\WcMfpc;


if (! defined('ABSPATH')) { exit; }

/**
 * Class Config
 *
 * @package InvincibleBrands\WcMfpc
 */
class Config
{

    /**
     * @var string
     */
    public $hosts                   = '127.0.0.1:11211';

    /**
     * @var bool
     */
    public $memcached_binary        = true;

    /**
     * @var string
     */
    public $authpass                = '';

    /**
     * @var string
     */
    public $authuser                = '';

    /**
     * @var int
     */
    public $expire                  = 86400;

    /**
     * @var int
     */
    public $browsercache            = 14400;

    /**
     * @var string
     */
    public $prefix_meta             = 'meta-';

    /**
     * @var string
     */
    public $prefix_data             = 'data-';

    /**
     * @var string
     */
    public $charset                 = 'utf-8';

    /**
     * @var bool
     */
    public $cache_loggedin          = true;

    /**
     * @var string
     */
    public $nocache_cookies         = '';

    /**
     * @var string
     */
    public $nocache_woocommerce_url = 'checkout|my-account|cart|wc-|wc_';

    /**
     * @var string
     */
    public $nocache_url             = 'wp-|wp_|contact|track-your-order|cart|checkout|my-account|addons|removed|gdsr|wp_rg|woo_braintreecw';

    /**
     * @var bool
     */
    public $response_header         = true;

    /**
     * @var bool
     */
    public $comments_invalidate     = true;

    /**
     * @var bool
     */
    public $pingback_header         = false;

    /**
     * @var Config[]string|array
     */
    public $global               = [];

    /**
     * Config constructor.
     *
     * @param array $config  Optional array with config to be set.
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
    }

    /**
     * Processes a given Config Array and sets its contents for all keys which are a known attribute of this
     * Config::class.
     *
     * @param array $config
     *
     * @return Config $this
     */
    public function setConfig($config = [])
    {
        if (empty($config) || ! is_array($config)) {

            return $this;
        }

        foreach ($config as $key => $value) {

            if (property_exists(self::class, $key)) {

                $this->{$key} = trim(esc_attr($value));

            }

        }

        return $this;
    }

    /**
     * Returns an array of this Config::class
     *
     * @return Config|array
     */
    public function getConfig()
    {
        $config = (array) $this;
        unset($config[ 'global' ]);

        return $config;
    }

    /**
     * @return Config|array
     */
    public static function getDefaultConfig()
    {
        $default = new Config();

        return $default->getConfig();
    }

    /**
     * Returns the "Global Config Key" = HTTP_HOST.
     *
     * @return string
     */
    public static function getGlobalConfigKey()
    {
        if (empty($_SERVER[ 'HTTP_HOST' ])) {

            return '127.0.0.1';
        }

        return $_SERVER[ 'HTTP_HOST' ];
    }

    /**
     * Loads options stored in the DB. Keeps defaults if loading was not successful.
     *
     * @param bool $loadFromDatabase  Optional parameter to force loading from DB.
     *
     * @todo Button to optionally force loading settings from DB has to be implemented in Admin!
     *
     * @return void
     */
    public function load($loadFromDatabase = false)
    {
        global $wc_mfpc_config_array;

        $options = $wc_mfpc_config_array;

        if (empty($options) || $loadFromDatabase) {

            $options = get_site_option(Data::globalOption);

        }

        $this->global = $options;

        if (isset($options[ self::getGlobalConfigKey() ])) {

            $this->setConfig($options[ self::getGlobalConfigKey() ]);

        }
    }

    /**
     * Saves the actual Config in this object as array in the DB.
     *
     * @return bool
     */
    public function save()
    {
        $options = get_site_option(Data::globalOption, []);
        $options[ self::getGlobalConfigKey() ] = $this->getConfig();

        $this->global = $options;

        return update_site_option(Data::globalOption, $options);
    }

    /**
     * Saves the actual this Config object as array in the DB.
     *
     * @return bool
     */
    public function delete()
    {
        $options = get_site_option(Data::globalOption, []);

        if (isset($options[ Config::getGlobalConfigKey() ])) {

            unset($options[ Config::getGlobalConfigKey() ]);

        }

        $this->global = $options;

        return update_site_option(Data::globalOption, $options);
    }

    /**
     * @return string
     */
    public function getHosts()
    {
        return $this->hosts;
    }

    /**
     * @return bool
     */
    public function isMemcachedBinary()
    {
        return $this->memcached_binary;
    }

    /**
     * @return string
     */
    public function getAuthpass()
    {
        return $this->authpass;
    }

    /**
     * @return string
     */
    public function getAuthuser()
    {
        return $this->authuser;
    }

    /**
     * @return int
     */
    public function getExpire()
    {
        return $this->expire;
    }

    /**
     * @return int
     */
    public function getBrowsercache()
    {
        return $this->browsercache;
    }

    /**
     * @return string
     */
    public function getPrefixMeta()
    {
        return $this->prefix_meta;
    }

    /**
     * @return string
     */
    public function getPrefixData()
    {
        return $this->prefix_data;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * @return bool
     */
    public function isCacheLoggedin()
    {
        return $this->cache_loggedin;
    }

    /**
     * @return string
     */
    public function getNocacheCookies()
    {
        return $this->nocache_cookies;
    }

    /**
     * @return string
     */
    public function getNocacheWoocommerceUrl()
    {
        return $this->nocache_woocommerce_url;
    }

    /**
     * Determines the RegEx to exclude dynamic woocommerce pages.
     *
     * @param string $nocache_woocommerce_url
     */
    public function setNocacheWoocommerceUrl(string $nocache_woocommerce_url = '')
    {
        $this->nocache_woocommerce_url = $nocache_woocommerce_url;

        if (empty($nocache_woocommerce_url) && class_exists('WooCommerce')) {

            $toReplace             = [ home_url(), '/' ];
            $replaceWith           = [ '', '', ];
            $pageWcCheckout        = str_replace($toReplace, $replaceWith, wc_get_page_permalink('checkout'));
            $pageWcMyaccount       = str_replace($toReplace, $replaceWith, wc_get_page_permalink('myaccount'));
            $pageWcCart            = str_replace($toReplace, $replaceWith, wc_get_page_permalink('cart'));
            $noCacheWooCommerceUrl = $pageWcCheckout . '|' . $pageWcMyaccount . '|' . $pageWcCart . '|wc-|wc_';

            $this->nocache_woocommerce_url = $noCacheWooCommerceUrl;

        }
    }

    /**
     * @return string
     */
    public function getNocacheUrl()
    {
        return $this->nocache_url;
    }

    /**
     * @return bool
     */
    public function isResponseHeader()
    {
        return $this->response_header;
    }

    /**
     * @return bool
     */
    public function isCommentsInvalidate()
    {
        return $this->comments_invalidate;
    }

    /**
     * @return bool
     */
    public function isPingbackHeader()
    {
        return $this->pingback_header;
    }

    /**
     * @return array
     */
    public function getGlobal()
    {
        return $this->global;
    }

}