<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
    public function beforeFilter() {
        // Call parent's beforeFilter
        parent::beforeFilter();

        // Custom logic to handle incoming requests
        // This code will be executed before any controller action is invoked
        if (!$this->authenticate()) {
            // Handle unauthorized access
            $this->response->statusCode(401);
            $this->response->send(); // Send the response
            exit; // Stop further execution
        }
    }

    protected function authenticate() {
        $header = $this->request->header('Authorization');
        // Remove "Basic " prefix
        $base64Credentials = substr($header, 6);
        // Decode base64-encoded credentials
        $credentials = base64_decode($base64Credentials);
        // Split username and password
        list($username, $password) = explode(':', $credentials);
        
        $envVariables = $this->parseEnv(__DIR__."/.env");

        return $envVariables['user'] == $username && $envVariables['pass'] == $password;
    }
    
    private function parseEnv($filePath) {
        $file = fopen($filePath, 'r');
        $envVariables = [];
    
        // Read each line of the file
        while (($line = fgets($file)) !== false) {
            // Ignore lines starting with '#' (comments) or empty lines
            if (strpos(trim($line), '#') === 0 || trim($line) === '') {
                continue;
            }
            // Split each line by the '=' sign
            list($key, $value) = explode('=', $line, 2);
            
            // Remove any surrounding whitespace and quotes from the value
            $value = trim($value);
            if (in_array(substr($value, 0, 1), ['"', "'"]) && substr($value, 0, 1) === substr($value, -1)) {
                $value = substr($value, 1, -1);
            }
    
            // Store key-value pairs in the array
            $envVariables[$key] = $value;
        }
    
        fclose($file);
        return $envVariables;
    }
}
