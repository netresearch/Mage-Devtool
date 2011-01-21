<?php
/**
 * Magento
 *
 * PHP version 5
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mage
 * @package   Mage_Devtool
 * @author    Stephan Hoyer <stephan.hoyer@netresearch.de>
 * @copyright 2011 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/leistungen/magento-ecommerce.html
 */

/**
 * Netresearch_Productvisibility_Model_Observer
 *
 * @category  Mage
 * @package   Mage_Devtool_Model_Observer
 * @author    Stephan Hoyer <stephan.hoyer@netresearch.de>
 * @copyright 2011 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/leistungen/magento-ecommerce.html
 */
class Mage_Devtool_Model_Observer
{
    const PARAM_NAME_SHOW_DEVTOOL = 'show-devtool';
    const PARAM_NAME_HIDE_DEVTOOL = 'hide-devtool';
    
    /**
     * catch all thrown events
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function attachToAllEvents($observer)
    {
        if (false == Mage::helper('devtool')->isDevtoolVisible()) {
            return;
        }
        $this->attachToEvents('global');
        $this->attachToEvents('frontend');
        $this->attachToEvents('adminhtml');
    }
    
    /**
     * Change config that logEvent method is called on every event 
     * of given areathat is dispached
     *
     * @param string $area Can be one of global, frontend, adminhtml
     *
     * @return void
     **/ 
    protected function attachToEvents($area)
    {
        foreach (Mage::getConfig()->getNode($area)->events->children() as $event) {
                $event->observers->devtool->type = 'singleton';
                $event->observers->devtool->class = 'devtool/observer';
                $event->observers->devtool->method = 'logEvent';
        }
    }

    /**
     * log event
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function logEvent(Varien_Event_Observer $observer)
    {
        if (false == Mage::helper('devtool')->isDevtoolVisible()) {
            return;
        }
        $event = Mage::getModel('devtool/event')->init($observer);
        Mage::getSingleton('devtool/event_collection')->add($event);
    }
    
    /**
     * replace placeholder by event tab of devtool
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     */
    public function attachEventHtml($observer)
    {
        if (false == Mage::helper('devtool')->isDevtoolVisible()) {
            return;
        }
        $response = Mage::app()->getResponse();
        $events = Mage::app()->getLayout()->createBlock(
            'Mage_Devtool_Block_Events',
            'events',
            array('template' => 'devtool/events.phtml')
        )->toHtml();
        $response->setBody(
            str_replace(
                Mage_Devtool_Block_Events_Placeholder::EVENTS_HTML_PLACEHOLDER,
                $events,
                $response->getBody()
            )
        );
    }
    
    /**
     * Checks for devtool related get-parameteters 
     *
     * @param Varien_Event_Observer $observer Observer
     *
     * @return void
     **/
    public function checkParams($observer)
    {
        $params = $observer
            ->getControllerAction()
            ->getRequest()
            ->getParams();
        if (array_key_exists(self::PARAM_NAME_SHOW_DEVTOOL, $params)) {
            Mage::getSingleton('core/session')->setShowDevtool(true);
        }
        if (array_key_exists(self::PARAM_NAME_HIDE_DEVTOOL, $params)) {
            Mage::getSingleton('core/session')->setShowDevtool(false);
        }
    }
}
