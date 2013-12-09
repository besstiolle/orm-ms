<?php
/**
 * Contains all the debug system.
 *
 * @since 0.0.1
 * @author Bess
 * @package Orm
 **/

/**
 *  Allow display stacktrace and other informations in a console or on the page
 *                                                                                     
 *  example
 *  <code>
 *          Trace::debug('i am here !');
 *          Trace::info('process finished');
 *          Trace::warn('Be carfull : too much results will be displayed');
 *          Trace::error('oups houston, we have got a problem... :( ');
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
 
final class Trace
{
	public static $DEBUG = 0;
	public static $INFO = 1;
	public static $WARN = 2;
	public static $ERROR = 3;
	
	//Current level (by default)
	public static $level = 2;
	
	protected static $logFile;
	protected static $logUrl;
	
	protected function __construct() {
	}

	/**
    * Display a message with DEBUG level
    * 
    * @param string the message to display
    */
	public static final	function debug($msg) {	
		self::innerWriter(Trace::$DEBUG, 'debug', $msg);
	}

    /**
    * Display a message with INFO level
    * 
    * @param string the message to display
    */
	public static final	function info($msg) {	
		self::innerWriter(Trace::$INFO, 'info', $msg);
	} 

    /**
    * Display a message with WARN level
    * 
    * @param string the message to display
    */
	public static final	function warn($msg) {	
		self::innerWriter(Trace::$WARN, 'warn', $msg);
	} 

    /**
    * Display a message with ERROR level
    * 
    * @param string the message to display
    */
	public static final	function error($msg) {	
		self::innerWriter(Trace::$ERROR, 'error', $msg);
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
	 * @param string the level of message.
	 * @param string the css class to display the "inline" message
	 * @param string the message to display
	 */
	private static final function innerWriter($level, $cssClass, $msg){
		
		if(self::$level > $level) {return;}
				
		//in file log
		file_put_contents(self::getLogFile() , date('Y-m-d H:i:s', time())." - [$cssClass] - $msg \n", FILE_APPEND );
		
		//In php log
		error_log($msg);
	}
}

?>