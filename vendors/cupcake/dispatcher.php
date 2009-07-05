<?php

class Dispatcher {
  /**
 * Base URL
 *
 * @var string
 * @access public
 */
	var $base = false;
/**
 * webroot path
 *
 * @var string
 * @access public
 */
	var $webroot = '/';	
/**
 * Current URL
 *
 * @var string
 * @access public
 */
	var $here = false;
/**
 * the params for this request
 *
 * @var string
 * @access public
 */
	var $params = null;
	
	var $output = null;
	
	var $command_line = false;
	
  function dispatch($url = null, $additionalParams = array()) {
  	if ($this->base === false) {
			$this->base = $this->baseUrl();
		}

		if (is_array($url)) {
			$url = $this->__extractParams($url, $additionalParams);
		} else {
			if ($url) {
				$_GET['url'] = $url;
			}
			$url = $this->getUrl();
			$this->params = array_merge($this->parseParams($url), $additionalParams);
		}
    
		$this->here = $this->base . '/' . $url;
	  
	  $controller = $this->__getController();

		Router::setRequestInfo(array(
			$this->params, array('base' => $this->base, 'here' => $this->here, 'webroot' => $this->webroot)
		));
	  
	  if(!$controller)  {	    
	    $controller = new ApplicationController();
	    $this->_invoke($controller, $this->params, true);
	    return;
	  }
	  	
		$controller->base = $this->base;
		$controller->here = $this->here;
		$controller->webroot = $this->webroot;
		$controller->params = $this->params;
		$controller->action = $this->params['action'];
  		
		if (!empty($this->params['data'])) {
			$controller->data = $this->params['data'];
		} else {
			$controller->data = null;
		}	
		$this->_invoke($controller, $this->params);
  
  }

/**
* This is where things get returned to the browser
*/
  function _invoke($controller, $params, $action_missing=false) {
 
		$keys = array_flip(get_class_methods($controller));
		if(array_key_exists($this->params["action"], $keys)) {
		  // Do something here when the action exists perhaps call the action?
		  $this->output = $controller->render($this->params['action'], $this->params['controller'], $this->params['action']);
		} else if($action_missing == true){
		  $this->output = $controller->render("missing_action", "missing", $this->params['action']);
		} else if($action_missing == false) {
		  $this->output = $controller->render("missing_action", $this->params['controller'], $this->params['action']);		  
		}
		
		if($this->command_line) {
		  // Don't output the content if it's from the command line 
		} else {
		  // Since it's not from the command line output the content
		  echo $this->output;
		}
  }


	function getUrl($uri = null, $base = null) {
		if (empty($_GET['url'])) {
			if ($uri == null) {
				$uri = $this->uri();
			}
			if ($base == null) {
				$base = $this->base;
			}
			$url = null;
			$tmpUri = preg_replace('/^(?:\?)?(?:\/)?/', '', $uri);
			$baseDir = preg_replace('/^\//', '', dirname($base)) . '/';

			if ($tmpUri === '/' || $tmpUri == $baseDir || $tmpUri == $base) {
				$url = $_GET['url'] = '/';
			} else {
				if ($base && strpos($uri, $base) !== false) {
					$elements = explode($base, $uri);
				} elseif (preg_match('/^[\/\?\/|\/\?|\?\/]/', $uri)) {
					$elements = array(1 => preg_replace('/^[\/\?\/|\/\?|\?\/]/', '', $uri));
				} else {
					$elements = array();
				}

				if (!empty($elements[1])) {
					$_GET['url'] = $elements[1];
					$url = $elements[1];
				} else {
					$url = $_GET['url'] = '/';
				}

				if (strpos($url, '/') === 0 && $url != '/') {
					$url = $_GET['url'] = substr($url, 1);
				}
			}
		} else {
			$url = $_GET['url'];
		}
		if ($url{0} == '/') {
			$url = substr($url, 1);
		}
		return $url;
	}
  
  
  function __extractParams($url, $additionalParams) {
    
  }
  
  function parseParams($fromUrl) {
    $params = array();
    include CONFIGS . 'routes.php';
		$params = array_merge(Router::parse($fromUrl), $params);
	
		if (strlen($params['action']) === 0) {
			$params['action'] = 'index';
		}
	 
	 if (isset($_GET)) {
			$url = (ini_get('magic_quotes_gpc') === '1' ? stripslashes_deep($_GET) : $_GET );
			if (isset($params['url'])) {
				$params['url'] = array_merge($params['url'], $url);
			} else {
				$params['url'] = $url;
			}
		}	
		
		return $params;
  }
  

/**
 * Get controller to use, either plugin controller or application controller
 *
 * @param array $params Array of parameters
 * @return mixed name of controller if not loaded, or object if loaded
 * @access private
 */
	function &__getController($params = null) {
		if (!is_array($params)) {
			$original = $params = $this->params;
		}
		
		$controller = false;
		
		$ctrlClass = Inflector::camelize($params['controller']);
	  $ctrlClass = $ctrlClass . 'Controller';
	
	  if($this->__loadControllerFile($ctrlClass)) {
	    $controller = new $ctrlClass();
	  } else {
	    $controller = false;
	  }
	
		return $controller;
	}
  
  function __loadControllerFile($className=null) {
    $app_dir = APP_BASE_URL . DS . "controllers" . DS;
    if(!class_exists("ApplicationController")) {
      @include_once $app_dir . "application_controller.php";
    }
    if(!class_exists("ApplicationController")) {
      return false;
    }
    
    $name = Inflector::underscore($className);
    $file_path = $app_dir . $name . ".php";
    @include_once $file_path;
    
    if(!class_exists($className)) {
      return false;
    } else {
      return true;
    }
  }
  
  /**
 * Returns the REQUEST_URI from the server environment, or, failing that,
 * constructs a new one, using the PHP_SELF constant and other variables.
 *
 * TODO: It really needs to be broken down into smaller methods, so testing it can be
 * much easier.
 *  
 * @return string URI
 * @access public
 */
	function uri() {
		foreach (array('HTTP_X_REWRITE_URL', 'REQUEST_URI', 'argv') as $var) {
			if ($uri = env($var)) {
				if ($var == 'argv') {
					$uri = $uri[0];
				}
				break;
			}
		}
	  
		$base = preg_replace('/^\//', '', '' . APP_BASE_URL);
  
		if ($base) {
			$uri = preg_replace('/^(?:\/)?(?:' . preg_quote($base, '/') . ')?(?:url=)?/', '', $uri);
		}
		if (PHP_SAPI == 'isapi') {
			$uri = preg_replace('/^(?:\/)?(?:\/)?(?:\?)?(?:url=)?/', '', $uri);
		}
		if (!empty($uri)) {
			if (key($_GET) && strpos(key($_GET), '?') !== false) {
				unset($_GET[key($_GET)]);
			}
		
			$uri = preg_split('/\?/', $uri, 2);
      
			if (isset($uri[1])) {
				parse_str($uri[1], $_GET);
			}
			
			$uri = $uri[0];
		} elseif (empty($uri) && is_string(env('QUERY_STRING'))) {
			$uri = env('QUERY_STRING');
		}
		
		if (strpos($uri, 'index.php') !== false) {
			list(, $uri) = explode('index.php', $uri, 2);
		}
		if (empty($uri) || $uri == '/' || $uri == '//') {
			return '';
		}
		
		return str_replace('//', '/', '/' . $uri);
	}

/**
 * Returns a base URL and sets the proper webroot
 *
 * @return string Base URL
 * @access public
 */  
	function baseUrl() {
		$dir = $webroot = null;
		$config = array('base' => false, 'baseUrl' => false, 'dir' => APP_DIR, 'webroot' => WEBROOT_DIR);
	
	  if(!is_array($config)) {
      $config = array();
	  }
		extract($config);
	  
		if (!$base) {
			$base = $this->base;
		}
		if ($base !== false) {
			$this->webroot = $base . '/';
			return $this->base = $base;
		}
		
		if (!$baseUrl) {
			$replace = array('<', '>', '*', '\'', '"');
			$base = str_replace($replace, '', dirname(env('PHP_SELF')));
      
      if ($webroot === 'webroot' && $webroot === basename($base)) {
				$base = dirname($base);
			}
			
			if ($webroot === 'public' && $webroot === basename($base)) {
				$base = dirname($base);
			}
			
			if ($dir === 'app' && $dir === basename($base)) {
				$base = dirname($base);
			}

			if ($base === DS || $base === '.') {
				$base = '';
			}
      
			$this->webroot = $base .'/';
			return $base;
		}
		
		$file = null;

		if ($baseUrl) {
			$file = '/' . basename($baseUrl);
			$base = dirname($baseUrl);

			if ($base === DS || $base === '.') {
				$base = '';
			}
			$this->webroot = $base .'/';

			if (strpos($this->webroot, $dir) === false) {
				$this->webroot .= $dir . '/' ;
			}
			if (strpos($this->webroot, $webroot) === false) {
				$this->webroot .= $webroot . '/';
			}
			return $base . $file;
		}
		return false;
	}
	
}

?>