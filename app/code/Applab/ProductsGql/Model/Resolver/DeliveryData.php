<?php
namespace Applab\ProductsGql\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;


class DeliveryData implements ResolverInterface
{
   
    public function __construct(
        \Magento\Catalog\Model\Product\Attribute\Repository $attributeRepository
    )
    {
        $this->attributeRepository = $attributeRepository;
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
        if (!isset($args['store_id']))
        {
            throw new GraphQlInputException(__('Id required. Stroe Id is required!'));
        }
        try {
            $delivery_date_data = $delivery_day_data = [];
            $delivery_date = $this->attributeRepository->get('delivery_date')->setStoreId($args['store_id']); 
            if ($delivery_date && $delivery_date->usesSource()) {
                $options_date  = $delivery_date->getSource()->getAllOptions();      
                foreach ($options_date as $key => $option) {
                    if($option['value']){
                        $delivery_date_data[$key]['id']     = $option['value'];
                        $delivery_date_data[$key]['value']  = $option['label'];
                    }        
                } 
            }
            $delivery_time = $this->attributeRepository->get('delivery_time')->setStoreId($args['store_id']); 
            if ($delivery_time && $delivery_time->usesSource()) {
                $options  = $delivery_time->getSource()->getAllOptions();      
                foreach ($options as $key => $option) {
                    if($option['value']){
                        $delivery_day_data[$key]['id']     = $option['value'];
                        $delivery_day_data[$key]['value']  = $option['label'];
                    }        
                } 
            }

            return array_merge($delivery_date_data,$delivery_day_data);
       
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        }
        exit('returned false');
        return fasle;
    }
}