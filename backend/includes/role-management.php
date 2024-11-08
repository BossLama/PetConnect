<?php

function isRoleUser($role_id)
{
    return $role_id == 1;
}

function isRoleProfessional($role_id)
{
    return $role_id == 2;
}

function isRoleAdmin($role_id)
{
    return $role_id == 3;
}

?>