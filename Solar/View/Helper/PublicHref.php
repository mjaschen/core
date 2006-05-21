<?php
/**
 * 
 * Helper for public hrefs.
 * 
 * @category Solar
 * 
 * @package Solar_View
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */

/**
 * Solar_View_Helper
 */
Solar::loadClass('Solar_View_Helper');
 
/**
 * 
 * Helper for public hrefs.
 * 
 * @category Solar
 * 
 * @package Solar_View
 * 
 */
class Solar_View_Helper_PublicHref extends Solar_View_Helper {
    
    /**
     * 
     * Internal URI object for creating links.
     * 
     * @var Solar_Uri_Public
     * 
     */
    protected $_uri = null;
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config User-defined configuration.
     * 
     */
    public function __construct($config)
    {
        // do the real configuration
        parent::__construct($config);
        
        // public uri processor
        $this->_uri = Solar::factory('Solar_Uri_Public');
    }
    
    /**
     * 
     * Returns an href to a public resource.
     * 
     * @param Solar_Uri_Public|string $spec The public resource href specification.
     * 
     * @param bool $raw Return the resource string without escaping (default false).
     * 
     * @return string
     * 
     */
    public function publicHref($spec, $raw = false)
    {
        if ($spec instanceof Solar_Uri_Public) {
            // already a public uri object
            $href = $spec->fetch();
        } else {
            // build-and-fetch the string as a public href
            $href = $this->_uri->quick($spec);
        }
        
        // return escaped or not?
        if ($raw) {
            return $href;
        } else {
            return $this->_view->escape($href);
        }
    }
}
?>