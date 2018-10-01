<?php
function getProperty($class, $property, $default)
{
    if (property_exists($class, $property)) {
        return $class->{$property};
    }
    return $default;
}