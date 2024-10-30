<?php

namespace LiveForms\__;

if (!defined("ABSPATH")) die("Shit happens!");

class Template
{
    public $data;
    public $tagRule = "/\{\{([^\}]+)\}\}/";
    public $tagRuleTemp = false;

    function setRule($rule)
    {
        $this->tagRuleTemp = $rule;
        return $this;
    }

    function fetch($template, $data)
    {
        if(is_file($template))
            $template = file_get_contents($template);

        $this->data = $data;
        $regex = $this->tagRuleTemp ? $this->tagRuleTemp : $this->tagRule;
        $compiled = preg_replace_callback($regex, [$this, '_var'], $template);
        $this->tagRuleTemp = false;
        return $compiled;
    }

    function _var($matched)
    {
        return wplf_valueof($this->data, $matched[1]);
    }

}