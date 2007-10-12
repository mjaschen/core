<?php
class Solar_Example_Model_TestSolarFoo extends Solar_Sql_Model {
    
    /**
     * 
     * Model setup.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        $dir = str_replace('_', DIRECTORY_SEPARATOR, __CLASS__)
             . DIRECTORY_SEPARATOR
             . 'Setup'
             . DIRECTORY_SEPARATOR;
        
        $this->_table_name = Solar::run($dir . 'table_name.php');
        $this->_table_cols = Solar::run($dir . 'table_cols.php');
        
        $this->_addFilter('email', 'validateEmail');
        $this->_addFilter('uri', 'validateUri');
        
        $this->_index = array(
            'created',
            'updated',
            'email' => 'unique',
            'uri',
        );
    }
}