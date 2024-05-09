<?php

App::uses('AppController', 'Controller');

class TestsController extends AppController
{
    public $components = array('RequestHandler');

    public function helloWorld() {
        $this->set(array(
            'message' => 'Hello, World!',
            '_serialize' => array('message')
        ));
    }
}
