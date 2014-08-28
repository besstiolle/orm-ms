<?php
/**
 * Contains all the debug system.
 *
 * @since 0.0.1
 * @author Bess
 **/

/**
 *  Allow display stacktrace and other informations in a console or on the page
 *                                                                                     
 *  example
 *  <code>
 *          OrmTrace::debug('i am here !');
 *          OrmTrace::info('process finished');
 *          OrmTrace::warn('Be carfull : too much results will be displayed');
 *          OrmTrace::error('oups houston, we have got a problem... :( ');
 *  </code>
 * 
 * the property $level of this class defines the level of the messages to displaying
 *  So a level of "WARN" will only display warning and error messages
 *  By default the level is setted to WARN
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/   
 
final class OrmTrace {

	/**
	* DEBUG & INFO & WARN & ERROR level
	**/
	public static $DEBUG = 0;

	/**
	* INFO & WARN & ERROR level
	**/
	public static $INFO = 1;

	/**
	* WARN & ERROR level
	**/
	public static $WARN = 2;

	/**
	* ERROR level
	**/
	public static $ERROR = 3;

	/**
	* inner path to the log file
	**/
	protected static $logFile;

	/**
	* inner url to the log file 
	**/
	protected static $logUrl;
	
	/**
	* protected constructor
	**/
	protected function __construct() {
	}

	/**
    * Display a message with DEBUG level
    * 
    * @param string $msg the message to display
    */
	public static final	function debug($msg) {	
		self::innerWriter(OrmTrace::$DEBUG, 'debug', $msg);
	}

    /**
    * Display a message with INFO level
    * 
    * @param string $msg the message to display
    */
	public static final	function info($msg) {	
		self::innerWriter(OrmTrace::$INFO, 'info', $msg);
	} 

    /**
    * Display a message with WARN level
    * 
    * @param string $msg the message to display
    */
	public static final	function warn($msg) {	
		self::innerWriter(OrmTrace::$WARN, 'warn', $msg);
	} 

    /**
    * Display a message with ERROR level
    * 
    * @param string $msg the message to display
    */
	public static final	function error($msg) {	
		self::innerWriter(OrmTrace::$ERROR, 'error', $msg);
	}
	
	/**
	 * Will return the path of the log file
	 *
	 * @return string the path of the log file
	 **/
	public static final function getLogFile(){
		if(self::$logFile == null){
			$config = cmsms()->GetConfig();
			self::$logFile = $config['root_path'].'/tmp/cache/orm.log';
		}
		return self::$logFile;
	}
	
	/**
	 * Will return the url of the log file
	 *
	 * @return string the url of the log file
	 **/
	public static final function getLogUrl(){
		if(self::$logUrl == null){
			$config = cmsms()->GetConfig();
			self::$logUrl = $config['root_url'].'/tmp/cache/orm.log';
		}
		return self::$logUrl;
	}
	
	/**
	 * Do all the suff behind
	 *
	 * @param string $level the level of message.
	 * @param string $cssClass the css class to display the "inline" message
	 * @param string $msg the message to display
	 */
	private static final function innerWriter($level, $cssClass, $msg){
		$orm = cmsms()->GetModuleOperations()->get_module_instance('Orm');

		if($orm->GetPreference('loglevel', OrmTrace::$INFO) > $level) {return;}
		
		$content = date('Y-m-d H:i:s', time())." - [$cssClass] - $msg \n";
				
		//in file log
		file_put_contents(self::getLogFile() ,$content, FILE_APPEND );

		//In php log
		error_log($msg);
	}
}

?>
