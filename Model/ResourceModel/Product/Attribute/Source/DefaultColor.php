<?php

namespace Dmatthew\DefaultColor\Model\ResourceModel\Product\Attribute\Source;

use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;

class DefaultColor extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    private $registry;

    private $attributeRepository;

    private $helper;

    private $attributeCollectionFactory;

    public function __construct(
        \Magento\Framework\Registry $registry,
        AttributeRepositoryInterface $attributeRepository,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Attribute\CollectionFactory $attributeCollectionFactory
    ) {
        $this->registry = $registry;
        $this->attributeRepository = $attributeRepository;
        $this->helper = $helper;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    public function getAllOptions()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->getProduct();
        $result = [];
        try {
            $colorAttribute = $this->attributeRepository->get(\Magento\Catalog\Model\Product::ENTITY, 'color');
            if ($product && $product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $colorAttribute = $this->attributeCollectionFactory->create()->setProductFilter($product)->addFieldToFilter('attribute_id', $colorAttribute->getAttributeId())->getFirstItem();
                $attributeOptionsData = $this->getAttributeOptionsData($colorAttribute);
                $result[] = ['value' => '', 'label' => ' '];
                foreach ($attributeOptionsData as $attributeOption) {
                    $result[] = [
                        'value' => $attributeOption['id'], 'label' => $attributeOption['label']
                    ];
                }
            }
            else {
                return $colorAttribute->getOptions();
            }
        }
        catch (\Exception $e) {
            return [['value' => '', 'label' => 'No color attribute available']];
        }

        return $result;
    }

    /**
     * Get current product
     * @return mixed
     */
    private function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @param Attribute $attribute
     * @return array
     */
    protected function getAttributeOptionsData($attribute)
    {
        $attributeOptionsData = [];
        foreach ($attribute->getOptions() as $attributeOption) {
            $optionId = $attributeOption['value_index'];
            $attributeOptionsData[] = [
                'id' => $optionId,
                'label' => $attributeOption['label']
            ];
        }
        return $attributeOptionsData;
    }
}