<?php

namespace DupChallenge\Traits;

/**
 * Singleton trait
 */
trait SingletonTrait
{

    /**
     * Protected class constructor to prevent direct object creation
     */
    protected function __construct()
    {
    }

    /**
     * Prevent object cloning
     */
    final protected function __clone()
    {
    }

    /**
	 * Get the singleton instance of the class.
     *
     * @return object Singleton instance of the class.
     */
    final public static function getInstance()
    {

        /**
         * Collection of instance.
         *
         * @var array
         */
        static $instance = [];

        /**
		 * Get the called class name.
         */
        $called_class = get_called_class();

        if (! isset($instance[ $called_class ]) ) {

            $instance[ $called_class ] = new $called_class();

            /**
             * Dependent items can use the dup_challenge_singleton_init_{$called_class} hook to execute code
             */
            do_action(sprintf('dup_challenge_singleton_init_%s', $called_class));

        }

        return $instance[ $called_class ];
    }
}
