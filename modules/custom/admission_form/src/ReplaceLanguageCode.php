<?php

namespace Drupal\admission_form;
use Drupal\paragraphs\Entity\Paragraph;
use voku\helper\HtmlDomParser;
use Drupal\Component\Serialization\Json;

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
    /*print $node->field_trailer->value;
exit;*/
//if($node->field_left->value!='' && $node->field_trailer->value!=''){
  if($node->field_left->value!=''){
    return true;

  }

 
   $message2 = getmovie($node->field_url->value,$node->field_id->value);
  
/*print_r($message2['field_trailer']);
 
   exit;*/
  
    $results = array();

   //////////////////////////////////////////////
   if (isset($message2['field_poster_url'])) {
    $node->field_poster_url->value = $message2['field_poster_url'];
    }
     //////////////////////////////////////////////

    if (isset($message2['field_trailer'])) {
    $node->field_trailer->value = $message2['field_trailer'];
    }
     //////////////////////////////////////////////

    if (isset($message2['field_keyword'])) {
    $node->field_keyword->value = $message2['field_keyword'];
    }
     //////////////////////////////////////////////
    

    if (isset($message2['field_season_title'])) {
    $node->field_season_title->value = $message2['field_season_title'];
    }
     //////////////////////////////////////////////
  
  
   if ($message2['body']) {
    $node->body->value = $message2['body'];
    }
     //////////////////////////////////////////////
   $field_left =[];
       foreach($message2['field_left'] as $item) {
      $field_left[] = $item[0];
    }
   if (isset($field_left[0])) {
    $node->field_left = $field_left;
    }
      //////////////////////////////////////////////
   $field_right =[];
       foreach($message2['field_right'] as $item) {
      $field_right[] = $item[0];
    }
   if (isset($field_right[0])) {
    $node->field_right = $field_right;
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
   $field_episodes =[];
       foreach($message2['field_episodes'] as $item) {
      $field_episodes[] = $item[0];
    }
   if (isset($field_episodes[0])) {
    $node->field_episodes = $field_episodes;
    }
     //////////////////////////////////////////////
   $field_download_url =[];
       foreach($message2['field_download_url'] as $item) {
      $field_download_url[] = $item[0];
    }
   if (isset($field_download_url[0])) {
    $node->field_download_url = $field_download_url;
    }
   //////////////////////////////////////////////

  //  $node->set('langcode', 'de');
    $results[] = $node->save();
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

$cover = array();

    $cover = $dom->findOne('#mv-info a.mvi-cover')->getAttribute('style');
   if(!$cover) $cover = $dom->findOne('#mv-info #content-cover')->getAttribute('style');
     preg_match('/\(([^)]+)\)/', $cover, $match);
    //$src[$i++] = $match[1];
     if ($cover) {
      $cover = $match[1];
      
     }
$movie['field_poster_url'] = $cover;

    $des = array();

    $des = $dom->findOne('#mv-info .mvic-desc .desc .f-desc')->text();
    $movie['body'] = $des;

/*print_r($movie);
exit;*/
$left = array();

    $items = $dom->find('#mv-info .mvic-desc .mvic-info .mvici-left');
    foreach($items as $post2) {
        foreach($post2 as $post) {
           if($post->findOne("p")->text()){
        $left[] = array(
            $post->findOne("p")->text(),
          //  $post->findOne(".person .data .caracter")->text(),
          //  $post->findOne(".person .img img")->getAttribute('src')

                        );
    }
    
    }
    }
$movie['field_left'] = $left;

$right = array();

    $items = $dom->find('#mv-info .mvic-desc .mvic-info .mvici-right');
    foreach($items as $post2) {
        foreach($post2 as $post) {
           if($post->findOne("p")->text()){
        $right[] = array(
            $post->findOne("p")->text(),
          //  $post->findOne(".person .data .caracter")->text(),
          //  $post->findOne(".person .img img")->getAttribute('src')

                        );
    }
    
    }
    }

$movie['field_right'] = $right;

$keyword = array();

    $keyword = $dom->findOne('#mv-keywords')->text();
    

$movie['field_keyword'] = $keyword;

   $player2 = array();

    $items = $dom->find('#content-embed #player2');
    foreach($items as $post2) {
        foreach($post2 as $post) {
            if ($post->findOne("iframe")->hasAttribute('src')) {
        $player2[] = array(
            $post->findOne("iframe")->getAttribute('src'),
          //  $post->findOne(".person .data .caracter")->text(),
          //  $post->findOne(".person .img img")->getAttribute('src')

                        );
    }
    
    }
    }
$movie['field_player'] = $player2;

 $episodes = array();

    $items = $dom->find('#seasons .les-content');
    foreach($items as $post2) {
        foreach($post2 as $post) {
            if ($post->findOne("a")->hasAttribute('href')) {
        $episodes[] = array(
            $post->findOne("a")->getAttribute('href'),

                        );
    }
    
    }
    }
$movie['field_episodes'] = $episodes;

 $field_season_title = array(); 
    $field_season_title = $dom->findOne("#seasons .les-title")->text();
  $movie['field_season_title'] = $field_season_title;

  
  $field_trailer = array(); 
    $field_trailer = $dom->findOne("#iframe-trailer")->getAttribute('src');
  $movie['field_trailer'] = $field_trailer;



    

   $download_link = array();

    $items = $dom->find('#list-dl .embed-selector');
    foreach($items as $post2) {
        foreach($post2 as $post) {
            if ($post->findOne("a")->hasAttribute('href')) {
        $download_link[] = array(
            $post->findOne("a")->getAttribute('href'),
          //  $post->findOne(".person .data .caracter")->text(),
          //  $post->findOne(".person .img img")->getAttribute('src')

                        );
    }
    
    }
    }
     $movie['field_download_url'] = $download_link;
/*print "<pre>";
    print_r($articles);
    print "</pre>";*/
    return $movie;
}
