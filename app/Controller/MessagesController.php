<?php
App::uses('AppController', 'Controller');

class MessagesController extends AppController
{
    public $components = array('RequestHandler','Paginator');

    public function getMessageByUser(){
        if ($this->request->is('post')) {
            try {
                $json = json_decode($this->request->input());
            
                $this->Paginator->settings = array(
                    'page' => $json->page,
                    'limit' => $json->limit,
                    'order' => array('message_id' => 'desc')
                );

                // Paginate the User model
                $data = $this->Paginator->paginate('Message', array(
                                                                    'user_id' => $json->userid,
                                                                    // 'message_id' => $json->message_id
                                                                ));

                // Get pagination metadata
                $paginationMeta = $this->request->params['paging']['Message'];

                // Construct response data including paginated data and pagination metadata
                $responseData = array(
                    'success' => true,
                    'data' => $data,
                    'paging' => array(
                        'page' => $paginationMeta['page'], // Current page number
                        'totalPages' => $paginationMeta['pageCount'], // Total number of pages
                        'totalCount' => $paginationMeta['count'] // Total number of records
                    ),
                    'message' => 'Paginated data retrieved successfully'
                );

                // Convert response data to JSON and send response
                $this->autoRender = false; // Disable view rendering
                $this->response->type('json'); // Set response content type
                $this->response->body(json_encode($responseData)); // Set response body
                return $this->response; // Return the response
            } catch (NotFoundException $th) {
                $this->ret(401,'Invalid request');
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
            $this->loadModel('Message');
            $data = array(
                'user_id' => $json->userid,
                'message_id' => $json->message_id,
                'message' => $json->message,
                'created' => $this->getDate()
            );
            $res = $this->Message->save($data);
            $data = json_decode(json_encode($res),true);

            $this->loadModel('List');
            $this->List->save(array(
                'List' => array(
                    'userid' => $data['Message']['user_id'],
                    'message_id' => $data['Message']['id'],
                    'created' =>  $this->getDate(),
                    'msg_id' => $json->message_id
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


    private function readCookie() {
        // Load the CookieComponent
        $this->Cookie = $this->Components->load('Cookie');
        // Check if the cookie exists
        if ($this->Cookie->check('UserToken')) {
            // Read the cookie data
            $cookieData = $this->Cookie->read('UserToken');
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