<?php

namespace App\Events\Model;

use ReflectionClass;

class ModelChangedEvent extends ContextModelEvent
{
    public function broadcastAs()
    {
        // Quick enough
        return sprintf('%s-changed', strtolower((new ReflectionClass($this->_model))->getShortName()));
    }
}