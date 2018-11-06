<?php

Validator::extend('Time', function($attribute, $value, $parameters)
{
    return preg_match('/^(([0-1]?[0-9])|([2][0-3])):([0-5]?[0-9])(:([0-5]?[0-9]))?$/', $value);
});