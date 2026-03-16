<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Menu;

class MenuItemBuilder
{
    private ?string $icon = null;

    private ?string $parent = null;

    private int $position = 0;

    private ?string $permission = null;

    private string $module = '';

    public function __construct(
        private readonly string $label,
        private readonly string $route,
    ) {}

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function parent(string $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function position(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function permission(string $permission): self
    {
        $this->permission = $permission;

        return $this;
    }

    public function module(string $module): self
    {
        $this->module = $module;

        return $this;
    }

    public function build(): MenuItem
    {
        return new MenuItem(
            label: $this->label,
            route: $this->route,
            icon: $this->icon,
            parent: $this->parent,
            position: $this->position,
            permission: $this->permission,
            module: $this->module,
        );
    }
}
