<?php

namespace DupChallenge\Utils;

/**
 * Tree node class
 */
class TreeNode
{
    /**
     * The node ID
     *
     * @var int
     */
    private $id;

    /**
     * The node name
     *
     * @var string
     */
    private $name;

    /**
     * The node path
     *
     * @var string
     */
    private $path;

    /**
     * The node type
     *
     * @var string
     */
    private $type;

    /**
     * The node count
     *
     * @var int
     */
    private $nodeCount;

    /**
     * Parent
     *
     * @var TreeNode|null
     */
    private $parent;

    /**
     * The node size
     *
     * @var int
     */
    private $size;

    /**
     * The last modified time
     *
     * @var string
     */
    private $lastModified;

    /**
     * The node depth
     *
     * @var int
     */
    private $depth;

    /**
     * The node children
     *
     * @var TreeNode[]
     */
    private $children = [];

    /**
     * Constructor
     *
     * @param int           $id           The node ID
     * @param string        $path         The node path
     * @param string        $name         The node name
     * @param string        $type         The node type
     * @param int           $nodeCount    The node count
     * @param TreeNode|null $parent       The parent node
     * @param int           $size         The node size
     * @param string        $lastModified The last modified time
     * @param int           $depth        The node depth
     */
    public function __construct(
        $id,
        $path,
        $name,
        $type,
        $nodeCount,
        TreeNode|null $parent,
        $size,
        $lastModified,
        $depth
    ) {
        $this->id = $id;
        $this->path = $path;
        $this->name = $name;
        $this->type = $type;
        $this->nodeCount = $nodeCount;
        $this->parent = $parent;
        $this->size = $size;
        $this->lastModified = $lastModified;
        $this->depth = $depth;
    }

    /**
     * Get the node ID
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add a child node
     *
     * @param TreeNode $node The child node
     *
     * @return void
     */
    public function addChild(TreeNode $node)
    {
        $this->children[] = $node;
    }

    /**
     * Get the node children
     *
     * @return array<TreeNode>
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Convert the node to an array
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        $children = [];

        foreach ($this->children as $child) {
            $children[] = $child->toArray();
        }

        return [
            'id'            => $this->id,
            'path'          => $this->path,
            'name'          => $this->name,
            'type'          => $this->type,
            'node_count'    => $this->nodeCount,
            'parent'        => $this->parent ? $this->parent->getId() : null,
            'size'          => $this->size,
            'last_modified' => $this->lastModified,
            'depth'         => $this->depth,
            'children'      => $children
        ];
    }
}
