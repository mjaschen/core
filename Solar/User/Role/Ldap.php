<?php
/**
 * 
 * Get roles (groups) from an LDAP server.
 *
 * @category Solar
 * 
 * @package Solar
 * 
 * @subpackage Solar_User
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
 * Get roles (groups) from an LDAP server.
 *
 * @category Solar
 * 
 * @package Solar
 * 
 * @subpackage Solar_User
 * 
 */
class Solar_User_Role_Ldap extends Solar_Base {
    
    /**
     * 
     * Array of user configuration values.
     * 
     * Keys are:
     * 
     * url => (string) URL to the LDAP server. Takes the format of "ldaps://example.com:389".
     * 
     * basedn => (string) The base DN for the LDAP search; example: "o=my company,c=us".
     * 
     * filter => (string) An sprintf() filter string for the LDAP search; %s represents the username.
     * Example: "uid=%s".
     * 
     * attrib => (string) Use these attributes to find role names.
     * 
     * binddn => (string) Bind to the LDAP server as this distinguished name.
     * 
     * bindpw => (string) Bind to the LDAP server as with this password.
     * 
     * @access protected
     * 
     * @var array
     * 
     */
    protected $_config = array(
        'url' => null,
        'basedn' => null,
        'filter' => null,
        'attrib' => array('ou'),
        'binddn' => null,
        'bindpw' => null,
    );
    
    /**
     * 
     * Constructor.
     * 
     */
    public function __construct($config = null)
    {
        // make sure we have LDAP available
        if (! extension_loaded('ldap')) {
            return $this->_error(
                'ERR_EXTENSION',
                array('extension' => 'ldap'),
                E_USER_ERROR
            );
        }
        
        // continue construction
        parent::__construct($config);
    }


    /**
     * 
     * Fetch roles for a user.
     * 
     * @access public
     * 
     * @param string $user Username to get roles for.
     * 
     * @return array An array of roles discovered in LDAP.
     * 
     */
    public function fetch($user)
    {
        // connect
        $conn = @ldap_connect($this->_config['url']);
        
        // did the connection work?
        if (! $conn) {
            return $this->_error(
                'ERR_CONNECT',
                array('url' => $this->_config['url']),
                E_USER_ERROR
            );
        }
        
        // upgrade to LDAP3 when possible
        @ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        
        // bind to the server
        if ($this->_config['binddn']) {
            // authenticated bind
            $bind = @ldap_bind($conn, $this->_config['binddn'], $this->_config['bindpw']);
        } else {
            // anonumous bind
            $bind = @ldap_bind($conn);
        }
        
        // did we bind to the server?
        if (! $bind) {
            // not using $this->_error() because we need fine control
            // over the error text.
            return Solar::error(
                get_class($this), // class name
                @ldap_errno($conn), // error number
                @ldap_error($conn), // error text
                array($this->_config), // other info
                E_USER_NOTICE // error level
            );
        }
        
        // search for the groups
        $filter = sprintf($this->_config['filter'], $user);
        $attrib = (array) $this->_config['attrib'];
        $result = ldap_search($conn, $this->_config['basedn'], $filter, $attrib);
        
        // get the first entry from the search result and free the result.
        $entry = ldap_first_entry($conn, $result);
        ldap_free_result($result);
        
        // now get the data from the entry and close the connection.
        $data = ldap_get_attributes($conn, $entry);
        ldap_close($conn);
        
        // go through the attribute data and add to the list. only
        // retain numeric keys; the ldap entry will have some
        // associative keys that are metadata and not useful to us here.
        $list = array();
        foreach ($attrib as $attr) {
            if (isset($data[$attr]) && is_array($data[$attr])) {
                foreach ($data[$attr] as $key => $val) {
                    if (is_int($key)) {
                        $list[] = $val;
                    }
                }
            }
        }
        
        // done!
        return $list;
    }
}
?>