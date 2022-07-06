<?php

namespace ZnCore\Enum\Base;

use ZnCore\Enum\Helpers\EnumHelper;
use ZnLib\Components\ArrayRepository\Base\BaseArrayCrudRepository;

abstract class BaseEnumCrudRepository extends BaseArrayCrudRepository
{

    abstract public function enumClass(): string;

    protected function getItems(): array
    {
        return EnumHelper::getItems($this->enumClass());
    }
}
