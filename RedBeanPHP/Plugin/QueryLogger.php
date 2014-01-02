<?php 
namespace RedBeanPHP\Plugin; 
use \RedBeanPHP\Observer as Observer;
use \RedBeanPHP\Plugin as Plugin;
use \RedBeanPHP\Observable as Observable;
use \RedBeanPHP\Adapter\DBAdapter as DBAdapter; 
/**
 * Query Logger
 *
 * @file    RedBean/Plugin/QueryLogger.php
 * @desc    Query logger, can be attached to an observer that signals the sql_exec event.
 * @author  Gabor de Mooij and the RedBeanPHP Community
 * @license BSD/GPLv2
 *
 * (c) copyright G.J.G.T. (Gabor) de Mooij and the RedBeanPHP Community.
 * This source file is subject to the BSD/GPLv2 License that is bundled
 * with this source code in the file license.txt.
 */
class QueryLogger implements Observer, Plugin
{

	/**
	 * @var array
	 */
	protected $logs = array();

	/**
	 * Singleton pattern
	 * Constructor - private
	 */
	private function __construct() { }

	/**
	 * Creates a new instance of the Query Logger and attaches
	 * this logger to the adapter.
	 *
	 * @static
	 *
	 * @param Observable $adapter the adapter you want to attach to
	 *
	 * @return QueryLogger
	 */
	public static function getInstanceAndAttach( Observable $adapter )
	{
		$queryLog = new QueryLogger;

		$adapter->addEventListener( 'sql_exec', $queryLog );

		return $queryLog;
	}

	/**
	 * Implementation of the onEvent() method for Observer interface.
	 * If a query gets executed this method gets invoked because the
	 * adapter will send a signal to the attached logger.
	 *
	 * @param  string                    $eventName ID of the event (name)
	 * @param  DBAdapter $adapter   adapter that sends the signal
	 *
	 * @return void
	 */
	public function onEvent( $eventName, $adapter )
	{
		if ( $eventName == 'sql_exec' ) {
			$this->logs[] = $adapter->getSQL();
		}
	}

	/**
	 * Searches the logs for the given word and returns the entries found in
	 * the log container.
	 *
	 * @param  string $word word to look for
	 *
	 * @return array
	 */
	public function grep( $word )
	{
		$found = array();
		foreach ( $this->logs as $log ) {
			if ( strpos( $log, $word ) !== FALSE ) {
				$found[] = $log;
			}
		}

		return $found;
	}

	/**
	 * Returns all the logs.
	 *
	 * @return array
	 */
	public function getLogs()
	{
		return $this->logs;
	}

	/**
	 * Clears the logs.
	 *
	 * @return void
	 */
	public function clear()
	{
		$this->logs = array();
	}
}