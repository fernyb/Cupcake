<?php
class Router {
  
  var $routes = array();

// List of action prefixes used in connected routes
  var $__prefixes = array();

/**
 * 'Constant' regular expression definitions for named route elements
 *
 * @var array
 * @access private
 */
  	var $__named = array(
  		'Action'	=> 'index|show|add|create|edit|update|remove|del|delete|view|item',
  		'Year'		=> '[12][0-9]{3}',
  		'Month'		=> '0[1-9]|1[012]',
  		'Day'		=> '0[1-9]|[12][0-9]|3[01]',
  		'ID'		=> '[0-9]+',
  		'UUID'		=> '[A-Fa-f0-9]{8}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{4}-[A-Fa-f0-9]{12}'
  	);
		
		static public $instance = false;
		
  	static function getInstance() {
  	  if(!self::$instance) {
  	    self::$instance = new Router();
  	  }
  	  return self::$instance;
	  }
  	
  	static function connect($route, $default = array(), $params = array()) {
  		$_this = Router::getInstance();

  		if (!isset($default['action'])) {
  			$default['action'] = 'index';
  		}
  		
  		$_this->routes[] = array($route, $default, $params);
  		return $_this->routes;
	}
	
		
/**
 * Finds URL for specified action.
 *
 * Returns an URL pointing to a combination of controller and action. Param
 * $url can be:
 *
 * - Empty - the method will find adress to actuall controller/action.
 * - '/' - the method will find base URL of application.
 * - A combination of controller/action - the method will find url for it.
 *
 * @param mixed $url Cake-relative URL, like "/products/edit/92" or "/presidents/elect/4"
 *   or an array specifying any of the following: 'controller', 'action',
 *   and/or 'plugin', in addition to named arguments (keyed array elements),
 *   and standard URL arguments (indexed array elements)
 * @param mixed $full If (bool) true, the full base URL will be prepended to the result.
 *   If an array accepts the following keys
 *    - escape - used when making urls embedded in html escapes query string '&'
 *    - full - if true the full base URL will be prepended.
 * @return string Full translated URL with base path.
 * @access public
 * @static
 */
	function url($url = null, $full = false) {
		$_this = Router::getInstance();
		$defaults = $params = array('controller' => null, 'action' => 'index');
      
		if (is_bool($full)) {
			$escape = false;
		} else {
			extract(array_merge(array('escape' => false, 'full' => false), $full));
		}
    
		if (!empty($_this->__params)) {
			if (isset($this) && !isset($this->params['requested'])) {
				$params = $_this->__params[0];
			} else {
				$params = end($_this->__params);
			}
		}
		$path = array('base' => null);

		if (!empty($_this->__paths)) {
			if (isset($this) && !isset($this->params['requested'])) {
				$path = $_this->__paths[0];
			} else {
				$path = end($_this->__paths);
			}
		}
    
		$base = $path['base'];
		$extension = $output = $mapped = $q = $frag = null;

/*
  Begin: If url is Array
  TODO: Code below really needs to be moved into another method that will make it easier to test and refactor
*/
		if (is_array($url)) {
			if (isset($url['base']) && $url['base'] === false) {
				$base = null;
				unset($url['base']);
			}
			if (isset($url['full_base']) && $url['full_base'] === true) {
				$full = true;
				unset($url['full_base']);
			}
			if (isset($url['?'])) {
				$q = $url['?'];
				unset($url['?']);
			}
			if (isset($url['#'])) {
				$frag = '#' . urlencode($url['#']);
				unset($url['#']);
			}
			if (empty($url['action'])) {
				if (empty($url['controller']) || $params['controller'] === $url['controller']) {
					$url['action'] = $params['action'];
				} else {
					$url['action'] = 'index';
				}
			}

			$plugin = false;

			$url = array_merge(array('controller' => $params['controller'], 'plugin' => $params['plugin']), $_this->filter($url, true));

			if ($plugin !== false) {
				$url['plugin'] = $plugin;
			}

			if (isset($url['ext'])) {
				$extension = '.' . $url['ext'];
				unset($url['ext']);
			}
			$match = false;
    
			foreach ($_this->routes as $i => $route) {
				if (count($route) === 3) {
					$route = $_this->compile($i);
				}
				$originalUrl = $url;

				if (isset($route[4]['persist'], $_this->__params[0])) {
					$url = array_merge(array_intersect_key($params, Router::combine($route[4]['persist'], '/')), $url);
				}
				if ($match = $_this->mapRouteElements($route, $url)) {
					$output = trim($match, '/');
					$url = array();
					break;
				}
				$url = $originalUrl;
			}

			$named = $args = array();
			$skip = array(
				'bare', 'action', 'controller', 'plugin', 'ext', '?', '#', 'prefix', $_this->__admin
			);

			$keys = array_values(array_diff(array_keys($url), $skip));
			$count = count($keys);

			// Remove this once parsed URL parameters can be inserted into 'pass'
			for ($i = 0; $i < $count; $i++) {
				if ($i === 0 && is_numeric($keys[$i]) && in_array('id', $keys)) {
					$args[0] = $url[$keys[$i]];
				} elseif (is_numeric($keys[$i]) || $keys[$i] === 'id') {
					$args[] = $url[$keys[$i]];
				} else {
					$named[$keys[$i]] = $url[$keys[$i]];
				}
			}

			if ($match === false) {
				list($args, $named)  = array(Router::filter($args, true), Router::filter($named));
				if (!empty($url[$_this->__admin])) {
					$url['action'] = str_replace($_this->__admin . '_', '', $url['action']);
				}

				if (empty($named) && empty($args) && (!isset($url['action']) || $url['action'] === 'index')) {
					$url['action'] = null;
				}

				$urlOut = Router::filter(array($url['controller'], $url['action']));
  
				if (isset($url['plugin']) && $url['plugin'] != $url['controller']) {
					array_unshift($urlOut, $url['plugin']);
				}

				if ($_this->__admin && isset($url[$_this->__admin])) {
					array_unshift($urlOut, $_this->__admin);
				}
			
				$output = join('/', $urlOut) . '/';
			}

			if (!empty($args)) {
				$args = join('/', $args);
				if ($output{strlen($output) - 1} != '/') {
					$args = '/'. $args;
				}
				$output .= $args;
			}

			if (!empty($named)) {
				foreach ($named as $name => $value) {
					$output .= '/' . $name . $_this->named['separator'] . $value;
				}
			}
		
			$output = str_replace('//', '/', $base . '/' . $output);
		  $url = $output;
		}
/*
  End: if url is Array
*/

		if (((strpos($url, '://')) || (strpos($url, 'javascript:') === 0) || (strpos($url, 'mailto:') === 0)) || (!strncmp($url, '#', 1))) {
		  return $url;
		}
		if (empty($url)) {
			if (!isset($path['here'])) {
				$path['here'] = '/';
			}
			$output = $path['here'];
		} elseif (substr($url, 0, 1) === '/') {
			$output = $base . $url;
		} else {
			$output = $base . '/';
			$output .= Inflector::underscore($params['controller']) . '/' . $url;
		}
		$output = str_replace('//', '/', $output);
	
		if ($full && defined('FULL_BASE_URL')) {
			$output = FULL_BASE_URL . $output;
		}
		if (!empty($extension) && substr($output, -1) === '/') {
			$output = substr($output, 0, -1);
		}
		return $output . $extension . $_this->queryString($q, array(), $escape) . $frag;
	}


/**
 * Maps a URL array onto a route and returns the string result, or false if no match
 *
 * @param array $route Route Route
 * @param array $url URL URL to map
 * @return mixed Result (as string) or false if no match
 * @access public
 * @static
 */
	function mapRouteElements($route, $url) {
		if (isset($route[3]['prefix'])) {
			$prefix = $route[3]['prefix'];
			unset($route[3]['prefix']);
		}

		$pass = array();
		$defaults = $route[3];
		$routeParams = $route[2];
		$params = Router::diff($url, $defaults);
		$urlInv = array_combine(array_values($url), array_keys($url));

		$i = 0;
		while (isset($defaults[$i])) {
			if (isset($urlInv[$defaults[$i]])) {
				if (!in_array($defaults[$i], $url) && is_int($urlInv[$defaults[$i]])) {
					return false;
				}
				unset($urlInv[$defaults[$i]], $defaults[$i]);
			} else {
				return false;
			}
			$i++;
		}

		foreach ($params as $key => $value) {
			if (is_int($key)) {
				$pass[] = $value;
				unset($params[$key]);
			}
		}
		list($named, $params) = Router::getNamedElements($params);

		if (!strpos($route[0], '*') && (!empty($pass) || !empty($named))) {
			return false;
		}

		$urlKeys = array_keys($url);
		$paramsKeys = array_keys($params);
		$defaultsKeys = array_keys($defaults);

		if (!empty($params)) {
			if (array_diff($paramsKeys, $routeParams) != array()) {
				return false;
			}
			$required = array_values(array_diff($routeParams, $urlKeys));
			$reqCount = count($required);

			for ($i = 0; $i < $reqCount; $i++) {
				if (array_key_exists($required[$i], $defaults) && $defaults[$required[$i]] === null) {
					unset($required[$i]);
				}
			}
		}
		$isFilled = true;

		if (!empty($routeParams)) {
			$filled = array_intersect_key($url, array_combine($routeParams, array_keys($routeParams)));
			$isFilled = (array_diff($routeParams, array_keys($filled)) === array());
			if (!$isFilled && empty($params)) {
				return false;
			}
		}

		if (empty($params)) {
			return Router::__mapRoute($route, array_merge($url, compact('pass', 'named', 'prefix')));
		} elseif (!empty($routeParams) && !empty($route[3])) {

			if (!empty($required)) {
				return false;
			}
			foreach ($params as $key => $val) {
				if ((!isset($url[$key]) || $url[$key] != $val) || (!isset($defaults[$key]) || $defaults[$key] != $val) && !in_array($key, $routeParams)) {
					if (!isset($defaults[$key])) {
						continue;
					}
					return false;
				}
			}
		} else {
			if (empty($required) && $defaults['plugin'] === $url['plugin'] && $defaults['controller'] === $url['controller'] && $defaults['action'] === $url['action']) {
				return Router::__mapRoute($route, array_merge($url, compact('pass', 'named', 'prefix')));
			}
			return false;
		}

		if (!empty($route[4])) {
			foreach ($route[4] as $key => $reg) {
				if (array_key_exists($key, $url) && !preg_match('#' . $reg . '#', $url[$key])) {
					return false;
				}
			}
		}
		return Router::__mapRoute($route, array_merge($filled, compact('pass', 'named', 'prefix')));
	}


/**
 * Merges URL parameters into a route string
 *
 * @param array $route Route
 * @param array $params Parameters
 * @return string Merged URL with parameters
 * @access private
 */
	function __mapRoute($route, $params = array()) {
		if (isset($params['plugin']) && isset($params['controller']) && $params['plugin'] === $params['controller']) {
			unset($params['controller']);
		}

		if (isset($params['prefix']) && isset($params['action'])) {
			$params['action'] = str_replace($params['prefix'] . '_', '', $params['action']);
			unset($params['prefix']);
		}

		if (isset($params['pass']) && is_array($params['pass'])) {
			$params['pass'] = implode('/', Router::filter($params['pass'], true));
		} elseif (!isset($params['pass'])) {
			$params['pass'] = '';
		}

		if (isset($params['named'])) {
			if (is_array($params['named'])) {
				$count = count($params['named']);
				$keys = array_keys($params['named']);
				$named = array();

				for ($i = 0; $i < $count; $i++) {
					$named[] = $keys[$i] . $this->named['separator'] . $params['named'][$keys[$i]];
				}
				$params['named'] = join('/', $named);
			}
			$params['pass'] = str_replace('//', '/', $params['pass'] . '/' . $params['named']);
		}
		$out = $route[0];

		foreach ($route[2] as $key) {
			$string = null;
			if (isset($params[$key])) {
				$string = $params[$key];
				unset($params[$key]);
			} else {
				$key = $key . '/';
			}
			$out = str_replace(':' . $key, $string, $out);
		}

		if (strpos($route[0], '*')) {
			$out = str_replace('*', $params['pass'], $out);
		}

		return $out;
	}
	

/**
 * Takes an array of URL parameters and separates the ones that can be used as named arguments
 *
 * @param array $params			Associative array of URL parameters.
 * @param string $controller	Name of controller being routed.  Used in scoping.
 * @param string $action	 	Name of action being routed.  Used in scoping.
 * @return array
 * @access public
 * @static
 */
	function getNamedElements($params, $controller = null, $action = null) {
		$_this = Router::getInstance();
		$named = array();

		foreach ($params as $param => $val) {
			if (isset($_this->named['rules'][$param])) {
				$rule = $_this->named['rules'][$param];
				if (Router::matchNamed($param, $val, $rule, compact('controller', 'action'))) {
					$named[$param] = $val;
					unset($params[$param]);
				}
			}
		}
		return array($named, $params);
	}
		

	// Filter method is a depended in url method
	function filter($var, $isArray = false) {
		if (is_array($var) && (!empty($var) || $isArray)) {
			return array_filter($var, array('Router', 'filter'));
		}
		if ($var === 0 || $var === '0' || !empty($var)) {
			return true;
		}
		return false;
	}


/**
 * Creates an associative array using a $path1 as the path to build its keys, and optionally
 * $path2 as path to get the values. If $path2 is not specified, all values will be initialized
 * to null (useful for Set::merge). You can optionally group the values by what is obtained when
 * following the path specified in $groupPath.
 *
 * @param array $data Array from where to extract keys and values
 * @param mixed $path1 As an array, or as a dot-separated string.
 * @param mixed $path2 As an array, or as a dot-separated string.
 * @param string $groupPath As an array, or as a dot-separated string.
 * @return array Combined array
 * @access public
 * @static
 */
	function combine($data, $path1 = null, $path2 = null, $groupPath = null) {
		if (empty($data)) {
			return array();
		}

		if (is_object($data)) {
			$data = get_object_vars($data);
		}
		
		if (is_array($path1)) {
			$format = array_shift($path1);
			$keys = Set::format($data, $format, $path1);
		} else {
			$keys = Set::extract($data, $path1);
		}

		if (!empty($path2) && is_array($path2)) {
			$format = array_shift($path2);
			$vals = Set::format($data, $format, $path2);

		} elseif (!empty($path2)) {
			$vals = Set::extract($data, $path2);

		} else {
			$count = count($keys);
			for ($i = 0; $i < $count; $i++) {
				$vals[$i] = null;
			}
		}

		if ($groupPath != null) {
			$group = Set::extract($data, $groupPath);
			if (!empty($group)) {
				$c = count($keys);
				for ($i = 0; $i < $c; $i++) {
					if (!isset($group[$i])) {
						$group[$i] = 0;
					}
					if (!isset($out[$group[$i]])) {
						$out[$group[$i]] = array();
					}
					$out[$group[$i]][$keys[$i]] = $vals[$i];
				}
				return $out;
			}
		}

		return array_combine($keys, $vals);
	}
	
/**
 * Computes the difference between a Set and an array, two Sets, or two arrays
 *
 * @param mixed $val1 First value
 * @param mixed $val2 Second value
 * @return array Computed difference
 * @access public
 * @static
 */
	function diff($val1, $val2 = null) {
		if (empty($val1)) {
			return (array)$val2;
		}
		if (empty($val2)) {
			return (array)$val1;
		}
		$out = array();

		foreach ($val1 as $key => $val) {
			$exists = array_key_exists($key, $val2);

			if ($exists && $val2[$key] != $val) {
				$out[$key] = $val;
			} elseif (!$exists) {
				$out[$key] = $val;
			}
			unset($val2[$key]);
		}

		foreach ($val2 as $key => $val) {
			if (!array_key_exists($key, $out)) {
				$out[$key] = $val;
			}
		}
		return $out;
	}
	
	
/**
 * Generates a well-formed querystring from $q
 *
 * @param mixed $q Query string
 * @param array $extra Extra querystring parameters.
 * @param bool $escape Whether or not to use escaped &
 * @return array
 * @access public
 * @static
 */
	function queryString($q, $extra = array(), $escape = false) {
		if (empty($q) && empty($extra)) {
			return null;
		}
	
		$join = '&';
		if ($escape === true) {
			$join = '&amp;';
		}
		$out = '';

		if (is_array($q)) {
			$q = array_merge($extra, $q);
		} else {
			$out = $q;
			$q = $extra;
		}
		$out .= http_build_query($q, null, $join);
		if (isset($out[0]) && $out[0] != '?') {
			$out = '?' . $out;
		}
		return $out;
	}
	
	
/**
 * Strip escape characters from parameter values.
 *
 * @param mixed $param Either an array, or a string
 * @return mixed Array or string escaped
 * @access public
 * @static
 */
	function stripEscape($param) {
		$_this = Router::getInstance();
		if (!is_array($param) || empty($param)) {
			if (is_bool($param)) {
				return $param;
			}

			$return = preg_replace('/^(?:[\\t ]*(?:-!)+)/', '', $param);
			return $return;
		}
		foreach ($param as $key => $value) {
			if (is_string($value)) {
				$return[$key] = preg_replace('/^(?:[\\t ]*(?:-!)+)/', '', $value);
			} else {
				foreach ($value as $array => $string) {
					$return[$key][$array] = $_this->stripEscape($string);
				}
			}
		}
		return $return;
	}

/**
 * Parses given URL and returns an array of controllers, action and parameters
 * taken from that URL.
 *
 * @param string $url URL to be parsed
 * @return array Parsed elements from URL
 * @access public
 * @static
 */
	function parse($url) {
		$_this = Router::getInstance();
		if (!$_this->__defaultsMapped) {
			$_this->__connectDefaultRoutes();
		}
		$out = array('pass' => array(), 'named' => array());
		$r = $ext = null;

		if (ini_get('magic_quotes_gpc') === '1') {
			$url = stripslashes_deep($url);
		}

		if ($url && strpos($url, '/') !== 0) {
			$url = '/' . $url;
		}
		if (strpos($url, '?') !== false) {
			$url = substr($url, 0, strpos($url, '?'));
		}
		extract($_this->__parseExtension($url));

		foreach ($_this->routes as $i => $route) {
			if (count($route) === 3) {
				$route = $_this->compile($i);
			}

			if (($r = $_this->__matchRoute($route, $url)) !== false) {
				$_this->__currentRoute[] = $route;
				list($route, $regexp, $names, $defaults, $params) = $route;
				$argOptions = array();

				if (array_key_exists('named', $params)) {
					$argOptions['named'] = $params['named'];
					unset($params['named']);
				}
				if (array_key_exists('greedy', $params)) {
					$argOptions['greedy'] = $params['greedy'];
					unset($params['greedy']);
				}
				array_shift($r);

				foreach ($names as $name) {
					$out[$name] = null;
				}
				if (is_array($defaults)) {
					foreach ($defaults as $name => $value) {
						if (preg_match('#[a-zA-Z_\-]#i', $name)) {
							$out[$name] = $value;
						} else {
							$out['pass'][] = $value;
						}
					}
				}

				foreach ($r as $key => $found) {
					if (empty($found) && $found != 0) {
						continue;
					}

					if (isset($names[$key])) {
						$out[$names[$key]] = $_this->stripEscape($found);
					} elseif (isset($names[$key]) && empty($names[$key]) && empty($out[$names[$key]])) {
						break;
					} else {
						$argOptions['context'] = array('action' => $out['action'], 'controller' => $out['controller']);
						extract($_this->getArgs($found, $argOptions));
						$out['pass'] = array_merge($out['pass'], $pass);
						$out['named'] = $named;
					}
				}

				if (isset($params['pass'])) {
					for ($j = count($params['pass']) - 1; $j > -1; $j--) {
						if (isset($out[$params['pass'][$j]])) {
							array_unshift($out['pass'], $out[$params['pass'][$j]]);
						}
					}
				}
				break;
			}
		}

		if (!empty($ext)) {
			$out['url']['ext'] = $ext;
		}
		return $out;
	}	

/**
 * Connects the default, built-in routes, including admin routes, and (deprecated) web services
 * routes.
 *
 * @return void
 * @access private
 */
	function __connectDefaultRoutes() {
		if ($this->__defaultsMapped) {
			return;
		}
		
		$this->connect('/:controller', array('action' => 'index'));
		$this->connect('/:controller/:action/*');

		if ($this->named['rules'] === false) {
			$this->connectNamed(true);
		}
		$this->__defaultsMapped = true;
	}
	
	/**
 * Parses a file extension out of a URL, if Router::parseExtensions() is enabled.
 *
 * @param string $url
 * @return array Returns an array containing the altered URL and the parsed extension.
 * @access private
 */
	function __parseExtension($url) {
		$ext = null;

		if ($this->__parseExtensions) {
			if (preg_match('/\.[0-9a-zA-Z]*$/', $url, $match) === 1) {
				$match = substr($match[0], 1);
				if (empty($this->__validExtensions)) {
					$url = substr($url, 0, strpos($url, '.' . $match));
					$ext = $match;
				} else {
					foreach ($this->__validExtensions as $name) {
						if (strcasecmp($name, $match) === 0) {
							$url = substr($url, 0, strpos($url, '.' . $name));
							$ext = $match;
						}
					}
				}
			}
			if (empty($ext)) {
				$ext = 'html';
			}
		}
		return compact('ext', 'url');
	}

/**
 * Checks to see if the given URL matches the given route
 *
 * @param array $route
 * @param string $url
 * @return mixed Boolean false on failure, otherwise array
 * @access private
 */
	function __matchRoute($route, $url) {
		list($route, $regexp, $names, $defaults) = $route;

		if (!preg_match($regexp, $url, $r)) {
			return false;
		} else {
			foreach ($defaults as $key => $val) {
				if ($key{0} === '[' && preg_match('/^\[(\w+)\]$/', $key, $header)) {
					if (isset($this->__headerMap[$header[1]])) {
						$header = $this->__headerMap[$header[1]];
					} else {
						$header = 'http_' . $header[1];
					}

					$val = (array)$val;
					$h = false;

					foreach ($val as $v) {
						if (env(strtoupper($header)) === $v) {
							$h = true;
						}
					}
					if (!$h) {
						return false;
					}
				}
			}
		}
		return $r;
	}
	
/**
 * Builds a route regular expression
 *
 * @param string $route			An empty string, or a route string "/"
 * @param array $default		NULL or an array describing the default route
 * @param array $params			An array matching the named elements in the route to regular expressions which that element should match.
 * @return array
 * @see routes
 * @access public
 * @static
 */
	function writeRoute($route, $default, $params) {
		if (empty($route) || ($route === '/')) {
			return array('/^[\/]*$/', array());
		}
		$names = array();
		$elements = explode('/', $route);

		foreach ($elements as $element) {
			if (empty($element)) {
				continue;
			}
			$q = null;
			$element = trim($element);
			$namedParam = strpos($element, ':') !== false;

			if ($namedParam && preg_match('/^:([^:]+)$/', $element, $r)) {
				if (isset($params[$r[1]])) {
					if ($r[1] != 'plugin' && array_key_exists($r[1], $default)) {
						$q = '?';
					}
					$parsed[] = '(?:/(' . $params[$r[1]] . ')' . $q . ')' . $q;
				} else {
					$parsed[] = '(?:/([^\/]+))?';
				}
				$names[] = $r[1];
			} elseif ($element === '*') {
				$parsed[] = '(?:/(.*))?';
			} else if ($namedParam && preg_match_all('/(?!\\\\):([a-z_0-9]+)/i', $element, $matches)) {
				$matchCount = count($matches[1]);

				foreach ($matches[1] as $i => $name) {
					$pos = strpos($element, ':' . $name);
					$before = substr($element, 0, $pos);
					$element = substr($element, $pos + strlen($name) + 1);
					$after = null;

					if ($i + 1 === $matchCount && $element) {
						$after = preg_quote($element);
					}

					if ($i === 0) {
						$before = '/' . $before;
					}
					$before = preg_quote($before, '#');

					if (isset($params[$name])) {
						if (isset($default[$name]) && $name != 'plugin') {
							$q = '?';
						}
						$parsed[] = '(?:' . $before . '(' . $params[$name] . ')' . $q . $after . ')' . $q;
					} else {
						$parsed[] = '(?:' . $before . '([^\/]+)' . $after . ')?';
					}
					$names[] = $name;
				}
			} else {
				$parsed[] = '/' . $element;
			}
		}
		return array('#^' . join('', $parsed) . '[\/]*$#', $names);
	}
	
/**
 * Compiles a route by numeric key and returns the compiled expression, replacing
 * the existing uncompiled route.  Do not call statically.
 *
 * @param integer $i
 * @return array Returns an array containing the compiled route
 * @access public
 */
	function compile($i) {
		$route = $this->routes[$i];

		if (!list($pattern, $names) = $this->writeRoute($route[0], $route[1], $route[2])) {
			unset($this->routes[$i]);
			return array();
		}
		$this->routes[$i] = array(
			$route[0], $pattern, $names,
			array_merge(array('plugin' => null, 'controller' => null), (array)$route[1]),
			$route[2]
		);
		return $this->routes[$i];
	}	


/**
 * Clears all routes
 *
 * @access public
 * @static
 */
 static function clearRoutes() {
   $_this = Router::getInstance();
   $_this->routes = array();
   $_this->__prefixes = array();
 }
   

/**
 * Returns the list of prefixes used in connected routes
 *
 * @return array A list of prefixes used in connected routes
 * @access public
 * @static
 */
	function prefixes() {
		$_this = Router::getInstance();
		return $_this->__prefixes;
	}
	

/**
 * Takes parameter and path information back from the Dispatcher
 *
 * @param array $params Parameters and path information
 * @return void
 * @access public
 * @static
 */
	function setRequestInfo($params) {
		$_this = Router::getInstance();
		$defaults = array('plugin' => null, 'controller' => null, 'action' => null);
		$params[0] = array_merge($defaults, (array)$params[0]);
		$params[1] = array_merge($defaults, (array)$params[1]);
		list($_this->__params[], $_this->__paths[]) = $params;

		if (count($_this->__paths)) {
			if (isset($_this->__paths[0]['namedArgs'])) {
				foreach ($_this->__paths[0]['namedArgs'] as $arg => $value) {
					$_this->named['rules'][$arg] = true;
				}
			}
		}
	}
	
}


?>