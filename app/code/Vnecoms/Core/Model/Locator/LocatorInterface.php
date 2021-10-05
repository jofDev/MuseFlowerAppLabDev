<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Core\Model\Locator;

/**
 * Interface LocatorInterface
 */
interface LocatorInterface
{
    /**
     * @return \Vnecoms\Core\Model\Key
     */
    public function getLicense();
}
