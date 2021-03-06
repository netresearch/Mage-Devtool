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
 * Mage_Devtool_Block_Layout_Block
 *
 * @category  Mage
 * @package   Mage_Devtool
 * @author    Stephan Hoyer <stephan.hoyer@netresearch.de>
 * @copyright 2011 Netresearch GmbH & Co.KG <http://www.netresearch.de/>
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/leistungen/magento-ecommerce.html
 */
class Mage_Devtool_Block_Layout_Block extends Mage_Core_Block_Template
{
    /**
     * initialize block
     *
     * @param Mage_Core_Model_Layout $layout    Layout
     * @param string                 $blockName Name of the block
     *
     * @return Mage_Devtool_Block_Layout_Block
     */
    public function init(Mage_Core_Model_Layout $layout, $blockName)
    {
        $this->setLayout($layout);
        $this->_block = $layout->getBlock($blockName);
        return $this;
    }
    
    /**
     * get html output
     *
     * @return string
     */
    protected function _toHtml()
    {
        $output = '';
        foreach ($this->_block->getSortedChildren() as $child) {
            $block = new Mage_Devtool_Block_Layout_Block();
            if ($this->getLayout()) {
                $output .= $block->init($this->getLayout(), $child)->toHtml();
            }
        }
        return sprintf('<li id="%s"><a href= "#">%s</a><ul>%s</ul></li>',
            uniqid('devtool-'),
            $this->_block->getNameInLayout() ? 
                $this->_block->getNameInLayout() : 
                Mage_Devtool_Helper_Data::NO_CAPTION_LABEL,
            $output
        ) ;
    }
}
