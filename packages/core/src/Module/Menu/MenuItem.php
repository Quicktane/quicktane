<?php

declare(strict_types=1);

namespace Quicktane\Core\Module\Menu;

readonly class MenuItem
{
    /**
     * @param  array<MenuItem>  $children
     */
    public function __construct(
        public string $label,
        public string $route,
        public ?string $icon = null,
        public ?string $parent = null,
        public int $position = 0,
        public ?string $permission = null,
        public string $module = '',
        public array $children = [],
    ) {}

    public static function make(string $label, string $route): MenuItemBuilder
    {
        return new MenuItemBuilder($label, $route);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'label' => $this->label,
            'route' => $this->route,
            'position' => $this->position,
            'module' => $this->module,
        ];

        if ($this->icon !== null) {
            $data['icon'] = $this->icon;
        }

        if ($this->permission !== null) {
            $data['permission'] = $this->permission;
        }

        if ($this->children !== []) {
            $data['children'] = array_map(
                fn (MenuItem $child): array => $child->toArray(),
                $this->children,
            );
        }

        return $data;
    }
}
