<?php

namespace DupChallenge\Utils;

use DupChallenge\Utils\TreeNode;

/**
 * Tree utility class to build and manipulate trees
 */
class Tree
{
    /**
     * Hashmap of nodes
     * Key is the node ID, value is the node object
     *
     * @var array
     */
    private $nodes = [];

    /**
     * The root node
     *
     * @var TreeNode
     */
    private $root;

    /**
     * Add a node to the tree
     *
     * @param TreeNode      $node   The node to add
     * @param TreeNode|null $parent The parent node
     *
     * @return void
     */
    public function addNode(TreeNode $node, $parent = null)
    {
        $nodeId = $node->getId();

        $this->nodes[$nodeId] = $node;

        if ($parent) {
            $parent->addChild($node);
        } else {
            $this->root = $node;
        }
    }

    /**
     * Get a node by ID
     *
     * @param int $id The node ID
     *
     * @return TreeNode|null The node object
     */
    public function getNode(int|null $id)
    {
        return isset($this->nodes[$id]) ? $this->nodes[$id] : null;
    }

    /**
     * Get the tree array
     *
     * @return array The tree nodes structure
     */
    public function toArray()
    {
        return $this->root->toArray();
    }
}
