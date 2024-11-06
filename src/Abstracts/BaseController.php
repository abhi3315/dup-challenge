<?php

namespace DupChallenge\Abstracts;

abstract class BaseController
{
	/**
	 * Instances
	 *
	 * @var static[]
	 */
    private static $instances = array();

    /**
     * Get instance
     *
     * @return static
     */
    public static function getController()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    /**
     * Class constructor
     */
    abstract protected function __construct();
}
