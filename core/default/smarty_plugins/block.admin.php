<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.admin.php
 * Type:     block
 * Name:     admin
 * Purpose:  wrap in correct admin templates
 * -------------------------------------------------------------
 */
function smarty_block_admin($params, $content, &$smarty, &$repeat)
{
    
    // only output on the closing tag
    if(!$repeat){
        if (isset($content)) {
            
            if((bool)$params['editor']){
              $smarty->assign("ZZZZeditor",true);
            } else {
              $smarty->assign("ZZZZeditor",false);            }
            
                        $tpl_vars		= $smarty->_tpl_vars;

			$admin_theme_path           = $tpl_vars['admin_theme_path'];
			$admin_theme_global_path    = $tpl_vars['admin_theme_global_path'];

                        $default_admin_theme_path           = $tpl_vars['default_admin_theme_path'];
			$default_admin_theme_global_path    = $tpl_vars['default_admin_theme_global_path'];

			$admin_url 		= $tpl_vars['admin_theme_url'];
			$current_path		= $tpl_vars['current_path'];
			
			$smarty->assign("ZZZZtitle",$params['title']);			
			$smarty->assign("ZZZZcontent",$content);

			$context_nav = $current_path . "/_nav.tpl";	
			$context_action_nav = $current_path . "/_nav_".$tpl_vars['action_name'].".tpl";
                        $default_context_nav = "";
                        $default_context_action_nav = "";

                        
                        // first, see if we have what we need in the current admin theme
			if(file_exists($context_action_nav)){
				$context_nav_to_use = $context_action_nav;
			}else{
				if(file_exists($context_nav)){
					$context_nav_to_use = $context_nav;
				} else {
                                    // we don't, try the default admin theme

                                }
			}
                        if(isset($context_nav_to_use)){
                            $nav_markup = $smarty->fetch($context_nav_to_use);
                            $smarty->assign("ZZZZnav", $nav_markup);
                        }

            switch($params['layout']){
            	case "1-col":
            	
            	break;
            	
            	case "3-col":
            	
            	default:
                    if(file_exists($admin_theme_global_path."/_header.tpl")){
                        $content = $smarty->fetch($admin_theme_global_path."/_2-col.tpl");
                    } else {
                        $content = $smarty->fetch($default_admin_theme_global_path."/_2-col.tpl");
                    }

            	break;
            }

            if(file_exists($admin_theme_global_path."/_header.tpl")){
                $header = $smarty->fetch($admin_theme_global_path."/_header.tpl");
            } else {
                $header = $smarty->fetch($default_admin_theme_global_path."/_header.tpl");
            }


            if(file_exists($admin_theme_global_path."/_footer.tpl")){
                $footer = $smarty->fetch($admin_theme_global_path."/_footer.tpl");
            } else {
                $footer = $smarty->fetch($default_admin_theme_global_path."/_footer.tpl");
            }
            
            $content = $header . $content . $footer;
                                    
            return $content;
            
        }
    }
}
