<?php
/**
 * 
 * Solar command to make a model class from an SQL table.
 * 
 * @category Solar
 * 
 * @package Solar_Cli
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id: Auth.php 2428 2007-04-02 00:44:19Z pmjones $
 * 
 */

/**
 * 
 * Solar command to make a model class from an SQL table.
 * 
 * @category Solar
 * 
 * @package Solar_Cli
 * 
 */
class Solar_Cli_MakeModel extends Solar_Controller_Command {
    
    /**
     * 
     * The base directory where we will write the class file to, typically
     * the local PEAR directory.
     * 
     * @var string
     * 
     */
    protected $_target = null;
    
    /**
     * 
     * The table name we're making the model from.
     * 
     * @var string
     * 
     */
    protected $_table = null;
    
    /**
     * 
     * Array of model-class templates (skeletons).
     * 
     * @var array
     * 
     */
    protected $_tpl = array();
    
    /**
     * 
     * Write out a series of model, record, and collection classes for a model.
     * 
     * @param string $class The target class name for the model.
     * 
     * @return void
     * 
     */
    protected function _exec($class = null)
    {
        $this->_println('Making model.');
        
        // we need a class name, at least
        if (! $class) {
            throw $this->_exception('ERR_NO_CLASS');
        }
        
        // we need a table name
        $this->_setTable($class);
        
        // we need a target directory
        $this->_setTarget();
        
        // emit feedback
        $this->_println("Using table '{$this->_table}'.");
        $this->_println("Writing '$class' to '{$this->_target}'.");
        
        // load the templates
        $this->_loadTemplates();
        
        // get the table info
        $sql = Solar::factory('Solar_Sql', $this->_getSqlConfig());
        $table_cols = $sql->fetchTableCols($this->_table);
        if (! $table_cols) {
            throw $this->_exception('ERR_NO_COLS', array(
                'table' => $this->_table
            ));
        }
        
        // what class the model extends from
        $extends = 'Solar_Sql_Model';
        
        // get the class model template
        $text = str_replace(
            array(':class', ':extends'),
            array($class, $extends),
            $this->_tpl['model']
        );
        
        // write the model class
        $target = $this->_target
                . str_replace('_', DIRECTORY_SEPARATOR, $class)
                . '.php';
        
        if (file_exists($target)) {
            $this->_println('Model class exists.');
        } else {
            $this->_println('Writing model class.');
            file_put_contents($target, $text);
        }
        
        
        // get the setup dir
        $dir = Solar::fixdir(
            $this->_target . str_replace('_', '/', $class) . '/Setup'
        );
        @mkdir($dir, 0777, true);
        
        // write the cols file
        $this->_println('Updating table cols.');
        $text = var_export($table_cols, true);
        file_put_contents($dir . 'table_cols.php', "<?php\nreturn $text;");
        
        // write the name file
        $this->_println('Updating table name.');
        file_put_contents($dir . 'table_name.php', "<?php\nreturn '{$this->_table}';");
        
        // write the record template
        $record_target = substr($target, 0, -4) . DIRECTORY_SEPARATOR . 'Record.php';
        $text = str_replace(
            array(':class', ':extends'),
            array($class, $extends),
            $this->_tpl['record']
        );
        
        if (file_exists($record_target)) {
            $this->_println('Record class exists.');
        } else {
            $this->_println('Writing record class.');
            file_put_contents($record_target, $text);
        }
        
        // write the record template
        $collection_target = substr($target, 0, -4) . DIRECTORY_SEPARATOR . 'Collection.php';
        $text = str_replace(
            array(':class', ':extends'),
            array($class, $extends),
            $this->_tpl['collection']
        );
        
        if (file_exists($collection_target)) {
            $this->_println('Collection class exists.');
        } else {
            $this->_println('Writing collection class.');
            file_put_contents($collection_target, $text);
        }
        
        // done!
        $this->_println("Done.");
    }
    
    /**
     * 
     * Loads the template array from skeleton files.
     * 
     * @return void
     * 
     */
    protected function _loadTemplates()
    {
        $this->_tpl = array();
        $dir = Solar::fixdir(dirname(__FILE__) . '/MakeModel/Data');
        $list = glob($dir . '*.php');
        foreach ($list as $file) {
            $key = substr(basename($file), 0, -4);
            $this->_tpl[$key] = file_get_contents($file);
        }
    }
    
    /**
     * 
     * Sets the base directory target.
     * 
     * @return void
     * 
     */
    protected function _setTarget()
    {
        $target = $this->_options['target'];
        if (! $target) {
            // use the same target as 2 levels up from this class,
            // should be the PEAR dir (or main Solar dir)
            $target = Solar::dirname(__FILE__, 2);
        }
        
        $this->_target = Solar::fixdir($target);
    }
    
    /**
     * 
     * Sets the table name; determines from the class name if no table name is
     * given.
     * 
     * @param string $class The class name for the model.
     * 
     * @return void
     * 
     */
    protected function _setTable($class)
    {
        $table = $this->_options['table'];
        if (! $table) {
            // try to determine from the class name
            $pos = strpos($class, 'Model_');
            if (! $pos) {
                throw $this->_exception('ERR_CANNOT_DETERMINE_TABLE');
            }
            
            // convert Solar_Model_TableName to table_name
            $table = substr($class, $pos + 6);
            $table = preg_replace('/([a-z])([A-Z])/', '$1_$2', $table);
            $table = strtolower($table);
        }
        
        $this->_table = $table;
    }
    
    /**
     * 
     * Gets the SQL connection parameters from the command line options.
     * 
     * @return array An array of SQL connection parameters suitable for 
     * passing as a Solar_Sql_Adapter class config.
     * 
     */
    protected function _getSqlConfig()
    {
        $config = array();
        $list = array('adapter', 'host', 'port', 'user', 'pass', 'name');
        foreach ($list as $key) {
            $val = $this->_options[$key];
            if ($val) {
                $config[$key] = $val;
            }
        }
        return $config;
    }
}
