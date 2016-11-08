<?php
use yii\helpers\Html;
use mi\modules\credit\widgets\CreditPager;

//echo $pages->getPage();
$nav = [];
$page = $pages->page + 1;
$nav[0] = '<li class="num_cover">' . $page . '</li>';
        
for($left = $page - 1,$right = $page + 1; ($left >= 1 || $right <= $pages->pageCount) && count($nav) < 5;) {
    
    if($left >= 1) {
        array_unshift($nav,'<li data-page="' . $left . '">' . $left . '</li>');
        $left -= 1;
    }
    
    if($right <= $pages->pageCount) {
        array_push($nav,'<li data-page="' . $right . '">' . $right . '</li>');
        $right += 1;
	}
}

$pagination = implode('',$nav);
?>
<!--页码开始-->

<?= CreditPager::widget(['pagination' => $pages])?>



<!--页码结束-->

<!-- <div class="pages">
    <div class="page_left">
        <ul>
            <span>共<?= $pages->pageCount ?>页</span>
            <li data-page="1"><?= Html::img('@credit_s/images/code_left1.png') ?></li>
            <li data-page="<?= $page - 1 ?>"><?= Html::img('@credit_s/images/code_left2.png') ?></li>
        </ul>
    </div>
    <div class="page_num">
    	<?= $pagination ?>
    </div>
    <div class="page_right">
        <li data-page="<?= $page + 1 ?>"><?= Html::img('@credit_s/images/code_right1.png') ?></li>
        <li data-page="<?= $pages->pageCount ?>"><?= Html::img('@credit_s/images/code_right2.png') ?></li>
        <span>跳转到:</span>
        <input type="text" id="go-page" class="go_num"/>
        <input type="button" class="go"/>
    </div>
</div> -->

<script>
$(function(){
    function pageselectCallback(page_id, jq) {
        alert(page_id); //回调函数，进一步使用请参阅说明文档
    }
    //$("#Pagination").pagination(<?= $pages->defaultPageSize?>);

    $('.searchPage').on('click', '.go', function(){
        var page = $('#go-page-num').val() ? $('#go-page-num').val() : 1; 

        $.ajax({
            url : '<?= $submitUrl ?>',
            data : $('form').serialize() + '&page=' + page,
            type : "get",
            success : function (res){
                $('#fill-content').html(res);
            }   
        });
    });

});

</script>