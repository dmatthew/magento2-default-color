<?php

namespace Dmatthew\DefaultColor\Block\Plugin\Product\View\Type;

use Magento\Catalog\Model\Product;
use Magento\Swatches\Helper\Data as SwatchHelper;

class Configurable
{
    protected $swatchHelper;
    protected $serializer;

    public function __construct(
        SwatchHelper $swatchHelper,
        \Magento\Framework\Serialize\Serializer\Json $serializer
    ) {
        $this->swatchHelper = $swatchHelper;
        $this->serializer = $serializer;
    }

    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        $config
    ) {
        $config = $this->serializer->unserialize($config);
        if (!empty($config['preSelectedGallery'])) return $this->serializer->serialize($config);
        $preSelectedGallery = $this->getPreSelectedGallery(
            $subject->getProduct()
        );
        if ($preSelectedGallery) {
            $config['preSelectedGallery'] = $preSelectedGallery;
            $config['default_color_value'] = $subject->getProduct()->getData('default_color_value');
        }
        return $this->serializer->serialize($config);
    }

    private function getPreSelectedGallery(Product $configurableProduct)
    {
        $product = $this->swatchHelper->loadVariationByFallback(
            $configurableProduct,
            ['color' => $configurableProduct->getData('default_color_value')]// OR: $configurableProduct->getCustomAttribute('default_color_value')
        );
        return $product ? $this->swatchHelper->getProductMediaGallery($product) : [];
    }
}