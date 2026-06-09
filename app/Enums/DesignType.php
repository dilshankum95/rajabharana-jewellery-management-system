<?php

namespace App\Enums;

enum DesignType: string
{
    case Catalog = 'catalog';
    case Custom = 'custom';

    public function label(): string
    {
        return config('jewellery.design_types.'.$this->value, $this->value);
    }
}
