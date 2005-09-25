<?php

/**
* 
* Class for SQL select generation and results.
* 
* @category Solar
* 
* @package Solar
* 
* @subpackage Solar_Sql
* 
* @author Paul M. Jones <pmjones@solarphp.com>
* 
* @license LGPL
* 
* @version $Id$
* 
*/


/**
* 
* Class for SQL select generation and results.
* 
* Proposed usage:
* 
* <code>
* $select = Solar::object('Solar_Sql_Select');
* 
* // select these columns
* $select->cols(
*   'id',
* 	'n_last',
* 	'n_first',
* 	'adr_street',
* 	'adr_city',
* 	'adr_region AS state',
* 	'adr_postcode AS zip',
* 	'adr_country',
* );
* 
* // from this table
* $select->from('contacts'); // single or multi
* 
* // on these ANDed conditions
* $select->where('n_last = :lastname');
* $select->where('adr_city = :city');
* 
* // reverse-ordered by first name
* $select->order('n_first DESC')
* 
* // get 50 at a time
* $select->paging(50);
* 
* // fetch the first page of all rows of this bound data ...
* // remember :lastname and :city in the setWhere() calls above.
* $data = ('lastname' => 'Jones', 'city' => 'Memphis');
* 
* // use this to indicate which page of results we want
* $page = 1;
* 
* // fetch all rows as an array (the default)
* $select->fetch('all');
* $result = $select->exec($data, $page);
* 
* // alternatively, get a Solar_Sql_Result object
* $select->fetch('result'); // or null
* $result = $select->exec($data, $page);
* 
* // find out the count of rows, and how many pages there are.
* // this comes back as an array('count' => ?, 'pages' => ?).
* $total = $select->countPages($data);
* 
* </code>
* 
* @category Solar
* 
* @package Solar
* 
* @subpackage Solar_Sql
* 
*/

class Solar_Sql_Select extends Solar_Base {
	
	
	/**
	* 
	* User-provided configuration.
	* 
	* Keys are:
	* 
	* sql => (string|array) Name of the shared SQL object, or array of (driver,
	* options) to create a standalone SQL object.
	* 
	* locale => (string) Path to locale files.
	* 
	* paging => (int) Number of rows per page.
	* 
	* @access protected
	* 
	* @var array
	* 
	*/
	
	protected $config = array(
		'sql'    => 'sql',
		'paging' => 10,
		'fetch'  => 'all',
	);
	
	
	protected $parts = array(
		'cols'   => array(),
		'from'   => array(),
		'join'   => array(),
		'where'  => array(),
		'group'  => array(),
		'having' => array(),
		'order'  => array(),
		'limit'  => array(
			'count'  => 0,
			'offset' => 0
		),
	);
		
	/**
	* 
	* The number of rows per page.
	* 
	* @access protected
	* 
	* @var int
	* 
	*/
	
	protected $paging = 10;
	
	
	/**
	* 
	* A fetch mode ('all', 'one', 'row', etc.)
	* 
	* Set to empty/null/false to get a result object.
	* 
	* Eventually, a PDO_FETCHMODE_* constant.
	* 
	* @access protected
	* 
	* @var string
	* 
	*/
	
	protected $fetch = 'all';
	
	
	/**
	* 
	* Data to bind into the query as key => value pairs.
	* 
	* @access protected
	* 
	* @var array
	* 
	*/
	
	protected $bind = array();
	
	
	/**
	* 
	* Constructor.
	* 
	* @access public
	* 
	* @param array $config User-provided config values.
	* 
	*/

	public function __construct($config = null)
	{
		// basic construction
		parent::__construct($config);
		
		// connect to the database
		if (is_string($this->config['sql'])) {
			// use a shared object
			$this->sql = Solar::shared($this->config['sql']);
		} else {
			// use a standalone object
			$this->sql = Solar::object(
				$this->config['sql'][0],
				$this->config['sql'][1]
			);
		}
		
		// set up defaults
		$this->paging($this->config['paging']);
		$this->fetch($this->config['fetch']);
	}
	
	
	/**
	* 
	* Read-only access to properties.
	* 
	* @access public
	* 
	* @return mixed The property value.
	* 
	*/

	public function __get($key)
	{
		return $this->$key;
	}
	
	
	/**
	* 
	* Sets the paging value.
	* 
	* @access public
	* 
	* @param int $val The number of rows to page at.
	* 
	* @return void
	* 
	*/

	public function paging($val)
	{
		// force a positive integer
		$val = (int) $val;
		if ($val < 1) {
			$val = 1;
		}
		
		// set the paging value
		$this->paging = $val;
	}
	
	
	/**
	* 
	* Sets the fetch-mode value.
	* 
	* @access public
	* 
	* @param string $mode The fetch mode (null to return a result object, or
	* 'all', 'row', 'col', etc. to use a fetch*() method.
	* 
	* @return void
	* 
	*/

	public function fetch($mode = null)
	{
		$this->fetch = strtolower(trim($mode));
	}
	
	
	/**
	* 
	* Adds data to bind into the query.
	* 
	* @access public
	* 
	* @param mixed $key The replacement key in the query.  If this is an
	* array or object, the $val parameter is ignored, and all the
	* key-value pairs in the array (or all properties of the object) are
	* added to the bind.
	* 
	* @param mixed $val The value to use for the replacement key.
	* 
	* @return void
	* 
	*/

	public function bind($key, $val = null)
	{
		if (is_array($key)) {
			$this->bind = array_merge($this->bind, $key);
		} elseif (is_object($key)) {
			$this->bind = array_merge((array) $this->bind, $key);
		} else {
			$this->bind[$key] = $val;
		}
	}
	
	
	/**
	* 
	* Unsets bound data.
	* 
	* @access public
	* 
	* @param mixed $spec The key to unset.  If a string, unsets that one
	* bound value; if an array, unsets the list of values; if empty, unsets
	* all bound values (the default).
	* 
	* @return void
	* 
	*/

	public function unbind($spec = null)
	{
		if (empty($spec)) {
			$this->bind = array();
		} else {
			settype($spec, 'array');
			foreach ($spec as $key) {
				unset($this->bind[$key]);
			}
		}
	}
	
	
	/**
	* 
	* Clears query properties.
	* 
	* @access public
	* 
	* @param string $key The property to clear; if empty, clears all
	* query properties.
	* 
	* @return void
	* 
	*/

	public function clear($key = null)
	{
		$tmp = array($this->parts);
		
		if (empty($key)) {
			// clear all
			foreach ($tmp as $key) {
				$this->parts[$key] = array();
			}
		} elseif (in_array($key, $tmp)) {
			// clear some
			$this->parts[$key] = array();
		}
		
		// make sure limit has a count and offset
		if (empty($this->parts['limit'])) {
			$this->parts['limit'] = array(
				'count' => 0,
				'offset' => 0
			);
		}
	}
	
	
	/**
	* 
	* Adds columns to the query.
	* 
	* @access public
	* 
	* @param string|array $cols The columns to add.
	* 
	* @return void
	* 
	*/

	public function cols($spec)
	{
		if (is_string($spec)) {
			$spec = explode(',', $spec);
		} else {
			settype($spec, 'array');
		}
		
		$this->parts['cols'] = array_merge($this->parts['cols'], $spec);
	}
	
	
	/**
	* 
	* Adds a table to the query, optionally with columns from that table.
	* 
	* @access public
	* 
	* @param string|array $from The table name(s) to select from.
	* 
	* @return void
	* 
	*/

	public function from($spec)
	{
		if (is_string($spec)) {
			$spec = explode(',', $spec);
		} else {
			settype($spec, 'array');
		}
		
		$this->parts['from'] = array_merge($this->parts['from'], $spec);
	}
	
	
	/**
	* 
	* Adds joins to the query, one at a time.
	* 
	* @access public
	* 
	* @param string $name The table name to join to.
	* 
	* @param string $code Join on this condition.
	* 
	* @param string $type The type of join to perform, e.g.
	* "left", "right", "inner", etc.  Typically not needed.
	* 
	* @return void
	* 
	*/

	public function join($name, $cond, $type = null)
	{
		$this->parts['join'][] = array(
			'type' => null,
			'name' => $name,
			'cond' => $cond
		);
	}
	
	
	/**
	* 
	* Adds a WHERE condition to the query.
	* 
	* @access public
	* 
	* @param string $condition The WHERE condition.
	* 
	* @param string $op Whether to 'AND' or 'OR' this condition with
	* existing conditions (default is 'AND').
	* 
	* @return void
	* 
	*/

	public function where($filter, $op = 'AND')
	{
		if ($this->parts['where']) {
			$this->parts['where'][] = strtoupper($op) . ' ' . $condition;
		} else {
			$this->parts['where'][] = $condition;
		}
	}
	
	
	/**
	* 
	* Adds a grouping to the query.
	* 
	* @access public
	* 
	* @param string|array $spec The column(s) to group by.
	* 
	* @return void
	* 
	*/

	public function group($spec)
	{
		if (is_string($spec)) {
			$spec = explode(',', $spec);
		} else {
			settype($spec, 'array');
		}
		
		$this->parts['group'] = array_merge($this->parts['group'], $spec);
	}
	
	
	/**
	* 
	* Adds a HAVING filter to the query.
	* 
	* @access public
	* 
	* @param string $condition The HAVING filter.
	* 
	* @param string $op Whether to 'AND' or 'OR' this filter with
	* existing filters (default is 'AND').
	* 
	* @return void
	* 
	*/

	public function having($filter, $op = 'AND')
	{
		if ($this->parts['having']) {
			$this->parts['having'][] = strtoupper($op) . ' ' . $filter;
		} else {
			$this->parts['having'][] = $filter;
		}
	}
	
	
	/**
	* 
	* Adds a row order to the query.
	* 
	* @access public
	* 
	* @param string|array $spec The column(s) and direction to order by.
	* 
	* @return void
	* 
	*/

	public function order($spec)
	{
		if (is_string($spec)) {
			$spec = explode(',', $spec);
		} else {
			settype($spec, 'array');
		}
		
		// force 'ASC' or 'DESC' on each order spec, default is ASC.
		foreach ($spec as $key => $val) {
			$asc  = (strtoupper(substr($val, -4)) == ' ASC');
			$desc = (strtoupper(substr($val, -5)) == ' DESC');
			if (! $asc && ! $desc) {
				$spec[$key] .= ' ASC';
			}
		}
		
		// merge them into the current order set
		$this->parts['order'] = array_merge($this->parts['order'], $spec);
	}
	
	
	/**
	* 
	* Sets a limit count and offset to the query.
	* 
	* @access public
	* 
	* @param int $count The number of rows to return.
	* 
	* @param int $offset Start returning after this many rows.
	* 
	* @return void
	* 
	*/

	public function limit($count = null, $offset = null)
	{
		$this->parts['limit']['count']  = (int) $count;
		$this->parts['limit']['offset'] = (int) $offset;
	}
	
	
	/**
	* 
	* Fetch an array of results based on the current query properties.
	* 
	* @access public
	* 
	* @param string $type The type of fetch to perform (all, one, row, etc).
	* 
	* @param array $data Data to bind to the SELECT statement.
	* 
	* @param int $page The page number of results to fetch.
	* 
	* @return mixed The query results in an array (or as a string, if
	* fetching only 'one').
	* 
	*/

	public function exec($page = null)
	{
		if (! $this->fetch || $this->fetch == 'result') {
			$method = 'exec';
		} else {
			$method = 'fetch' . ucfirst($this->fetch);
		}
		
		$page = (int) $page;
		if ($page) {
			$this->parts['limit'] = $this->pageLimit($page);
		}
		
		$exec = false; // don't execute, just get the statement
		$stmt = $this->sql->select($this->parts, $this->bind, $exec);
		
		return $this->sql->$method($stmt);
	}
	
	
	/**
	* 
	* Get the count of rows and number of pages for the current query.
	* 
	* @access public
	* 
	* @param string $col The column to COUNT() on.
	* 
	* @return array An associative array with keys 'count' (the total number
	* of rows) and 'pages' (the number of pages based on $this->paging).
	* 
	*/

	public function countPages($col = '*')
	{
		// make a self-cloned copy so that all settings are identical
		$select = $this->__clone();
		
		// clear out all columns, then add a single COUNT column
		$select->clear('cols');
		$select->cols('COUNT($col)');
		
		// clear any limits
		$select->clear('limit');
		
		// select the count of rows and free the cloned copy
		$select->fetch('one');
		$result = $select->exec($data);
		unset($select);
		
		// was there an error?
		if (Solar::isError($result)) {
			return $result;
		}
		
		// $result is the row-count; how many pages does it convert to?
		$pages = 0;
		if ($result > 0) {
			$pages = ceil($result / $this->paging);
		}
		
		// done!
		return array(
			'count' => $result,
			'pages' => $pages
		);
	}
	
	
	/**
	* 
	* Calculates the limit count and offset for a given page number.
	* 
	* Pages are 1-based; page 1 is records 1-10, page 2 is 11-20, and
	* so on.  Page 0 is nonexistent, and will not set a limit.
	* 
	* @access protected
	* 
	* @param int $page The page number to get limits for.
	* 
	* @return array An associative array with 'count' and 'offset' values.
	* 
	*/
	
	protected function pageLimit($page = null)
	{
		if ($page !== null && $page !== false && $page > 0) {
			$count  = $this->paging;
			$offset = $count * ($page - 1);
		} else {
			$count  = 0;
			$offset = 0;
		}
		
		return array(
			'count'  => $count,
			'offset' => $offset
		);
	}
}
?>