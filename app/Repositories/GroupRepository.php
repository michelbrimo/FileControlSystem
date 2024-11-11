<?php

namespace App\Repositories;

use App\Models\Group;

class GroupRepository{
    public function create($data) {
        return Group::create($data);
    }
}