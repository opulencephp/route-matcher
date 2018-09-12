<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/route-matcher/blob/master/LICENSE.md
 */

namespace Opulence\Routing\UriTemplates\Compilers\Parsers\Nodes;

/**
 * Defines a route node
 */
class Node
{
    /** @var string The node type */
    private $type;
    /** @var mixed|null The value of the node */
    private $value;
    /** @var Node|null The parent node */
    private $parent;
    /** @var Node[] The child nodes */
    private $children = [];

    /**
     * @param string $type The node type
     * @param mixed $value The value of the node
     */
    public function __construct(string $type, $value = null)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * Adds a child to this node
     *
     * @param Node $node The child to add
     * @return Node Returns this for chaining
     */
    public function addChild(Node $node): Node
    {
        $node->setParent($this);
        $this->children[] = $node;

        return $this;
    }

    /**
     * Gets the list of children of this node
     *
     * @return Node[] The list of children
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Gets the parent node
     *
     * @return Node The parent node
     */
    public function getParent(): Node
    {
        return $this->parent;
    }

    /**
     * Gets the node type
     *
     * @return string The node type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Gets the value of this node
     *
     * @return mixed|null The value of this node if there is one, otherwise null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Gets whether or not this node has children
     *
     * @return bool True if this node has children, otherwise false
     */
    public function hasChildren(): bool
    {
        return \count($this->children) > 0;
    }

    /**
     * Gets whether or not this node is the root
     *
     * @return bool True if this is a root node, otherwise false
     */
    public function isRoot(): bool
    {
        return $this->parent === null;
    }

    /**
     * Sets the parent node
     *
     * @param Node|null $parent The parent node
     */
    public function setParent($parent): void
    {
        $this->parent = $parent;
    }
}