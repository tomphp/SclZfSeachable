<?php

namespace SclZfSearchable\View\Helper;

use SclZfSearchable\SearchInfo\SearchInfoInterface;
use Zend\View\Helper\AbstractHelper;

class SortableColumn extends AbstractHelper
{
    public function __invoke($title, $column, $searchable, $partial, $params = array())
    {
        if (!is_array($params)) {
            $params = array();
        }

        $searchInfo = $searchable->getSearchInfo();

        $params['searchable'] = $searchable;

        $params['title'] = $title;
        $params['column'] = $column;

        $params['active'] = ($column == $searchInfo->getOrderBy());

        $params['ascending'] = ($searchInfo->getOrder() === SearchInfoInterface::SORT_ASC);

        if (is_array($partial)) {
            if (count($partial) != 2) {
                throw new \Exception(
                    'A view partial supplied as an array must contain two values: the filename and its module'
                );
            }

            if ($partial[1] !== null) {
                $partialHelper = $this->view->plugin('partial');
                return $partialHelper($partial[0], $params);
            }

            $partial = $partial[0];
        }

        $partialHelper = $this->view->plugin('partial');
        return $partialHelper($partial, $params);
    }
}
