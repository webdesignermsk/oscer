<?php

namespace Bambamboole\LaravelCms\Permissions\Models;

class Permission extends \Spatie\Permission\Models\Permission
{

    function getGroup(){
        return strtok($this->name,  '.');
    }
}
