<?php
/**
 * Copyright © 2017 SmallStripes. All rights reserved.
 * See COPYING.txt for license details.
 */


namespace SmallStripes\FeaturedCategories\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\CategoryRepository;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

class FeaturedCategories extends Template implements BlockInterface
{
    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $categoryHelper;
    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;


    /**
     * FeaturedCategories constructor.
     * @param Context $context
     * @param Category $categoryHelper
     * @param CategoryRepository $categoryRepository
     * @param CollectionFactory $categoryCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Category $categoryHelper,
        CategoryRepository $categoryRepository,
        CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryHelper = $categoryHelper;
        $this->categoryRepository = $categoryRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->setTemplate('widget/category.phtml');
    }

    /**
     * @return array
     */
    public function getFeaturedCategories()
    {

        $categories = explode(',', $this->getData('categories'));
        $photos = explode(',', $this->getData('photos'));
        $discounts = explode(',', $this->getData('discounts'));
        $catObjects = array();
        foreach ($categories as $key => $value) {
            $catObject = $this->getCategory($value);
            if ($catObject) {
                $catObject->setFeaturedImage($photos[$key]);
                if (isset($discounts[$key]) && $discounts[$key] != 0) {
                    $catObject->setDiscount($discounts[$key]);
                }
                $catObjects[] = $catObject;
            }
        }
        return $catObjects;
    }

    /**
     * @param $categoryId
     * @return \Magento\Catalog\Api\Data\CategoryInterface|mixed|null
     */
    public function getCategory($categoryId)
    {
        $ids = $this->getCategoriesId();
        if (in_array($categoryId, $ids)) {
            return $this->getCategoryRepository()->get($categoryId);
        }
        return null;
    }

    /**
     * @return \Magento\Catalog\Model\CategoryRepository
     */
    public function getCategoryRepository()
    {
        return $this->categoryRepository;
    }

    /**
     * @return array
     */
    protected function getCategoriesId()
    {
        if (!isset($this->categoryIds)) {
            $collection = $this->categoryCollectionFactory->create();
            $this->categoryIds = $collection->getAllIds();
        }
        return $this->categoryIds;
    }


}
