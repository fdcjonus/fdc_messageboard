<?php
App::uses('AppModel', 'Model');

class User extends AppModel
{
    public $name = 'User';
    public $useTable = 'users';

    public $validate = array(
        'name' => array(
            'rule' => 'notEmpty',
            'message' => 'Please enter your name'
        ),
        'username' => array(
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'This email is already taken'
            ),
            'validEmail' => array(
                'rule' => 'email',
                'message' => 'Please enter a valid email address'
            )
        ),
        'password' => array(
            'rule' => array('minLength', 6),
            'message' => 'Password must be at least 6 characters long'
        )
    );
}

?>