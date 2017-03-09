<?php

use Phinx\Migration\AbstractMigration;

class AddUsersTable extends AbstractMigration
{
    public function change()
    {
        $users = $this->table('users');

        $users->addColumn('email', 'string', ['limit' => 255])
            ->addColumn('password_hash', 'string', ['limit' => 255])
            ->addColumn('first_name', 'string', ['limit' => 255])
            ->addColumn('last_name', 'string', ['limit' => 255])
            ->addColumn('role', 'string', ['limit' => 255])
            ->addIndex('email', ['unique' => true])
            ->create();
    }
}
