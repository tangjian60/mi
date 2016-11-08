<?php
/**
 * SexyTemplate
 * a mini php template engine. just like artTemplate(JavaScript).
 *
 * @author qpwoeiru96 <qpwoeiru96@gmail.com>
 * @version 0.0.5
 */
 
/**
 * @0.0.3: 支持模板缓存
 *
 * @0.0.4：支持Layou布局
 *
 * @0.0.5: 支持自定义起始闭合标签
 *
 * @0.0.6: Wrapper 更友好的错误提示
 */

namespace SexyTemplate
{
    const VERSION = '0.0.6';

    /**
     * 函数包装头部 用于内部变量支持 以及初始化返回字符串
     */
    const T_FUNCTION_HEAD = 'extract($vars);unset($vars);$_ = "";';

    /**
     * 函数包装尾部
     */
    const T_FUNCTION_END = 'return $_;';

    /**
     * 语句万能结尾
     */
    const T_STATEMENT_END =  ";";

    /**
     * 字符串收集
     */
    const T_COLLECT = '$_ .= ';

    class Compiler
    {
        /**
         * 语法分析钩子
         * @var array
         */
        protected $_statementParser = [];

        /**
         * 起始标签
         *
         * @var string
         */
        public $openTag = '<%';

        /**
         * 闭合标签
         *
         * @var string
         */
        public $closeTag = '%>';
        /**
         * 是否开启调试模式
         * @var bool
         */
        public $debug = false;

        public function __construct()
        {
            $this->init();
        }

        public function init() { }

        /**
         * 获取语法映射列表
         *
         * @return array
         */
        public static function getSyntaxMap()
        {
            $varRe = '\$[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
            $varMixRe = '\$[a-zA-Z_\x7f-\xff][^ ]*';
            $stringRe = '(?<char>[\'"])([^\k<char>]+)\k<char>';

            return [
                /**
                 * 用来支持 == 跟 = 输出/转义输出
                 */
                "Print" => ['#^(?<mark>={1,2}) *(?<statement>.*)#i', function ($mark, $statement) {
                    return T_COLLECT . ($mark === '==' ? $statement : "htmlspecialchars({$statement})");
                }],

                /**
                 * 支持 ?= 和 ?== 语法
                 */
                "ConditionPrint" => ["#^\?(?<mark>={1,2}) *(?<first>{$varMixRe}?) *: *(?<second>{$varMixRe}|{$stringRe})#", function ($mark, $first, $second) {
                    return T_COLLECT . " ((isset({$first}) && empty({$first})) ? " . ($mark === "=="
                        ? "{$first} : {$second});"
                        : "htmlspecialchars({$first}) : htmlspecialchars({$second}));");
                }],

                /**
                 * 支持 loop 语法
                 */
                "Loop" => ["#^/loop$|^loop {1,}(?<array>{$varMixRe}) {1,}(?<key>{$varRe}) *(?<val>{$varRe})?#", function ($array, $key, $val) {
                    if (!$array) return "endforeach;";
                    return $val ? "foreach({$array} as {$key} => {$val}):" : "foreach({$array} as {$key}):";
                }],

                /**
                 * 支持 if else elseif 语法
                 */
                "If" => ["#^(?<endif>/if)$|^(?<else>else)$|^(?<elseif>else)?if {1,}(?<statement>.+)#", function ($endif, $else, $elseif, $statement) {
                    if($endif) return 'endif;';
                    if($else) return 'else:';
                    if($elseif) return "elseif ({$statement}):";
                    return "if ({$statement}):";
                }]
            ];
        }

        /**
         * 魔术方法
         *
         * @param string $name
         * @param array $args
         * @return bool|void|mixed
         * @throws TargetSyntaxNotFoundException|\BadMethodCallException
         */
        public function __call($name, $args)
        {
            if(preg_match('#enable(?<syntaxName>[a-zA-z0-9]+)Syntax#', $name, $matches)) {
                $syntaxName = $matches['syntaxName'];
                $syntaxList = self::getSyntaxMap();

                if($syntaxName === "All") {
                    foreach($syntaxList as $syntax) {
                        $this->insertParser($syntax[0], $syntax[1]);
                    }
                    return;
                } elseif(isset($syntaxList[$syntaxName])) {
                    $this->insertParser($syntaxList[$syntaxName][0], $syntaxList[$syntaxName][1]);
                    return;
                } else {
                    throw new TargetSyntaxNotFoundException($syntaxName);
                }
            }

            throw new \BadMethodCallException();
        }

        /**
         * 插入语法分析钩子
         *
         * @param string $re 正则表达式
         * @param callable $callback 调用函数
         * @param bool $insertAtEnd 是否插入在最后面
         */
        public function insertParser($re, callable $callback, $insertAtEnd = false)
        {
            if ($insertAtEnd)
                array_unshift($this->_statementParser, [$re, $callback]);
            else
                array_push($this->_statementParser, [$re, $callback]);
        }

        /**
         * 核心方法 用于分析语句
         *
         * @param  string $statement
         * @return string
         */
        protected function parseStatement($statement)
        {
            foreach ($this->_statementParser as $parser) {

                list($re, $callback) = $parser;

                if (preg_match($re, $statement, $matches)) {

                    $object = null;

                    //@todo: 处理静态方法
                    if (is_array($callback)) {
                        list($object, $method) = $callback;
                        $ref = new \ReflectionMethod($object, $method);
                    } else {
                        $ref = new \ReflectionFunction($callback);
                    }

                    $params = [];

                    foreach ($ref->getParameters() as $param) {
                        if ($param->name === 'matches') {
                            $params[] = $matches;
                        } else {
                            $params[] = isset($matches[$param->name]) ? $matches[$param->name] : null;
                        }
                    }

                    if (is_array($callback)) {
                        return $ref->invokeArgs($object, $params) . T_STATEMENT_END;
                    } else {
                        return $ref->invokeArgs($params) . T_STATEMENT_END;
                    }
                }
            }

            return $statement . T_STATEMENT_END;
        }

        /**
         * 转义字符串
         *
         * @param  string $str 需要进行转义的字符串
         * @return string
         */
        protected function escapeNormalString($str)
        {
            return str_replace("'", "\\'", str_replace('\\', '\\\\', $str));
        }

        /**
         * 收集正常输出的文本
         *
         * @param  string $string 要输出的文本
         * @return string
         */
        protected function collectString($string)
        {
            if ($string === '') return '';
            return T_COLLECT . "'" . $this->escapeNormalString($string) . "'" . T_STATEMENT_END;
        }

        /**
         * 编译模板
         *
         * @param string $template
         * @return bool|Wrapper
         */
        public function compile($template)
        {
            $templateArr = explode($this->openTag, $template);

            $output = [];
            foreach($templateArr as $k => $v) {
                if($k === 0) {
                    $output[] = $this->collectString($v);
                    continue;
                }

                $pos = stripos($v, $this->closeTag);
                if($pos === false) {
                    if($v !== '') $output[] = $this->collectString($v);
                } else {
                    $statement = substr($v, 0, $pos);
                    if($statement !== '')
                        $output[] = $this->parseStatement($statement);

                    $string = substr($v, $pos + 2);

                    if($string !== '')
                        $output[] = $this->collectString($string);
                }
            }

            $expression = implode("\n", $output);

            if ($this->debug) {
                $func = @create_function('', $expression);
                if (!$func) {
                    $error = error_get_last();
                    ErrorPrinter::printError($error, $template, $expression);
                    return false;
                }
            }

            return new Wrapper($expression, $this->debug);
        }
    }

    class FileCompiler extends Compiler
    {

        /**
         * 模板文件存放位置
         *
         * @var string
         */
        public $templateDir;

        /**
         * 模板文件扩展名
         *
         * @var string
         */
        public $templateExt = '.html';

        /**
         * 是否使用布局
         *
         * @var string
         */
        public $layout;

        protected $file;

        public function init()
        {
            $this->insertParser('#^include +([\'"])(?<file>[^\1]+)\1$#', array($this, 'parseInclude'));
            $this->insertParser('#^CONTENT$#', array($this, 'parseLayoutContent'));
        }

        /**
         * 分析引用数据
         *
         * @param string $file 文件名称
         * @param array $matches 匹配到的数据
         * @return bool|\SexyTemplate\Wrapper
         * @throws FileNotFoundException
         */
        public function parseInclude($file, $matches)
        {
            $filePath = $this->templateDir . DIRECTORY_SEPARATOR . $file . $this->templateExt;
            $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

            if(!file_exists($filePath)) {
                $position = "<%$matches[0]%>";
                throw new FileNotFoundException("template file '{$filePath}' is not found, pleast check at {$position}.");
            }

            return $this->compile(file_get_contents($filePath));
        }

        public function parseLayoutContent()
        {
            return $this->_compileFile($this->file);
        }

        protected function _compileFile($file)
        {
            $filePath = $this->templateDir . DIRECTORY_SEPARATOR . $file . $this->templateExt;
            $filePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $filePath);

            if(!file_exists($filePath)) {
                throw new FileNotFoundException("template file '{$filePath}' is not found.");
            }

            return $this->compile(file_get_contents($filePath));
        }

        /**
         * 编译文件
         *
         * @param string $file 文件名称
         * @return bool|\SexyTemplate\Wrapper
         * @throws FileNotFoundException
         */
        public function compileFile($file)
        {
            $this->file = $file;

            return $this->layout ? $this->_compileFile($this->layout) : $this->_compileFile($this->file);
        }
    }

    /**
     * 缓存接口
     *
     * @package SexyTemplate
     */
    interface TemplateCacheable
    {
        /**
         * 写入缓存
         *
         * @param string $key
         * @param Wrapper $wrapper
         * @return void
         */
        public function write($key, Wrapper $wrapper);

        /**
         * 读出缓存
         *
         * @param string $key
         * @return Wrapper|null
         */
        public function read($key);

    }

    /**
     * 缓存编译器 让模板引擎支持缓存 避免重复编译
     *
     * @package SexyTemplate
     */
    class FileCacheCompiler extends FileCompiler implements TemplateCacheable
    {
        /**
         * 存储缓存的文件夹
         *
         * @var string
         */
        public $cacheDir;


        public function read($key)
        {
            $path = $this->cacheDir . DIRECTORY_SEPARATOR . md5($key) . '.php';

            return file_exists($path) ? new Wrapper(file_get_contents($path), $this->debug) : null;
        }

        public function write($key, Wrapper $wrapper)
        {
            $path = $this->cacheDir . DIRECTORY_SEPARATOR . md5($key) . '.php';
            return file_put_contents($path, (string)$wrapper);
        }

        public function compileFile($file)
        {
            $this->file = $file;

            if(!($wrapper = $this->read($file))) {
                $wrapper = $this->layout ? $this->_compileFile($this->layout) : $this->_compileFile($this->file);
                $this->write($file, $wrapper);
            }

            return $wrapper;
        }
    }


    class FileNotFoundException extends \Exception {}

    class TargetSyntaxNotFoundException extends \Exception {}

    class TemplateCompileException extends \Exception {}

    class Wrapper
    {
        /**
         * @var \Object
         */
        protected $_ref;

        /**
         * @var string
         */
        protected $_expression = '';

        /**
         * @var boolean
         */
        protected $_debug;

        public function __construct($expression, $debug = false)
        {
            $this->_expression = $expression;
            $this->_debug = $debug;
        }

        /**
         * 绑定环境
         *
         * @param \Object $obj
         * @return $this
         */
        public function bind($obj)
        {
            $this->_ref = $obj;
            return $this;
        }

        /**
         * 渲染模板
         *
         * @param array $data
         * @return string
         * @throws TemplateCompileException
         */
        public function render(array $data = array())
        {
            $func = @create_function('$vars, $self', T_FUNCTION_HEAD . $this->_expression . T_FUNCTION_END);

            if(!$func)
                throw new TemplateCompileException();

            $refFunc = new \ReflectionFunction($func);

            if($this->_debug) {
                $expression = $this->_expression;
                set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) use ($expression) {

                    $lines = explode("\n", $expression);
                    $start = max(0, $errline - 5);
                    $end = min(count($lines) - 1, $start + 8);

                    $expression = "";
                    for($i = $start; $i <= $end; $i++) {
                        $expression .= htmlspecialchars($lines[$i]) . "\n";
                    }

                    print <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SexyTemplate Error</title>
<style>/*! normalize.css v3.0.2 | MIT License | git.io/normalize */html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}article,aside,details,figcaption,figure,footer,header,hgroup,main,menu,nav,section,summary{display:block}audio,canvas,progress,video{display:inline-block;vertical-align:baseline}audio:not([controls]){display:none;height:0}[hidden],template{display:none}a{background-color:transparent}a:active,a:hover{outline:0}abbr[title]{border-bottom:1px dotted}b,strong{font-weight:bold}dfn{font-style:italic}h1{font-size:2em;margin:.67em 0}mark{background:#ff0;color:#000}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sup{top:-0.5em}sub{bottom:-0.25em}img{border:0}svg:not(:root){overflow:hidden}figure{margin:1em 40px}hr{-moz-box-sizing:content-box;box-sizing:content-box;height:0}pre{overflow:auto}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}button,input,optgroup,select,textarea{color:inherit;font:inherit;margin:0}button{overflow:visible}button,select{text-transform:none}button,html input[type="button"],input[type="reset"],input[type="submit"]{-webkit-appearance:button;cursor:pointer}button[disabled],html input[disabled]{cursor:default}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}input{line-height:normal}input[type="checkbox"],input[type="radio"]{box-sizing:border-box;padding:0}input[type="number"]::-webkit-inner-spin-button,input[type="number"]::-webkit-outer-spin-button{height:auto}input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}fieldset{border:1px solid #c0c0c0;margin:0 2px;padding:.35em .625em .75em}legend{border:0;padding:0}textarea{overflow:auto}optgroup{font-weight:bold}table{border-collapse:collapse;border-spacing:0}td,th{padding:0}
.prettyprint{background:black;font-family:Menlo,'Bitstream Vera Sans Mono','DejaVu Sans Mono',Monaco,Consolas,monospace;font-size:12px;line-height:1.5;border:1px solid #ccc;padding:10px}.pln{color:white}@media screen{.str{color:#6f0}.kwd{color:#f60}.com{color:#93c}.typ{color:#458}.lit{color:#458}.pun{color:white}.opn{color:white}.clo{color:white}.tag{color:white}.atn{color:#9c9}.atv{color:#6f0}.dec{color:white}.var{color:white}.fun{color:#fc0}}@media print,projection{.str{color:#060}.kwd{color:#006;font-weight:bold}.com{color:#600;font-style:italic}.typ{color:#404;font-weight:bold}.lit{color:#044}.pun,.opn,.clo{color:#440}.tag{color:#006;font-weight:bold}.atn{color:#404}.atv{color:#060}}ol.linenums{margin-top:0;margin-bottom:0;color:white}a{color:gray;text-decoration:none}.container{width:1120px;margin:0 auto}body{font-family:PingHei,'Lucida Grande','Lucida Sans Unicode',Helvetica,Arial,Verdana,sans-serif}
</style>
</head>
<body>
    <div class="container">
        <h1>SexyTemplate Error</h1>
        <p>错误信息: {$errstr}<p>
        <p>错误位置: 第{$errline}行<p>
        <div class="code-area">
            <p>PHP代码:</p>
            <div class="right-side">
                <pre class="code-block prettyprint linenums:1 lang-php">{$expression}</pre>
            </div>
        </div>
    </div>
    <div style="text-align: center; color: gray;">author: <a href="http://blog.sou.la/">qpwoeiru96</a></div>
    <script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.js"></script>
    <script>
    window.onload = prettyPrint;
    </script>
</body>
</html>
EOT;
                    exit(-1);

                }, E_ALL);

                $html = $refFunc->invoke($data, $this->_ref);
                restore_error_handler();
                return $html;

            } else {
                return $refFunc->invoke($data, $this->_ref);
            }
        }

        public function __invoke(array $data = array())
        {
            return $this->render($data);
        }

        public function __toString()
        {
            return $this->_expression;
        }
    }

    class ErrorPrinter
    {
        static function printError($error, $template, $expression)
        {
            $error['message'] = htmlspecialchars($error['message']);
            $template = htmlspecialchars($template);
            $expression = htmlspecialchars($expression);
            print <<<EOT
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SexyTemplate Error</title>
<style>/*! normalize.css v3.0.2 | MIT License | git.io/normalize */html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}article,aside,details,figcaption,figure,footer,header,hgroup,main,menu,nav,section,summary{display:block}audio,canvas,progress,video{display:inline-block;vertical-align:baseline}audio:not([controls]){display:none;height:0}[hidden],template{display:none}a{background-color:transparent}a:active,a:hover{outline:0}abbr[title]{border-bottom:1px dotted}b,strong{font-weight:bold}dfn{font-style:italic}h1{font-size:2em;margin:.67em 0}mark{background:#ff0;color:#000}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sup{top:-0.5em}sub{bottom:-0.25em}img{border:0}svg:not(:root){overflow:hidden}figure{margin:1em 40px}hr{-moz-box-sizing:content-box;box-sizing:content-box;height:0}pre{overflow:auto}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}button,input,optgroup,select,textarea{color:inherit;font:inherit;margin:0}button{overflow:visible}button,select{text-transform:none}button,html input[type="button"],input[type="reset"],input[type="submit"]{-webkit-appearance:button;cursor:pointer}button[disabled],html input[disabled]{cursor:default}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}input{line-height:normal}input[type="checkbox"],input[type="radio"]{box-sizing:border-box;padding:0}input[type="number"]::-webkit-inner-spin-button,input[type="number"]::-webkit-outer-spin-button{height:auto}input[type="search"]{-webkit-appearance:textfield;-moz-box-sizing:content-box;-webkit-box-sizing:content-box;box-sizing:content-box}input[type="search"]::-webkit-search-cancel-button,input[type="search"]::-webkit-search-decoration{-webkit-appearance:none}fieldset{border:1px solid #c0c0c0;margin:0 2px;padding:.35em .625em .75em}legend{border:0;padding:0}textarea{overflow:auto}optgroup{font-weight:bold}table{border-collapse:collapse;border-spacing:0}td,th{padding:0}
.prettyprint{background:black;font-family:Menlo,'Bitstream Vera Sans Mono','DejaVu Sans Mono',Monaco,Consolas,monospace;font-size:12px;line-height:1.5;border:1px solid #ccc;padding:10px}.pln{color:white}@media screen{.str{color:#6f0}.kwd{color:#f60}.com{color:#93c}.typ{color:#458}.lit{color:#458}.pun{color:white}.opn{color:white}.clo{color:white}.tag{color:white}.atn{color:#9c9}.atv{color:#6f0}.dec{color:white}.var{color:white}.fun{color:#fc0}}@media print,projection{.str{color:#060}.kwd{color:#006;font-weight:bold}.com{color:#600;font-style:italic}.typ{color:#404;font-weight:bold}.lit{color:#044}.pun,.opn,.clo{color:#440}.tag{color:#006;font-weight:bold}.atn{color:#404}.atv{color:#060}}ol.linenums{margin-top:0;margin-bottom:0;color:white}a{color:gray;text-decoration:none}.container{width:1120px;margin:0 auto}body{font-family:PingHei,'Lucida Grande','Lucida Sans Unicode',Helvetica,Arial,Verdana,sans-serif}
</style>
</head>
<body>
    <div class="container">
        <h1>SexyTemplate Error</h1>
        <p>错误信息: {$error['message']}<p>
        <p>错误位置: 第{$error['line']}行<p>
        <div class="code-area">
            <p>模板代码:</p>
            <div class="left-side">
                <pre class="code-block prettyprint linenums:1 lang-html">{$template}</pre>
            </div>
            <p>PHP代码:</p>
            <div class="right-side">
                <pre class="code-block prettyprint linenums:1 lang-php">&lt;?php {$expression}</pre>
            </div>
        </div>
    </div>
    <div style="text-align: center; color: gray;">author: <a href="http://blog.sou.la/">qpwoeiru96</a></div>
    <script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.js"></script>
    <script>
    window.onload = prettyPrint;
    </script>
</body>
</html>
EOT;
            exit(-1);
        }
    }
}