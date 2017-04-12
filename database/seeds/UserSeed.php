<?php

use Phinx\Seed\AbstractSeed;

class UserSeed extends AbstractSeed
{
    public function run()
    {
        $users = $this->table('users');

        $users->insert([
            'id' => 1,
            'email' => 'bruce@wayneindustries.com',
            'first_name' => 'Bruce',
            'last_name' => 'Wayne',
            'password_hash' => password_hash('batman', PASSWORD_BCRYPT),
            'role' => 'admin'
        ]);

        $users->save();
    }
}
