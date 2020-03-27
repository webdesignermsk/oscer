<?php

namespace Bambamboole\LaravelCms\Theming\BladeComponents;

use Bambamboole\LaravelCms\Menus\Models\Menu;
use Bambamboole\LaravelCms\Theming\Contracts\Theme;
use Illuminate\View\Component;

class MenuBladeComponent extends Component
{
    protected Theme $theme;

    protected Menu $menu;

    public function __construct(Theme $theme, string $name)
    {
        $this->theme = $theme;
        $this->menu = Menu::resolve($name);
    }

    public function render()
    {
        return view(
            $this->theme->getMenus()[$this->menu->name]['template'],
            ['menu' => $this->menu]
        );
    }
}