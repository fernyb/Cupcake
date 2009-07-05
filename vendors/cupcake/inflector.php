<?php
class Inflector {
/**
 * Pluralized words.
 *
 * @var array
 * @access private
 **/
	var $pluralized = array();
/**
 * List of pluralization rules in the form of pattern => replacement.
 *
 * @var array
 * @access public
 * @link http://book.cakephp.org/view/47/Custom-Inflections
 **/
	var $pluralRules = array();
/**
 * Singularized words.
 *
 * @var array
 * @access private
 **/
	var $singularized = array();
/**
 * List of singularization rules in the form of pattern => replacement.
 *
 * @var array
 * @access public
 * @link http://book.cakephp.org/view/47/Custom-Inflections
 **/
	var $singularRules = array();
/**
 * Plural rules from inflections.php
 *
 * @var array
 * @access private
 **/
	var $__pluralRules = array();
/**
 * Un-inflected plural rules from inflections.php
 *
 * @var array
 * @access private
 **/
	var $__uninflectedPlural = array();
/**
 * Irregular plural rules from inflections.php
 *
 * @var array
 * @access private
 **/
	var $__irregularPlural = array();
/**
 * Singular rules from inflections.php
 *
 * @var array
 * @access private
 **/
	var $__singularRules = array();
/**
 * Un-inflectd singular rules from inflections.php
 *
 * @var array
 * @access private
 **/
	var $__uninflectedSingular = array();
/**
 * Irregular singular rules from inflections.php
 *
 * @var array
 * @access private
 **/
	var $__irregularSingular = array();

/**
 * Gets a reference to the Inflector object instance
 *
 * @return object
 * @access public
 */
	function &getInstance() {
		static $instance = array();

		if (!$instance) {
			$instance[0] = new Inflector();
		}
		return $instance[0];
	}

/**
 * Returns the given lower_case_and_underscored_word as a CamelCased word.
 *
 * @param string $lower_case_and_underscored_word Word to camelize
 * @return string Camelized word. LikeThis.
 * @access public
 * @static
 * @link http://book.cakephp.org/view/572/Class-methods
 */
	function camelize($lowerCaseAndUnderscoredWord) {
		return str_replace(" ", "", ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord)));
	}	

/**
 * Returns the given camelCasedWord as an underscored_word.
 *
 * @param string $camelCasedWord Camel-cased word to be "underscorized"
 * @return string Underscore-syntaxed version of the $camelCasedWord
 * @access public
 * @static
 * @link http://book.cakephp.org/view/572/Class-methods
 */
	function underscore($camelCasedWord) {
		return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $camelCasedWord));
	}

/**
 * Returns Cake model class name ("Person" for the database table "people".) for given database table.
 *
 * @param string $tableName Name of database table to get class name for
 * @return string Class name
 * @access public
 * @static
 * @link http://book.cakephp.org/view/572/Class-methods
 */
	function classify($tableName) {
		return Inflector::camelize(Inflector::singularize($tableName));
	}
	

/**
 * Return $word in singular form.
 *
 * @param string $word Word in plural
 * @return string Word in singular
 * @access public
 * @static
 * @link http://book.cakephp.org/view/572/Class-methods
 */
	function singularize($word) {
		$_this =& Inflector::getInstance();
		if (!isset($_this->singularRules) || empty($_this->singularRules)) {
			$_this->__initSingularRules();
		}

		if (isset($_this->singularized[$word])) {
			return $_this->singularized[$word];
		}
		extract($_this->singularRules);

		if (!isset($regexUninflected) || !isset($regexIrregular)) {
			$regexUninflected = __enclose(join( '|', $uninflected));
			$regexIrregular = __enclose(join( '|', array_keys($irregular)));
			$_this->singularRules['regexUninflected'] = $regexUninflected;
			$_this->singularRules['regexIrregular'] = $regexIrregular;
		}

		if (preg_match('/(.*)\\b(' . $regexIrregular . ')$/i', $word, $regs)) {
			$_this->singularized[$word] = $regs[1] . substr($word, 0, 1) . substr($irregular[strtolower($regs[2])], 1);
			return $_this->singularized[$word];
		}

		if (preg_match('/^(' . $regexUninflected . ')$/i', $word, $regs)) {
			$_this->singularized[$word] = $word;
			return $word;
		}

		foreach ($singularRules as $rule => $replacement) {
			if (preg_match($rule, $word)) {
				$_this->singularized[$word] = preg_replace($rule, $replacement, $word);
				return $_this->singularized[$word];
			}
		}
		$_this->singularized[$word] = $word;
		return $word;
	}



/**
 * Initializes singular inflection rules.
 *
 * @return void
 * @access protected
 */
	function __initSingularRules() {
		$coreSingularRules = array(
			'/(s)tatuses$/i' => '\1\2tatus',
			'/^(.*)(menu)s$/i' => '\1\2',
			'/(quiz)zes$/i' => '\\1',
			'/(matr)ices$/i' => '\1ix',
			'/(vert|ind)ices$/i' => '\1ex',
			'/^(ox)en/i' => '\1',
			'/(alias)(es)*$/i' => '\1',
			'/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
			'/([ftw]ax)es/' => '\1',
			'/(cris|ax|test)es$/i' => '\1is',
			'/(shoe)s$/i' => '\1',
			'/(o)es$/i' => '\1',
			'/ouses$/' => 'ouse',
			'/uses$/' => 'us',
			'/([m|l])ice$/i' => '\1ouse',
			'/(x|ch|ss|sh)es$/i' => '\1',
			'/(m)ovies$/i' => '\1\2ovie',
			'/(s)eries$/i' => '\1\2eries',
			'/([^aeiouy]|qu)ies$/i' => '\1y',
			'/([lr])ves$/i' => '\1f',
			'/(tive)s$/i' => '\1',
			'/(hive)s$/i' => '\1',
			'/(drive)s$/i' => '\1',
			'/([^fo])ves$/i' => '\1fe',
			'/(^analy)ses$/i' => '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
			'/([ti])a$/i' => '\1um',
			'/(p)eople$/i' => '\1\2erson',
			'/(m)en$/i' => '\1an',
			'/(c)hildren$/i' => '\1\2hild',
			'/(n)ews$/i' => '\1\2ews',
			'/^(.*us)$/' => '\\1',
			'/s$/i' => '');

		$coreUninflectedSingular = array(
			'.*[nrlm]ese', '.*deer', '.*fish', '.*measles', '.*ois', '.*pox', '.*sheep', '.*ss', 'Amoyese',
			'bison', 'Borghese', 'bream', 'breeches', 'britches', 'buffalo', 'cantus', 'carp', 'chassis', 'clippers',
			'cod', 'coitus', 'Congoese', 'contretemps', 'corps', 'debris', 'diabetes', 'djinn', 'eland', 'elk',
			'equipment', 'Faroese', 'flounder', 'Foochowese', 'gallows', 'Genevese', 'Genoese', 'Gilbertese', 'graffiti',
			'headquarters', 'herpes', 'hijinks', 'Hottentotese', 'information', 'innings', 'jackanapes', 'Kiplingese',
			'Kongoese', 'Lucchese', 'mackerel', 'Maltese', 'media', 'mews', 'moose', 'mumps', 'Nankingese', 'news',
			'nexus', 'Niasese', 'Pekingese', 'Piedmontese', 'pincers', 'Pistoiese', 'pliers', 'Portuguese', 'proceedings',
			'rabies', 'rice', 'rhinoceros', 'salmon', 'Sarawakese', 'scissors', 'sea[- ]bass', 'series', 'Shavese', 'shears',
			'siemens', 'species', 'swine', 'testes', 'trousers', 'trout', 'tuna', 'Vermontese', 'Wenchowese',
			'whiting', 'wildebeest', 'Yengeese');

		$coreIrregularSingular = array(
			'atlases' => 'atlas',
			'beefs' => 'beef',
			'brothers' => 'brother',
			'children' => 'child',
			'corpuses' => 'corpus',
			'cows' => 'cow',
			'ganglions' => 'ganglion',
			'genies' => 'genie',
			'genera' => 'genus',
			'graffiti' => 'graffito',
			'hoofs' => 'hoof',
			'loaves' => 'loaf',
			'men' => 'man',
			'monies' => 'money',
			'mongooses' => 'mongoose',
			'moves' => 'move',
			'mythoi' => 'mythos',
			'numina' => 'numen',
			'occiputs' => 'occiput',
			'octopuses' => 'octopus',
			'opuses' => 'opus',
			'oxen' => 'ox',
			'penises' => 'penis',
			'people' => 'person',
			'sexes' => 'sex',
			'soliloquies' => 'soliloquy',
			'testes' => 'testis',
			'trilbys' => 'trilby',
			'turfs' => 'turf');

/*
		$singularRules = Set::pushDiff($this->__singularRules, $coreSingularRules);
		$uninflected = Set::pushDiff($this->__uninflectedSingular, $coreUninflectedSingular);
		$irregular = Set::pushDiff($this->__irregularSingular, $coreIrregularSingular);
*/
		$singularRules = $coreSingularRules;
		$uninflected = $coreUninflectedSingular;
		$irregular = $coreIrregularSingular;
		
		$this->singularRules = array('singularRules' => $singularRules, 'uninflected' => $uninflected, 'irregular' => $irregular);
		$this->singularized = array();
	}

/**
 * Returns the given underscored_word_group as a Human Readable Word Group.
 * (Underscores are replaced by spaces and capitalized following words.)
 *
 * @param string $lower_case_and_underscored_word String to be made more readable
 * @return string Human-readable string
 * @access public
 * @static
 * @link http://book.cakephp.org/view/572/Class-methods
 */
	function humanize($lowerCaseAndUnderscoredWord) {
		return ucwords(str_replace("_", " ", $lowerCaseAndUnderscoredWord));
	}
				
}
	
	
/**
 * Enclose a string for preg matching.
 *
 * @param string $string String to enclose
 * @return string Enclosed string
 */
	function __enclose($string) {
		return '(?:' . $string . ')';
	}
	
		
?>