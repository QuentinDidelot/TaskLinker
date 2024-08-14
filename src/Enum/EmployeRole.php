<?php

namespace App\Enum;

enum EmployeRole : string {
    case CHEF_DE_PROJET = 'chef_de_projet';
    case COLLABORATEUR = 'collaborateur';

    public function getRole(): string
    {
        return $this->value;
    }
};