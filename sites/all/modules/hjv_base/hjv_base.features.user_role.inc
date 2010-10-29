<?php

/**
 * Implementation of hook_user_default_roles().
 */
function hjv_base_user_default_roles() {
  $roles = array();

  // Exported role: 1. Gruppe
  $roles['1. Gruppe'] = array(
    'name' => '1. Gruppe',
  );

  // Exported role: 2. Gruppe
  $roles['2. Gruppe'] = array(
    'name' => '2. Gruppe',
  );

  // Exported role: 3. Gruppe
  $roles['3. Gruppe'] = array(
    'name' => '3. Gruppe',
  );

  // Exported role: Befalingsmand
  $roles['Befalingsmand'] = array(
    'name' => 'Befalingsmand',
  );

  // Exported role: anonymous user
  $roles['anonymous user'] = array(
    'name' => 'anonymous user',
  );

  // Exported role: authenticated user
  $roles['authenticated user'] = array(
    'name' => 'authenticated user',
  );

  return $roles;
}
