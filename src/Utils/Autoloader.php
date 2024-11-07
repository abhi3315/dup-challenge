<?php

namespace DupChallenge\Utils;

/**
 * Autoloader calss
 */
final class Autoloader
{
    const ROOT_NAMESPACE = 'DupChallenge\\';

    /**
     * Register autoloader function
     *
     * @return void
     */
    public static function register()
    {
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    /**
     * Load class
     *
     * @param string $className class name
     *
     * @return bool return true if class is loaded
     */
    public static function load($className)
    {
        foreach (self::getNamespacesMapping() as $namespace => $mappedPath) {
            if (strpos($className, $namespace) !== 0) {
                continue;
            }

            $filepath = $mappedPath . str_replace('\\', '/', substr($className, strlen($namespace))) . '.php';
            if (file_exists($filepath)) {
                include_once $filepath;
                return true;
            }
        }

        return false;
    }

    /**
     * Return namespace mapping
     *
     * @return string[]
     */
    protected static function getNamespacesMapping()
    {
        // the order is important, it is necessary to insert the longest namespaces first
        return array(
            self::ROOT_NAMESPACE => DUP_CHALLENGE_PATH . '/src/'
        );
    }
}
