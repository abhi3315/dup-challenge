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
     * @return static The singleton instance of the called class.
     */
    final public static function getInstance()
    {
        /**
         * Static array to hold the singleton instances of the called classes.
         *
         * @var array
         */
        static $instances = [];

        /**
         * Get the called class name.
         */
        $calledClass = get_called_class();

        if (! isset($instances[ $calledClass ]) ) {

            $instances[ $calledClass ] = new $calledClass();

            /**
             * Dependent items can use the dup_challenge_singleton_init_{$calledClass} hook to execute code
             */
            do_action(sprintf('dup_challenge_singleton_init_%s', $calledClass));

        }

        return $instances[ $calledClass ];
    }
}
