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
 * Tree view endpoint
 */
class TreeViewEndpoint implements RestEndpointInterface
{
    use SingletonTrait;

    /**
     * The endpoint
     *
     * @var string
     */
    private $route = '/tree-view';

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
            'permission_callback' => [$this, 'permissionCallback'],
            'args' => [
                'id' => [
                    'required' => false,
                    'type' => 'integer',
                    'description' => 'The ID of the node to start the tree from. If not provided, the tree will start from the root.',
                    'validate_callback' => [$this, 'validatePositiveNumeric']
                ],
                'depth' => [
                    'required' => false,
                    'type' => 'integer',
                    'description' => 'The depth of the tree. If not provided, the entire tree will be returned.',
                    'validate_callback' => [$this, 'validatePositiveNumeric']
                ],
                'view' => [
                    'required' => false,
                    'type' => 'string',
                    'description' => 'The view to render the tree with. If not provided, the default view will be flat.',
                    'default' => 'flat',
                    'enum' => ['flat', 'nested'],
                    'sanitize_callback' => 'sanitize_text_field',
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
        $id = $request->get_param('id');
        $depth = $request->get_param('depth');
        $view = $request->get_param('view');

        if ($id === null) {
            $id = DirectoryTreeViewController::getInstance()->getNodeIdByPath(DUP_WP_ROOT_PATH);
        }

        $tree = DirectoryTreeViewController::getInstance()->getTree($id, $depth, $view);

        return new WP_REST_Response($tree, 200);
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
     * Validate a positive numeric value
     *
     * @param mixed $value The value to validate
     *
     * @return bool|WP_Error True if the value is valid, WP_Error object otherwise
     */
    public function validatePositiveNumeric($value)
    {
        if (!is_numeric($value) || $value < 0) {
            return new WP_Error('rest_invalid_param', 'The value must be a positive number', ['status' => 400]);
        }

        return true;
    }
}
