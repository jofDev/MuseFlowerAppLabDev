<?php

namespace Applab\SwatchData\DataProvider\Product\LayeredNavigation;

use Magento\Eav\Model\Config;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;
use Magento\Swatches\Helper\Data;
use Psr\Log\LoggerInterface;
use Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\LayerBuilder;
use Magento\CatalogGraphQl\DataProvider\Product\LayeredNavigation\LayerBuilderInterface;
use Magento\Framework\Exception\LocalizedException;

class DataProviderAggregationPlugin extends LayerBuilder implements LayerBuilderInterface
{
    private $builders;
    protected $_logger;
    protected $eavConfig;
    private $swatchHelper;
    private $renderLayered;

    public function __construct(
        array $builders,
        LoggerInterface $logger,
        Config $eavConfig,
        Data $swatchHelper,
        RenderLayered $renderLayered
    )
    {
        $this->builders = $builders;
        $this->_logger = $logger;
        $this->eavConfig = $eavConfig;
        $this->swatchHelper = $swatchHelper;
        $this->renderLayered = $renderLayered;
    }

    public function build(
        AggregationInterface $aggregation,
        ?int $storeId
    ): array
    {
        $layers = [];
        foreach ($this->builders as $builder)
        {
            $layers[] = $builder->build($aggregation, $storeId);
        }
        $layers = \array_merge(...$layers);
        foreach ($layers as $key => $value) 
        {
            $attribute = $this->eavConfig->getAttribute('catalog_product', $layers[$key]['attribute_code']);
            if ($this->swatchHelper->isSwatchAttribute($attribute))
            {
                for ($i = 0; $i < count($layers[$key]['options']); $i++)
                {
                    $hashcodeData = $this->swatchHelper->getSwatchesByOptionsId([$layers[$key]['options'][$i]['value']]);
                    $typeName = $this->getswatchType($hashcodeData[$layers[$key]['options'][$i]['value']]['type']);

                    $temp = [
                        'type' => $typeName,
                        'value' => $hashcodeData[$layers[$key]['options'][$i]['value']]['value']
                    ];

                    $layers[$key]['options'][$i]['swatch_data'] = $temp;
                }
            }
        }

        return \array_filter($layers);
    }

    public function getswatchType($valueType)
    {
        switch ($valueType)
        {
            case 0:
                return 'TextSwatchData';
            case 1:
                return 'ColorSwatchData';
            case 2:
                return 'ImageSwatchData';
            break;
        }
    }
}