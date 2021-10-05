<?php
namespace Vnecoms\Sms\Controller\Adminhtml\Blocklist;

abstract class Action extends \Vnecoms\Sms\Controller\Adminhtml\Action
{
    /**
     * Is access to section allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vnecoms_Sms::block_list') && parent::_isAllowed();
    }
}
