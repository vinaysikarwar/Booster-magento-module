<?php
/**
 * Full Page Cache
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * http://opensource.org/licenses/OSL-3.0
 *
 * @package      OSSCube_Fpc
 * @copyright    Copyright (c) 2014 vinay sikarwar
 * @author       Vinay Sikarwar 
 * @license      http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

/**
 * Class OSSCube_Fpc_Test_Model_Fpc
 */
class OSSCube_Fpc_Test_Model_Fpc extends EcomDev_PHPUnit_Test_Case
{

    /**
     * @test
     */
    public function saveAndLoad()
    {
        $fpc = Mage::getSingleton('fpc/fpc');
        $key = 'osscube_fpc';
        $value = 'test';
        $fpc->save($value, $key);
        $this->assertTrue($fpc->load($key) === $value);
        $fpc->remove($key);
        $this->assertTrue($fpc->load($key) === false);
    }

}
