<?php
namespace Applab\EventsPro\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

class EventRegistration implements ResolverInterface
{
    protected $eventsProRegistrationFactory;
 
    public function __construct(
        \Applab\EventsPro\Model\EventsProRegistrationFactory $eventsProRegistrationFactory
    )
    {
        $this->eventsProRegistrationFactory = $eventsProRegistrationFactory;
    }
    /**
     * @param Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array|\Magento\Framework\GraphQl\Query\Resolver\Value|mixed
     * @throws GraphQlInputException
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null)
    {
        $result = []; 
        if (count($args['input']) < 11)
        {
            throw new GraphQlInputException(__('Required inputs key is missing !'));
        }
        try {

            $eventRegistration = $this->eventsProRegistrationFactory->create();
            $eventRegistration->setEventId($args['input']['event_id']);
            $eventRegistration->setEventName($args['input']['event_name']);
            $eventRegistration->setStoreId($args['input']['store_id']);
            $eventRegistration->setCustomerId($args['input']['customer_id']);
            $eventRegistration->setFirstName($args['input']['first_name']);
            $eventRegistration->setMiddleName($args['input']['middle_name']);
            $eventRegistration->setLastName($args['input']['last_name']);
            $eventRegistration->setAge($args['input']['age']);
            $eventRegistration->setPhone($args['input']['phone']);
            $eventRegistration->setEmail($args['input']['email']);
            $eventRegistration->setGender($args['input']['gender']);
            $eventRegistration->save();

            $result['status']   = __('success');
            $result['message']  = __('Successfully registered!');
            
        } catch (NoSuchEntityException $e) {
            //throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
            $result['status']  = __('failed');
            $result['message'] = __('Registered failed.').' '.$e->getMessage();
        }
        return $result;
    }
}