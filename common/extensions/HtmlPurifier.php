<?php
/**
 * HtmlPurifier 是一个 {@link http://htmlpurifier.org HTML Purifier}的包装.
 *
 * 虽然Yii已经包含一个HtmlPurifier,但是因为Yii版本更新不及时,
 * 而XSS漏洞随着浏览器引擎的更新增多或者减少,所以及时更新HtmlPurifier是
 * 非常有必要的。 目前版本为: 4.5.0
 *
 * 可以作为一个Widget或者一个控制器的过滤器.
 *
 * 性能不是非常好,因为包含了太多的文件了.
 *
 * Usage as a class:
 * <pre>
 * $p = new HtmlPurifier();
 * $p->options = array('URI.AllowedSchemes'=>array(
 *   'http' => true,
 *   'https' => true,
 * ));
 * $text = $p->purify($text);
 * </pre>
 *
 * Usage as validation rule:
 * <pre>
 * array('text','filter','filter'=>array($obj=new CHtmlPurifier(),'purify')),
 * </pre>
 *
 * @author     $Author: wuzhiqiang $
 * @version    $Rev: 1764 $
 * @date       $Date: 2014-03-14 16:18:54 +0800 (周五, 2014-03-14) $
 * @copyright  2003-2013 DFWSGROUP.COM
 * @link       http://tc.dfwsgroup.com/
 */

namespace common\extensions;
require_once __DIR__ . '/HTMLPurifier/HTMLPurifier.auto.php';

class HtmlPurifier extends \COutputProcessor
{
    /**
     * @var mixed array|stdClass|iniFilePath
     * @see http://htmlpurifier.org/live/configdoc/plain.html
     */
    public $options = null;

    /**
     * Processes the captured output.
     * This method purifies the output
     *  using {@link http://htmlpurifier.org HTML Purifier}.
     * @param string $output the captured output to be processed
     */
    public function processOutput($output)
    {
        $output = $this->purify($output);
        parent::processOutput($output);
    }

    /**
     * Purifies the HTML content by removing malicious code.
     * @param string $content the content to be purified.
     * @return string the purified content
     */
    public function purify($content)
    {

        $purifier = new \HTMLPurifier($this->options);
        $purifier->config->set('Cache.SerializerPath', \Yii::app()->getRuntimePath());
        return $purifier->purify($content);
    }
}
