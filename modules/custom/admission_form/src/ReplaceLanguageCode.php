<?php

namespace Drupal\admission_form;
use Drupal\paragraphs\Entity\Paragraph;
use voku\helper\HtmlDomParser;
use Drupal\Component\Serialization\Json;
use Drupal\pathauto\PathautoState;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\File\FileSystemInterface;

class ReplaceLanguageCode {
public static function replaceLangcode3($nid, &$context){
  $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

   $results[] = $node->delete();
 }

   public static function replaceLangcode2($nid, &$context){
 $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    $message = 'Replacing langcode(und to de)...';
     $results = array();
$title = $node->title->value;

preg_match('#\((.*?)\)#', $title, $match);

$string = intval($match[1]);

if (is_int($string) && $string!=0) {

$node->field_year->value = $string;

}
 $results[] = $node->save();
   }

  public static function replaceLangcode($nid, &$context){
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
    $message = 'Replacing langcode(und to de)...';
   if($node->isPublished()){

   }else{
    return true;
   }
    /*print $node->field_trailer->value;
exit;*/
//if($node->field_left->value!='' && $node->field_trailer->value!=''){
  if($node->field_player->value!='' && $node->field_movie_image->value!=''){
    return true;

  }

//  print $node->field_url->value;
//  exit;
   $message2 = getmovie($node->field_url->value,$node->field_id->value);
  
/*print_r($message2['field_trailer']);
 
   exit;*/
  
    $results = array();

   //////////////////////////////////////////////
  
   
     //////////////////////////////////////////////
     if($node->field_movie_image->value==''){
     $http = \Drupal::httpClient();
     $options = [
      'headers' => [
        'User-Agent' => 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:88.0) Gecko/20100101 Firefox/88.0',
        'Referer' => 'https://www.movieswatchonline0.com.pk/'
        ]
    ];
     $result = $http->request('get',$node->field_image_urls->value, $options);
     $body_data = $result->getBody()->getContents();
     

      $image_url = $node->field_image_urls->value;
      $file = file_save_data($body_data, "public://".\Drupal::service('file_system')->basename($image_url), FileSystemInterface::EXISTS_RENAME);
      
      $node->field_movie_image[] = [
        'target_id' => $file->id(),
        'alt' => 'Alt text',
        'title' => 'Title',
      ];
      }

  
  
   if ($message2['field_download_detail']) {
    $node->field_download_detail->value = preg_replace('/ style=("|\')(.*?)("|\')/','', $message2['field_download_detail']);
    }
    
    if ($message2['publushdate']) {
      $node->field_published->value = strtotime($message2['publushdate']);
      }
     //////////////////////////////////////////////
     $field_download_url =[];
     foreach($message2['field_download_url'] as $item) {
    $field_download_url[] = $item[0]."|".$item[1];
  }
 if (isset($field_download_url[0])) {
  $node->field_download_url = $field_download_url;
  }
    //////////////////////////////////////////////
   $field_player =[];
       foreach($message2['field_player'] as $item) {
      $field_player[] = $item[0];
    }
   if (isset($field_player[0])) {
    $node->field_player = $field_player;
    }
     //////////////////////////////////////////////
   $field_tags =[];
   
   foreach($message2['genre'] as $item) {
    $path = str_replace("https://www.movieswatchonline0.com.pk/category/","/genre/",$item[1]);
    $field_tags[] = tags_create($item[0],$path);
}
// print $field_tags;
 
 if (isset($field_tags[0])) {
$node->field_tags = $field_tags;
 }

     //////////////////////////////////////////////
  
   //////////////////////////////////////////////
   //print $node->changed->value;
  // $node->changed = $node->created->value;
   // $node->set('changed', $node->created->value);
    $results[] = $node->save();
    $connection = \Drupal::database();
    $query = $connection->update('node_field_data');
    $query->fields(array('changed' => $node->created->value)); 
    $query->condition('nid', $node->id());
    $query->execute();

    $query = $connection->update('node_field_revision');
    $query->fields(array('changed' => $node->created->value)); 
    $query->condition('nid', $node->id());
    $query->execute();
   /* $context['message'] = $message;
    $context['results'][] = $nid;*/
  }

 public static function replaceLangcodeFinishedCallback($success, $results, $operations) {
    // The 'success' parameter means no fatal PHP errors were detected. All
    // other error management should be handled using 'results'.
    if ($success) {
      $message = \Drupal::translation()->formatPlural(
        count($results),
        'One post processed.', '@count posts processed.'
      );
    }
    else {
      $message = t('Finished with an error.');
    }
    \Drupal::messenger()->addMessage($message);
  }


  
}

function getmovie($url='',$post_id='')
{
 
   $new_var = theme_get_setting('new_domain_name');
      $oldStr = theme_get_setting('old_domain_name');
      $oldStr = explode(",", $oldStr);
   
  $url = str_replace($oldStr, $new_var, $url );
  $movie = [];
  $dom = HtmlDomParser::file_get_html($url);

  $player2 = array();

  $items = $dom->find('#entry_info .singcont p');
  foreach($items as $post2) {
      foreach($post2 as $post) {
        if ($post->findOne("iframe")->hasAttribute('data-src')) {
      $player2[] = array(
        $post->findOne("iframe")->getAttribute('data-src'),
        );
  
                    }
  }
  }
$movie['field_player'] = $player2;
//////////////////////////////////////////////////////////
$genre = array();

$items = $dom->find('#entry_info .rightinfo p');
foreach($items as $post2) {
    foreach($post2 as $post) {
      if ($post->findOne("a")->getAttribute('itemprop')=='genre') {
    $genre[] = array(
      $post->findOne("a")->text(),
      $post->findOne("a")->getAttribute('href'),

                    );

                  }
}
}
$movie['genre'] = $genre;

//////////////////////////////////////////////////////////

$download_des = array();

$download_des = $dom->find('#entry_info .singcont', 1)->innerText();
 
$movie['field_download_detail'] = $download_des;

//////////////////////////////////////////////////////////

$download_link = array();

$items = $dom->find('#entry_info .singcont ', 1);
foreach($items as $post2) {
  $rep_link = '';
    foreach($post2 as $post) {
      if ($post->findOne("a")->hasAttribute('href')) {
       if($rep_link != $post->findOne("a")->getAttribute('href') )
    $download_link[] = array(
      $post->findOne("a")->text(),
      $post->findOne("a")->getAttribute('href'),

                    );
       $rep_link = $post->findOne("a")->getAttribute('href');
                  }     
}
}
$movie['field_download_url'] = $download_link;


//////////////////////////////////////////////////////////

$publushdate = array();

$publushdate = $dom->find('#entry_info meta', 1)->getAttribute('content');
$movie['publushdate'] = $publushdate;
//////////////////////////////////////////////////////////
// print "<pre>";
//     print_r($movie);
//     print "</pre>";
//     exit;



    return $movie;
}

function tags_create($cat,$path){
  
$storage = \Drupal::entityTypeManager()
  ->getStorage('taxonomy_term');
$terms = $storage->loadByProperties([ 
  'name' => $cat,
  'vid' => 'tags',
]);

if($terms == NULL) { //Create term and use
$created = _create_term($cat,'tags',$path);
if($created) {
//finding term by name
$storage = \Drupal::entityTypeManager()
  ->getStorage('taxonomy_term');
$newTerm = $storage->loadByProperties([ 
  'name' => $cat,
  'vid' => 'tags',
]);
$newTerm = reset($newTerm);
return !empty($newTerm) ? $newTerm->id() : '';
}
}
$terms = reset($terms);
return !empty($terms) ? $terms->id() : '';
}


function _create_term($name,$taxonomy_type,$path) {

$term = Term::create([
'name' => $name,
'vid' => $taxonomy_type,
'path' => [
  'alias' => $path,
  'pathauto' => PathautoState::SKIP,
],
])->save();
return TRUE;
}
