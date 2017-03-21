<?php

use Phinx\Migration\AbstractMigration;

class AddUsersTable extends AbstractMigration
{
    public function change()
    {
        $users = $this->table('users');

        $users->addColumn('email', 'string', ['limit' => 255]);
        $users->addColumn('password_hash', 'string', ['limit' => 255]);
        $users->addColumn('first_name', 'string', ['limit' => 255]);
        $users->addColumn('last_name', 'string', ['limit' => 255]);
        $users->addColumn('role', 'string', ['limit' => 255]);
        $users->addIndex('email', ['unique' => true]);

        $users->create();
    }
}
