<style>
.alert-danger {
    color: #a94442;
    background-color: #f2dede;
    border-color: #ebccd1;
}
.alert-success {
    color: #3c763d;
    background-color: #dff0d8;
    border-color: #d6e9c6;
}

.alert {
    padding: 5px;
    margin-bottom: 20px;
    border: 1px solid transparent;
    border-radius: 4px;
}
.close:hover, .close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
    filter: alpha(opacity=50);
    opacity: .5;
}
button.close {
    -webkit-appearance: none;
    padding: 0;
    cursor: pointer;
    background: transparent;
    border: 0;
}
.close {
    float: right;
    font-size: 21px;
    font-weight: bold;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    filter: alpha(opacity=20);
    opacity: .2;
}
</style>
<div id="alert" style="margin:10px 10px 0 35px;">
    <div id="alert-flash" class="<?= $class?> alert fade in">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <?= $msg ?>
    </div>
</div>
<script>
    jQuery(function(){
        jQuery('#alert-flash').on('click', '.close', function(){jQuery(this).parent().css('display', 'none');});
    });
</script>