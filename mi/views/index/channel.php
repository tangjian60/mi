	<?php 
use yii\helpers\Url;
use yii\helpers\Html;
use mi\assets\AppAsset;

use yii\grid\GridView;
use yii\widgets\LinkPager;
$this->registerCssFile('@credit_s/css/cnzz2_files/bootstrapl.css');
$this->registerCssFile('@credit_s/css/cnzz2_files/commonl.css');

?>
        </div>
<div class="page-wrapper" style="min-height: 273px;">
	<div class="table-responsive">
			<table class="table table-striped table-bordered table-hover">
				<thead>
					<tr class="success">
						<th>序号</th>
						<th>日期</th>
						<th>产品</th>
                        <th>渠道</th>
                        <th>有效激活</th>
                        <th>备注</th>
                      	</tr>
				</thead>
				<tbody>
					<?php 
					$LIM =1;
					if($pagee>0){
						$LIM =10*$pagee+1;
					}
						foreach ($models as $key=>$valu):?>
                    <tr>
                        <td><?=$key+$LIM?></td><!--1-->
                        <td><?=substr($valu['addtime'],0,10)?></td><!--2-->
                        <td><?=$valu['name']?></td><!--3-->
                         <td><?=$valu['browse']?></td>
                         <td><?=$valu['visitors']?></td><!--6-->
                         <td> </td><!--7-->
                         </tr>
                         <?php endforeach;?>
                        <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>本页汇总</td>
                      <td><?=$scount?></td>
                      <td></td>
                  </tr>
                  <tr>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>全部汇总</td>
                      <td><?=$micout['visitors']?></td>
                      <td></td>
                  </tr>
                 </tbody>
			</table>
		
		</div>
		 <div class="paginate">
		 <div class="row">
		 <div class="col-sm-6">当前第<?=$pagee+1?>页，总<?=$pagenum?>页</div>
		 <div class="col-sm-6">
		 <nav>
		<?php 	echo LinkPager::widget([
    			'pagination' => $pages,
				
				'nextPageLabel' => '下一页',
				'prevPageLabel' => '上一页',
	])?>
		 </nav></div></div></div>
		 </div>
		 