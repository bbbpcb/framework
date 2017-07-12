<?php
function FormID($Inc = null)
{
    static $FormID;
    if (!$FormID) {
        $FormID = 1;
        return $FormID;
    }
    if ($Inc === null) {
        $FormID += 1;
        return $FormID;
    } else {
        return $FormID + $Inc;
    }
}
function FormItem($class, $label, $input, $hint = '')
{
    if ($hint) {
        $hint = "<span class=\"FormHint\">&nbsp;{$hint}&nbsp;</span>";
    }
    return "<div class=\"form-group FormItem {$class}\">{$label}{$hint}{$input}<div style=\"clear:both\"></div></div>";
}
function FormParam($param)
{
    $s = '';
    if (is_array($param)) {
        foreach ($param as $key => $value) {
            $s .= "{$key}=\"" . H($value) . '" ';
        }
    }
    return $s;
}
function FormTag($tag, $content, $param)
{
    return "<{$tag} " . FormParam($param) . ">{$content}</{$tag}>";
}
function FormInput($param)
{
    return '<input ' . FormParam($param) . '/>';
}
function FormLabel($id, $caption = '')
{
    return $caption ? "<label class=\"FormLabel\" for=\"{$id}\">{$caption}</label>" : '';
}
function FormOption($input, $caption = '')
{
    return "<label class=\"FormOption\">{$input}{$caption}</label>&nbsp;&nbsp;";
}
function FormOptions($options, $value = null)
{
    if ($value === null) {
        return $options;
    }
    $input = array();
    if (!is_array($value)) {
        $value = array(
            $value
        );
    }
    if (!is_array($options)) {
        $options = $options ? explode(',', $options) : array();
    }
    foreach ($options as $option) {
        if ($option == '-') {
            $input[] = '-';
            continue;
        }
        if (substr($option, 0, 1) == '*') {
            $option = substr($option, 1);
        }
        $opt = $option;
        $pos = strpos($option, '=>');
        if ($pos !== false) {
            $opt = substr($option, $pos + 2);
        }
        $input[] = (in_array($opt, $value) ? '*' : '') . $option;
    }
    return implode(',', $input);
}
function FormEdit($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $param['id']    = $id;
    $param['class'] = 'form-control Form FormEdit ' . V($param['class']);
    $param['type']  = V($param['type'], 'text');
    $param['name']  = $name;
    $param['value'] = $remember ? I($name, $value) : $value;
    return FormItem('FormItemEdit', FormLabel($id, $caption), FormInput($param), $hint);
}
function FormHidden($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $param['id']    = $id;
    $param['class'] = 'form-control Form FormHidden ' . V($param['class']);
    $param['type']  = 'hidden';
    $param['name']  = $name;
    $param['value'] = $remember ? I($name, $value) : $value;
    return FormItem('FormHidden', true ? "" : FormLabel($id, $caption), FormInput($param), $hint);
}
function FormText($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id                = 'Form' . FormID();
    $param['id']       = $id;
    $param['class']    = 'form-control Form FormText ' . V($param['class']);
    $param['type']     = 'text';
    $param['readonly'] = 'readonly';
    $param['name']     = $name;
    $param['value']    = $remember ? I($name, $value) : $value;
    return FormItem('FormItemText', FormLabel($id, $caption), FormInput($param), $hint);
}
function FormHtml($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $param['id']    = $id;
    $param['class'] = 'Form FormHtml ' . V($param['class']);
    $param['name']  = $name;
    return FormItem('FormItemHtml', FormLabel($id, $caption), FormTag("div", $value, $param), $hint);
}
function FormPassword($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $param['id']    = $id;
    $param['class'] = 'form-control Form FormPassword ' . V($param['class']);
    $param['type']  = 'password';
    $param['name']  = $name;
    $param['value'] = $remember ? I($name, $value) : $value;
    return FormItem('FormItemPassword', FormLabel($id, $caption), FormInput($param), $hint);
}
function FormCheckbox($name, $caption = '', $hint = '', $options = array(), $param = array(), $remember = true)
{
    $label          = FormLabel('Form' . FormID(), $caption);
    $param['class'] = 'checkbox Form FormCheckbox ' . V($param['class']);
    $param['type']  = 'checkbox';
    $param['name']  = $name . '[]';
    $input          = '';
    $valued         = isset($_REQUEST[$name]);
    $value          = $valued ? explode('|', I($name)) : array();
    if (!is_array($options)) {
        $options = $options ? explode(',', $options) : array();
    }
    foreach ($options as $option) {
        if ($option == '-') {
            $input .= '<br/>';
            continue;
        }
        $id          = 'Form' . FormID();
        $param['id'] = $id;
        $checked     = substr($option, 0, 1) == '*';
        if ($checked) {
            $option = substr($option, 1);
        }
        $pos = strpos($option, '=>');
        if ($pos !== false) {
            $caption = substr($option, '0', $pos);
            $option  = substr($option, $pos + 2);
        } else {
            $caption = $option;
        }
        $param['value'] = $option;
        if ($remember && $valued) {
            $checked = in_array($option, $value);
        }
        if ($checked) {
            $param['checked'] = 'checked';
        } elseif (isset($param['checked'])) {
            unset($param['checked']);
        }
        $input .= FormOption(FormInput($param), $caption);
    }
    return FormItem('FormItemCheckbox', $label, '<div id="FormOptions_' . FormID() . '" class="checkbox FormOptions">' . $input . '</div>', $hint);
}
function FormRadiobox($name, $caption = '', $hint = '', $options = array(), $param = array(), $remember = true)
{
    $label          = FormLabel('Form' . FormID(), $caption);
    $param['class'] = 'radio Form FormRadiobox ' . V($param['class']);
    $param['type']  = 'radio';
    $param['name']  = $name;
    $input          = '';
    $valued         = isset($_REQUEST[$name]);
    $value          = I($name);
    if (!is_array($options)) {
        $options = $options ? explode(',', $options) : array();
    }
    foreach ($options as $option) {
        if ($option == '-') {
            $input .= '<br/>';
            continue;
        }
        $id          = 'Form' . FormID();
        $param['id'] = $id;
        $checked     = substr($option, 0, 1) == '*';
        if ($checked) {
            $option = substr($option, 1);
        }
        $pos = strpos($option, '=>');
        if ($pos !== false) {
            $caption = substr($option, '0', $pos);
            $option  = substr($option, $pos + 2);
        } else {
            $caption = $option;
        }
        $param['value'] = $option;
        if ($remember && $valued) {
            $checked = $option == $value;
        }
        if ($checked) {
            $param['checked'] = 'checked';
        } elseif (isset($param['checked'])) {
            unset($param['checked']);
        }
        $input .= FormOption(FormInput($param), $caption) . '';
    }
    return FormItem('FormItemRadiobox', $label, '<div id="FormOptions_' . FormID() . '" class="radio FormOptions">' . $input . '</div>', $hint);
}
function FormCombobox($name, $caption = '', $hint = '', $options = array(), $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $label          = FormLabel($id, $caption);
    $param['id']    = $id;
    $param['class'] = 'form-control Form FormCombobox ' . V($param['class']);
    $param['name']  = $name;
    $input          = '';
    $valued         = isset($_REQUEST[$name]);
    $value          = I($name);
    if (!is_array($options)) {
        $options = $options ? explode(',', $options) : array();
    }
    foreach ($options as $option) {
        if ($option == '-') {
            $input .= FormTag('option', '----------------', array());
            continue;
        }
        $checked = substr($option, 0, 1) == '*';
        if ($checked) {
            $option = substr($option, 1);
        }
        $pos = strpos($option, '=>');
        if ($pos !== false) {
            $caption = substr($option, '0', $pos);
            $option  = substr($option, $pos + 2);
        } else {
            $caption = $option;
        }
        $cfg = array(
            'value' => $option
        );
        if ($remember && $valued) {
            $checked = $option == $value;
        }
        if ($checked) {
            $cfg['selected'] = 'selected';
        }
        $input .= FormTag('option', $caption, $cfg);
    }
    return FormItem('FormItemCombobox', $label, FormTag('select', $input, $param), $hint);
}
function FormListbox($name, $caption = '', $hint = '', $options = array(), $param = array(), $remember = true)
{
    $id                = 'Form' . FormID();
    $label             = FormLabel($id, $caption);
    $param['id']       = $id;
    $param['class']    = 'form-control Form FormListbox ' . V($param['class']);
    $param['name']     = $name . '[]';
    $param['multiple'] = 'multiple';
    $input             = '';
    $valued            = isset($_REQUEST[$name]);
    $value             = $valued ? explode('|', I($name)) : array();
    if (!is_array($options)) {
        $options = $options ? explode(',', $options) : array();
    }
    foreach ($options as $option) {
        if ($option == '-') {
            $input .= FormTag('option', '--------------', array());
            continue;
        }
        $checked = substr($option, 0, 1) == '*';
        if ($checked) {
            $option = substr($option, 1);
        }
        $pos = strpos($option, '=>');
        if ($pos !== false) {
            $caption = substr($option, '0', $pos);
            $option  = substr($option, $pos + 2);
        } else {
            $caption = $option;
        }
        $cfg = array(
            'value' => $option
        );
        if ($remember && $valued) {
            $checked = in_array($option, $value);
        }
        if ($checked) {
            $cfg['selected'] = 'selected';
        }
        $input .= FormTag('option', $caption, $cfg);
    }
    return FormItem('FormItemListbox', $label, FormTag('select', $input, $param), $hint);
}
function FormMemo($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $param['id']    = $id;
    $param['class'] = 'form-control Form FormMemo ' . V($param['class']);
    $param['name']  = $name;
    return FormItem('FormItemMemo', FormLabel($id, $caption), FormTag('textarea', $remember ? I($name, $value) : $value, $param), $hint);
}
function FormKindEditorScript()
{
    static $KindEditor;
    if (!$KindEditor) {
        $KindEditor = true;
        return '<script type="text/javascript">(function(css){var meta=document.createElement("link");meta.setAttribute("rel","stylesheet");meta.setAttribute("type","text/css");meta.setAttribute("href",css);document.getElementsByTagName("head")[0].appendChild(meta);})("/gears/editor/themes/default/default.css");window.KindEditors={};</script><script type="text/javascript" src="/gears/editor/kindeditor.js"></script><script type="text/javascript" src="/gears/editor/lang/zh_CN.js"></script>';
    }
    return '';
}
function FormEditor($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $param['id']    = $id;
    $param['class'] = 'Form FormEditor ' . V($param['class']);
    $param['name']  = $name;
    return FormItem('FormItemEditor', FormLabel($id, $caption), FormTag('textarea', $remember ? I($name, $value) : $value, $param), $hint) . FormKindEditorScript() . "<script type=\"text/javascript\">KindEditor.ready(function(K){var editor=K.create('#{$id}',{width:'100%',minHeight:260,uploadJson:'/gears/editor/upload.php',fileManagerJson:'/gears/editor/manager.php',allowFileManager:true,filterMode:false,afterBlur:function(){this.sync();}});window.KindEditors['{$id}']=editor;});</script>";
}
function FormEditorUpload($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $param['id']    = $id;
    $param['class'] = 'form-control Form FormEditorUpload ' . V($param['class']);
    $param['type']  = 'text';
    $param['name']  = $name;
    $param['value'] = $remember ? I($name, $value) : $value;
    return FormItem('FormItemUpload', FormLabel($id, $caption), '<div class="input-group">' . FormInput($param) . "<span class=\"input-group-btn\"><button id=\"{$id}Button\" class=\"btn btn-default\" type=\"button\">\xe4\xb8\x8a\xe4\xbc\xa0</button></span></div>", $hint) . FormKindEditorScript() . "<script type=\"text/javascript\">KindEditor.ready(function(K){var KindEditor{$id}=K.editor({uploadJson:'/gears/editor/upload.php',fileManagerJson:'/gears/editor/manager.php',allowFileManager:true});K('#{$id}Button').click(function(){KindEditor{$id}.loadPlugin('insertfile',function(){KindEditor{$id}.plugin.fileDialog({fileUrl:K('#{$id}').val(),clickFn:function(url,title){K('#{$id}').val(url+((title && url!=title)?'#'+title:''));KindEditor{$id}.hideDialog();}});});});});</script>";
}
function FormEditorMap($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $param['id']    = $id;
    $param['class'] = 'form-control Form FormEditorMap ' . V($param['class']);
    $param['type']  = 'text';
    $param['name']  = $name;
    $param['value'] = $remember ? I($name, $value) : $value;
    return FormItem('FormItemMap', FormLabel($id, $caption), '<div class="input-group">' . FormInput($param) . "<span class=\"input-group-btn\"><button id=\"{$id}Button\" class=\"btn btn-default\" type=\"button\">\xe9\x80\x89\xe5\x8f\x96</button></span></div>", $hint) . FormKindEditorScript() . "<script type=\"text/javascript\">
                KindEditor.ready(function(K){K('#{$id}Button').click(function(){" . "var mapWidth=558;var mapHeight=360;var html=['<div style=\"padding:10px 20px;\">','<div class=\"ke-header\">','<div class=\"ke-left\">','\xe5\x9c\xb0\xe5\x9d\x80:'+' <input id=\"kindeditor_plugin_map_address\" name=\"address\" class=\"ke-input-text\" value=\"\" style=\"width:200px;\" /> ','<span class=\"ke-button-common ke-button-outer\">','<input type=\"button\" name=\"searchBtn\" class=\"ke-button-common ke-button\" value=\"\xe6\x90\x9c\xe7\xb4\xa2\" />','</span>','</div>','<div class=\"ke-right\"></div>','<div class=\"ke-clearfix\"></div>','</div>','<div class=\"ke-map\" style=\"width:'+mapWidth+'px;height:'+mapHeight+'px;\"></div>','</div>'].join('');var dialog=K.dialog({width:mapWidth+42,title:'\xe9\x80\x89\xe5\x8f\x96\xe5\x9d\x90\xe6\xa0\x87',body:html,closeBtn:{name:'\xe5\x85\xb3\xe9\x97\xad',click:function(e){dialog.remove()}},yesBtn:{name:'\xe7\xa1\xae\xe5\xae\x9a',click:function(e){var map=win.map;var centerObj=map.getCenter();var center=centerObj.lng+','+centerObj.lat;K('#{$id}').val(center);dialog.remove()}},beforeRemove:function(){searchBtn.remove();if(doc){doc.write('')}iframe.remove()}});var div=dialog.div,addressBox=K('[name=\"address\"]',div),searchBtn=K('[name=\"searchBtn\"]',div),checkbox=K('[name=\"insertDynamicMap\"]',dialog.div),win,doc;var iframe=K('<iframe class=\"ke-textarea\" frameborder=\"0\" src=\"/gears/editor/plugins/baidumap/center.html?center='+K('#{$id}').val()+'\" style=\"width:'+mapWidth+'px;height:'+mapHeight+'px;\"></iframe>');function ready(){win=iframe[0].contentWindow;doc=K.iframeDoc(iframe)}iframe.bind('load',function(){iframe.unbind('load');if(K.IE){ready()}else{setTimeout(ready,0)}});K('.ke-map',div).replaceWith(iframe);searchBtn.click(function(){win.search(addressBox.val())});" . "});});</script>";
}
function FormUpload($name, $caption = '', $hint = '', $value = null, $param = array(), $remember = true)
{
    $id             = 'Form' . FormID();
    $param['id']    = $id;
    $param['class'] = 'form-control Form FormUpload ' . V($param['class']);
    $param['type']  = 'text';
    $param['name']  = $name;
    $param['value'] = $remember ? I($name, $value) : $value;
    return FormItem('FormItemUpload', FormLabel($id, $caption), '<div class="input-group">' . FormInput($param) . "<span class=\"input-group-btn\"><button id=\"{$id}Button\" class=\"btn btn-default\" type=\"button\" onclick=\"BirdUpload('#{$id}',{},'upload',{$id}Uploaded,'\xe6\xad\xa3\xe5\x9c\xa8\xe4\xb8\x8a\xe4\xbc\xa0\xe2\x80\xa6');\">\xe4\xb8\x8a\xe4\xbc\xa0</button></span></div>", $hint) . "<script type=\"text/javascript\">function {$id}Uploaded(html,params,bind){if(params.done){\$(\"#{$id}\").val(params.filename);}else{BirdToast(bind,params.error);}}</script>";
}
function Form($name, $caption = '', $html = '', $forms = array(), $param = array(), $remember = true)
{
    $param['action'] = V($param['action'], R('REQUEST_URI'));
    $param['method'] = V($param['method'], 'POST');
    $form            = '<div class="FormFrame"><form role="form" ' . FormParam($param) . '>' . FormInput(array(
        'type' => 'hidden',
        'name' => 'FORMNAME',
        'value' => $name
    ));
    foreach ($forms as $item) {
        if (!is_array($item)) {
            $form .= $item ? $item : "<div style=\"clear:both;\"></div>";
        } else {
            $width = 100;
            $type  = strtolower(array_shift($item));
            if (is_numeric($type)) {
                $width = max(min(intval($type), 100), -100);
                $type  = strtolower(array_shift($item));
            }
            if ((!$width) || ($type == "clear")) {
                $form .= "<div style=\"clear:both;\"></div>";
            } elseif ($type) {
                $form .= "<div style=\"float:" . ($width > 0 ? "left" : "right") . ";width:" . abs($width) . "%;\">";
                $form .= "<div style=\"margin:0 5px;\">";
                $form .= F('Form' . $type, $item);
                $form .= "</div>";
                $form .= "</div>";
            }
        }
    }
    $form .= "<div style=\"clear:both;\"></div>";
    $form .= "<div class=\"FormSubmit\" style=\"margin:0 5px;\">";
    if ($caption) {
        $form .= FormInput(array(
            'type' => 'submit',
            'class' => 'Form FormButton btn btn-primary',
            'value' => $caption
        ));
    }
    $form .= $html;
    $form .= '</div>';
    $form .= '</form></div>';
    return $form;
}
function FormFill(&$item, $key, $values)
{
    if (is_array($item) && count($item)) {
        if (!(strlen($item[0]) && is_numeric($item[0]))) {
            array_unshift($item, 100);
        }
        while (count($item) < 6) {
            $item[] = '';
        }
        if (in_array(strtolower($item[1]), array(
            'checkbox',
            'listbox'
        ))) {
            $value = V($values[$item[2]]);
            if ($value) {
                $item[5] = FormOptions($item[5], explode('|', $value));
            }
        } elseif (in_array(strtolower($item[1]), array(
            'radiobox',
            'combobox'
        ))) {
            $value = V($values[$item[2]]);
            if ($value) {
                $item[5] = FormOptions($item[5], $value);
            }
        } else {
            $item[5] = V($values[$item[2]], $item[5]);
        }
    }
}
function FormData($form, $reset = false, $post = true)
{
    $data = array();
    foreach ($form as $item) {
        if (is_array($item)) {
            if (count($item) && is_numeric($item[0])) {
                array_shift($item);
            }
            if (count($item) > 1) {
                $data[$item[1]] = I($post ? V($_POST[$item[1]]) : V($_GET[$item[1]]), $reset ? V($item[4]) : '', true);
            }
        }
    }
    return $data;
}