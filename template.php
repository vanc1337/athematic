<?php
/**
 * @file
 * Contains theme override functions and preprocess functions for the theme.
 *
 * ABOUT THE TEMPLATE.PHP FILE
 *
 *   The template.php file is one of the most useful files when creating or
 *   modifying Drupal themes. You can modify or override Drupal's theme
 *   functions, intercept or make additional variables available to your theme,
 *   and create custom PHP logic. For more information, please visit the Theme
 *   Developer's Guide on Drupal.org: http://drupal.org/theme-guide
 *
 * OVERRIDING THEME FUNCTIONS
 *
 *   The Drupal theme system uses special theme functions to generate HTML
 *   output automatically. Often we wish to customize this HTML output. To do
 *   this, we have to override the theme function. You have to first find the
 *   theme function that generates the output, and then "catch" it and modify it
 *   here. The easiest way to do it is to copy the original function in its
 *   entirety and paste it here, changing the prefix from theme_ to athematic_.
 *   For example:
 *
 *     original: theme_breadcrumb()
 *     theme override: athematic_breadcrumb()
 *
 *   where athematic is the name of your sub-theme. For example, the
 *   zen_classic theme would define a zen_classic_breadcrumb() function.
 *
 *   If you would like to override either of the two theme functions used in Zen
 *   core, you should first look at how Zen core implements those functions:
 *     theme_breadcrumbs()      in zen/template.php
 *     theme_menu_local_tasks() in zen/template.php
 *
 *   For more information, please visit the Theme Developer's Guide on
 *   Drupal.org: http://drupal.org/node/173880
 *
 * CREATE OR MODIFY VARIABLES FOR YOUR THEME
 *
 *   Each tpl.php template file has several variables which hold various pieces
 *   of content. You can modify those variables (or add new ones) before they
 *   are used in the template files by using preprocess functions.
 *
 *   This makes THEME_preprocess_HOOK() functions the most powerful functions
 *   available to themers.
 *
 *   It works by having one preprocess function for each template file or its
 *   derivatives (called template suggestions). For example:
 *     THEME_preprocess_page    alters the variables for page.tpl.php
 *     THEME_preprocess_node    alters the variables for node.tpl.php or
 *                              for node-forum.tpl.php
 *     THEME_preprocess_comment alters the variables for comment.tpl.php
 *     THEME_preprocess_block   alters the variables for block.tpl.php
 *
 *   For more information on preprocess functions and template suggestions,
 *   please visit the Theme Developer's Guide on Drupal.org:
 *   http://drupal.org/node/223440
 *   and http://drupal.org/node/190815#template-suggestions
 */

/*
 *   truncate_text takes as argument a string (to be ttruncated) an
 * integer at which to truncate the string, and an optional string to
 * append, by default this will be null.
 * 
 * This function is built by Guy Labbé only minor corrections and variable
 * renaming have been done herein.
 */

function truncate_text($string_to_trunc, $char_at_which_to_trunc, $append=NULL) {
     if(strlen($string_to_trunc) > $char_at_which_to_trunc) {
          $string_to_trunc = substr($string_to_trunc, 0, $char_at_which_to_trunc);
          $string_to_trunc .= $append;
     }
     return $string_to_trunc;
}


//What do I do?
function athematic_link($variables) {

  return '<a href="' . check_plain(url($variables['path'], $variables['options'])) . '"' . drupal_attributes($variables['options']['attributes']) . '>' . ($variables['options']['html'] ? $variables['text'] : check_plain($variables['text'])) . '</a>';
}

/**
 * Returns HTML for a menu link and submenu.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: Structured array data for a menu link.
 *
 * @ingroup themeable
 */

function athematic_menu_link(array $variables) {

  $element = $variables['element'];
  $sub_menu = '';

  if ($element['#below']) {
    $sub_menu = drupal_render($element['#below']);
  }
  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  
 
   
  $menuname = "main-menu"; // Drupal's name for the main menu
 $menu_link[] = NULL; // Array declaration for preg_match
 $reg_pat = "/<a href=\"(.*)\"(.*)class=\"(.*)\">/"; // regex pattern for a classed link
  
  
    //  Check if the element is part of the main menu and is a classed link
   if (strcasecmp($element['#original_link']['menu_name'], $menuname) == 0 && preg_match($reg_pat, $output, $menu_link) == 1) {
        
        $reg_pat = "/(.*)active(.*)/"; // Regex for active

        //  Check if the classed element is active
        if (preg_match($reg_pat, $menu_link['3']) == 1) {
            //Change active li element to have a css ID of current
            $ret_val = '<li id="current"' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "<div class='left-tab-bottom'><!-- Left tab bottom for theming --> <div class='right-tab-bottom'><!-- right tab bottom for theming --> </div> <!--/.right-tab-bottom--></div> <!-- /.left-tab-bottom --> </li>\n";
        } else {
            $ret_val = '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
        }
   } 
    else{
        $ret_val = '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
      }

return $ret_val;
}



/**
 * Returns HTML for a list of recent content.
 *
 * @param $variables
 *   An associative array containing:
 *   - nodes: An array of recent node objects.
 *
 * @ingroup themeable
 */

function athematic_node_recent_block($variables) {
  $rows = array();
  $output = '';

  $l_options = array('query' => drupal_get_destination());
  foreach ($variables['nodes'] as $node) {
    $row = array();
    $row[] = array(
      'data' => theme('node_recent_content', array('node' => $node)),
      'class' => 'title-author-content',
    );
    $row[] = array(
      'data' => node_access('update', $node) ? l(t('edit'), 'node/' . $node->nid . '/edit', $l_options) : '',
      'class' => 'edit',
    );
    $row[] = array(
      'data' => node_access('delete', $node) ? l(t('delete'), 'node/' . $node->nid . '/delete', $l_options) : '',
      'class' => 'delete',
    );
    $rows[] = $row;
  }

  if ($rows) {
    $output = theme('table', array('rows' => $rows));
    if (user_access('access content overview')) {
      $output .= theme('more_link', array('url' => 'admin/content', 'title' => t('Show more content')));
    }
  }

  return $output;
}

// I print out the article links
function athematic_links($variables) {
  $links = $variables['links'];

  $attributes = $variables['attributes'];
  $heading = $variables['heading'];
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    $output = '';

    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = array(
          'text' => $heading,
          // Set the default level of the heading.
          'level' => 'h2',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    $output .= '<ul' . drupal_attributes($attributes) . '>';

    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      $class = array($key);

      // Add first, last and active classes to the list of links to help out themers.
      if ($i == 1) {
        $class[] = 'first';
      }
      if ($i == $num_links) {
        $class[] = 'last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
          && (empty($link['language']) || $link['language']->language == $language_url->language)) {
        $class[] = 'active';
      }
      $output .= '<li' . drupal_attributes(array('class' => $class)) . '>';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        $output .= l($link['title'], $link['href'], $link);
      }
      elseif (!empty($link['title'])) {

        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $i++;
      $output .= "</li>\n ";
    }

    $output .= '</ul>';
  }

  return $output;
  

}

/**
 * Returns HTML for a form element.
 *
 * Each form element is wrapped in a DIV container having the following CSS
 * classes:
 * - form-item: Generic for all form elements.
 * - form-type-#type: The internal element #type.
 * - form-item-#name: The internal form element #name (usually derived from the
 *   $form structure and set via form_builder()).
 * - form-disabled: Only set if the form element is #disabled.
 *
 * In addition to the element itself, the DIV contains a label for the element
 * based on the optional #title_display property, and an optional #description.
 *
 * The optional #title_display property can have these values:
 * - before: The label is output before the element. This is the default.
 *   The label includes the #title and the required marker, if #required.
 * - after: The label is output after the element. For example, this is used
 *   for radio and checkbox #type elements as set in system_element_info().
 *   If the #title is empty but the field is #required, the label will
 *   contain only the required marker.
 * - invisible: Labels are critical for screen readers to enable them to
 *   properly navigate through forms but can be visually distracting. This
 *   property hides the label for everyone except screen readers.
 * - attribute: Set the title attribute on the element to create a tooltip
 *   but output no label element. This is supported only for checkboxes
 *   and radios in form_pre_render_conditional_form_element(). It is used
 *   where a visual label is not needed, such as a table of checkboxes where
 *   the row and column provide the context. The tooltip will include the
 *   title and required marker.
 *
 * If the #title property is not set, then the label and any required marker
 * will not be output, regardless of the #title_display or #required values.
 * This can be useful in cases such as the password_confirm element, which
 * creates children elements that have their own labels and required markers,
 * but the parent element should have neither. Use this carefully because a
 * field without an associated label can cause accessibility challenges.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #title_display, #description, #id, #required,
 *     #children, #type, #name.
 *
 * @ingroup themeable
 */

function athematic_form_element($variables) {
  $element = &$variables['element'];
  // This is also used in the installer, pre-database setup.
  $t = get_t();

  // This function is invoked as theme wrapper, but the rendered form element
  // may not necessarily have been processed by form_builder().
  $element += array(
    '#title_display' => 'before',
  );

  // Add element #id for #type 'item'.
  if (isset($element['#markup']) && !empty($element['#id'])) {
    $attributes['id'] = $element['#id'];
  }
  // Add element's #type and #name as class to aid with JS/CSS selectors.
  $attributes['class'] = array('form-item');
  if (!empty($element['#type'])) {
    $attributes['class'][] = 'form-type-' . strtr($element['#type'], '_', '-');
  }
  if (!empty($element['#name'])) {
    $attributes['class'][] = 'form-item-' . strtr($element['#name'], array(' ' => '-', '_' => '-', '[' => '-', ']' => ''));
  }
  // Add a class for disabled elements to facilitate cross-browser styling.
  if (!empty($element['#attributes']['disabled'])) {
    $attributes['class'][] = 'form-disabled';
  }
  $output = '<div' . drupal_attributes($attributes) . '>' . "\n";

  // If #title is not set, we don't display any label or required marker.
  if (!isset($element['#title'])) {
    $element['#title_display'] = 'none';
  }
  $prefix = isset($element['#field_prefix']) ? '<span class="field-prefix">' . $element['#field_prefix'] . '</span> ' : '';
  $suffix = isset($element['#field_suffix']) ? ' <span class="field-suffix">' . $element['#field_suffix'] . '</span>' : '';

  switch ($element['#title_display']) {
    case 'before':
    case 'invisible':
      $output .= ' ' . theme('form_element_label', $variables);
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;

    case 'after':
      $output .= ' ' . $prefix . $element['#children'] . $suffix;
      $output .= ' ' . theme('form_element_label', $variables) . "\n";
      break;

    case 'none':
    case 'attribute':
      // Output no label and no required marker, only the children.
      $output .= ' ' . $prefix . $element['#children'] . $suffix . "\n";
      break;
  }

  if (!empty($element['#description'])) {
    $output .= '<div class="description">' . $element['#description'] . "</div>\n";
  }

  $output .= "</div>\n";

  /*
   *  Kill the throbber css backgroud replacement for tags text-field.
   * 
   *   For some reason the form-autocomplete class gets its BG replaced 
   * universally when the throbber is called, this really messes up the tags
   * element if you don't catch it.  I caught it here but this is really a
   * messy way to do it, not sure what would be better.
   */

  if(!empty ($element['#field_name']) && $element['#field_name']=='field_tags'){
$output ='<div class="form-item form-type-textfield form-item-field-tags-und">' . 
  '<label for="edit-field-tags-und">Tags </label> <div id="input-text-wrapper">' . 
  '<!-- Wrapper for text input --><input type="text" id="edit-field-tags-und" name="field_tags[und]" value="" size="60" maxlength="1024" class="form-text form-autocomplete-tags" />' .
        '</div> <!-- /#input-text-wrapper --><input type="hidden" id="edit-field-tags-und-autocomplete" value="http://localhost/drupal-7.7/?q=taxonomy/autocomplete/field_tags" disabled="disabled" class="autocomplete" />'. 
'<div class="description">Enter a comma-separated list of words to describe your content.</div>
</div>';
  }

  return $output;
}

/**
 * Returns HTML for a textfield form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #description, #size, #maxlength,
 *     #required, #attributes, #autocomplete_path.
 *
 * @ingroup themeable
 */

function athematic_textfield($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'text';
  element_set_attributes($element, array('id', 'name', 'value', 'size', 'maxlength'));
  _form_set_class($element, array('form-text'));
   


  $extra = '';
  if ($element['#autocomplete_path'] && drupal_valid_path($element['#autocomplete_path'])) {
    drupal_add_library('system', 'drupal.autocomplete');
    $element['#attributes']['class'][] = 'form-autocomplete';

    $attributes = array();
  
    $attributes['type'] = 'hidden';
    $attributes['id'] = $element['#attributes']['id'] . '-autocomplete';
    $attributes['value'] = url($element['#autocomplete_path'], array('absolute' => TRUE));
    $attributes['disabled'] = 'disabled';
    $attributes['class'][] = 'autocomplete';

    $extra = '<input' . drupal_attributes($attributes) . ' />';
    
    
  }

  $output = '<div id="input-text-wrapper"><!-- Wrapper for text input --><input' . drupal_attributes($element['#attributes']) . ' /> </div> <!-- /#input-text-wrapper -->';

  return $output . $extra;
  
}

/**
 * Returns HTML for a password form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #description, #size, #maxlength,
 *     #required, #attributes.
 *
 * @ingroup themeable
 */

function athematic_password($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'password';
  element_set_attributes($element, array('id', 'name', 'size', 'maxlength'));
  _form_set_class($element, array('form-text'));

  return '<div id="input-text-wrapper"><!-- Wrapper for text input --><input' . drupal_attributes($element['#attributes']) . ' /> </div> <!-- /#input-text-wrapper -->';
}

/**
 * Returns HTML for a textarea form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #title, #value, #description, #rows, #cols, #required,
 *     #attributes
 *
 * @ingroup themeable
 */

function athematic_textarea($variables) {
  $element = $variables['element'];
  element_set_attributes($element, array('id', 'name', 'cols', 'rows'));
  _form_set_class($element, array('form-textarea'));

  $wrapper_attributes = array(
    'class' => array('form-textarea-wrapper'),
  );

  // Add resizable behavior.
 
  if (!empty($element['#resizable'])) {
    drupal_add_library('system', 'drupal.textarea');
    $wrapper_attributes['class'][] = 'resizable';
  }

  $output = '<div' . drupal_attributes($wrapper_attributes) . '><div class="text-area-top"></div> <!--/.text-area-top -->';
  $output .= '<textarea' . drupal_attributes($element['#attributes']) . '>' . check_plain($element['#value']) . '</textarea>';
  $output .= '<div class="text-area-bottom"></div> <!--/.text-area-bottom --></div>';
  return $output;
}

/**
 * Returns HTML for a breadcrumb trail.
 *
 * @param $variables
 *   An associative array containing:
 *   - breadcrumb: An array containing the breadcrumb links.
 */

function athematic_breadcrumb($variables) {
    $breadcrumb = $variables['breadcrumb'];

    if (!empty($breadcrumb)) {

        // Wrap each breadcrumb link with a div

        foreach ($breadcrumb as $bc_key => $bc_value) {
            if ($bc_key==0 && $bc_key != count($breadcrumb)-1)
            {
                $breadcrumb[$bc_key] = '<div class="breadcrumb-link-wrapper-first">&nbsp;&nbsp;' . $bc_value . '&nbsp;&nbsp;</div><!--/.breadcrumb-link-wrapper-->';
            }
            elseif($bc_key==0 && $bc_key == count($breadcrumb)-1)
            {
                $breadcrumb[$bc_key] = '<div class="breadcrumb-last-wrap-even"><div class="breadcrumb-link-wrapper-first">&nbsp;&nbsp;' . $bc_value . '&nbsp;&nbsp;</div></div><!--/.breadcrumb-link-wrapper,.breadcrumb-last-wrap-even-->';
            }
            elseif($bc_key == count($breadcrumb)-1 && $bc_key % 2 ==0)
            {
               $breadcrumb[$bc_key] = '<div class="breadcrumb-last-wrap-even"><div class="breadcrumb-link-wrapper-last-even">&nbsp;&nbsp;' . $bc_value . '&nbsp;&nbsp;</div></div><!--/.breadcrumb-link-wrapper,.breadcrumb-last-wrap-even-->';
            }
            elseif($bc_key == count($breadcrumb)-1 && $bc_key % 2 !=0)
            {
               $breadcrumb[$bc_key] = '<div class="breadcrumb-last-wrap-odd"><div class="breadcrumb-link-wrapper-last-odd">&nbsp;&nbsp;' . $bc_value . '&nbsp;&nbsp;</div></div><!--/.breadcrumb-link-wrapper,.breadcrumb-last-wrap-odd-->';
            }
            elseif ($bc_key % 2 ==0){
                $breadcrumb[$bc_key] = '<div class="breadcrumb-link-wrapper-even">&nbsp;&nbsp;' . $bc_value . '&nbsp;&nbsp;</div><!--/.breadcrumb-link-wrapper-->';
            }
            else{
                $breadcrumb[$bc_key] = '<div class="breadcrumb-link-wrapper-odd">&nbsp;&nbsp;' . $bc_value . '&nbsp;&nbsp;</div><!--/.breadcrumb-link-wrapper-->';

            }
        }

    // Provide a navigational heading to give context for breadcrumb links to
    // screen-reader users. Make the heading invisible with .element-invisible.
    $output = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

    $output .= '<div class=breadcrumb-wrapper> <div class="breadcrumb">' . implode('', $breadcrumb) . '</div></div><!-- /.breadcrumb, .breadcrumb-wrapper-->';
    return $output;
  }
}

/**
 * Returns HTML for a form.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #action, #method, #attributes, #children
 *
 * @ingroup themeable
 */

function athematic_form($variables) {

  $element = $variables['element'];
  if (isset($element['#action'])) {
    $element['#attributes']['action'] = drupal_strip_dangerous_protocols($element['#action']);
  }
  element_set_attributes($element, array('method', 'id'));
  if (empty($element['#attributes']['accept-charset'])) {
    $element['#attributes']['accept-charset'] = "UTF-8";
  }
   if ($element['#id']=='comment-form'){
       $element['subject']['#maxlength']=59;
       $form_return='<div id="comment-form-wrapper">  <h2 class="title comment-form"> Add new comment </h2>' .  '<form' . drupal_attributes($element['#attributes']) . '><div>' . $element['#children'] . '</div></form></div> <!-- /#comment-form-wrapper -->';
   }
   else
   {
       $form_return=  '<form' . drupal_attributes($element['#attributes']) . '><div>' . $element['#children'] . '</div></form>';
   }
   

  // Anonymous DIV to satisfy XHTML compliance.
  return $form_return;
}

/**
 * Returns HTML for a button form element.
 *
 * @param $variables
 *   An associative array containing:
 *   - element: An associative array containing the properties of the element.
 *     Properties used: #attributes, #button_type, #name, #value.
 *
 * @ingroup themeable
 */

function athematic_button($variables) {
  $element = $variables['element'];
  $element['#attributes']['type'] = 'submit';
  element_set_attributes($element, array('id', 'name', 'value'));

  $element['#attributes']['class'][] = 'form-' . $element['#button_type'];
  if (!empty($element['#attributes']['disabled'])) {
    $element['#attributes']['class'][] = 'form-button-disabled';
  }
  

  return '<input' . drupal_attributes($element['#attributes']) . ' />';
}


/**
 * Override or insert variables into the html templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("html" in this case.)
 */
/* -- Delete this line if you want to use this function
function athematic_preprocess_html(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // The body tag's classes are controlled by the $classes_array variable. To
  // remove a class from $classes_array, use array_diff().
  //$variables['classes_array'] = array_diff($variables['classes_array'], array('class-to-remove'));
}
// */

/**
 * Override or insert variables into the page templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("page" in this case.)
 */
/* -- Delete this line if you want to use this function
function athematic_preprocess_page(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the node templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("node" in this case.)
 */
/* -- Delete this line if you want to use this function
function athematic_preprocess_node(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');

  // Optionally, run node-type-specific preprocess functions, like
  // athematic_preprocess_node_page() or athematic_preprocess_node_story().
  $function = __FUNCTION__ . '_' . $variables['node']->type;
  if (function_exists($function)) {
    $function($variables, $hook);
  }
}
// */

/**
 * Override or insert variables into the comment templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("comment" in this case.)
 */
/* -- Delete this line if you want to use this function
function athematic_preprocess_comment(&$variables, $hook) {
  $variables['sample_variable'] = t('Lorem ipsum.');
}
// */

/**
 * Override or insert variables into the block templates.
 *
 * @param $variables
 *   An array of variables to pass to the theme template.
 * @param $hook
 *   The name of the template being rendered ("block" in this case.)
 */
/* -- Delete this line if you want to use this function
function athematic_preprocess_block(&$variables, $hook) {
  // Add a count to all the blocks in the region.
  $variables['classes_array'][] = 'count-' . $variables['block_id'];
}
// */
