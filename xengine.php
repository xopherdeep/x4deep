<?php
	/**
	 * Xengine Version 2.x
	 * @author XopherDeeP <heylisten@xtiv.net>
	 * @version v2.2.3
	**/
	
	/*
	  The Artistic License 2.0

           Copyright (c) 2014 MidKnight Knerd

     Everyone is permitted to copy and distribute verbatim copies
      of this license document, but changing it is not allowed.
	*/ 

	class Xengine
	{
		var $_CFG;
		var $_SET = array(
			'action'	=> '',
			'method'	=> '',
			'HTML' => array(
				'HEAD' => array(
					'TITLE' => null,
					'CSS'   => '',
					'JS'    => '',
					'STYLE' => ''
				),
				'BODY' => array(
					'HTML' => '',
					'CSS'  => '',
					'JS'   => ''
				)
			)
		);
		var $_DBF;
		var $Xtra;
		var $method;
		var $_debugReport = array();

		function __construct($cfg = false,$parent = false)
		{	
			$this->_comment("Xengine Started!");
			ini_set('display_errors', $cfg['debug']['on']);

			if(!$cfg)
				die("Configuration Needed");

			// We need to be able to write to this directory...
			if(!is_writeable($cfg['dir']['cfg'])){
				die($cfg['dir']['cfg']." Not Writable!"); 
			}

			$this->_CFG   = $cfg;
			$this->_LANG  = $cfg['lang'] ;
			$this->_BOTS  = $cfg['bots_list'];

			$this->set('suite',$cfg['suite']);

			$this->set('_cfg',$cfg); 

			if(!defined('DB_CFG'))
				define("DB_CFG", $cfg['dir']['cfg']."/cfg.db.$_SERVER[HTTP_HOST].inc");

			if(!defined('SVR_FILES'))
				define("SVR_FILES", CFG_DIR."/$_SERVER[HTTP_HOST]");

			if(!defined('HTTP_HOST'))
				define("HTTP_HOST", $_SERVER['HTTP_HOST']);

			// Installation
			if(!is_dir($cfg['dir']['libs'])){

			}

		}


		/*
			The Client has 'Knocked' on the website's 'Door'
			Identify the whole: user, location, content, module, timestamp
			Decide what to do with them. 
		*/
		public function knock()
		{ 
			session_start();
			try {
				register_shutdown_function(array( &$this, "shutDownFunction" ));
				
				$_SESSION['xRan']= 0;



				$this->keyhole();											// Identify the Whole.
				$this->openDoor();											// Open the Door a.k.a Execute Mods.
				$this->browse(); 											// Display the Content, HTML, JSON, XML, JPEG.
				exit;														// EXIT
			}catch(Exception $e){ 
				$this->githubIssue(array(
					'summary'     => 'X ERROR :: '.$e->getMessage(), 
					'description' => $e->getMessage(),
					'attr' => array(
						'type'      => 'defect',
						'component' => 'x'.ucfirst($this->_SET['action']),
						'priority'  => 'trivial',
						'reporter'  => $_SESSION['user']['username'].'@'.$_SERVER['HTTP_HOST'],
						'keywords'  =>  $this->_SET['action'].'::'. $this->_SET['method'],
						'milestone' => 'Bee Hive'
					)
    			));
				$this->set(array(
					'action' => 'access',
					'method' => 'error',
					'params' => array(
						'error' => $e->getMessage()
					),
					'request' => array(
						'action' => $this->_SET['action'],
						'method' => $this->_SET['method']
					)
				));

				//$this->dump($this->_SET);
				$this->browse(); 
			}
		}

		private function keyhole()
		{ 
			// Who Am I?
			$this->whoAmI();
			
			// Where Am I?
			$this->whereAmI();
 			
 			// What Content Type am I
			$this->whatAmI();
			
			// How Am I Built
 			$this->howAmI();

			// When Am I Happening
			// $this->whenAmI();
			
			// Why Am I Existing
			// $this->whyAmI();
 			
 			
		}

		// The Key holds an array of Bool
		private function whoAmI($who=false)
		{
			// Define Their KEY - Refrenced for Access. 
			$this->Key = array(
				'is' => array(
					'admin'   => isset($_SESSION['user']) ?  ($_SESSION['user']['power_lvl'] > 7)  : false,
					'user'    => (isset($_SESSION['user']) && !empty($_SESSION['user'])) ,
					'guest'   => ( isset($_SESSION['user'] ) != true),
					'bot'     => ( preg_match('/'.implode("|", $this->_BOTS).'/', $_SERVER['HTTP_USER_AGENT'], $matches) ) ? 
						array_search($matches[0], $bots_list) : false
				)
			); 

		}

		private function reload($q)
		{
			$uri = parse_url($_SERVER['REQUEST_URI']);
			header("Location: ".$uri['path'].'?'.$q);
		}

		private function whereAmI()
		{
			if( isset($_GET['syncDb']) ){
				$this->syncDbTables();
				$this->reload();
			}

			// This Function Sets all the variables on where the Client IS based on the URL they've Hit.
			$this->uri         = substr( $_SERVER['REQUEST_URI'], 1 );	// Begins with a / - slice it off.
			$this->url         = parse_url( $this->uri );				
			$this->_SET['params']        = (isset($this->url['path'])) ? explode('/', $this->url['path']) : array('/');
			
			
			if(!isset($this->_SET['params'][1])){
				$this->_SET['params'] = array('','');
			}

			 
			// BOOL
			$this->atSideDoor  = ($this->_SET['params'][0] === $this->_CFG['dir']['sidedoor'] 
				|| $this->_SET['params'][1] === $this->_CFG['dir']['sidedoor']);	 

			// Back Door - Admin Panel of Pages. 
			$this->atBackDoor  = ($this->_SET['params'][0] === $this->_CFG['dir']['backdoor']);	// BOOL
			$this->atGodDoor   = ($this->_SET['params'][0] === $this->_CFG['dir']['goddoor']);	// BOOL
			
			$this->atFrontDoor = ($this->atGodDoor || $this->atBackDoor || $this->atSideDoor ) ? false : true;
			$this->atMailBox   = ($this->_SET['params'][0] === $this->_CFG['dir']['bin']);		// BOOL 


			$this->set('atBackDoor',$this->atBackDoor );
		}

		public function whatAmI()
		{
			// Let's Discover if this document is looking for an extension
			$e = explode('.', $this->url['path']);
			$this->Key['is']['content'] = (count($e) > 1) ? $e[count($e)-1] : 'html';
		}

		// Here we determine what it is the client is trying to access. 
		private function howAmI()
		{	
			// ../Xtra/method/param1/param2/param3/etc	
			$p = $this->_SET['params'];
			$this->_SET['action'] = $this->_SET['method'] = 'index';

			// If we are at the back door, remove it out of our params.
			

			// First check to see which door we're at.
			if ($this->atBackDoor || $this->atGodDoor) {
				unset($p[0]);
				$p = array_values($p);
			} 

			// Now check the side door.
			if ($this->atSideDoor) {
				unset($p[0]);
				$p = array_values($p);
			} 

			if ( isset($p[0]) ) {
				$a = $this->_SET['action']   = ($p[0]) ? $p[0] : 'index';
				unset($p[0]);
			}

			if ( isset($p[1]) ) {
				$m = $this->_SET['method'] = ($p[1]) ? $p[1] : 'index';
				unset($p[1]);
			}

			foreach ($p as $key => $value) {
				if($value == '.json' || $value == '.xml'){
					$_SESSION['output'] = 'json';
					unset($p[$key]);

				}
			}

			$this->_SET['params'] = $p;
		}

		private function openDoor()
		{
			// Door #1 
			// If we Do not have a DB Connected & Setup; Run through the DB Setup.
			if( false == file_exists(DB_CFG) ){
				// We need to know how to connect to our db first!
				// This Xtra configures the Connection to the Database. 
				$this->_SET['action'] = 'wwwSetup';
				$this->_SET['method'] = 'install';

				$this->atSideDoor     = true;
				//$this->dump()
			} else {		
				// With the DB communicating, we able to Run! 
				// Let the Dogs Run and Bark at them: AutoRuns. 
				// Access, Login, Analytics, Backup, wwwSetup
				$this->autoRunSniff();
			}
			
			// The Door is Open; All the Xtras are Locked & Loaded; the Xengine is Up and Running;
			// Allow them to the Walk the Path Requested, ie: /login/logout )
			$this->walkPath();
		}

		/*
			Here is where we loop through the autoRun methods
			allowing them to sniff the Request and do their own Magic.
		*/ 

		private function autoRunSniff(){
			// autoRun the  requisets. 
			$_SESSION['xRan']++;
			$this->_comment('Xengine started: '.$_SESSION['xRan']);

			

			$this->_comment("Running AutoRun Xtra's");
			//	$this->dump($this->q());
			$q = $this->q();

			// ok... we have a db file. but do we have a db!?
			$this->Q("SHOW TABLES LIKE '".$this->Q->db['prefix']."configs'");

    		if($q->ERROR){
    			// Log the Error to The Trac System!!!!
				$sql = '';

				if(isset( $this->Q->sql )){
					$sql = $this->removeLocalData($this->lang( 
						$this->_LANG['DB']['ERROR']['SQL'],
						array('sql' => $this->Q->sql)
					),$this->Q);
				}

				$description = '';
				$description .= $sql;	

				$error = array(
					'summary'     => 'DB ERROR :: '.$this->removeLocalData($this->Q->ERROR,$this->Q), 
					'description' => $description,
					'attr' => array(
						'type'      => 'defect',
						//'component' => 'x'.ucfirst($this->_SET['action']),
						'priority'  => 'trivial',
						'reporter'  => $_SESSION['user']['username'].'@'.$_SERVER['HTTP_HOST'],
						'keywords'  =>  $this->_SET['action'].'::'. $this->_SET['method']
					)
    			);

				$this->githubIssue($error);

				$this->set(array(
					'action' => 'access',
					'method' => 'db',
					'params' => array( $this->uri, $q->ERROR )
				));

				$this->reload('syncDb');

    		}else{
		    	if(isset($_GET['theme'])){
		    		$this->STYLE = $_GET['theme'];
		    	}

				$xphp = $this->getXtras();
				
				// $this->dump($xphp); 

    			foreach($xphp as $k => $x){
    				try {	    			
	    				$class = $x['class'];		
						$methods = get_class_methods($class);

    					if(in_array('autoRun', $methods)){	 
							$this->_comment("Auto Run: ". $class);	
							
							$this->_xtra = $class;
							// $return = $class::autoRun($this); 
							
							$class = new $class($this->_CFG);
							
							$class = $this->mergeO($class,$this);

							$return = call_user_func_array(array($class,'autoRun'), array($this));

							$this->_comment('AutoRan '.get_class($class).': ');
 
							foreach ($class as $key => $value) {
								$this->$key = $value;
							} 

							if(is_array($return)){ 
								$this->_SET = $this->set($return);
								$this->_SET = array_merge($return,$this->_SET);

							}


							$this->_comment("AutoRun Found in: ".get_class($class)." ~ Complete...");
	    				}
    				} catch (Exception $e) {
    					$this->_comment($e->getMessage());
    				}
	    		}
    		}
		}

		private function walkPath()
		{
			$this->_comment("Xtra: ".$this->_SET['action']." | Method:".$this->_SET['method']);
			// Look to see if any Xtra matches
			$Xtra = 'x'.ucwords($this->_SET['action']);

			$this->_comment("Looking for Class $Xtra");
			$php  = XPHP_DIR."/$Xtra/$Xtra.php";
			$this->_comment("Looking for file $php");
			if( file_exists($php) ){
				$this->runXtra($Xtra,$php);
			}
		}

		function is_class_method($type="public", $method, $class) {
			    $refl = new ReflectionMethod($class, $method);
			    switch($type) {
			        case "static":
			        	return $refl->isStatic();
			        break;
			        case "public":
			        	return $refl->isPublic();
			        break;
			        case "private":
			        	return $refl->isPrivate();
			        break;
			    }
			}

		private function runXtra($Xtra,$php)
		{
			# require the dynamic file.
			require_once($php);
			# create a new class

			$Xtra = new $Xtra($this->_CFG);
			$Xtra = $this->mergeO($Xtra,$this);

			# if the method exists...
			$public   = (method_exists($Xtra,$this->_SET['method']) && $this->is_class_method('public', $this->_SET['method'], $Xtra));
			$security = ($this->Key['is']['admin']) ? method_exists($Xtra,$this->_SET['method']) : $public;

			if( $security )
			{
				// $Xtra->Q = $this->Q;
    			# call the function w/ params
    			$this->_comment("Running $php");	

    			array_values($this->_SET['params']);


    		 
				$return = call_user_func_array( array($Xtra,$this->_SET['method']), $this->_SET['params'] );

				$this->_SET = $this->apply($this->_SET,$Xtra->_SET); 

				// var_dump($return);
				// exit;
				
				if(is_array($return))
					$this->_SET = array_merge($return,$this->_SET);
				


				// $this->dump($this->_SET); 

				# We might have logged in...
    			$this->whoAmI($this->Key);
    			$this->whatAmI($this->Key);

				if($return && $this->_SET['action'] == 'login' && $this->Key['is']['user']){
					//$this->_SET['action'] = (isset($this->wait['Xtra'])) ? $this->wait['Xtra'] : $this->_SET['action'] ;
					//$this->_SET['method'] = (isset($this->wait['method'])) ? $this->wait['method'] : $this->_SET['method'];
					// Run this over on more time...
					//$this->walkThru();
					// makes for a quick No-redirect ;)		
					//unset($_POST['password']);
				}
			# Illegal Method has been Called!
    		}else{

    			// Test to see if Navi knows where to go.
    			
    // 			$this->set(array(
				// 	'action' => 'access',
				// 	'method' => 'error',
				// 	'params' => array(
				// 		'error' => $e->getMessage()
				// 	),
				// 	'request' => array(
				// 		'action' => $this->_SET['action'],
				// 		'method' => $this->_SET['method']
				// 	)
				// ));
    			# Kill the Engine send a 404!!

    			$this->_SET['anchor'] = $this->_SET['action'];
				$this->_SET['action'] = 'access';
				$this->_SET['method'] = '404';

				$this->_SET['params'] = array($this->_LANG['404'],$this->_LANG['404_msg']);

				# We might have logged in...
    			$this->whoAmI($this->Key);
    			$this->whatAmI($this->Key);
    		}
		}

		private function browse() 
		{			
			$this->_comment("Sending data to Browser");			
			// Dislays/Outputs Data To Browser.
			
	    	$this->set('IS_USER',$this->Key['is']['user']);
			$this->set('IS_ADMIN',$this->Key['is']['admin']);

			$this->display($this->Key['is']['content'], $this->_SET);

			//$this->dump($this->_SET);
		//	exit;

		}

		private function display($type='html',$array)
		{ 
			// Choose which type of content we are displaying.
			//exit;
			ob_clean();
			if(isset($_REQUEST['callback'])){
				$callback = $_REQUEST['callback'];
				//start output
				if ($callback) {
				    header('Content-Type: text/javascript');
				    echo $callback . '(' . json_encode($array) . ');';
				} else {
				    header('Content-Type: application/x-json');
				    echo json_encode($array);
				}	
				exit;
			}else{
				switch ($type) {
					default:
						# HTML
						header('Content-Type: text/html');
						$this->viewTemplate();
					break;

					case 'json': 
						ob_clean();	

						$whitelist = array('success','data','header','version','error','recordsTotal','draw','recordsFiltered');
 
						$u = parse_url($_SERVER['REQUEST_URI']);

						//parse_str($u['query'],$G);

						foreach ($array as $key => $value) {
							$unset = true;
							foreach ($whitelist as $k => $w) {
								if($key == $w || isset($_GET[$key]) )
									$unset = false;
							}



							if($unset){
								unset($array[$key]);
							} 
							
						}


						// unset($array['HTML']);
						// unset($array['ICON']);
						// unset($array['admin_menu']);
						// unset($array['xtras']);
						// unset($array['_LANG']);
						// unset($array['_cfg']);
						// unset($array['qBlox']);
						// unset($array['user']);
						// unset($array['SVR']);
						// unset($array['navi']);
						// unset($array['deku']);
						// unset($array['SUPER_ADMIN']);
						
						// foreach ($array['CONFIG'] as $key => $value) {
						// 	unset($array[$key]);
						// }

						// unset($array['CONFIG']);



						header('Content-type: text/javascript');
		    			echo json_encode($array);
					break;
					
					case ('xml'): 
						ob_clean();	
						header('Content-Type: text/xml');
		    			echo $this->viewXml($array); 
					break;
				}	
			}
		}

		private function viewTemplate(){
			// This handles the templating, which file to load, what theme to request, what thumbnailer to use.

			// Pre Bootstrap...
			// $layout = ($this->atBackDoor) ? 'backdoor' : 'frontdoor';

			$layout = ($this->atBackDoor) ? 'watchtower' : 'frontdoor'; 
			$layout = ($this->atSideDoor) ? 'sidedoor' : $layout;
			
			$layout = ($this->atGodDoor) ?  'iframe' : $layout;	
				if($this->Key['is']['admin'] == true){
			}

 
			// DATABASE ERROR
			if(false !== $this->Q->ERROR && file_exists(DB_CFG)){
				// function removeLocalData($r,$Q)
				// {
				// 	$r = str_replace($Q->db['database'].'.', '', $r);
				// 	$r = str_replace($Q->db['prefix'], '', $r);
				// 	return $r;
				// } 

				$sql = '';

				if(isset( $this->Q->sql )){
					$sql = $this->removeLocalData($this->lang( 
						$this->_LANG['DB']['ERROR']['SQL'],
						array('sql' => $this->Q->sql)
					),$this->Q);
				}

				$description = '';
				$description .= $sql;	

				$error = array(
					'summary'     => 'DB ERROR :: '.$this->removeLocalData($this->Q->ERROR,$this->Q), 
					'description' => $description,
					'attr' => array(
						'type'      => 'defect',
						'component' => 'xCore',//.ucfirst($this->_SET['action']),
						'priority'  => 'trivial',
						'reporter'  => $_SESSION['user']['username'].'@'.$_SERVER['HTTP_HOST'],
						'keywords'  =>  $this->_SET['action'].'::'. $this->_SET['method']
					)
    			);

				//$this->githubIssue($error);

				$layout = 'sidedoor';
				$this->set(array(
					'action' => 'access', 
					'method' => 'db', 
					'request' => $this->Q->ERROR, 
					'reason' => $this->lang( 
						$this->_LANG['DB']['ERROR']['SQL'],
						array('sql' => $this->Q->sql)
					)
				)); 

				$this->reload('syncDb');
			}

			$lib = explode('/', $this->_CFG['dir']['libs']);
			$lib = $lib[count($lib)-1];



			$tpl = XPHP_DIR.'/x'.ucfirst($this->_SET['action']).'/'.$this->_SET['method']; 
			$tpl = ( file_exists($tpl.'.html') ) ? $tpl.'.html' : $tpl.'.tpl' ; 
			$tpl = ( file_exists($tpl) ) ; 
		  	$this->set('TPL_EXISTS',$tpl);


			// This is our last chance to change the output's HTML template.  
			$html_door = ($this->atBackDoor) ? $this->_CFG['html']['private'] : $this->_CFG['html']['public'];

			// Override any all all links with custom navigation.
			if( isset($this->Key['heylisten']) ){
				// Get List of Bloxs.
				$this->set(array(
					'action' => 'index',
					'method' => 'index'
				));	 
				

			}else if(!$tpl){
				$this->set(array(
					'action' => 'access',
					'method' => '404',
					'anchor' => $this->_SET['action']
				));				
				//$this->_SET['HTML']['HEAD']['TITLE'] = "Page Template Not Found";
			}


			if(is_array($this->_SET['_LANG'])){
				$lang = array_merge_recursive($this->_LANG,$this->_SET['_LANG']); 
			}
 

			$assign = array(
				//f
				'lib_core'    => $this->_CFG['lib_core'],
				// 'version'  =>'4',
				'Xtra'        => $this->_SET['action'],
				'method'      => $this->_SET['method'],
				'params'      => $this->_SET['params'],
				'Door'        => $html_door,
				'toBackDoor'  => $this->_CFG['dir']['backdoor'],
				'toFrontDoor' => $this->_CFG['dir']['frontdoor'],
				'toSideDoor'  => $this->_CFG['dir']['sidedoor'], 
				'toGodDoor'   => $this->_CFG['dir']['goddoor'],
				'HTTP_HOST'   => $_SERVER['HTTP_HOST'],
				'html_title'  => $_SERVER['HTTP_HOST'],
				'USER'        => $_SESSION['user'],
				'ERROR'       => false, // Set this to Display an Errord
				
				// Sets the template variable TPL_EXISTS to make sure we have the page
				'LANG'        => $lang ,
				
				// KEY
				'masterKey'   => $this->Key,
				
				
				// Depreciate
				'IS_ADMIN'    => $this->_LANG,
				'blox'        => false,
				'LAYOUT'      => $layout,
				'LAYOUTS'     =>  $this->_CFG['html'],
				'thumb'       => '/'.$this->_CFG['dir']['backdoor'].'/'.$lib.'/phpThumb/phpThumb.php?f=png&q=100&',
				'URL'         => parse_url($_SERVER['REQUEST_URI'])
			);
			 
			// var_dump($assign['TPL_EXISTS']);
			// exit;
			$this->_SET['HTML']['JSAN'] = file_get_contents($this->_CFG['dir']['bin'].'/js/ux/JSAN.js');



			// Initate Smarty and Pass the assigned vars.
			$this->initSmarty(array_merge($assign, $this->_SET));
		}

 		/**
	     * initSmarty() includes the smarty files and configures the new class:
	     * @return new Smarty() w/ cfg;
	     */
		private function initSmarty($a){
			# Include the Smarty Class
			
			$this->lib($this->_CFG['SMARTY_V'].'/libs/Smarty.class.php');

			$dir          = LIBS_DIR."/".$this->_CFG['SMARTY_V'];
			
			// Be sure to clean again.
			
			//ob_clean();
			
			# Start the Smarty Class
			$this->smarty = new Smarty();	
			
			# Configure Smarty		 
			$tmps = $this->_CFG['dir']['cfg']."/__cache";	

			if(!is_dir($tmps)){
				mkdir($tmps , 0777); 
			}

			$this->smarty->compile_dir  = $tmps;
			$this->smarty->cache_dir    = $dir."/cache";
			$this->smarty->config_dir   = $dir."/configs";
 			$this->smarty->template_dir =  array(
 				$this->_CFG['dir']['html'],
 				$this->_CFG['dir']['Xtra']
 			);

			$this->smarty->assign($a);			
 
			
			if($this->_CFG['debug']['cache']  == false){
				$this->smarty->clearAllCache();
			}


			ob_clean();
			$this->smarty->display('index.tpl');
			//
			if($this->_CFG['debug']['cache']  == false){
				$this->smarty->clearAllCache();
			}

			return $this->smarty;
		}

 		
	    private function ob_start(){
	    	/** Improve buffer usage and compression */
			if (function_exists('ob_start')){
				/** Check that Zlib is not enabled by default (value should be 1-9, else it will be an empty string) */
				if (@ini_get('zlib.output_compression') || !function_exists('ob_gzhandler')){
					ob_start();
				}else{
					ob_start('ob_gzhandler');
				}
			}
	    }

		/**
		 * The main function for converting to an XML document.
		 * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
		 *
		 * @param array $data
		 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
		 * @param SimpleXMLElement $xml - should only be used recursively
		 * @return string XML
		 */
		private function viewXml($data, $rootNodeName = 'data', $xml=null)
		{
			// turn off compatibility mode as simple xml throws a wobbly if you don't.
			ini_set ('zend.ze1_compatibility_mode', 0);

			if ($xml == null)
			{
				$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
			}

			// loop through the data passed in.
			foreach($data as $key => $value)
			{
				// no numeric keys in our xml please!
				if (is_numeric($key))
				{
					// make string key...
					$key = "unknownNode_". (string) $key;
				}

				// replace anything not alpha numeric
				$key = preg_replace('/[^a-z]/i', '', $key);

				// if there is another array found recrusively call this function
				if (is_array($value))
				{
					$node = $xml->addChild($key);
					// recrusive call.
					$this->viewXml($value, $rootNodeName, $node);
				} else {
					// add single node.
	                $value = htmlentities($value);
					$xml->addChild($key,$value);
				}
			}
			// pass back as string. or simple xml object if you want!
			return $xml->asXML();
		}

		/*
		 * lib($file) a simple include function to easily grab
		 * the location of our library files
		 */
		public function lib($file){
			$this->_comment(get_class($this)." Requesting Library $file");
			try{
				require_once(LIBS_DIR.'/'.$file);
			}catch(Exception $e){
				throw new Exception(get_class($this)." Failed to Load Library: ".$file, 1); 
			} 
		}


		////////////////////////////////
		////////////////////////////////
		////////////////////////////////
		public function q()
		{
			if(!isset($this->Q)){
				
				/*if(get_parent_class($this) != ''){
					echo get_class($this);
					$c = get_parent_class($this);
					$this->Q = parent::q();
				} */

				$this->_comment("Init DB from: ". get_class($this));

				require(DB_CFG);
				foreach($this->db as $key => $value){
					if($key == 'pass'){
						for($i=0;$i<count($this->mainDomain()); $i++){
							$value = base64_decode($value);
						}
					}
					$this->db[$key] = $value;
				}

				$db 	= "x".$this->_CFG['db'];

				if(!class_exists($db)){
					$this->_comment('Loading DB');
					$this->lib("x4deep/$db.php");
				}

				$this->Q = new $db($this->db['host'],$this->db['user'],$this->db['pass'],$this->db['database'],$this->db['prefix']);					
			}else{
				$this->_comment("Recycled DB from: ". get_class($this));
				//$this->Q = parent::q();
			}

			// Return the class that may have already been created...
			return $this->Q;
		}

		function getXTras(){
			// This is where we should get the control panel icons.


			if(!isset($this->_xtras)){
				// We should only run this once 
				if ($handle = opendir(XPHP_DIR)) {
					$time = microtime(true); 
					$this->_comment("Loading Xtra Files...");

					$files = array();

					// Open the Xtras Directory
				    while (false !== ($file = readdir($handle))) {
				        if ( substr($file, 0, 1) == 'x' ) {

				    		$this->_comment("Opening Directory: $file");  
				        	// Open the PHP File, which should be the same name as the Directory 
				            
							$class = $file;
							$file  = $file.'.php';

			            	require_once(XPHP_DIR.'/'.$class.'/'.$file);

			            	$this->_comment("Loaded Xtra: $class File: $file");

			            	$rc = new ReflectionClass($class);
							$doc = $rc->getDocComment();

							if($doc){
								// $data =  trim(preg_replace('/\r?\n *\* */', ' ', $doc));
								 
								// preg_match_all('/@([a-z]+)\s+(.*?)\s*(?=$|@[a-z]+\s)/s', $data, $matches);
								// $info = array_combine($matches[1], $matches[2]);
								$info = $this->readPhpComment($doc);

								$ext = explode('.',$file);
								$jig = array(
									'author'  => '',
									'class'   => $ext[0],
									'file'    => $file,
									'icon'    => '',
									'link'    => '',
									'mini'    => '',
									'name'    => '',
									'version' => 0
								);

								$files[$file] = array_merge($jig,$info);
							}
				        }
				    }
				    closedir($handle);
					ksort($files);
					//$this->set('xphp_files',$files);
					$this->_xtras = $this->_SET['xtras'] = $files;

					$time = round(microtime(true) - $time,5); 
					$this->_comment("Loaded ".count($files)." Xtra Files in ".$time);
				} 
			}else{
				$files = $this->_SET['xtras'] = $this->_xtras;
			}
			
			return $files;
		}
// check this out
		function readPhpComment($doc) 
		{
			$data =  trim(preg_replace('/\r?\n *\* */', ' ', $doc)); 
			preg_match_all('/@([a-z]+)\s+(.*?)\s*(?=$|@[a-z]+\s)/s', $data, $matches);
			return array_combine($matches[1], $matches[2]);
		}

		function syncDbTables(){
			// Go through all the modules. 
			$mods = $this->getXTras();
			foreach($mods as $k => $v){
				$php = str_replace('.php','',$k);
				if( method_exists($php,'dbSync') ){
					$db = $php::dbSync();
					if(!empty($db)){
						foreach($db as $table => $columns){
							if(!empty($columns)){
								$this->syncTable($table,$columns);
							} 
						}
					}
				}
			}
		}

		function mysys($cmd,$debug=false){
	  		if($debug)
	  			echo "# $cmd;";

		    return system("($cmd)2>&1");
		} 

		// MySQL Function...
		function syncTable($table,$columns){
			$q = $this->q();
			// Get Current Table

			// $chk = $q->Q("DESC $table");

			// // RENAME THIS TABLE TO STANDARD PREFIX TEQ
			// if(!empty($chk) && $q->PREFIX){
			// 	$q->Q("RENAME TABLE $table TO $q->PREFIX$table");
			// }

			//$c = $q->Q("DESC $q->PREFIX$table");

			$sql = '';

			$c = $q->Q("SHOW TABLES LIKE '$q->PREFIX$table'");

			if(!empty($c)){
				$c = $q->Q("DESC $q->PREFIX$table");
			}

			if(!empty($c) && is_array($columns)){
				foreach($c as $k => $v){
					$col = $v['Field'];
					// Check Columns
					if( isset( $columns[$col] ) ){
						// Column Doesn't Match
						if(is_array($columns[$col])){
							if($columns[$col]['Type'] != $v['Type'] || (isset($columns[$col]['Default']) && $v['Default'] != $columns[$col]['Default'])  ){
								// Add Sync to Sql
								$sync = "`$v[Field]` `$v[Field]` ".$columns[$col]['Type'];

								if(isset($columns[$col]['Default'])){
									$sync .= ' DEFAULT "'.$columns[$col]['Default'].'"';
								}


								$sql = ($sql) ? $sql.", CHANGE $sync " : $sync;
							}
						}else if(is_string($columns[$col]) && isset($columns[$columns[$col]]['Type'])){
							$sync = "`$col` `$columns[$col]` ".$columns[$columns[$col]]['Type'];
							$sql = ($sql) ? $sql.", CHANGE $sync " : $sync;
						}
						unset( $columns[$col] );
						unset( $c[$k] );
					}
				}

				// Change Columns
				if($sql){
					// Run SQL
					$q->Q("ALTER TABLE `$q->PREFIX$table` CHANGE $sql");
					if(isset($_GET['debug'])){
						//echo $q->mSql.'<hr>';
						//echo $q->error;
					}
					return $q->error;
				}

				// New columns
				if(!empty($columns)){
					foreach($columns as $k => $v){
						if(is_array($v)){
							$sync = "`$k` ".$v['Type'];
							$q->Q("ALTER TABLE `$q->PREFIX$table` ADD $sync");
						}
						//echo $q->error;
					}

				}


			}else{

				if( is_array($columns) ){
				# CREATE TABLE
					foreach($columns as $k => $v){
						if(is_array($v)){
							$sync = "`$k` ".$v['Type'];
							$sql = ($sql) ? $sql.", $sync " : $sync;
						}
					}
					$q->Q("CREATE TABLE `$q->PREFIX$table`( id INT(6) NOT NULL AUTO_INCREMENT, PRIMARY KEY(id),$sql );");
					// echo $q->error;
				}else{
				# Rename Table...
					$chk = $q->Q("SHOW TABLES LIKE '$table'");

					if(empty($chk)){
						$chk = $q->Q("SHOW TABLES LIKE '$q->PREFIX$table'");
						// RENAME THIS TABLE TO STANDARD PREFIX TEQ
						if(!empty($chk)){
							$q->Q("RENAME TABLE $q->PREFIX$table TO $q->PREFIX$columns");
						}
					}else{
						$q->Q("RENAME TABLE $table TO $q->PREFIX$columns");
					}

				}
			}
		}

		function mainDomain(){
			$domain = $_SERVER['HTTP_HOST'];
			$domain = explode('.',$domain);
			$i      = 0;
			while(count($domain) > 2){
				unset($domain[$i]);
				$i++;
			}
			$domain = implode('.',$domain);
			return $domain;
		}

		

		/*
	     */

		 public function set($k,$v=false){
	    	//$this->dump($this->_SET);
	    	if(!is_array($k)){
	    		//var_dump($k); 
				return $this->_SET[$k] = $v;
	    		$class = get_class($this);
	    		$_SESSION[$class][$k] = $v;
	    	}else if(is_array($k)){
	    		return $this->_SET = array_merge($this->_SET,$k);
	    		 
	    	}
		} 

		private function apply($array,$default){
			$new = array();
			foreach ($default as $key => $value) {
				// if array has new from default.
				if( isset( $array[$key] )){
					if( is_array($array[$key]) ){
						// we are dealing with an array, dig deeper.
						$new[$key] = $this->apply($array[$key],$default[$key]);
					} else {
						// ok - we've hit a value, lets set it in the new array.
						$new[$key] = $array[$key];
					} 
				}else{
					// the new array that got passed.
					$new[$key] = $value;
				}
			}
			return $new;
		}

		private function mergeO($obj,$default){
			foreach ($default as $key => $value) {
				$obj->$key = $value;
			}
			return $obj;
		}

		public function shutDownFunction() { 
			$error = error_get_last();
		  
		    $t = $error['type'];

		    if($t === E_ERROR /*|| $t === E_WARNING*/){ 
				//ob_clean();
				function FriendlyErrorType($type){ 
				    switch($type){ 
				        case E_ERROR: // 1 // 
				            return 'E_ERROR'; 
				        case E_WARNING: // 2 // 
				            return 'E_WARNING'; 
				        case E_PARSE: // 4 // 
				            return 'E_PARSE'; 
				        case E_NOTICE: // 8 // 
				            return 'E_NOTICE'; 
				        case E_CORE_ERROR: // 16 // 
				            return 'E_CORE_ERROR'; 
				        case E_CORE_WARNING: // 32 // 
				            return 'E_CORE_WARNING'; 
				        case E_CORE_ERROR: // 64 // 
				            return 'E_COMPILE_ERROR'; 
				        case E_CORE_WARNING: // 128 // 
				            return 'E_COMPILE_WARNING'; 
				        case E_USER_ERROR: // 256 // 
				            return 'E_USER_ERROR'; 
				        case E_USER_WARNING: // 512 // 
				            return 'E_USER_WARNING'; 
				        case E_USER_NOTICE: // 1024 // 
				            return 'E_USER_NOTICE'; 
				        case E_STRICT: // 2048 // 
				            return 'E_STRICT'; 
				        case E_RECOVERABLE_ERROR: // 4096 // 
				            return 'E_RECOVERABLE_ERROR'; 
				        case E_DEPRECATED: // 8192 // 
				            return 'E_DEPRECATED'; 
				        case E_USER_DEPRECATED: // 16384 // 
				            return 'E_USER_DEPRECATED'; 
				    } 
				    return ""; 
				} 


				$file = realpath($_SERVER['DOCUMENT_ROOT']);

				$file = str_replace($file , '', $error['file']);

			    $e = array(
					'summary'     => FriendlyErrorType($error['type'])
						.' :: '.$error['message'].' :: '.$file.'#L'.$error['line'], 
					'description' => $error['message'].' :: '.'[source:trunk'.$file.'#L'.$error['line'].']',
					'attr'        => array(
						'type'      => 'defect',
						//'component' => 'x'.ucfirst($this->_SET['action']),
						'priority'  => 'trivial',
						'reporter'  => $_SESSION['user']['username'].'@'.$_SERVER['HTTP_HOST'],
						'keywords'  =>  $this->_SET['action'].'::'. $this->_SET['method']
					)
				);

				$this->githubIssue($e);		    	
		    }

		}

		private function removeLocalData($r,$Q)
			{
			$r = str_replace($Q->db['database'].'.', '', $r);
			$r = str_replace($Q->db['prefix'], '', $r);
			return $r;
		} 

		/*
			Accepts an Array of reportable Data. 
		*/
		public function githubIssue($error='')
		{

			// echo 'End of the Line<pre>';
			// var_dump($error);
			// exit;

			$this->_SET['action'] = 'access';
			$this->_SET['method'] = 'error';

			// This file is generated by Composer
			// $this->lib('vendor/autoload.php');

			// $client = new \Github\Client(
			//    new \Github\HttpClient\CachedHttpClient(array('cache_dir' => '/tmp/github-api-cache'))
			// );
			// $issues = $client->api('user')->repositories('XopherDeeP');
			
			// $this->dump($issues); 
		}


		///////////////////////////// 
		//// Debug Functions
		//
		//

		// This should be used sparingly, and in production only!
		public function _comment($msg,$clear=false){
			$time = round(microtime(true) - $this->_CFG['debug']['runtime'],5); 
			$echo = $this->_debugReport[] = ">'''[wiki:".get_class($this)."]''': ^[~~".$time."~~]^ $msg <br/>";



			if($this->_CFG['debug']['on'])
				echo $echo;



		}

		public function devBug($var,$title,$dump = true,$halt = true)
		{
			# code...
			$this->_comment($title);
			$this->_dump($var,$dump,$halt);
		}

		// Same with this ^^^
		public function dump($var,$dump=true,$halt = true)
		{
			if($this->_CFG['debug']['on']){
				if($dump){
					echo '<pre>';
					var_dump($var);
					echo '</pre>';
				} else {
					echo $var;
				}
			}
			if($halt)
				exit();
		}

		/**
		 * rebuildSession();
		 * If accessing the site and users php session was lost.
		 * This will attempt to restore the session quitly using
		 * the browser cookies .
		 *
		 */
		function rebuildSession(){
			# If there are cookies, but there isnt a session...
			if( isset($_COOKIE['user']['secret']) && !isset($_SESSION['user']['secret']) ){
				//// Level 1 Security Check ////
				$u = $_COOKIE['user'];
				$secret = sha1(
					md5(
						$u['email'].base64_decode($u['hash']).$u['id'].$u['username']
					)
				);

				# Check Cookie Secret before hitting the DB
				if( $secret === $u['secret'] ){
					# Passed Level 1 Authority - Allows access to find user in DB.
					$q = $this->q();
					$row = $q->Select('email,hash,id,username,user_secret,user_lastvisit','Users',array(
						'email'=>$u['email']
					));

					//// Level 2 Security Check ////
					if($row[0]['user_secret'] === $secret){
						# Client is the Correct User.- ReBuild Session Data
						$this->setUser($row);
					}
				}
			}
		}

		function setConfig($option,$value){
			$q = $this->q();

			$cfg = $q->Select('*','config',array('config_option'=>$option));
			if(empty($cfg)){
				return $q->Insert('config',array(
					'config_option'	=> $option,
					'config_value'	=> $value
				));
			}else{
				return $q->Update('config',array(
					'config_value'	=> $value
				),array(
					'id'	=> $cfg[0]['id']
				));
			}
		}

		// allows
		public function __call($method,$args){

			if(isset($this->_xtra)){
				$class = $this->_xtra;
				if(method_exists($class,$method)){
					$class = new $class($this->_CFG);
					$class = $this->mergeO($class,$this);
					return call_user_func_array(array($class,$method), $args);
				}
			}
 

			if(!isset($this->_xtras)){
				$this->getXtras();
			}

			foreach($this->_xtras as $x){
		    	$class = $x['class'];	
				if(method_exists($class,$method)){
					$class = new $class($this->_CFG);
					$class = $this->mergeO($class,$this);
					return call_user_func_array(array($class,$method), $args);
				}
		    }
			

	        // if(method_exists($x['class'],$method))
	        // return call_user_method_array($method,$x['class'],$args);
		    //throw new Exception("This Method {$method} doesn't exist in ".get_class($this));
		    $this->_comment("This Method {$method} doesn't exist in ".get_class($this));
		}

		public function lang($str,$extract)
		{
			extract($extract);

			// $this->dump($username);
			// exit();

			return eval('return "'.$str.'";');
		}

		public function initRPC($rpc=false)
		{
			if(!$rpc && isset($this->_RPC))
				$rpc = $this->_RPC;
			else 
				return false;

			if(!class_exists('Zend_Loader')){
				$this->lib('Zend/Loader.php'); 
				
				$z = new Zend_Loader();
				$z->loadClass('Zend_XmlRpc_Client');
			}


			$c = new Zend_XmlRpc_Client("http://$rpc[user]:$rpc[pass]@$rpc[www]");

			// Need to bypass the webdav auth
			$http = $c ->getHttpClient(); 
			$http->setAuth( $rpc['user'], $rpc['pass'], Zend_Http_Client::AUTH_BASIC );
			$c->setHttpClient($http);  

			return $this->RPC = $c;
		}

		function is_email ($email, $checkDNS = false) {
	        //      Check that $email is a valid address
	        //              (http://tools.ietf.org/html/rfc3696)
	        //              (http://tools.ietf.org/html/rfc2822)
	        //              (http://tools.ietf.org/html/rfc5322#section-3.4.1)
	        //              (http://tools.ietf.org/html/rfc5321#section-4.1.3)
	        //              (http://tools.ietf.org/html/rfc4291#section-2.2)
	        //              (http://tools.ietf.org/html/rfc1123#section-2.1)

	        //      the upper limit on address lengths should normally be considered to be 256
	        //              (http://www.rfc-editor.org/errata_search.php?rfc=3696)
	        if (strlen($email) > 256)       return false;   //      Too long

	        //      Contemporary email addresses consist of a "local part" separated from
	        //      a "domain part" (a fully-qualified domain name) by an at-sign ("@").
	        //              (http://tools.ietf.org/html/rfc3696#section-3)
	        $index = strrpos($email,'@');

	        if ($index === false)           return false;   //      No at-sign
	        if ($index === 0)                       return false;   //      No local part
	        if ($index > 64)                        return false;   //      Local part too long

	        $localPart              = substr($email, 0, $index);
	        $domain                 = substr($email, $index + 1);
	        $domainLength   = strlen($domain);
	       
	        if ($domainLength === 0)        return false;   //      No domain part
	        if ($domainLength > 255)        return false;   //      Domain part too long

	        //      Let's check the local part for RFC compliance...
	        //
	        //      Any ASCII graphic (printing) character other than the
	        //      at-sign ("@"), backslash, double quote, comma, or square brackets may
	        //      appear without quoting.  If any of that list of excluded characters
	        //      are to appear, they must be quoted
	        //              (http://tools.ietf.org/html/rfc3696#section-3)
	        if (preg_match('/^"(?:.)*"$/', $localPart) > 0) {
	                //      Quoted-string tests:
	                //
	                //      Note that since quoted-pair
	                //      is allowed in a quoted-string, the quote and backslash characters may
	                //      appear in a quoted-string so long as they appear as a quoted-pair.
	                //              (http://tools.ietf.org/html/rfc2822#section-3.2.5)
	                $groupCount     = preg_match_all('/(?:^"|"$|\\\\\\\\|\\\\")|(\\\\|")/', $localPart, $matches);
	                array_multisort($matches[1], SORT_DESC);
	                if ($matches[1][0] !== '')                                                                              return false;   //      Unescaped quote or backslash character inside quoted string
	                if (preg_match('/^"\\\\*"$/', $localPart) > 0)                                  return false;   //      "" and "\" are slipping through - must tidy this up
	        } else {
	                //      Unquoted string tests:
	                //
	                //      Period (".") may...appear, but may not be used to start or end the
	                //      local part, nor may two or more consecutive periods appear.
	                //              (http://tools.ietf.org/html/rfc3696#section-3)
	                if (preg_match('/^\\.|\\.\\.|\\.$/', $localPart) > 0)                   return false;   //      Dots in wrong place

	                //      Any excluded characters? i.e. <space>, @, [, ], \, ", <comma>
	                if (preg_match('/[ @\\[\\]\\\\",]/', $localPart) > 0)
	                        //      Check all excluded characters are escaped
	                        $stripped = preg_replace('/\\\\[ @\\[\\]\\\\",]/', '', $localPart);
	                        if (preg_match('/[ @\\[\\]\\\\",]/', $stripped) > 0)            return false;   //      Unquoted excluded characters
	        }

	        //      Now let's check the domain part...

	        //      The domain name can also be replaced by an IP address in square brackets
	        //              (http://tools.ietf.org/html/rfc3696#section-3)
	        //              (http://tools.ietf.org/html/rfc5321#section-4.1.3)
	        //              (http://tools.ietf.org/html/rfc4291#section-2.2)
	        if (preg_match('/^\\[(.)+]$/', $domain) === 1) {
	                //      It's an address-literal
	                $addressLiteral = substr($domain, 1, $domainLength - 2);
	                $matchesIP              = array();
	               
	                //      Extract IPv4 part from the end of the address-literal (if there is one)
	                if (preg_match('/\\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/', $addressLiteral, $matchesIP) > 0) {
	                        $index = strrpos($addressLiteral, $matchesIP[0]);
	                       
	                        if ($index === 0) {
	                                //      Nothing there except a valid IPv4 address, so...
	                                return true;
	                        } else {
	                                //      Assume it's an attempt at a mixed address (IPv6 + IPv4)
	                                if ($addressLiteral[$index - 1] !== ':')                        return false;   //      Character preceding IPv4 address must be ':'
	                                if (substr($addressLiteral, 0, 5) !== 'IPv6:')          return false;   //      RFC5321 section 4.1.3

	                                $IPv6 = substr($addressLiteral, 5, ($index ===7) ? 2 : $index - 6);
	                                $groupMax = 6;
	                        }
	                } else {
	                        //      It must be an attempt at pure IPv6
	                        if (substr($addressLiteral, 0, 5) !== 'IPv6:')                  return false;   //      RFC5321 section 4.1.3
	                        $IPv6 = substr($addressLiteral, 5);
	                        $groupMax = 8;
	                }

	                $groupCount     = preg_match_all('/^[0-9a-fA-F]{0,4}|\\:[0-9a-fA-F]{0,4}|(.)/', $IPv6, $matchesIP);
	                $index          = strpos($IPv6,'::');

	                if ($index === false) {
	                        //      We need exactly the right number of groups
	                        if ($groupCount !== $groupMax)                                                  return false;   //      RFC5321 section 4.1.3
	                } else {
	                        if ($index !== strrpos($IPv6,'::'))                                             return false;   //      More than one '::'
	                        $groupMax = ($index === 0 || $index === (strlen($IPv6) - 2)) ? $groupMax : $groupMax - 1;
	                        if ($groupCount > $groupMax)                                                    return false;   //      Too many IPv6 groups in address
	                }

	                //      Check for unmatched characters
	                array_multisort($matchesIP
	[1], SORT_DESC);
	                if ($matchesIP[1][0] !== '')                                                            return false;   //      Illegal characters in address

	                //      It's a valid IPv6 address, so...
	                return true;
	        } else {
	                //      It's a domain name...

	                //      The syntax of a legal Internet host name was specified in RFC-952
	                //      One aspect of host name syntax is hereby changed: the
	                //      restriction on the first character is relaxed to allow either a
	                //      letter or a digit.
	                //              (http://tools.ietf.org/html/rfc1123#section-2.1)
	                //
	                //      NB RFC 1123 updates RFC 1035, but this is not currently apparent from reading RFC 1035.
	                //
	                //      Most common applications, including email and the Web, will generally not permit...escaped strings
	                //              (http://tools.ietf.org/html/rfc3696#section-2)
	                //
	                //      Characters outside the set of alphabetic characters, digits, and hyphen MUST NOT appear in domain name
	                //      labels for SMTP clients or servers
	                //              (http://tools.ietf.org/html/rfc5321#section-4.1.2)
	                //
	                //      RFC5321 precludes the use of a trailing dot in a domain name for SMTP purposes
	                //              (http://tools.ietf.org/html/rfc5321#section-4.1.2)
	                $matches        = array();
	                $groupCount     = preg_match_all('/(?:[0-9a-zA-Z][0-9a-zA-Z-]{0,61}[0-9a-zA-Z]|[a-zA-Z])(?:\\.|$)|(.)/', $domain, $matches);
	                $level          = count($matches[0]);

	                if ($level == 1)                                                                                        return false;   //      Mail host can't be a TLD

	                $TLD = $matches[0][$level - 1];
	                if (substr($TLD, strlen($TLD) - 1, 1) === '.')                          return false;   //      TLD can't end in a dot
	                if (preg_match('/^[0-9]+$/', $TLD) > 0)                                         return false;   //      TLD can't be all-numeric

	                //      Check for unmatched characters
	                array_multisort($matches[1], SORT_DESC);
	                if ($matches[1][0] !== '')                                                                      return false;   //      Illegal characters in domain, or label longer than 63 characters

	                //      Check DNS?
	                if ($checkDNS && function_exists('checkdnsrr')) {
	                        if (!(checkdnsrr($domain, 'A') || checkdnsrr($domain, 'MX'))) {
	                                                                                                                                        return false;   //      Domain doesn't actually exist
	                        }
	                }

	                //      Eliminate all other factors, and the one which remains must be the truth.
	                //              (Sherlock Holmes, The Sign of Four)
	                return true;
	        }
		}		


	}
?>
