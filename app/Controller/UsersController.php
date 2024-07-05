<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController 
{
    public $components = array('RequestHandler');

    public function getUsers(){
        if ($this->request->is('get')) {
            $this->ret(200,$this->User->find('all'));
        }else 
            $this->ret(401,'Invalid request');
    }

    public function getUsersById(){
        if ($this->request->is('post')) {
            $this->loadModel('User');
            $data = $this->User->find('all', array(
                'conditions' => array(
                    'User.id' => $this->readCookie('user'),
                ),
            ));
            $this->ret(201,$data);
        }else
            $this->ret(401,'Invalid request');
    }

    public function getUserDetail(){
        if ($this->request->is('post')) {
            $json = json_decode($this->request->input());
            $this->loadModel('User');
            $data = $this->User->find('all', array(
                'conditions' => array(
                    'User.id' => $json->userid,
                ),
            ));
            $this->ret(201,$data);
        }else
            $this->ret(401,'Invalid request');
    }

    public function login(){
        if ($this->request->is('post')) {
            $json = json_decode($this->request->input());
            $this->loadModel('User');
            $user = $this->User->findByUsername($json->username);
            if ($user) {
                if (md5($json->password) == $user['User']['password']) {
                    $id = $user['User']['id'];
                    $secureKey = bin2hex(random_bytes(32));
                    $this->deleteCookie();
                    $this->setcookie('token',$secureKey);
                    $this->setcookie('user',$id);
                    $this->updateLoginTime($id);
                    $this->deleteToken($id);
                    $this->saveToken($id,md5($secureKey));
                    $this->addAccessLogs($id,'login',$json->username);
                    $this->Session->write('userid', $id);
                    $this->ret(200,'Credential is valid');
                }else
                    $this->ret(403,'Credential is not valid');
            }else
                $this->ret(404,'User not found');
        }else 
            $this->ret(401,'Invalid request');
    }

    public function register(){
        $this->loadModel('User');

        if ($this->request->is('post')){
            $this->User->set($this->request->data);
            if($this->User->validates()){
                $json = json_decode($this->request->input());
                $user = $this->User->findByUsername($json->username);
                if (!$user) {
                    if ($json->confirm === $json->password) {
                        if ($this->User->save(array(
                            'User' => array(
                                'name' => strtoupper($json->name),
                                'username' => $json->username,
                                'password' => md5($json->password),
                                'created' =>  $this->getDate(),
                                'last_login_time' => $this->getDate()
                            )
                        ))) {
                            $fetch = $this->User->findByUsername($json->username);
                            $id = $fetch['User']['id'];
                            $secureKey = bin2hex(random_bytes(32));
                            $this->deleteCookie();
                            $this->setcookie('token',$secureKey);
                            $this->setcookie('user',$id);
                            $this->deleteToken($id);
                            $this->saveToken($id,md5($secureKey));
                            $this->addAccessLogs($id,'register',$json->username);
                            
                            $this->ret(201,'Successfully registered');
                        }else
                            $this->ret(409,'Failed to register');
                    }else
                        $this->ret(409,'Confirm password is not equal to your password');
                }else
                    $this->ret(401,'Failed to register');
            }else
                $this->ret(501,'Failed to register');
        }
    }

    public function update(){
        if ($this->request->is('post')){
            $json = json_decode($this->request->input());
            $this->loadModel('User');
            $user = $this->User->findById($this->readCookie('user'));
            if ($user) {
                if ($this->User->save(array(
                    'id' => $this->readCookie('user'),
                    'img_url' => $json->img_url,
                    'name' => strtoupper($json->name),
                    'birthdate' => $json->birthdate,
                    'gender' => strtoupper($json->gender),
                    'hubby' => $json->hubby,
                    'last_login_time' => $this->getDate()
                )))
                    $this->ret(201,'Successfully updated');
                else
                    $this->ret(409,'Failed to update credential');
            }else
                $this->ret(401,'User not found');
        }else
            $this->ret(401,'Invalid request');
    }
    

    public function delete(){
        if ($this->request->is('post')){
            $json = json_decode($this->request->input());
            $this->loadModel('User');
            $user = $this->User->findById($json->id);
            if ($user) {
                if ($this->User->delete($user['User']['id'])) {
                    $this->loadModel('Secret');
                    $this->Secret->delete(
                        array(
                            'Secret.user_id' => $user['User']['id']
                        )
                    );
                    $this->ret(201,'Successfully deleted');
                } else
                    $this->ret(409,'Failed to delete credential');
            }else
                $this->ret(401,'User not found');
        }else
            $this->ret(201,'Invalid request');
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
        $this->Cookie->delete('token');
        $this->Cookie->delete('user');
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

        $this->Cookie->write($username, $cookieData);
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

    private function addAccessLogs($id,$type,$username){
        $this->loadModel('Log');
        $data = array(
            'userid' => $id,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'username' => $username,
            'created' => $this->getDate(),
            'type' => $type
        );
        $this->Log->save($data);
    }
    private function updateLoginTime($id){
        $this->loadModel('User');
        $this->User->save(array('id' => $id,'last_login_time' => $this->getDate()));
    }
    private function saveToken($id,$token){
        $this->loadModel('Secret');
        $data = array(
            'user_id' => $id,
            'token' => $token,
            'created' => $this->getDate(),
        );
        $this->Secret->save($data);
    }
    private function deleteToken($id){
        $this->loadModel('Secret');
        $secret = $this->Secret->findByUser_id($id);
        if ($secret)
            $this->Secret->delete($secret['Secret']['id']);
    }
    private function uploadImage($source){
        // Destination file path (the new location for the image)
        $destination = getcwd().'/profiles/'.$this->getFilename($source);

        return rename($source, $destination);
    }
    private function getFilename($url){
        $expl = explode("\\\\",$url);
        return $expl[count($expl)-1];
    }
}

?>