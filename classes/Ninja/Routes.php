<?php

namespace Ninja;

interface Routes
{
    public function getRoutes();
    public function getAuthentication();
    public function checkPermission($permission);
}
