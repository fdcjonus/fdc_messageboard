<?php
App::uses('AppModel', 'Model');

class User extends AppModel
{
    public $name = 'User';
    public $useTable = 'users';

    public $validate = array(
        'name' => array(
            'between' => array(
                'rule' => array('lengthBetween', 5, 20),
                'message' => 'Between 5 to 15 characters',
                'required' => true
            )
        ),
        // 'username' => array(
        //     'unique' => array(
        //         'rule' => 'isUnique',
        //         'message' => 'This email is already taken'
        //     ),
        //     'validEmail' => array(
        //         'rule' => 'email',
        //         'message' => 'Please enter a valid email address'
        //     )
        // ),
        // 'password' => array(
        //     // 'rule' => array('minLength', 6),
        //     // 'message' => 'Password must be at least 6 characters long'
        //     'notEmpty' => array(
        //         'rule' => 'notEmpty',
        //         'message' => 'password cannot be empty',
        //         'required' => true
        //     ),
        //     'minLength' => array(
        //         'rule' => array('minLength', '5'),
        //         'message' => 'password must be at least 5 characters long',
        //     )
        // )
    );
}

?>