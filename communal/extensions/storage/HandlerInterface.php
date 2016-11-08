<?php
namespace communal\extensions\storage;
/**
 * 存储操作操作器
 */
interface HandlerInterface
{
    /**
     * 初始化操作器
     * 
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function init(array $params = array());

    /**
     * 上传文件
     * 
     * @param  [type] $localPath  [description]
     * @return [type]             [description]
     */
    public function upload($localPath);

    /**
     * 修改文件 [相当于删除文件然后重新上传一份]
     * 
     * @param  [type] $localPath  [description]
     * @param  [type] $remotePath [description]
     * @return [type]             [description]
     */
    public function modify($localPath, $remotePath);

    /**
     * 文件附加
     * 
     * @param  [type] $localPath  [description]
     * @param  [type] $remotePath [description]
     * @return [type]             [description]
     */
    public function append($localPath, $remotePath);

    /**
     * 下载文件
     * 
     * @param  [type] $remotePath [description]
     * @return [type]             [description]
     */
    public function download($remotePath);

    /**
     * 删除文件
     * 
     * @param  [type] $remotePath [description]
     * @return [type]             [description]
     */
    public function delete($remotePath);

    /**
     * 获取文件的一些元信息
     * 
     * @param  [type] $remotePath [description]
     * @return [type]             [description]
     */
    public function getMetaInfo($remotePath);

    /**
     * 设置文件元信息
     */
    public function setMetaInfo($remotePath, $data);

}