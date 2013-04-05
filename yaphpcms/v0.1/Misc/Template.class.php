<?php
/**
 * 模板编译类
 *
 * @file            Misc_Template.class.php
 * @package         Yap
 * @version         0.1
 * @copyright       Copyright (c) 2013 {@link http://www.yaphpcms.com yaphpcms} All rights reserved
 * @license         http://www.apache.org/licenses/LICENSE-2.0.html Apache License 2.0
 * @author          mrmsl <msl-138@163.com>
 * @date            2013-04-05 10:30:19
 * @lastmodify      $Date$ $Author$
 */
class Misc_Template {
    private $_caching  = false;
    private $_cache_id = '';
    private $_ttl      = 3600;

    public function compile($controller, $action, $theme_path = '') {

        $template_file = $theme_path . $controller . '/' . $action . C('TEMPLATE_SUFFIX');
        $theme_path    = $theme_path = FRONT_THEME_PATH ? FRONT_THEME_PATH ?
        $compile_dir   = $theme_path . "templates_c/{$controller}/";
        $compile_file  = $compile_dir . $action . '.php';
        $cache_file    = $theme_path . "templates_d/{$controller}/{$action}.{$this->_cache_id}" . C('HTML_SUFFIX');

        !is_dir($compile_dir) && mkdir($compile_dir, 0755, true);

        if (!is_file($template_file)) {
            throw new Exception(L('_TEMPLATE_NOT_EXIST_') . "($template_file)");
        }
        elseif(is_file($compile_file) && filemtime($template_file) < filemtime($compile_file)) {//未编译或编译已过期

            if ($this->_caching) {//缓存

                if (is_file($cache_file) && filemtime($cache_file) > time() - $this->_ttl)) {//缓存未过期
                    return $cache_file;
                }
            }
            else {
                return $compile_file;
            }
        }

        if(!is_file($template_file)) {
            throw new Exception(L('_TEMPLATE_NOT_EXIST_') . "($template_file)");
        }

        $source = file_get_contents($template_file);

        if (false !== strpos($source, '{template')) {//template()
            $source = preg_replace('#\{template\s+(.+)\}#', '<?php require(template(\\1)); ?>', $source);
        }

        if (false !== strpos($source, '{require')) {//include()
            $source = preg_replace('#\{include\s+(.+)\}#', '<?php include(\\1); ?>', $source);
        }

        if (false !== strpos($source, '{php')) {//php
            $source = preg_replace('#\{php\s+(.+)\}#', '<?php \\1?>', $source);
        }

        if (false !== strpos($source, '{echo')) {//php
            $source = preg_replace('#\{echo\s+(.+)\}#', '<?php echo \\1; ?>', $source);
        }

        //if
        if (false !== strpos($source, '{if')) {
            $source = preg_replace('#\{if\s+(.+?)\}#', '<?php if(\\1) { ?>', $source);
            $source = preg_replace('#\{/if\}#', '<?php } ?>', $source);
        }
        if (false !== strpos($source, '{else')) {
            $source = preg_replace('#\{else\}#', '<?php } else { ?>', $source);
            $source = preg_replace('#\{elseif\s+(.+?)\}#', '<?php } elseif (\\1) { ?>', $source);
        }

        //for 循环
        if (false !== strpos($source, '{for')) {
            $source = preg_replace('#\{for\s+(.+?)\}#','<?php for(\\1) { ?>', $source);
            $source = preg_replace('#\{/for\}#','<?php } ?>', $source);
        }

        //foreach
        if (false !== strpos($source, '{foreach')) {
            $source = preg_replace('#\{foreach\s+(\S+)\s+(\S+)\}#', '<?php \$n=1;if(is_array(\\1)) { foreach(\\1 as \\2) { ?>', $source);
            $source = preg_replace('#\{foreach\s+(\S+)\s+(\S+)\s+(\S+)\}#', '<?php \$n=1; if(is_array(\\1)) { foreach(\\1 as \\2 => \\3) { ?>', $source);
            $source = preg_replace('#\{/foreach\}#', '<?php \$n++; } unset(\$n); ?>', $source);
        }

        $source = preg_replace('#\{(\w+)\}#', '<?php echo \\1;?>', $source);
        $source = preg_replace('#\{\$(\w+)\}#', '<?php echo \\1;?>', $source);
        $source = "<?php\n!defined('YAP_PATH') && exit('Access Denied'); ?>" . $source;

        !is_dir($v = $theme_path . "templates_c/{$controller}/") && mkdir($v, 0755, true);

        file_put_contents($v . $action . '.php', $source);

        return $compile_file;
    }//end compile

    public function fetch($controller, $action, $theme_path = '', $return = true) {
        $compile_file = $this->compile($controller, $action, $theme_path, true);

        if ($this->_caching) {
            $cache_file = $theme_path . "templates_d/{$controller}/{$action}.{$this->_cache_id}" . C('HTML_SUFFIX');

            if (is_file($cache_file) && filemtime($cache_file) > time() - $this->_ttl)) {//缓存未过期

                if ($return) {
                    return file_get_contents($cache_file);
                }

                $compile_file = $cache_file;
            }
        }

        ob_start();

        require($this->_caching ? $cache_file : $compile_file);

        if ($return || $this->_caching) {
            $content = ob_get_contents();
            ob_end_clean();

            $this->_caching && file_put_contents($cache_file);

            return $content;
        }
    }

    public function display($controller, $action, $theme_path = '') {
        echo $this->fetch($controller, $action, $theme_path, true);
    }
}