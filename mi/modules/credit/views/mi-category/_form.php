<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mi\modules\credit\models\MiCategory;
use yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model mi\modules\credit\models\MiCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="mi-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <div class="form-group field-micategory-level has-success">
	<label for="micategory-level" class="control-label">分类</label>
	<select size=1 name="MiCategory[level]" id="micategory-level"  class="form-control">
	<option <?php if($model->attributes['level']== '0'){ echo 'selected';};?> value="0">一级</option>
	<option <?php if($model->attributes['level']== '1'){ echo 'selected';};?> value="1">二级</option>
	<option <?php if($model->attributes['level']== '2'){ echo 'selected';};?> value="2">三级</option>
	</select>
	<div class="help-block"></div>
	</div>
	
	<div class="form-group field-micategory-category_id">
	<label for="micategory-category_id" class="control-label">父类</label>
	<select name="MiCategory[category_id]" id="micategory-category_id"  class="form-control">
	<option value="0">0</option>
	</select>
	<div class="help-block"></div>
	</div>

	<?php $this->beginBlock('test') ?>  
	    var micategory = 0;
	    $(function($) {  
	   	$("#micategory-level").change(function(){
	   		micategory = $("#micategory-level").find("option:selected").val();
	   			 $.ajax({
	 	           url : '<?= Url::to(["/credit/mi-category/search"]) ?>',
	 	           data : {level:micategory},
	 	           type : "get",
	 	           success : function (res){
	 	           	//console.log(res)
	 	           	var res = eval('(' + res + ')'); 
		           	var htmll ='';
        	    	   $.each(res, function(v2, vl) {
        	    		   htmll +='<option value="'+vl.id+'">'+vl.title+'</option>'; 
              	     });                 	
        	    	   $('#micategory-category_id').html(htmll);
	 	       			
	 	           	}
	 	       });
	        //alert(micategory);
	      }); 
	    }); 
	<?php $this->endBlock() ?>  
	<?php $this->registerJs($this->blocks['test'], \yii\web\View::POS_END); ?>  

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
