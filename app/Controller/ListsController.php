<?php
App::uses('AppController', 'Controller');

class ListsController extends AppController
{
    public $components = array('RequestHandler','Paginator');
    public $uses = array('List', 'Message');

    public function initialize()
    {
        parent::initialize();
    }

    public function getListByUser(){
        if ($this->request->is('post')) {
            try {
                // $json = json_decode($this->request->input());
                // $id = $this->readCookie('user');
                // $conditions = array(
                //     'OR' => array(
                //         array(
                //             'userid' => $id,
                //         ),
                //         array(
                //             'msg_id' => $id
                //         )
                //     )
                // );

                // $this->Paginator->settings = array(
                //     'conditions' => $conditions,
                //     'page' => $json->page,
                //     'limit' => $json->limit,
                //     'order' => array('message_id' => 'desc')
                // );
                // // Paginate the User model
                // $data = $this->Paginator->paginate();

                // // Get pagination metadata
                // $paginationMeta = $this->request->params['paging']['List'];

                // // Construct response data including paginated data and pagination metadata
                // $responseData = array(
                //     'success' => true,
                //     'data' => $data,
                //     'paging' => array(
                //         'page' => $paginationMeta['page'], // Current page number
                //         'totalPages' => $paginationMeta['pageCount'], // Total number of pages
                //         'totalCount' => $paginationMeta['count'] // Total number of records
                //     ),
                //     'message' => 'Paginated data retrieved successfully'
                // );

                // // Convert response data to JSON and send response
                // $this->autoRender = false; // Disable view rendering
                // $this->response->type('json'); // Set response content type
                // $this->response->body(json_encode($responseData)); // Set response body
                // return $this->response; // Return the response

                $json = json_decode($this->request->input());
                $id = $this->readCookie('user');

                $this->paginate = array(
                    'fields' => array('List.id','List.userid','List.msg_id','User.name','User.img_url','Message.id','Message.message','Message.created'), // Select fields from both tables
                    'joins' => array(
                        array(
                            'table' => 'messages',
                            'alias' => 'Message',
                            'type' => 'INNER',
                            'conditions' => array(
                                'Message.id = List.message_id'
                            ),
                        ),
                        array(
                            'table' => 'users',
                            'alias' => 'User',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'List.userid = User.id'
                            )
                        )
                    ),
                    'conditions' => array(
                        'OR' => array(
                            array(
                                'List.userid' => $id,
                            ),
                            array(
                                'List.msg_id' => $id
                            )
                        ),
                    ),
                    'group' => array('List.userid'),
                    'limit' => $json->limit,
                    'page' => $json->page,
                    'order' => array('List.id' => 'desc')
                );
        
                // Paginate the User model with custom query join
                $messages = $this->paginate('List');
                $this->ret(200,$messages);
            } catch (Exception $th) {
                $this->ret(501,$th->getMessage());
            }
        }else
            $this->ret(401,'Invalid request');
    }
    public function getFilteredList(){
        if ($this->request->is('post')) {
            $json = json_decode($this->request->input());
            // $this->loadModel('List');
            // $data = $this->List->find('all', array(
            //     'limit' => 1,
            //     'conditions' => array(
            //         'List.userid' => $json->userid,
            //         'List.msg_id' => $json->message_id
            //     ),
            //         'order' => array('List.id DESC'), // Order by descending order based on field3
            //         // 'group' => 'Message.message_id' 
            //     ));
            // $this->ret(201,$data);

            // $this->loadModel('List');
            $results = $this->List->query("SELECT DISTINCT * FROM `lists` 
            WHERE userid = $json->userid AND msg_id = $json->message_id 
            OR userid = $json->message_id AND msg_id = $json->userid
            GROUP BY created
            ORDER BY created DESC
            LIMIT 1");
            $this->ret(201,$results);
        }else
            $this->ret(401,'Invalid request');
    }

    // public function addToList(){
    //     if ($this->request->is('post')) {
    //         $json = json_decode($this->request->input());
    //         $this->loadModel('List');
    //         $this->List->save(array(
    //             'List' => array(
    //                 'userid' => $json->userid,
    //                 'message_id' => $json->message_id,
    //                 'created' =>  $this->getDate(),
    //             )
    //         ));
    //         $this->ret(201,'Successfully added');
    //     }
    // }

    public function deleteList(){ 
        if ($this->request->is('post')) {
            try {
                $json = json_decode($this->request->input());
                // $this->loadModel('List');
                $id = $this->List->findById($json->id);
                if (!$id) {
                    $this->ret(401,'Something went wrong');
                }else{
                    $this->List->deleteAll(array('List.userid' => $id['List']['userid']));
                    $this->ret(201,'Successfully deleted');
                }
            } catch (Exception $th) {
                $this->ret(500,'Something went wrong');
            }
        }
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