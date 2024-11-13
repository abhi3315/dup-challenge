<?php

namespace DupChallenge\Controllers\Endpoints;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;
use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\RestEndpointInterface;
use DupChallenge\Controllers\DirectoryScannerController;

/**
 * Start scan endpoint
 */
class StartScanEndpoint implements RestEndpointInterface
{
    use SingletonTrait;

    /**
     * The endpoint
     *
     * @var string
     */
    private $route = '/start-scan';

    /**
     * Register the endpoint
     *
     * @return void
     */
    public function register()
    {
        register_rest_route(self::ENDPOINT_NAMESPACE, $this->route, [
            'methods' => WP_REST_Server::READABLE,
            'callback' => [$this, 'handleRequest'],
            'permission_callback' => [$this, 'permissionCallback']
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
        DirectoryScannerController::getInstance()->startScanJob();

        return new WP_REST_Response(['message' => 'Scan started'], 200);
    }

    /**
     * Permission callback
     *
     * @return bool|WP_Error True if the user has permission, WP_Error object otherwise
     */
    public function permissionCallback()
    {
        if (!current_user_can('manage_options')) {
            return new WP_Error('rest_forbidden', __('You do not have permission to access this resource.', 'dup-challenge'), ['status' => 403]);
        }

        return true;
    }
}
