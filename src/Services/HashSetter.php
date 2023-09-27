<?php

namespace App\Services;


class HashSetter
{
    public function makeHash() : string
    {
        return sha1(time());
    }
}