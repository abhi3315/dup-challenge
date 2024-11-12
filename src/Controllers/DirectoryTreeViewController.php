<?php

namespace DupChallenge\Controllers;

use DupChallenge\Traits\SingletonTrait;
use DupChallenge\Controllers\Tables\FileSystemNodesTable;
use DupChallenge\Controllers\Tables\FileSystemClosureTable;
use DupChallenge\Utils\Tree;
use DupChallenge\Utils\TreeNode;

/**
 * Controller for the directory tree view
 */
class DirectoryTreeViewController
{
    use SingletonTrait;

    /**
     * Get node ID by path
     *
     * @param string $path The path of the node
     *
     * @return int The node ID or 0 if not found
     */
    public function getNodeIdByPath($path)
    {
        global $wpdb;

        $nodesTable = FileSystemNodesTable::getInstance()->getName();

        $query = "SELECT id FROM $nodesTable WHERE path = %s";

        $nodeId = $wpdb->get_var($wpdb->prepare($query, $path));

        return $nodeId ? (int) $nodeId : 0;
    }

    /**
     * Get the tree in flat or nested view
     *
     * @param int|null $id    The ID of the node to start the tree from. If not provided, the tree will start from the root.
     * @param int|null $depth The depth of the tree. If not provided, the entire tree will be returned.
     * @param string   $view  The view to render the tree with. If not provided, the default view will be flat.
     *
     * @return array<string, mixed> The tree nodes structure
     */
    public function getTree(int|null $id = null, int|null $depth = null, $view = 'flat')
    {
        $nodes = $this->getTreeNodes($id, $depth);

        if ($view === 'nested') {
            return $this->buildNestedTree($nodes);
        }

        return $this->buildFlatTree($nodes);
    }

    /**
     * Get the tree nodes
     *
     * @param int $id    The ID of the node to start the tree from. If not provided, the tree will start from the root.
     * @param int $depth The depth of the tree. If not provided, the entire tree will be returned.
     *
     * @return array<object> The tree nodes
     */
    private function getTreeNodes($id = null, $depth = null)
    {
        global $wpdb;

        $nodesTable = FileSystemNodesTable::getInstance()->getName();
        $nodesClosureTable = FileSystemClosureTable::getInstance()->getName();

        $columns = [
            'node.' . FileSystemNodesTable::COLUMN_ID,
            'node.' . FileSystemNodesTable::COLUMN_NAME,
            'node.' . FileSystemNodesTable::COLUMN_PATH,
            'node.' . FileSystemNodesTable::COLUMN_TYPE,
            'node.' . FileSystemNodesTable::COLUMN_PARENT_ID,
            'node.' . FileSystemNodesTable::COLUMN_NODE_COUNT,
            'node.' . FileSystemNodesTable::COLUMN_SIZE,
            'node.' . FileSystemNodesTable::COLUMN_LAST_MODIFIED,
            'closure.' . FileSystemClosureTable::COLUMN_DEPTH
        ];

        $query = "SELECT " . implode(', ', $columns) . "
			FROM $nodesTable node
			JOIN $nodesClosureTable closure
			ON node." . FileSystemNodesTable::COLUMN_ID . " = closure." . FileSystemClosureTable::COLUMN_DESCENDANT;

        $params = [];
        $conditions = [];

        if ($id) {
            $conditions[] = 'closure.' . FileSystemClosureTable::COLUMN_ANCESTOR . ' = %d';
            $params[] = $id;
        }

        if ($depth) {
            $conditions[] = 'closure.' . FileSystemClosureTable::COLUMN_DEPTH . ' = %d';
            $params[] = $depth;
        }

        if (!empty($conditions)) {
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $query .= ' ORDER BY closure.' . FileSystemClosureTable::COLUMN_DEPTH . ', node.' . FileSystemNodesTable::COLUMN_NAME;

        $nodes = $wpdb->get_results($wpdb->prepare($query, $params));

        return $nodes;
    }

    /**
     * Build the flat tree
     *
     * @param array<object> $nodes The tree nodes
     *
     * @return array{
     *    id: int,
     *    name: string,
     *    path: string,
     *    type: string,
     *    node_count: int,
     *    parent_id: int,
     *    size: int,
     *    last_modified: string,
     *    depth: int
     * }[]|array The flat tree nodes structure
     */
    private function buildFlatTree(array $nodes)
    {
        $tree = [];

        foreach ($nodes as $node) {
            if (!$this->isValidNodeObject($node)) {
                continue;
            }

            $tree[] = [
                'id' => $node->id,
                'name' => $node->name,
                'path' => $node->path,
                'type' => $node->type,
                'node_count' => $node->node_count,
                'parent_id' => $node->parent_id,
                'size' => $node->size,
                'last_modified' => $node->last_modified,
                'depth' => $node->depth
            ];
        }

        return $tree;
    }

    /**
     * Build the nested tree
     *
     * @param array<object> $nodes The tree nodes
     *
     * @return array<string, mixed> The nested tree nodes structure
     */
    private function buildNestedTree(array $nodes)
    {
        $tree = new Tree();

        foreach ($nodes as $node) {
            if (!$this->isValidNodeObject($node)) {
                continue;
            }

            $parent = $tree->getNode($node->parent_id);

            $treeNode = new TreeNode(
                $node->id,
                $node->path,
                $node->name,
                $node->type,
                $node->node_count,
                $parent,
                $node->size,
                $node->last_modified,
                $node->depth
            );

            $tree->addNode($treeNode, $parent);
        }

        return $tree->toArray();
    }

    /**
     * Check if the node object is valid
     *
     * @param object $node The node object
     *
     * @return bool True if the node object is valid, false otherwise
     */
    private function isValidNodeObject($node)
    {
        return is_object($node)
            && isset($node->id)
            && isset($node->name)
            && isset($node->path)
            && isset($node->type)
            && isset($node->node_count)
            && isset($node->parent_id)
            && isset($node->size)
            && isset($node->last_modified)
            && isset($node->depth);
    }
}
