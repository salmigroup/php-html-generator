<?php

/*
 * Revo-cms created by salmigroup
 * ------------------------------
 * salmigroup@gmail.com
 * (+213) 0555 93 11 68
 * Sidi Bel Abbes, Algeria.
 * 
 * *****************************************************************************
 * *****************************************************************************
 */

class html {

    private static $tab = 0;
    private static $tmp = array();

    public function __construct() {
        
    }

    public static function add_tab() {
        self::$tab += 1;
        return '';
    }

    public static function del_tab() {
        self::$tab = self::$tab > 0 ? self::$tab - 1 : 0;
        return '';
    }

    public static function reset_tab() {
        self::$tab = 0;
        return '';
    }

    public static function tab() {
        return "\n" . str_repeat("\t", self::$tab);
    }

    public static function get_html_attributes($options) {
        $out = '';
        foreach ((array) $options as $opt => $value) {
            $out .= " $opt='$value'";
        }
        return $out;
    }

    // url manipulation
    public static function get_url_target($path) {
        if (substr($path, 0, 1) == '?' || substr($path, 0, 1) == '#')
            $type = 'local';
        elseif (preg_match("#(((https?|ftp)://(w{3}.)?)(?<!www)(w+-?)*.([a-z]{2,4}))#", $path))
            $type = 'external';
        else
            $type = 'internal';
        //
        return $type;
    }

    // HTML
    public static function html_file($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return "<HTML $opt>\n$str\n</HTML>" . self::reset_tab();
    }

    public static function open_html($opt) {
        $opt = self::get_html_attributes($opt);
        return self::reset_tab() . "<HTML $opt>" . self::add_tab();
    }

    public static function close_html() {
        return "\n</HTML>\n";
    }

    public static function head($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<HEAD $opt>\n$str\n</HEAD>" . self::del_tab();
    }

    public static function open_head($opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<HEAD $opt>" . self::add_tab();
    }

    public static function close_head() {
        return self::del_tab() . self::tab() . "</HEAD>";
    }

    public static function charset($charset) {
        return self::tab() . "<META charset=\"$charset\">";
    }

    public static function title($title) {
        return self::tab() . "<TITLE>$title</TITLE>";
    }

    public static function meta($name, $content) {
        return self::tab() . "<META name=\"$name\" content=\"$content\">";
    }

    public static function open_body($opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<BODY $opt>" . self::add_tab();
    }

    public static function close_body() {
        return self::del_tab() . self::tab() . "</BODY>";
    }

    public static function body($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<BODY $opt>\n$str\n</BODY>";
    }

    public static function a($path, $text, $opt = array()) {
        if (is_object(Path::is_initialised())) {
            $base_url = Path::$base_url;
            $url_alias = Path::$url_alias;
            $q = Path::$q;
            $is_front = Path::$is_front;
        } else {
            $base_url = '';
            $url_alias = '';
            $q = '';
            $is_front = TRUE;
        }
        // LINK
        $type = self::get_url_target($path); // SYSTEM.inc
        //
	if (($path == 'register' || $path == 'login') && $q != 'login' && $q != '' && substr($q, 0, 3) != 'int') {
            $path .= '?redirect=' . $q;
        }
        $path = ($type == 'internal') ? $base_url . $path : $path;
        // dbg(theme_list(array($path, $base_url.$q)), 'l/*');
        // OPTIONS

        /**
         * target class 
         */
        $_path = ($pos = strpos($path, '?')) ? substr($path, 0, $pos) : $path;
        $target_class = Helper::slugify(str_replace(array('../', '/'), array('', '-'), $_path));
        if ($target_class == '')
            $target_class = 'front-page';

        /**
         * is admin link 
         */
        $admin_class = substr($target_class, 0, 5) == 'admin' ? 'admin' : '';

        /**
         * is current link 
         */
        $is_current = FALSE;
        if (($type == 'internal')) {
            if ($target_class == 'front-page' && $is_front)
                $is_current = TRUE;
            else
                $is_current = ((!$is_front && $path == $base_url . $q) || ($url_alias && $path == $base_url . $url_alias));
        }
        $class = $is_current ? ' current' : '';
        $class .= ($type == 'internal') ? " $target_class $admin_class" : '';
        $class .= " $type";
        $opt['class'] = (isset($opt['class'])) ? $opt['class'] . $class : $class;
        $opt = self::get_html_attributes($opt);
        // OUT
        $out = "<a href='$path' $opt>$text</a>";
        return $out;
    }

// POPUP BLOCK
    public static function popub($content, $opt = array()) {
        $opt['class'] = (isset($opt['class'])) ? $opt['class'] .= ' popup_block' : 'popup_block';
        $opt['id'] = (isset($opt['id'])) ? $opt['id'] : 'popup_block';
        $title = (isset($opt['title'])) ? $opt['title'] : '';
        //	
        $out = '';
        $out .= self::open_div($opt);
        $out .= self::h(($title) ? $title : '', 2, array('class' => 'popup_title'));
        $out .= self::div($content, array('class' => 'popup_content'));
        $out .= self::close_div();
        return $out;
    }

    public static function label($label, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return "<LABEL $opt>$label</LABEL>";
    }

    public static function format_label($label) {
        $label = rtrim($label, ':');
        $label = str_replace(array('[', ']'), array(' > ', ''), $label);
        return strtoupper(substr($label, 0, 1)) . str_replace('_', ' ', substr($label, 1)) . ' : ';
    }

// DIV 
    public static function open_div($opt = array(), $id = '') {
        self::set_tmp('opened-divs-' . $id, self::get_tmp('opened-divs-' . $id, 0) + 1);
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<DIV $opt><!-- open $id -->" . self::add_tab();
    }

    public static function close_div($id = '') {
        self::set_tmp('opened-divs-' . $id, self::get_tmp('opened-divs-' . $id, 0) - 1);
        self::del_tab();
        return self::tab() . "</DIV><!-- close $id -->";
    }

    public static function close_opened_divs($id = '') {
        $opened = self::get_tmp('opened-divs-' . $id, 0);
        $out = '';
        if ($opened)
            for ($i = 0; $i < $opened; $i++) {
                // set_error('closing');
                $out .= self::close_div($id);
            }
        return $out;
    }

    public static function clear() {
        return self::div('', array('style' => 'clear: both;', 'class' => 'clear'));
    }

    public static function div($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<DIV $opt>$str</DIV>";
    }

    /**
     *
     * @param array $opt
     * @return string
     */
    public static function open_p($opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<P $opt>" . self::add_tab();
    }

    /**
     *
     * @return string
     */
    public static function close_p() {
        self::del_tab();
        return self::tab() . "</P>";
    }

    public static function p($str = '', $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<P $opt>$str</P>";
    }

    /**
     *
     * @param array $opt
     * @return string
     */
    public static function open_pre($opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<PRE $opt>" . self::add_tab();
    }

    /**
     *
     * @return string
     */
    public static function close_pre() {
        self::del_tab();
        return self::tab() . "</PRE>";
    }

    public static function pre($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<PRE $opt>$str</PRE>";
    }

// SPAN, CENTER, SMALL, B, H, BR, HR, PRE, ..
    public static function span($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return "<SPAN $opt>$str</SPAN>";
    }

    public static function center($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return "<CENTER $opt>$str</CENTER>";
    }

    public static function small($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return "<SMALL $opt>$str</SMALL>";
    }

    public static function b($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return "<B $opt>$str</B>";
    }

    public static function i($str, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return "<I $opt>$str</I>";
    }

    public static function h($str, $level = 1, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<H$level $opt>$str</H$level>";
    }

    public static function br() {
        return self::tab() . "<BR />";
    }

    public static function hr($opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<HR $opt />";
    }

// IMG, INPUT
    public static function img($src, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        return "<IMG src='$src' $opt />";
    }

// FORM, INPUT

    /**
     *
     * @param array $fields
     * @param array $opt
     * @return string 
     */
    public static function form($fields = NULL, $opt = array()) {
        $opt = self::get_html_attributes($opt);
        $out = self::open_form($opt);
        if ($fields) {
            foreach ($fields as $field => $options) {
                $out .= self::$field($options);
            }
        }
        $out .= self::close_form();
        return $out;
    }

    public static function open_form($opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<FORM $opt />";
    }

    public static function close_form() {
        return self::tab() . "</FORM>";
    }

    public static function input($opt = array()) {
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<INPUT $opt />";
    }

// LIST


    public static function _list($items, $opt = array(), &$level = 0) {
        //options
        $itemKey = array_key_exists('itemKey', $opt) ? $opt['itemKey'] : 'item';
        $subKey = array_key_exists('subKey', $opt) ? $opt['subKey'] : 'sub';
        $class = array_key_exists('class', $opt) ? $opt['class'] : '';
        $id = array_key_exists('id', $opt) ? $opt['id'] : '';

        //inverse items
        if (in_array('reverse', $opt))
            $items = array_reverse($items);

        $tab1 = str_repeat("\t", $level + 1);
        $tab2 = str_repeat("\t", $level + 2);
        $out = "\n$tab1<ul class='level-$level $class' id='$id'>";

        $odd = 1;
        $index = 0;
        foreach ($items as $key => $item) {
            $i = '';
            if (is_array($item)) {
                $level++;
                $li = (array_key_exists($itemKey, $item)) ? $item[$itemKey] : '';
                $sub = (array_key_exists($subKey, $item)) ? $item[$subKey] : $item;
                $_class = 'sub parent-' . str_replace(' ', '-', $key);

                $_li = ($li) ? $li : '';
                $i = "\n$tab2 $_li"
                        . self::_list($sub, array('class' => $_class), $level)
                        . "\n$tab2";
                $level--;
            } else {
                $class = '';
                if ($index == count($items) - 1)
                    $class = "last";
                if ($index == 0)
                    $class = "first";
                $class .= ($odd > 0) ? " odd" : " even";
                $odd = - $odd;
                $i = "\n$tab2$item";
            }
            $index++;
            $out .= "<li id='$key' class='$class'>$i</li>";
        }
        $out .= "\n$tab1</ul>";

        return $out;
    }

    public static function ul($items, $opt = array(), &$level = 0) {
        //options
        $itemKey = array_key_exists('itemKey', $opt) ? $opt['itemKey'] : 'item';
        $subKey = array_key_exists('subKey', $opt) ? $opt['subKey'] : 'sub';
        $class = array_key_exists('class', $opt) ? $opt['class'] : '';
        $id = array_key_exists('id', $opt) ? $opt['id'] : '';
        //inverse items
        if (in_array('reverse', $opt))
            $items = array_reverse($items);

        $tab1 = str_repeat("\t", $level + 1);
        $tab2 = str_repeat("\t", $level + 2);
        $out = "\n$tab1<ul class='$class level-$level' id='$id'>";

        $odd = 1;
        $index = 0;
        foreach ($items as $key => $item) {
            $i = '';
            if (is_array($item)) {
                $level++;
                $li = (array_key_exists($itemKey, $item)) ? $item[$itemKey] : '';
                $sub = (array_key_exists($subKey, $item)) ? $item[$subKey] : $item;
                $_class = 'sub parent-' . str_replace(' ', '-', $key);

                $_li = ($li) ? $li : '';
                $i = "\n$tab2 $_li"
                        . self::ul($sub, array('class' => $_class), $level)
                        . "\n$tab2";
                $level--;
            } else {
                $class = '';
                if ($index == count($items) - 1)
                    $class = "last";
                if ($index == 0)
                    $class = "first";
                $class .= ($odd > 0) ? " odd" : " even";
                $odd = - $odd;
                $i = "\n$tab2$item";
            }
            $index++;
            $out .= "<li id='$key' class='$class'>$i</li>";
        }
        $out .= "\n$tab1</ul>";

        return $out;
    }

    public static function ol($items, $opt = array(), &$level = 0) {
        //options
        $itemKey = array_key_exists('itemKey', $opt) ? $opt['itemKey'] : 'item';
        $subKey = array_key_exists('subKey', $opt) ? $opt['subKey'] : 'sub';
        $class = array_key_exists('class', $opt) ? $opt['class'] : '';
        $id = array_key_exists('id', $opt) ? $opt['id'] : '';
        //inverse items
        if (in_array('reverse', $opt))
            $items = array_reverse($items);

        $tab1 = str_repeat("\t", $level + 1);
        $tab2 = str_repeat("\t", $level + 2);
        $out = "\n$tab1<ul class='$class level-$level' id='$id'>";

        $odd = 1;
        $index = 0;
        foreach ($items as $key => $item) {
            $i = '';
            if (is_array($item)) {
                $level++;
                $li = (array_key_exists($itemKey, $item)) ? $item[$itemKey] : '';
                $sub = (array_key_exists($subKey, $item)) ? $item[$subKey] : $item;
                $_class = 'sub parent-' . str_replace(' ', '-', $key);

                $_li = ($li) ? $li : '';
                $i = "\n$tab2 $_li"
                        . self::ol($sub, array('class' => $_class), $level)
                        . "\n$tab2";
                $level--;
            } else {
                $class = '';
                if ($index == count($items) - 1)
                    $class = "last";
                if ($index == 0)
                    $class = "first";
                $class .= ($odd > 0) ? " odd" : " even";
                $odd = - $odd;
                $i = "\n$tab2$item";
            }
            $index++;
            $out .= "<li id='$key' class='$class'>$i</li>";
        }
        $out .= "\n$tab1</ul>";

        return $out;
    }

// TABLE

    /**
     *
     * @param array $header
     * @param array $raws
     * @param array $opt
     * @return string 
     */
    public static function table($header, $raws, $opt = array()) {
        //options
        $style = array_key_exists('style', $opt) ? $opt['style'] : '';

        $_class = array_key_exists('class', $opt) ? $opt['class'] . ' themed-table' : 'themed-table';
        $_id = array_key_exists('id', $opt) ? $opt['id'] : '';
        // SELECT
        $select = (in_array('selectable', $opt) || (array_key_exists('selectable', $opt) && $opt['selectable']));
        $select_ids = (array_key_exists('select', $opt) && is_array($opt['select']) && count($opt['select'])) ? $opt['select'] : false;

        $_class .= ($select) ? ' selectable' : '';
        // SORTABLE
        $sortable = in_array('sortable', $opt);
        if ($sortable)
            rv_load_jui(); // api/view.class

        $theader = '';
        if ($header) { // HEADER
            $theader = "\n\t<tr class='thead header'>";
            if ($select)
                array_unshift($header, "<input type='checkbox' name='checkall' id='post' class='checkall' value='' />");

            foreach ($header as $index => $td) {
                if (is_array($td)) {
                    $label = $td['label'];
                    //sort
                    if (isset($td['sort'])) {
                        $getOrder = rv_input_get_get('order', 1, '');
                        $target = ($getOrder && $getOrder == $td['sort'] . ' desc') ? $td['sort'] . ' asc' : $td['sort'] . ' desc';
                        $active = '';
                        if ($getOrder) {
                            preg_match("#^$td[sort]#", $getOrder, $matchs);
                            $active = (count($matchs)) ? ' active' : '';
                        }
                        $label = html::a("?order=$target", $label, array('class' => 'sort ' . $target . $active));
                    }// end sort
                }
                else
                    $label = $td;

                $class = '';
                $colspan = (isset($opt['colspan']) && array_key_exists($index, $opt['colspan'])) ? "colspan = " . $opt['colspan'][$index] : '';
                if ($index == 0)
                    $class = "class='first'";
                if ($index == count($header) - 1)
                    $class = "class='last'";
                $theader .= "\n\t\t<th $class $colspan>$label</th>";
            }
            $theader .= "\n\t</tr>";
        }

        // body
        $tbody = "\n";
        $odd = 1;
        foreach ($raws as $index => $raw) {
            $class = '';
            if ($index == 0)
                $class = "first";
            if ($index == count($raws) - 1)
                $class = "last";

            $class .= ($odd > 0) ? " odd" : " even";
            $odd = - $odd;

            $is_sub_title = FALSE;
            $tr_colspan = 0;
            if (count($raw) == 1 && array_key_exists('sub_title', $raw)) {
                $tr_colspan = (count($header) > 1) ? "colspan = " . count($header) : '';
                $class .= ' sub-title';
                $is_sub_title = TRUE;
            }

            if (count($raw) == 1 && array_key_exists('fill_width_td', $raw)) {
                $tr_colspan = (count($header) > 1) ? "colspan = " . count($header) : '';
                $class .= ' fill-width-td';
            }

            $id = Helper::slugify($index);
            if ($sortable)
                $tbody .= "\n\t<div class='sortable-item'>";
            $tbody .= "\n\t<tr id='$id' class='$class'>";
            $class = '';
            if ($select && !$is_sub_title) {
                $item_id = ($select_ids && array_key_exists($index, $select_ids)) ? $select_ids[$index] : $index;
                array_unshift($raw, "<input type='checkbox' name='items[]' id='$item_id' class='item' value='$item_id' />");
            }

            for ($index = 0; $index < count($raw); $index++) {
                $colspan = ($tr_colspan) ? "$tr_colspan" : '';
                $td = current($raw);
                $id = Helper::slugify(key($raw));
                next($raw);
                if (is_array($td))
                    $td = '<pre>' . print_r($td, 1) . '</pre>';
//                    $td = theme_describe_table($td, '');

                $class = "td-$index $id";
                if ($index == 0)
                    $class .= " first";
                if ($index == count($raw) - 1)
                    $class .= " last";
                //
                $tbody .= "\n\t\t<td data-column='$id' class='$class' $colspan>$td</td>";
            }
            $tbody .= "\n\t</tr>";
            if ($sortable)
                $tbody .= "\n\t</div><!-- end sortable item -->";
        }

        // out
        $out = '';
        if ($sortable)
            $out .= "\n<div class='sortable'>";
        $out .= "\n<table cellspacing=0 border=0 class='$_class' id='$_id' style='$style'>\n";
        $out .= "\n<thead>";
        $out .= $theader;
        $out .= "\n</thead>";
        $out .= "\n<tbody>";
        $out .= $tbody;
        $out .= "\n</tbody>";
        $out .= "\n</table>";
        if ($sortable)
            $out .= "\n</div><!-- end sortable items -->";

        if (isset($opt['form'])) {
            $form = (array) $opt['form'];
            $form = array_merge(array('method' => 'post', 'submit_label' => rv_t('save'), 'action' => '', 'id' => 'form-' . rand(10000, 99999)), $form);
            $out = "\n<form method = '$form[method]' action = '$form[action]' id='$form[id]'>" . $out;
            $id = md5($form['id']);
            $out .= "\n<input type = 'hidden' name = 'fid' value = '$id' />";
            $out .= "\n<input type = 'submit' class='submit' value = '$form[submit_label]' />";
            $out .= "\n</form>";
        }
        return $out;
    }

// GRID
    public static function grid(array $items, $items_per_line = 2, $opt = array()) {
        $opt['class'] = array_key_exists('class', $opt) ? $opt['class'] . ' themed-grid-table' : 'themed-grid-table';
        $opt['id'] = array_key_exists('id', $opt) ? $opt['id'] : '';

        $cell_width = floor(100 / $items_per_line);
        $raws = array();
        while (current($items)) {
            $i = 0;
            $raw = array();
            while ($i < $items_per_line && $item = current($items)) {
                $raw[] = $item;
                $i++;
                next($items);
            }
            $raws[] = $raw;
        }
//        rv_set_message(printr($rows), MSG_TEST);
        return self::table(array(), $raws, $opt);
    }

// PAGINATOR
    public static function paginator($total_count, $_limit, $page = 0, $formId = '', $elarged = 0) {
        /*
          if (is_callable('theme_paginator')){
          return theme_paginator($total_count, $_limit, $page, $formId, $elarged);
          }
         */
        if ($total_count <= $_limit)
            return '';
        $page = (isset($_GET['page'])) ? $_GET['page'] : 0;
        $uri = substr(
                preg_replace(
                        '#page=[0-9]+#', '', $_SERVER['REQUEST_URI']
                ), strpos($_SERVER['REQUEST_URI'], '?') + 1
        );
        $uri = (substr($uri, 0, 1) == '&') ? substr($uri, 1) : $uri;
        $uri = '&' . $uri;
        $last = round($total_count / $_limit) - 1;
        $items = array();
        $i = 0;
        while ($i < ($total_count / $_limit)) {
            if ($i != $page)
                $items[] = self::a("?page=$i$uri", $i + 1);
            else
                $items[] = self::span($i + 1);
            $i++;
        }

        $odd = 1;
        $out = "\n<ul class='paginator'>";
        foreach ($items as $index => $item) {
            $class = '';
            if ($index == count($items) - 1)
                $class = "last";
            if ($index == 0)
                $class = "first";
            $class .= ($odd > 0) ? " odd" : " even";
            $odd = - $odd;
            $out .= "\n\t<li id='$index' class='$class'>$item</li>";
        }
        $out .= "\n</ul>";

        // set_hint($out);
        return self::div(
                        $out
                        , array('class' => 'paginator')
        );
    }

    public static function filter_contextual_links($link) {
        return !isset($link['permission']) || rv_user_can($link['permission']);
    }

    public static function render_contextual_links($links, $check_access = TRUE) {
        if ($check_access)
            $links = array_filter($links, 'self::filter_contextual_links');

        $out = NULL;
        if ($links) {
            foreach ($links as $control)
                $list[] = html::a($control['path'], rv_t($control['title']));

            $out = html::div(
                            html::a('#', 'configure block', array('class' => 'contextual-links-trigger'))
                            . html::_list($list, array('class' => 'contextual-links'))
                            , array('class' => 'contextual-links-wrapper')
            );
        }

        return $out;
    }

    public static function style($css) {
        return self::tab() . "<STYLE>\n$css\n</STYLE>";
    }

    public static function textarea($STR) {
        return self::tab() . "<TEXTAREA>\n$STR\n</TEXTAREA>";
    }

    // TMP array
    public static function set_tmp($dir, $var) {
        self::$tmp[$dir] = $var;
    }

    public static function unset_tmp($dir) {
        unset(self::$tmp[$dir]);
    }

    public static function get_tmp($dir, $default = false) {
        return (isset(self::$tmp[$dir])) ? self::$tmp[$dir] : $default;
    }

    public static function script($src, $script = '', $type = 'text/javascript') {
        return self::tab() . "<SCRIPT type='$type' src='$src'>\n$script\n</SCRIPT>";
    }

    public static function link($opt = array()) {
        $opt = \array_merge(array('href' => '', 'rel' => 'stylesheet'), $opt);
        $opt = self::get_html_attributes($opt);
        return self::tab() . "<LINK $opt />";
    }

}
