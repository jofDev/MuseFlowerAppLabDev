<?php
namespace Applab\EventsPro\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;

class EventData implements ResolverInterface
{
    protected $eventsProDataFactory;
 
    public function __construct(
        \Applab\EventsPro\Model\EventsProDataFactory $eventsProDataFactory
        )
    {
        $this->eventsProDataFactory = $eventsProDataFactory;
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
        $result = '';
        if (!isset($args['id']))
        {
            throw new GraphQlInputException(__('Id required. Set 0 to get all events!'));
        }
        try {
            $collection = $this->eventsProDataFactory->create()->getCollection();
            $collection->addFieldToFilter('status',1);
            if($args['id'] != 0){
                $collection->addFieldToFilter('id',$args['id'])->getFirstItem();    
            }
            $result = $collection->getData();
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        return $result;
    }
}