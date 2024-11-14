<?php

namespace DupChallenge\Controllers\Endpoints;

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use WP_Error;
use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Interfaces\RestEndpointInterface;
use DupChallenge\Controllers\DirectoryTreeViewController;

/**
 * Search endpoint
 */
class SearchEndpoint implements RestEndpointInterface
{
    use SingletonTrait;

    /**
     * The endpoint
     *
     * @var string
     */
    private $route = '/search';

    /**
     * Register the endpoint
     *
     * @return void
     */
    public function register()
    {
        register_rest_route(self::ENDPOINT_NAMESPACE, $this->route, [
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => [ $this, 'handleRequest' ],
            'permission_callback' => [ $this, 'permissionCallback' ],
            'args'                => [
                'query' => [
                    'required'          => true,
                    'type'              => 'string',
                    'description'       => __('The search query for path. It can include wildcards (%, _, etc).', 'dup-challenge'),
                    'validate_callback' => [ $this, 'validateSearchQuery' ],
                ],
                'exact' => [
                    'required'          => false,
                    'type'              => 'boolean',
                    'description'       => __('Whether to search for exact match or not.', 'dup-challenge'),
                    'default'           => false,
                    'validate_callback' => 'rest_validate_request_arg',
                ],
            ],
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
        $query = $request->get_param('query');

        $results = $this->search($query);

        return new WP_REST_Response($results, 200);
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
     * Validate the search query
     *
     * @param mixed $value The value to validate
     *
     * @return bool|WP_Error True if the value is valid, WP_Error object otherwise
     */
    public function validateSearchQuery($value)
    {
        if (! is_string($value) || empty($value)) {
            return new WP_Error('invalid_search_query', __('The search query must be a non-empty string', 'dup-challenge'));
        }

        return true;
    }

    /**
     * Search for files and directories
     *
     * @param string $query The search query
     *
     * @return array<string, mixed> The search results
     */
    private function search($query)
    {
        return DirectoryTreeViewController::getInstance()->searchByPath($query);
    }
}
