<?php

function loginUser()
: \Illuminate\Support\Optional
{
    $user = auth('api')->check() ? auth('api')->user() : null;
    return optional($user);
}

function getMasterOTP()
: string
{
    return '234567';
}