<?php
App::uses('AppModel', 'Model');

class Secret extends AppModel
{
    public $name = 'Secret';
    public $useTable = 'secrets';
}

?>