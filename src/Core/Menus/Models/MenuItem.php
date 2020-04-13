<?php

namespace Bambamboole\LaravelCms\Core\Menus\Models;

use Bambamboole\LaravelCms\Backend\Contracts\SavableModel;
use Bambamboole\LaravelCms\Backend\Resources\IsSavableEloquentModel;
use Bambamboole\LaravelCms\Core\Models\BaseModel;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property string name
 * @property string url
 * @property string menu
 * @property int order
 * @property Carbon updated_at
 * @property Carbon created_at
 */
class MenuItem extends BaseModel implements SavableModel
{
    use IsSavableEloquentModel;
}
