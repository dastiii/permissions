<?php

namespace dastiii\Permissions\Contracts;

interface Model
{
    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key);
}
