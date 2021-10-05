<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Core\Model\Locator;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;

/**
 * Class RegistryLocator
 */
class RegistryLocator implements LocatorInterface
{
    /**
     * @var Registry
     */
    private $_registry;

    /**
     * @var \Vnecoms\Core\Model\Key
     */
    private $_license;

    /**
     * @param Registry $registry
     */
    public function __construct(Registry $registry)
    {
        $this->_registry = $registry;
    }

    /**
     * {@inheritdoc}
     * @throws NotFoundException
     */
    public function getLicense()
    {
        if (null !== $this->_license) {
            return $this->_license;
        }

        if ($license = $this->_registry->registry('current_license')) {
            return $this->_license = $license;
        }

        throw new NotFoundException(__('The license was not registered'));
    }
}
