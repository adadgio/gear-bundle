<?php

namespace Adadgio\GearBundle\Component\Http;

/**
 * The URI helper to easily manipulate domains,
 * protocols, url and uri strings.
 */
class Uri
{
    /**
     * Check if the input url is a absolute remote address.
     *
     * @param  string  Input url or uri
     * @return boolean Return value
     * @deprecated Use isAbsolute instead
     */
    public static function isRemoteUrl($url)
    {
        $parts = parse_url($url);

        return (
            isset($parts['scheme'])
            && in_array($parts['scheme'], array('http', 'https', 'ftp', 'sftp'))
        ) ? true : false;
    }

    /**
     * Check if the input url is an absolute link.
     *
     * @param  string  Input url or uri
     * @return boolean Return value
     */
    public static function isAbsolute($url)
    {
        $parts = parse_url($url);

        return (
            isset($parts['scheme'])
            && in_array($parts['scheme'], array('http', 'https', 'ftp', 'sftp'))
        ) ? true : false;
    }

    /**
     * Check if the input url is a relative link.
     *
     * @param  string  Input url or uri
     * @return boolean Return value
     */
    public static function isRelative($url)
    {
        return (!self::isAbsolute($url));
    }

    /**
     * Check if the absolute url uses SSL.
     *
     * @param  string  Input url or uri
     * @return boolean Return value
     */
    public static function isHttps($url)
    {
        $parts = parse_url($url);
        return ($parts['scheme'] === 'https') ? true : false;
    }
}
