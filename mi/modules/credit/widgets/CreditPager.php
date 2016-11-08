<?php

namespace vecrm\modules\credit\widgets;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\base\Widget;
use yii\data\Pagination;

class CreditPager extends \yii\widgets\LinkPager{
	/**
     * @var string the CSS class for the active (currently selected) page button.
     */
   public $activePageCssClass = 'current';
   /**
     * @var string|boolean the label for the "next" page button. Note that this will NOT be HTML-encoded.
     * If this property is false, the "next" page button will not be displayed.
     */
    public $nextPageLabel = '<i></i>下一页';
    /**
     * @var string|boolean the text label for the previous page button. Note that this will NOT be HTML-encoded.
     * If this property is false, the "previous" page button will not be displayed.
     */
    public $prevPageLabel = '<i></i>上一页';
    /**
     * @var integer maximum number of page buttons that can be displayed. Defaults to 10.
     */
    public $maxButtonCount = 5;

    /**
     * set pages postion
     * defaut pages, if top, set per page  
     */
    public $position = 'default';

    public function run()
    {
        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }

        if($this->position == 'top'){
        	$result = $this->renderPerpage();
        }else{
        	$result = $this->renderPageButtons();
        }
        
        echo $result;
    }

    /**
     * Renders the per page Set
     * @return string the rendering result
     */
    protected function renderPerpage(){
    	
    	$leftImgSrc = Url::to('@credit_s/images/code_left2.png', true);
    	$rightImgSrc = Url::to('@credit_s/images/code_right1.png', true);
    	$currentPage = $this->pagination->getPage() + 1;
    	$perPage = $this->pagination->getPageSize();
    	$pageCount = $this->pagination->getPageCount();
    	$totalCount = $this->pagination->totalCount;

    	$perLi = [
    		Html::tag('li', '10', ['class' => 'page10' . ($perPage == 10 ? ' pageC_cove' : '')]),
    		Html::tag('li', '20', ['class' => 'page20' . ($perPage == 20 ? ' pageC_cove' : '')]),
    		Html::tag('li', '50', ['class' => 'page50' . ($perPage == 50 ? ' pageC_cove' : '')])
    	];
    	$li = implode("\n", $perLi);

    	$html = <<<EOF
<div class="page_choise">
    <span>每页显示：</span>
    <ul>
        {$li}
    </ul>
    <span>共{$totalCount}条</span>
    <span class="pageC_left" ><img src="{$leftImgSrc}" class="page" data-action="-" /></span>
    <span>{$currentPage}/{$pageCount}页</span>
    <span class="pageC_right" ><img src="{$rightImgSrc}" class="page" data-action="+" /></span>
</div>
EOF;
		return $html . $this->renderTopJs();
    }

    protected function renderTopJs(){
    	$url = Url::base();
    	$currentPage = $this->pagination->getPage() + 1;

    	$js = <<<EOF
<script>
$(function(){
    $('.page_choise').on('click', '.page10, .page20, .page50, .page', function(){
    	var page = 0;
    	if($(this).is('li')){
    		$(this).siblings().each(function(){
	            $(this).removeClass('pageC_cove');
	        });
	        $(this).addClass('pageC_cove');
    	}else{
    		var action  = $(this).data('action');
    		eval('page = {$currentPage}' + action + '1');
    	}
    	
        var per = $('.pageC_cove').html();
        

        $.ajax({
            url : "{$url}",
            data : $('.search-condition').serialize() + '&page=' + page + '&per-page=' + per,
            type : "get",
            success : function (res){
                $('#fill-content').html(res);
            }   
        });
    });
});
</script>
EOF;
		return $js;
    }


    /**
     * Renders the page buttons.
     * @return string the rendering result
     */
    protected function renderPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }

        $buttons = [];
        $currentPage = $this->pagination->getPage();

        // first page
        $firstPageLabel = $this->firstPageLabel === true ? '1' : $this->firstPageLabel;
        if ($firstPageLabel !== false) {
            $buttons[] = $this->renderPageButton($firstPageLabel, 0, $this->firstPageCssClass, $currentPage <= 0, false);
        }

        // prev page
        if ($this->prevPageLabel !== false) {
            if (($page = $currentPage - 1) < 0) {
                $page = 0;
            }
            $buttons[] = $this->renderPageButton($this->prevPageLabel, $page, $this->prevPageCssClass, $currentPage <= 0, false);
        }

        // internal pages
        list($beginPage, $endPage) = $this->getPageRange();
        for ($i = $beginPage; $i <= $endPage; ++$i) {
            $buttons[] = $this->renderPageButton($i + 1, $i, null, false, $i == $currentPage);
        }

        // next page
        if ($this->nextPageLabel !== false) {
            if (($page = $currentPage + 1) >= $pageCount - 1) {
                $page = $pageCount - 1;
            }
            $buttons[] = $this->renderPageButton($this->nextPageLabel, $page, $this->nextPageCssClass, $currentPage >= $pageCount - 1, false);
        }

        // last page
        $lastPageLabel = $this->lastPageLabel === true ? $pageCount : $this->lastPageLabel;
        if ($lastPageLabel !== false) {
            $buttons[] = $this->renderPageButton($lastPageLabel, $pageCount - 1, $this->lastPageCssClass, $currentPage >= $pageCount - 1, false);
        }

        $pageList = Html::tag('div', implode("\n", $buttons), $this->options);
        $search = Html::tag('div', '<span class="page-go">跳转<input type="text" id="go-page-num">页</span><a href="javascript:;" class="page-btn">GO</a>',['class'=>'searchPage']);
        $js = $this->renderJs();

        return Html::tag('div', Html::tag('div', $pageList, ['id' => 'Pagination']) . $search, ['class'=> 'pages']) . $js;
    }
   
   /**
   	* Renders a page button.  Override
   	* 
	* @param string $label the text label for the button
	* @param integer $page the page number
	* @param string $class the CSS class for the page button.
	* @param boolean $disabled whether this page button is disabled
	* @param boolean $active whether this page button is active
	* @return string the rendering result
   	*/
   	protected function renderPageButton($label, $page, $class, $disabled, $active){

   		$options = ['class' => $class === '' ? null : $class];
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }

        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);

            return Html::a($label, 'javascript:;', $options);
        }
        $options['data-page'] = $page;

        return Html::a($label, 'javascript:;', $options);
   	}

   	/**
   	 * js
   	 */
   	public function renderJs(){
   		$url = Url::base();
   		$js = <<<EOF
<script>
$(function(){
    $('.pages').on('click', 'a', function(){
        var page = $('#go-page-num').val();
        if(! page){
            page = $(this).data('page') + 1;
        }console.log('{$url}')

        var per = $('.pageC_cove').html();

        $.ajax({
            url : "{$url}",
            data : $('.search-condition').serialize() + '&page=' + page + '&per-page=' + per,
            type : "get",
            success : function (res){
                $('#fill-content').html(res);
            }   
        });
    });

});

</script>
EOF;
		return $js;
   	}
}
