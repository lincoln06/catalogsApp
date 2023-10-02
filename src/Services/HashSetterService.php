<?php

namespace App\Services;


class HashSetterService
{
    public function makeHash() : string
    {
        return sha1(time());
    }
}