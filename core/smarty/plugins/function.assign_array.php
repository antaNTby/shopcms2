<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     assign_array
 * Purpose:  assign a value to a template array index
 * -------------------------------------------------------------
 */
function smarty_function_assign_array($params, &$smarty)
{
    extract($params);

    if (empty($var)) {
        $smarty->trigger_error("assign: missing 'var' parameter");
        return;
    }

    if (empty($index)) {
        $smarty->trigger_error("assign: missing 'index' parameter");
        return;
    }

    if (!in_array('value', array_keys($params))) {
        $smarty->trigger_error("assign: missing 'value' parameter");
        return;
    }

    $oldVal = $smarty->get_template_vars($var);

    $oldVal[$index] = $value;

    $smarty->assign($var, $oldVal);
}


?>
