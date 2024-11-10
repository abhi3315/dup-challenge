<?php

namespace DupChallenge\Interfaces;

use WP_REST_Request;
use WP_REST_Response;

/**
 * Rest endpoint interface
 */
interface RestEndpointInterface
{
    /**
     * The endpoint namespace
     *
     * @var string
     */
    const ENDPOINT_NAMESPACE = 'dup-challenge/v1';

    /**
     * Register the endpoint
     *
     * @return void
     */
    public function register();

    /**
     * Handle a request
     *
     * @param WP_REST_Request $request The request object
     *
     * @return WP_REST_Response The response object
     */
    public function handleRequest(WP_REST_Request $request);
}
