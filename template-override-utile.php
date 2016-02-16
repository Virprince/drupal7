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
      
      <?php block_render(‘nommodule', 'idDuBloc') ?>

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
        if($vars['node']->type == 'page_index' or 'page_realisation' or 'page_prestation' or 'article' or 'page'):
            $node = node_load($vars['node']->nid);
            $output = field_view_field('node', $node, 'field_img_head', array('label' => 'hidden'));
            $vars['field_img_head'] = $output;
        endif;
    endif;

    /**** DANS LE TPL.PHP -->

                <?php if ($content['field_img_head'] = render($content['field_img_head'])):
                     print render($field_img_head);
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