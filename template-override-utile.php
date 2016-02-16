<?php
/**
 * Override utile sur les projets DRUPAL
 *  à placer dans le fichier template.php du theme  
 */
// ------------------------------------------------------------//
//  */ BLOCK RENDER --->                                       //
//  rendre un block en dur dans un fichier tpl.php             //
// ------------------------------------------------------------//

function block_render($module, $block_id) {
  $block = block_load($module, $block_id);
  $block_content = _block_render_blocks(array($block));
  $build = _block_get_renderable_array($block_content);
  $block_rendered = drupal_render($build);
  print $block_rendered;
}
    /**** DANS LE TPL.PHP -->
      
      <?php block_render('nommodule', 'idDuBloc') ?>

    *******/

// ------------------------------------------------------------//
//  */ PREPOCESS_PAGE --->                                     //
//  mettre tout les overrides dans la même fonction            //
// ------------------------------------------------------------//

function templateName_preprocess_page(&$vars, $hook) {

  //*************************************
  // If the node type is "blog_madness" the template suggestion will be "page--blog-madness.tpl.php".
  //*************************************

  if (isset($vars['node'])) {    
    $vars['theme_hook_suggestions'][] = 'page__'. $vars['node']->type;
  }

  //*******************************
  // rendu d'un field dans une page
  //*******************************
    
    if (isset($vars['node'])) :
        if($vars['node']->type == 'node' or 'nodetype' or 'foo'):
            $node = node_load($vars['node']->nid);
            $output = field_view_field('node', $node, 'field_name_of_field', array('label' => 'hidden'));
            $vars['field_name_of_field'] = $output;
        endif;
    endif;

    /**** DANS LE TPL.PHP -->

                <?php if ($field_name_of_field):
                    print render($field_name_of_field);
                endif;?>

    *****/


}

// ------------------------------------------------------------//
//  AJOUTER CLASS CSS EN FONCTION DE LA TAXONOMIE SUR UNE PAGE //
// ------------------------------------------------------------//

function taxonomy_node_get_terms($node, $key = 'tid') {
    if(arg(0)=='node' && is_numeric(arg(1))) {
        static $terms;
        if (!isset($terms[$node->vid][$key])) {
            $query = db_select('taxonomy_index', 'r');
            $t_alias = $query->join('taxonomy_term_data', 't', 'r.tid = t.tid');
            $v_alias = $query->join('taxonomy_vocabulary', 'v', 't.vid = v.vid');
            $query->fields( $t_alias );
            $query->condition("r.nid", $node->nid);
            $result = $query->execute();
            $terms[$node->vid][$key] = array();
            foreach ($result as $term) {
                $terms[$node->vid][$key][$term->$key] = $term;
            }
        }
        return $terms[$node->vid][$key];
    }
}
function templateName_preprocess_html(&$variables) {
    $node = node_load(arg(1));
    $results = taxonomy_node_get_terms($node);
    if(is_array($results)) {
        foreach ($results as $item) {
           $variables['classes_array'][] = "taxonomy-".strtolower(drupal_clean_css_identifier($item->name));
        }
    }
}

// --------------------------------------------------------------------------------------

// */ Les fichiers joint par champ fichier s'ouvrent en target="_blank"

function templateName_file_link($variables) {
  $file = $variables['file'];
  $icon_directory = $variables['icon_directory'];

  $url = file_create_url($file->uri);
  $icon = theme('file_icon', array('file' => $file, 'icon_directory' => $icon_directory));

  // Set options as per anchor format described at
  // http://microformats.org/wiki/file-format-examples
  $options = array(
      'attributes' => array(
          'type' => $file->filemime . '; length=' . $file->filesize,
      ),
  );

  // Use the description as the link text if available.
  if (empty($file->description)) {
    $link_text = $file->filename;
  } else {
    $link_text = $file->description;
    $options['attributes']['title'] = check_plain($file->filename);
  }

  //open files of particular mime types in new window
  $new_window_mimetypes = array('application/pdf', 'text/plain');
  if (in_array($file->filemime, $new_window_mimetypes)) {
    $options['attributes']['target'] = '_blank';
  }
  
  return '<span class="file">' . $icon . ' ' . l($link_text, $url, $options) . '</span>';

} 

// ------------------------------------------------------------//
//  */ OVERRIDE MENU --->                                      //
//  Changer la class de ul  + ul enfant                        //
// ------------------------------------------------------------//

function templateName_menu_tree__main_menu($variables) {
  return '<ul class="navbar-nav list-unstyled clearfix">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_menu_link().
 */
function templateName_menu_link(array $variables) {
  $element = $variables['element'];
  $sub_menu = '';

  if ($element['#below']) {
    // Wrap in dropdown-menu.
    unset($element['#below']['#theme_wrappers']);
    $sub_menu = '<ul class="sous-nav">' . drupal_render($element['#below']) . '</ul>';
  }
  $output = l($element['#title'], $element['#href'], $element['#localized_options']);
  return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

// ------------------------------------------------------------//
//  */ OVERRIDE SEARCH --->                                    //
//  Changer différent truc sur Search                          //
// ------------------------------------------------------------//

function templateName_form_search_block_form_alter(&$form, &$form_state, $form_id) {
    //$form['search_block_form']['#title'] = t('Rechercher'); // Change the text on the label element
    //$form['search_block_form']['#title_display'] = 'invisible'; // Toggle label visibilty
    $form['search_block_form']['#size'] = 20;  // define size of the textfield
    //$form['search_block_form']['#default_value'] = t('Search'); // Set a default value for the textfield
    $form['actions']['submit']['#value'] = t('GO!'); // Change the text on the submit button
    //$form['actions']['submit'] = array('#type' => 'image_button', '#src' => base_path() . path_to_theme() . '/images/search-button.png');

    // Add extra attributes to the text box
    //$form['search_block_form']['#attributes']['onblur'] = "if (this.value == '') {this.value = 'Search';}";
    //$form['search_block_form']['#attributes']['onfocus'] = "if (this.value == 'Search') {this.value = '';}";
    // Prevent user from searching the default text
    //$form['#attributes']['onsubmit'] = "if(this.search_block_form.value=='Search'){ alert('Please enter a search'); return false; }";

    // Alternative (HTML5) placeholder attribute instead of using the javascript
    $form['search_block_form']['#attributes']['placeholder'] = t('Rechercher');
} 
?>