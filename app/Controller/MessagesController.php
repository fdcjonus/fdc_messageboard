<?php
App::uses('AppController', 'Controller');

class MessagesController extends AppController
{
    public $components = array('RequestHandler','Paginator');
    public $uses = array('Message','List');

    public function getMessageByUser(){
        if ($this->request->is('post')) {
            try {
                // $json = json_decode($this->request->input());
                // $id = $this->readCookie('user');

                // $this->paginate = array(
                //     'conditions' => array(
                //         'OR' => array(
                //             array(
                //                 'user_id' => $json->userid,
                //                 'message_id' => $json->message_id
                //             ),
                //             array(
                //                 'user_id' => $json->message_id,
                //                 'message_id' => $json->userid
                //             )
                //         ),
                //     ),
                //     'limit' => $json->limit,
                //     'page' => $json->page,
                //     'order' => array('id' => 'desc')
                // );
        
                // // Paginate the User model with custom query join
                // $messages = $this->paginate('Message');
                // $this->ret(200,$messages);

                $json = json_decode($this->request->input());

                $this->paginate = array(
                    'fields' => array('List.id','List.userid','List.msg_id','User.name','User.img_url','Message.id','Message.message','Message.created'), // Select fields from both tables
                    'joins' => array(
                        array(
                            'table' => 'lists',
                            'alias' => 'List',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Message.id = List.message_id'
                            ),
                        ),
                        array(
                            'table' => 'users',
                            'alias' => 'User',
                            'type' => 'INNER',
                            'conditions' => array(
                                'User.id = List.userid'
                            )
                        )
                    ),
                    'conditions' => array(
                        'OR' => array(
                            array(
                                'List.userid' => $json->userid,
                                'List.msg_id' => $json->message_id
                            ),
                            array(
                                'List.userid' => $json->message_id,
                                'List.msg_id' => $json->userid
                            )
                        ),
                    ),
                    'limit' => $json->limit,
                    'page' => $json->page,
                    'order' => array('Message.created' => 'desc')
                );
        
                // Paginate the User model with custom query join
                $messages = $this->paginate('Message');
                $this->ret(200,$messages);
            } catch (NotFoundException $th) {
                $this->ret(501,'Internal Server Error');
            }
        }else
            $this->ret(401,'Invalid request');
    }

    public function searchMessage(){
        if ($this->request->is('post')) {
            try {
                $json = json_decode($this->request->input());
                $id = $this->readCookie('user');

                $this->paginate = array(
                    'conditions' => array(
                        'message LIKE ' => '%'.$json->search.'%',
                        'user_id' => $this->readCookie('user'),
                    ),
                    'limit' => $json->limit,
                    'page' => $json->page,
                    'order' => array('id' => 'desc')
                );
        
                // Paginate the User model with custom query join
                $messages = $this->paginate('Message');
                $this->ret(200,$messages);
            } catch (Exception $th) {
                $this->ret(501,'Internal Server Error');
            }
        }else
            $this->ret(401,'Invalid request');
    }

    public function getMessage(){
        if ($this->request->is('post')) {
            $json = json_decode($this->request->input());
            // $this->loadModel('Message');
            // $data = $this->Message->find('all', array(
            //     'limit' => 100,
            //     'fields' => array
            //     ('DISTINCT Message.message_id','Message.id','Message.user_id','Message.message','Message.created'),
            //     'conditions' => array(
            //         // array(
            //         //     'Message.user_id' => $json->userid,
            //         //     'Message.message_id' => $json->message_id
            //         // )
            //         'OR' => array(
            //             array(
            //                 'AND' => array(
            //                     array('Message.user_id' => $json->userid), // Condition: user = 1
            //                     array('Message.message_id' => $json->message_id) // Additional condition: status = 'active'
            //                 )
            //             ),
            //             array('Message.user_id' => $json->message_id), // Condition: user = 1
            //             array('Message.message_id' => $json->userid)
            //         )
            //     ),
            //     'order' => array('Message.created DESC'), // Order by descending order based on field3
            //     'group' => 'Message.message_id' 
            //     ));
            // $this->ret(201,$data); 

            $this->loadModel('Message');
            $results = $this->Message->query("SELECT DISTINCT message_id,user_id,id,message,created 
            FROM `messages` 
            WHERE user_id = $json->userid AND message_id = $json->message_id 
            OR user_id = $json->message_id AND message_id = $json->userid
            GROUP BY created
            ORDER BY created DESC
            LIMIT 1");
            $this->ret(201,$results);
        }else
            $this->ret(401,'Invalid request');
    }

    public function addMessage(){
        if ($this->request->is('post')) {
            $json = json_decode($this->request->input());
            $id = $this->readCookie('user');
            
            $data = array(
                'user_id' => $id,
                'message_id' => $json->ids[0],
                'message' => $json->msg,
                'created' => $this->getDate()
            );
            $res = $this->Message->save($data);
            $data = json_decode(json_encode($res),true);

            $this->loadModel('List');
            $this->List->save(array(
                'List' => array(
                    'userid' => $id,
                    'message_id' => $data['Message']['id'],
                    'created' =>  $this->getDate(),
                    'msg_id' => $json->ids[0]
                )
            ));

            $this->ret(201,'Successfully added');
        }else
            $this->ret(401,'Invalid request');
    }
    public function deleteMessage(){
        if ($this->request->is('post')) {
            $json = json_decode($this->request->input());
            $this->loadModel('Message');
            $this->Message->delete($json->id);
            $this->loadModel('List');
            $this->List->deleteAll(array(
                'List.message_id' => $json->id
            ));
            $this->ret(201,'Successfully deleted');
        }else
            $this->ret(401,'Invalid request');
    }
    public function deleteAll(){
        if ($this->request->is('post')) {
            $json = json_decode($this->request->input());
            $this->loadModel('Message');
            $this->Message->deleteAll(
                array(
                    'Message.user_id' => $this->Session->read('userid')
                )
            );
            $this->loadModel('List');
            $this->List->deleteAll(array(
                'List.userid' => $this->Session->read('userid')
            ));
            $this->ret(201,'Successfully deleted =>'.$this->Session->read('userid'));
        }else
            $this->ret(401,'Invalid request');
        
    }
    public function deleteAllByUser(){
        if ($this->request->is('post')) {
            $json = json_decode($this->request->input());
            $this->loadModel('Message');
            $this->Message->deleteAll(
                array(
                    'Message.user_id' => $this->Session->read('userid'),
                    'Message.message_id' => $json->message_id
                )
            );
            $this->loadModel('List');
            $this->List->deleteAll(array(
                'List.userid' => $this->Session->read('userid'),
                'List.msg_id' => $json->message_id
            ));
            $this->ret(201,'Successfully deleted =>'.$this->Session->read('userid'));
        }else
            $this->ret(401,'Invalid request');
        
    }


    private function readCookie($name) {
        // Load the CookieComponent
        $this->Cookie = $this->Components->load('Cookie');
        // Check if the cookie exists
        if ($this->Cookie->check($name)) {
            // Read the cookie data
            $cookieData = $this->Cookie->read($name);
            // Access the cookie data
            return $cookieData['value'];
        } else 
            return null;
    }

    private function deleteCookie() {
        // Load the CookieComponent
        $this->Cookie = $this->Components->load('Cookie');
        // Delete the cookie
        $this->Cookie->delete('UserToken');
    }

    private function setCookie($username,$value){
        // Load the CookieComponent
        $this->Cookie = $this->Components->load('Cookie');
        // Set the cookie
        $cookieData = array(
            'name' => $username,
            'value' => $value,
            'expire' => '+999999 week',
            'path' => '/',
        );

        $this->Cookie->write('UserToken', $cookieData);
    }

    private function ret($code,$message){
        $this->set(array(
            'status' => $code,
            'message' => $message,
            '_serialize' => array('status','message')
        ));  
    }
    private function getDate(){
        // Set the timezone
        date_default_timezone_set('Asia/Manila');
        // Get the current date and time
        return date('Y-m-d H:i:s');
    }
}

?>