<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));


	// API URL
	// for user
	Router::connect('/api/v1/userdet', array('controller' => 'users', 'action' => 'getUserDetail', 'ext' => 'json'));
	Router::connect('/api/v1/users', array('controller' => 'users', 'action' => 'getUsers', 'ext' => 'json'));
	Router::connect('/api/v1/user', array('controller' => 'users', 'action' => 'getUsersById', 'ext' => 'json'));
	Router::connect('/api/v1/login', array('controller' => 'users', 'action' => 'login', 'ext' => 'json'));
	Router::connect('/api/v1/register', array('controller' => 'users', 'action' => 'register', 'ext' => 'json'));
	Router::connect('/api/v1/update', array('controller' => 'users', 'action' => 'update', 'ext' => 'json'));
	Router::connect('/api/v1/delete', array('controller' => 'users', 'action' => 'delete', 'ext' => 'json'));

	Router::connect('/api/v1/lists', array('controller' => 'lists', 'action' => 'getListByUser', 'ext' => 'json'));
	Router::connect('/api/v1/list/filter', array('controller' => 'lists', 'action' => 'getFilteredList', 'ext' => 'json'));
	Router::connect('/api/v1/list', array('controller' => 'lists', 'action' => 'addToList', 'ext' => 'json'));
	Router::connect('/api/v1/list/delete', array('controller' => 'lists', 'action' => 'deleteList', 'ext' => 'json'));

	Router::connect('/api/v1/messages', array('controller' => 'messages', 'action' => 'getMessageByUser', 'ext' => 'json'));
	Router::connect('/api/v1/message', array('controller' => 'messages', 'action' => 'addMessage', 'ext' => 'json'));
	Router::connect('/api/v1/message/delete', array('controller' => 'messages', 'action' => 'deleteMessage', 'ext' => 'json'));
	Router::connect('/api/v1/message/deletes', array('controller' => 'messages', 'action' => 'deleteAll', 'ext' => 'json'));
	Router::connect('/api/v1/message/delete/user', array('controller' => 'messages', 'action' => 'deleteAllByUser', 'ext' => 'json'));
	Router::connect('/api/v1/message/get', array('controller' => 'messages', 'action' => 'getMessage', 'ext' => 'json'));
	Router::connect('/api/v1/message/search', array('controller' => 'messages', 'action' => 'searchMessage', 'ext' => 'json'));
/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
