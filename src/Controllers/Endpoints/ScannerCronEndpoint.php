<?php

namespace DupChallenge\Controllers\Endpoints;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;
use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\RestEndpointInterface;
use DupChallenge\Controllers\Crons\DirectoryScannerCron;

/**
 * Scanner cron endpoint
 */
class ScannerCronEndpoint implements RestEndpointInterface
{
    use SingletonTrait;

    /**
     * The endpoint
     *
     * @var string
     */
    private $route = '/scanner-cron';

    /**
     * Register the endpoint
     *
     * @return void
     */
    public function register()
    {
        register_rest_route(self::ENDPOINT_NAMESPACE, $this->route, [
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => [$this, 'handleRequest'],
                'permission_callback' => [$this, 'permissionCallback'],
            ],
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => [$this, 'handleRequest'],
                'permission_callback' => [$this, 'permissionCallback'],
                'args' => [
                    'interval' => [
                        'type' => 'integer',
                        'default' => 1,
                        'required' => false,
                        'validate_callback' => [$this, 'validateInterval']
                    ],
                    'enabled' => [
                        'type' => 'boolean',
                        'required' => true,
                        'validate_callback' => 'rest_validate_request_arg'
                    ]
                ]
            ]
        ]);
    }

    /**
     * Handle a request
     *
     * @param WP_REST_Request $request The request object
     *
     * @return WP_REST_Response The response object
     */
    public function handleRequest(WP_REST_Request $request)
    {
        if ($request->get_method() === 'GET') {
            $interval = DirectoryScannerCron::getInstance()->getCronInterval();
            $enabled = DirectoryScannerCron::getInstance()->isCronEnabled();

            return new WP_REST_Response([
                'interval' => $interval,
                'enabled' => $enabled
            ], 200);
        }

        $interval = $request->get_param('interval');
        $enabled = $request->get_param('enabled');

        DirectoryScannerCron::getInstance()->setCronInterval($interval);

        if (true === $enabled) {
            DirectoryScannerCron::getInstance()->schedule();
        } else {
            DirectoryScannerCron::getInstance()->unschedule();
        }

        return new WP_REST_Response(null, 200);
    }

    /**
     * Permission callback
     *
     * @return bool|WP_Error True if the user has permission, WP_Error object otherwise
     */
    public function permissionCallback()
    {
        return current_user_can('manage_options');
    }

    /**
     * Validate interval
     *
     * @param mixed $value The value to validate
     *
     * @return true|WP_Error
     */
    public function validateInterval($value)
    {
        if (!is_numeric($value) || $value < 1) {
            return new WP_Error('rest_invalid_interval', __('Interval must be a number greater than 0', 'duplicator-challenge'));
        }

        return true;
    }
}
