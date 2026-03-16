<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Menu;

class MenuRegistry
{
    /** @var array<MenuItem> */
    private array $items = [];

    public function register(MenuItem $menuItem): void
    {
        $this->items[] = $menuItem;
    }

    /**
     * @param  array<MenuItem>  $menuItems
     */
    public function registerMany(array $menuItems): void
    {
        foreach ($menuItems as $menuItem) {
            $this->items[] = $menuItem;
        }
    }

    /**
     * @return array<MenuItem>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @return array<MenuItem>
     */
    public function tree(): array
    {
        $rootItems = [];
        $childrenByParent = [];

        foreach ($this->items as $menuItem) {
            if ($menuItem->parent === null) {
                $rootItems[] = $menuItem;
            } else {
                $childrenByParent[$menuItem->parent][] = $menuItem;
            }
        }

        return $this->buildTree($rootItems, $childrenByParent);
    }

    /**
     * @param  array<MenuItem>  $items
     * @param  array<string, array<MenuItem>>  $childrenByParent
     * @return array<MenuItem>
     */
    private function buildTree(array $items, array $childrenByParent): array
    {
        $tree = [];

        foreach ($items as $menuItem) {
            $children = $childrenByParent[$menuItem->route] ?? [];

            if ($children !== []) {
                $children = $this->buildTree($children, $childrenByParent);
                usort($children, fn (MenuItem $a, MenuItem $b): int => $a->position <=> $b->position);
            }

            $tree[] = new MenuItem(
                label: $menuItem->label,
                route: $menuItem->route,
                icon: $menuItem->icon,
                parent: $menuItem->parent,
                position: $menuItem->position,
                permission: $menuItem->permission,
                module: $menuItem->module,
                children: $children,
            );
        }

        usort($tree, fn (MenuItem $a, MenuItem $b): int => $a->position <=> $b->position);

        return $tree;
    }
}
