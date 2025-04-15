<?php

namespace Drupal\admission_form;
use Drupal\paragraphs\Entity\Paragraph;
use voku\helper\HtmlDomParser;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Routing\TrustedRedirectResponse;

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

   public static function imageupdate2($nid, &$context){
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
       $message = 'Replacing langcode(und to de)...';
        $results = array();
   $title = $node->title->value;
   if ($node->field_image_urls->value) {
   
    $ch = curl_init($node->field_image_urls->value);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 2);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
curl_exec($ch);
if(curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200)
{
 
 // return true;  // Found Image
}else{
  $message2 = getmovie($node->field_url->value,$node->field_id->value);
 if (isset($message2['field_image_urls']) && strlen($message2['field_image_urls'])<600) {
      $node->field_image_urls->value = $message2['field_image_urls'];
      }
      if (isset($message2['field_poster_url']) && strlen($message2['field_poster_url'])<600) {
        $node->field_poster_url->value = $message2['field_poster_url'];
        }
        $results[] = $node->save();
}
   }elseif($node->field_image_urls->value=='' || $node->field_poster_url->value==''){
    $message2 = getmovie($node->field_url->value,$node->field_id->value);
    if (isset($message2['field_image_urls']) && strlen($message2['field_image_urls'])<600) {
      $node->field_image_urls->value = $message2['field_image_urls'];
      }
      if (isset($message2['field_poster_url']) && strlen($message2['field_poster_url'])<600) {
        $node->field_poster_url->value = $message2['field_poster_url'];
        }
        
        $results[] = $node->save();
   }
   
   
      }
      public static function imageupdate($nid, &$context){
        $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
           $message = 'Replacing langcode(und to de)...';
            $results = array();
       $title = $node->title->value;
       if ($node->field_image_urls->value) {
        $message2 = getmovie($node->field_url->value,$node->field_id->value);
        if (isset($message2['field_image_urls'])) {
          $node->field_image_urls->value = $message2['field_image_urls'];
          
          }

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
        
       }
      }
    
  public static function replaceLangcode($nid, &$context){

    //print getAdcashLibTag();
    //$node = \Drupal::entityTypeManager()->getStorage('node');
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $node = $storage->load($nid);
    $message = 'Replacing langcode(und to de)...';
    
//  if($m3_direct=$node->field_m3_direct->value=="yes"){
//   return true; 
//  }
  $load ='';
  
  $field_tags =array();
foreach($node->get('field_tags')->getValue() as $key=>$value){
  $field_tags[$value['target_id']] = $value['target_id'];
}
// print $_SERVER['HTTP_REFERER'];
// exit;
if(array_key_exists('94',$field_tags)){ //punjabi
  if($_SERVER['SERVER_NAME']!='hdmovie20.lat'){
  //return new TrustedRedirectResponse('https://hdmovie20.lat'.$node->toUrl()->toString());
  header('Location: https://hdmovie2.gay'.$node->toUrl()->toString());
  // print $node->toUrl()->toString();
  // exit;
  throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
  }
}
if(strpos($node->title->value, 'Altbalaji')){
  if($_SERVER['SERVER_NAME']!='hdmovie20.lat'){
    header('Location: https://hdmovie2.gay'.$node->toUrl()->toString());
  throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
  }
}
if($node->status->value==0){
  if($_SERVER['SERVER_NAME']!='hdmovie20.lat'){
    header('Location: https://hdmovie2.gay'.$node->toUrl()->toString());
  throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
  }
}
//print $_SERVER['SERVER_NAME']; //hdmovie2.golf
//print $node->status->value; 
  if(array_key_exists('91',$field_tags) || str_contains(@$node->field_url->value,'/series')){
    
    if(!$node->field_load_time->value){
      $load =1;
    }
    if(strtotime("+4 days", $node->field_load_time->value) < time()){
      $load =1;
    }
  } 
  if($node->field_left->value==''){
    $load =1;
  } 
  if($node->field_image_urls->value==''){
    $load =1;
  }
  if(@str_contains($node->field_player->value, 'movembed')){
    $load =1;
  }
  if(@$_REQUEST['load']=='1'){
    $load =1;
  }
  if($load==1){
    $node->field_load_time->value = time();
  }
//  exit;
if($load==''){ return true; }
//print ".";
   $message2 = getmovie($node->field_url->value,$node->field_id->value);

// print "load";
/*print_r($message2['field_trailer']);
 
   exit;*/
  
    $results = array();

   //////////////////////////////////////////////
   if (isset($message2['field_poster_url'])) {
    $node->field_poster_url->value = $message2['field_poster_url'];
    }
    //////////////////////////////////////////////
   if (isset($message2['field_image_urls'])) {
    $node->field_image_urls->value = $message2['field_image_urls'];
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
      $field_left[] = substr($item[0],0,599);
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
   $field_player_text =[];
   foreach($message2['field_player_text'] as $item) {
  $field_player_text[] = $item[0];
}
if (isset($field_player_text[0])) {
$node->field_player_text = $field_player_text;
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
   //print $node->changed->value;
  // $node->changed = $node->created->value;
   // $node->set('changed', $node->created->value);
   $changed = $node->changed->value;
    $results[] = $node->save();
    $connection = \Drupal::database();
    $query = $connection->update('node_field_data');
    $query->fields(array('changed' => $changed)); 
    $query->condition('nid', $node->id());
    $query->execute();

    $query = $connection->update('node_field_revision');
    $query->fields(array('changed' => $changed)); 
    $query->condition('nid', $node->id());
    $query->execute();
    $storage->resetCache([$node->id()]);
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
  
$curl = curl_init();
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_REFERER, 'https://'.$new_var);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:88.0) Gecko/20100101 Firefox/88.0");
$str = curl_exec($curl);
curl_close($curl);
$dom = HtmlDomParser::str_get_html($str);

$cover = array();

    $cover = $dom->findOne('#mv-info a.mvi-cover')->getAttribute('style');
   if(!$cover) $cover = $dom->findOne('#mv-info #content-cover')->getAttribute('style');
     preg_match('/\(([^)]+)\)/', $cover, $match);
    //$src[$i++] = $match[1];
     if ($cover) {
      $cover = $match[1];
      
     }
$movie['field_poster_url'] = $cover;

$field_image_urls = array(); 
$field_image_urls = $dom->findOne(".mvic-thumb img")->getAttribute('src');
$movie['field_image_urls'] = $field_image_urls;

    $des = array();

    $des = $dom->findOne('#mv-info .mvic-desc .desc .f-desc')->text();
    $movie['body'] = $des;

/*print_r($movie);
exit;*/
$left = array();

    $items = $dom->find('#mv-info .mvic-desc .mvic-info .mvici-left');
    foreach($items as $post2) {
      foreach($post2 as $post) {
         if($post->text()){
          foreach($post->find("p") as $post3) {
      $left[] = array($post3->findOne("p")->text());
      //  $post->findOne(".person .data .caracter")->text(),
      //  $post->findOne(".person .img img")->getAttribute('src')
          }
                  }
  
  }
  }
$movie['field_left'] = $left;
//print_r($left);
$right = array();

    $items = $dom->find('#mv-info .mvic-desc .mvic-info .mvici-right');
    foreach($items as $post2) {
        foreach($post2 as $post) {
           if($post->text()){
            foreach($post->find("p") as $post3) {
        $right[] = array($post3->findOne("p")->text());
        //  $post->findOne(".person .data .caracter")->text(),
        //  $post->findOne(".person .img img")->getAttribute('src')
            }
                    }
    
    }
    }
//     print "<pre>";
// print_r($right);
// exit;
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

$field_player_text = array();

$items = $dom->find('.idTabs li');
foreach($items as $post2) {
    foreach($post2 as $post) {
        if ($post->findOne(".les-title")->text()) {
    $field_player_text[] = array(
        $post->findOne(".les-title")->text(),

                    );
}

}
}

$movie['field_player_text'] = $field_player_text;

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
function getAdcashLibTag()
{
   
}
