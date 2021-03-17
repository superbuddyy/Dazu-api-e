<?php

declare(strict_types=1);

namespace App\Managers;

use App\Models\Category as CategoryModel;

class CategoryManager
{
    /**
     * @param CategoryModel|null $parent
     * @param string $hiddenAttributes
     * @param string $visibleAttributes
     * @param bool $removeEmptyChildren
     * @return mixed
     */
    public function getCategoryTree(
        CategoryModel $parent = null,
        $hiddenAttributes = '',
        $visibleAttributes = 'children',
        $removeEmptyChildren = false
    ) {
        if ($parent === null) {
            $result = CategoryModel::where('is_active', true)
                ->defaultOrder()
                ->get()
                ->makeHidden($hiddenAttributes)
                ->makeVisible($visibleAttributes)
                ->toTree();
        } else {
            $result = $this->getCategoryWithDescendants($parent)
                ->makeHidden($hiddenAttributes)
                ->makeVisible($visibleAttributes)
                ->toTree()
                ->first();
        }

        if ($removeEmptyChildren === true) {
            $remove_empty_children = function ($elemente) use (&$remove_empty_children) {
                foreach ($elemente as $element) {
                    if($element->children->isNotEmpty()) {
                        $remove_empty_children($element->children);
                    } else {
                        $element->unsetRelation('children');
                    }
                }
            };
            $remove_empty_children($result);
        }

        return $result;
    }
}
