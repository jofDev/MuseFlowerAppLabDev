<?php
namespace Vnecoms\SmsOoredoo\Model\Config\Source;


class MessageType extends \Magento\Framework\DataObject implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_LATIN = 'Latin';
    const TYPE_ARABIC_WITH_ARABIC_NUMBER = 'ArabicWithArabicNumbers';
    const TYPE_ARABIC_WITH_LATIN_NUMBER = 'ArabicWithLatinNumbers';

    /**
     * Generate list of email templates
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value'=>self::TYPE_LATIN, 'label'=> __('Latin')],
            ['value'=>self::TYPE_ARABIC_WITH_LATIN_NUMBER, 'label'=> __("Arabic with Latin Numbers")],
            ['value'=>self::TYPE_ARABIC_WITH_ARABIC_NUMBER, 'label'=> __('Arabic with Arabic Numbers')],
        ];
        return $options;
    }
}
