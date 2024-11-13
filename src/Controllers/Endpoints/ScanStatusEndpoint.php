<?php

namespace DupChallenge\Controllers\Endpoints;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;
use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\RestEndpointInterface;
use DupChallenge\Controllers\ScannerStatusController;

/**
 * Start status endpoint
 */
class ScanStatusEndpoint implements RestEndpointInterface
{
    use SingletonTrait;

    /**
     * The endpoint
     *
     * @var string
     */
    private $route = '/scan-status';

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
        $status = ScannerStatusController::getInstance()->getStatus();

        return new WP_REST_Response($status, 200);
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
}
