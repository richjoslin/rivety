<?php

// TODO: change this to NaturalDocs format

/**
 * Geeklog's Log Utility
 *
 * This class uses Zend Log.  Why have this class then?  Because we feel it should be as easy as
 * calling a static method on a class in order to log something...especially when dealing with
 * a plugin-based system.
 *
 * @author Tony Bibbs <tony@tonybibbs.com>
 * @version $Id:$
 *
 */
class Cts_Log
{
	/**
	 * Array of Zend Log instances
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Holds logical name of the default logger
	 * @var string
	 */
	private static $defaultLogger = null;

	/**
	 * Returns a logger and ensure that there is only one instance created in the process
	 *
	 * @access public
	 * @static
	 * @param Zend_Log_Writer Child of Zend_Log_Writer
	 * @param boolean $isDefault Indicates if this logger is the default one to use
	 *
	 */
	public static function registerLogger($loggerName, $zendWriter = '', $isDefault = false)
	{
		if (!isset(self::$instances[$loggerName]))
		{
			self::$instances[$loggerName] = new Zend_Log($zendWriter);
		}

		// If we weren't told this is the default logger yet none exists then force it
		if (!self::getDefaultLoggerName() AND !$isDefault) $isDefault = true;

		if ($isDefault)
		{
			if (isset(self::$defaultLogger))
			{
				throw new Exception('Default logger is already defined');
			}
			self::$defaultLogger = $loggerName;
		}
	}

	/**
	 * Useful for debugging only
	 *
	 * @static
	 * @return array Collection of Zend_Log descendents.
	 *
	 */
	public static function getLoggerInstances()
	{
		return self::$instances;
	}

	/**
	 * Returns the name of the default logger
	 *
	 * @static
	 * @param boolean $throwIfNull Indicate if we should throw an exception if the default logger
	 * is null
	 * @return string Logical name of the default logger otherwise false
	 *
	 */
	public static function getDefaultLoggerName($throwIfNull = true)
	{
		if (is_null(self::$defaultLogger) AND $throwIfNull) return false;
		return self::$defaultLogger;
	}

	/**
	 * Unregister's a logger
	 *
	 * @static
	 * @access public
	 * @param string $loggerName Logical name of the logger
	 */
	public static function unregisterLogger($loggerName)
	{
		if (!in_array($loggerName,array_keys(self::$instances))) return false;
		unset(self::$instances[$loggerName]);
		if (self::getDefaultLoggerName() == $loggerName) self::$defaultLogger = null;
	}

	/**
	 * Add a filter that will be applied before all log writers.
	 *
	 * Before a message will be received by any of the writers, it must be accepted by all filters
	 * added with this method.
	 *
	 * @param Zend_Log_Filter_Interface $filter
	 * @param string $loggerName Logical name of logger to add filter to
	 *
	 */
	public static function addFilter($filter, $loggerName = null)
	{
		if (is_null($loggerName)) $loggerName = self::getDefaultLoggerName();
		$logger = self::$instances[$loggerName];
		$logger->addFilter($filter);
		self::$instances[$loggerName] = $logger;
	}

	/**
	 * Add a writer.
	 *
	 * A writer is responsible for taking a log message and writing it out to storage.
	 *
	 * @param Zend_Log_Writer $writer Child of Zend_Log_Writer
	 * @param string $loggerName Logical name of logger to add writer to
	 *
	 */
	public static function addWriter($writer, $loggerName = null)
	{
		if (is_null($loggerName)) $loggerName = self::getDefaultLoggerName();

		$logger = self::$instances[$loggerName];
		$logger->addWriter($writer);
		self::$instances[$loggerName] = $logger;
	}

	/**
	 * Add a custom priority
	 *
	 * @param string $loggerName Logical name of the logger to add priority to
	 * @param string $name Priority name
	 * @param integer $priority Value for the priority
	 *
	 */
	public static function addPriority($name, $priority, $loggerName = null)
	{
		if (is_null($loggerName)) $loggerName = self::getDefaultLoggerName();
		$logger = self::$instances[$loggerName];
		$logger->addPriority($name, $priority);
		self::$instances[$loggerName] = $logger;
	}

	/**
	 * Set an extra item to pass to the log writers.
	 *
	 * @param string $loggerName Logical name of the logger to set event for
	 * @param unknown_type $name Name of the field
	 * @param unknown_type $value Value of the field
	 *
	 */
	public static function setEventItem($name, $value, $loggerName = null)
	{
		if (is_null($loggerName)) $loggerName = self::getDefaultLoggerName();
		$logger = self::$instances[$loggerName];
		$logger->setEventItem($name, $value);
		self::$instances[$loggerName] = $logger;
	}

	/**
	 * Gets the requested logger
	 *
	 * I can't think of a good reason to need this other than possibly handing the logger off to
	 * some ZF compatible library/app.
	 *
	 * @param string $loggerName Logical name of the logger to get
	 * @return Zend_Log Instance of Zend_Log requested.
	 */
	public static function &getLogger($loggerName = null)
	{
		if (is_null($loggerName)) $loggerName = self::getDefaultLoggerName();
		return self::$instances[$loggerName];
	}
	/**
	 * Logs a message
	 *
	 * For reference here are the default log levels available as class constants in Zend_Log:
	 *
	 * NOTE: see the helper function below that make logging direct to a log level easier.
     *
	 * @param string $message Message to log
	 * @param string $loggerName Logical name of logger instance
	 * @param int $priority Log priority
	 *
	 */
	public static function log($message, $priority = Zend_Log::INFO, $loggerName = null)
	{
		// TODO: the logger can't log before the app is installed, but the install process tries to use the logger

		if (is_null($loggerName)) $loggerName = self::getDefaultLoggerName();
		if (!$loggerName) throw new Exception('log() was not given a logger name and no default logger exists');
		$logger = self::$instances[$loggerName];
		$logger->log($message, $priority);
	}

	/**
	 * Logs emergency messages
	 *
	 * @param string $message Message to log
	 * @param string $loggerName Logical name of logger instance
	 *
	 */
	public static function emerg($message, $loggerName = null)
	{
		self::log($message, Zend_Log::EMERG, $loggerName);
	}
	/**
	 * Logs alert messages
	 *
	 * @param string $message Message to log
	 * @param string $loggerName Logical name of logger instance
	 *
	 */
	public static function alert($message, $loggerName = null)
	{
		self::log($message, Zend_Log::ALERT, $loggerName);
	}
	/**
	 * Logs critical messages
	 *
	 * @param string $message Message to log
	 * @param string $loggerName Logical name of logger instance
	 *
	 */
	public static function crit($message, $loggerName = null)
	{
		self::log($message, Zend_Log::CRIT, $loggerName);
	}

	/**
	 * Logs error messages
	 *
	 * @param string $message Message to log
	 * @param string $loggerName Logical name of logger instance
	 *
	 */
	public static function err($message, $loggerName = null)
	{
		self::log($message, Zend_Log::ERR, $loggerName);
	}

	/**
	 * Logs warning messages
	 *
	 * @param string $message Message to log
	 * @param string $loggerName Logical name of logger instance
	 *
	 */
	public static function warn($message, $loggerName = null)
	{
		self::log($message, Zend_Log::WARN, $loggerName);
	}

	/**
	 * Logs notice messages
	 *
	 * @param string $message Message to log
	 * @param string $loggerName Logical name of logger instance
	 *
	 */
	public static function notice($message, $loggerName = null)
	{
		self::log($message, Zend_Log::NOTICE, $loggerName);
	}

	/**
	 * Logs info messages
	 *
	 * @param string $message Message to log
	 * @param string $loggerName Logical name of logger instance
	 *
	 */
	public static function info($message, $loggerName = null)
	{
		self::log($message, Zend_Log::INFO, $loggerName);
	}

	/**
	 * Logs debug messages
	 *
	 * @param string $message Message to log
	 * @param string $loggerName Logical name of logger instance
	 *
	 */
	public static function debug($message, $loggerName = null)
	{
		self::log($message, Zend_Log::DEBUG, $loggerName);
	}

	public static function report($message, $var = null, $level = 7, $loggerName = null)
	{
		$message = $message . " : " . $var;
		self::log($message, $level,$loggerName);
	}

}
