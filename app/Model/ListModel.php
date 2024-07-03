<?php
App::uses('AppModel', 'Model');

class ListModel extends AppModel
{
    public $name = 'List';
    public $useTable = 'lists';

    public $validate = array(
        'userid' => array(
            'rule' => 'email',
            'required' => true,
        )
    );
}

?>