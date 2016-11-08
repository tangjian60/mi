<?php
namespace communal\helpers;

use \Yii;
use yii\db\Exception;
use yii\data\Pagination;

class PaginationHelper
{
	//分页
	public static function paging($query, array $config = [])
	{
		$countQuery = clone $query;
		$pages = new Pagination([
				'totalCount' => $countQuery->count() 
		]);
		if (isset($config['pageSize']))
			$pages->setPageSize($config['pageSize'], true);
		
		$model = $query->offset($pages->offset)->limit($pages->limit);

		if (isset($config['order']))
			$model = $model->orderBy($config['order']);

		
		$model = $model->all();

		$modelKey = isset($config['modelKey']) ? $config['modelKey'] : 'model';
		$pagesKey = isset($config['pagesKey']) ? $config['pagesKey'] : 'pages';
		
		return [$modelKey => $model, $pagesKey => $pages];
	}
}