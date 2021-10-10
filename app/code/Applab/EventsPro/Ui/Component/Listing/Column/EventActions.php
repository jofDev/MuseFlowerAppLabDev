<?php

namespace Applab\EventsPro\Ui\Component\Listing\Column;

class EventActions extends \Magento\Ui\Component\Listing\Columns\Column
{

    const URL_EDIT_PATH     = 'eventspro/index/edit';
    const URL_DELETE_PATH   = 'eventspro/index/delete';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @param \Magento\Framework\UrlInterface                              $urlBuilder
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory
     * @param array                                                        $components
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(static::URL_EDIT_PATH,['id' => $item['id'],]),
                            'label' => __('Edit'),
                        ],
                        'delete' => [ 
                            'href' => $this->urlBuilder->getUrl(static::URL_DELETE_PATH,['id' => $item['id'],]), 
                            'label' => __('Delete'), 
                            'confirm' => ['title' => 'Delete Event', 'message' => 'Do you realy want to delete this event ?'] 
                        ],
                    ];
                }
            }
        }
        return $dataSource;
    }
}