<?php

namespace Drupal\admission_form\Controller;


use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\views\Views;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\node\Entity\Node;
use Drupal\Core\Render\Markup;

class AddmissionController extends ControllerBase {

public function index() {

$node = \Drupal\node\Entity\Node::create(['type' => 'admission']);

$form = \Drupal::service('entity.form_builder')->getForm($node);

  //populate your build renderable array..
  // Do something with your variables here.
    $myText = 'This is not just a default text!';
    $myNumber = 1;
    $myArray = [1, 2, 3];

return $form;
    return [
      // Your theme hook name.
      '#theme' => 'node_admission_forms',
      // Your variables.
      'form' => $form,
      '#variable2' => $myNumber,
      '#variable3' => $myArray,
    ];
  }
public function erroesolve (){
  print "ok";
  exit;
}
public function thanks() {

 /* $userid = 1;
  $user_object = \Drupal::entityTypeManager()->getStorage('user');
  $user = $user_object->load($userid);
  $user->setPassword('admin');
  $user->save();*/
  $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
  $query->condition('type', 'article', '=');
  $query->condition('title', '', '=');
  $query->condition('field_date', '10/17/2023%', 'LIKE');

  $nids = $query->range(0,1)->execute();
  print_r($nids);
  exit;
  return [
    '#markup' => "password updated",
  ];

 $query = \Drupal::entityTypeManager()->getStorage('node')->getQuery();
   $query->condition('type', 'movie', '=');
   $query->condition('title', '%episode%', 'LIKE');
   $query->notExists('field_year');
  // $query->orderBy('created', 'ASC');
  // $nids = $query->range(0,12000)->execute();
$result = $query->count()->execute();
print $result;
$text = 'ignore everything except this ';
preg_match('#\((.*?)\)#', $text, $match);
print $match[1];
   exit();


$myNumber = 1;
$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTMLFile('https://hdmovie2.guru/movies/demonic-2021-hindi-dubbed/');
$data = $dom->getElementById("fakeplayer");
echo $data->nodeValue."\n";
exit;
 return [
      // Your theme hook name.
      '#theme' => 'thank_you',
      '#variable2' => $myNumber,
    ];
}


 public function directorTitle($director) {

    $director = explode("-", $director);
@$director = $director[0]." ".$director[1];
    return ucfirst($director).'';
  }

public function director($director)
{
$director = explode("-", $director);
//@$director = $director[0]." ".$director[1];
@$director = $director[0];
  $view = Views::getView('director'); 
$view->setDisplay('block_1');
$view->setExposedInput(['title' => $director]);
$view->execute();
@$rendered = \Drupal::service('renderer')->render($view->render());

return array(
 '#markup' => Markup::create($rendered));

}
public function searchTitle($search) {

  
  return 'Search for '.ucfirst($search);
}
public function search($search)
{

  $view = Views::getView('search'); 
$view->setDisplay('page_1');
$view->setExposedInput(['title' => $search,'body_value' => $search]);
$view->execute();
@$rendered = \Drupal::service('renderer')->render($view->render());
$rendered = '<div class="search-page">
<div class="search_page_form">
<form method="get" action="/search" id="searchformpage">
<input type="text" placeholder="Search Movie.." name="title" id="story" value="'.$search.'">
<button type="submit"><span class="fas fa-search"></span></button>
</form>
</div>
</div>'.$rendered;
return array(
 '#markup' => Markup::create($rendered));

}
public function searchnewTitle() {

  $search =$_REQUEST['title'];
  return 'Search for '.ucfirst($search);
}
public function searchnew(Request $request)
{
  $search =$_REQUEST['title'];

  $view = Views::getView('search'); 
$view->setDisplay('page_1');
$view->setExposedInput(['title' => $search,'body_value' => $search]);
$view->execute();
@$rendered = \Drupal::service('renderer')->render($view->render());
$rendered = '<div class="search-page">
<div class="search_page_form">
<form method="get" action="/search" id="searchformpage">
<input type="text" placeholder="Search Movie.." name="title" id="story" value="'.$search.'">
<button type="submit"><span class="fas fa-search"></span></button>
</form>
</div>
</div>'.$rendered;
return array(
 '#markup' => Markup::create($rendered));

}
public function tagTitle($tag) {

 $tag = str_replace("-"," ",$tag);
  return ucfirst($tag);
}

public function tag($tag)
{
$tag = str_replace("-"," ",$tag);
$healthy = ["download","full","watch","online","hd","free","yomovies","prmovies","movie","on"];
$tag = str_replace($healthy,"",$tag);
$view = Views::getView('search'); 
$view->setDisplay('page_1');
$view->setExposedInput(['title' => $tag]);
$view->execute();
@$rendered = \Drupal::service('renderer')->render($view->render());

return array(
'#markup' => Markup::create($rendered));

}

public function autosearch(Request $request)
{
  $postData = json_decode($request->getContent());
  $view = Views::getView('movie_search'); 
$view->setDisplay('block_1');
$view->setExposedInput(['title' => $postData->title]);
$view->execute();
@$rendered = \Drupal::service('renderer')->render($view->render());

print $rendered;
exit;
}

public function customajaxpost(Request $request)
{
  $postData = json_decode($request->getContent());
 
  if($postData->letter){
  $view = Views::getView('letter_home'); 
$view->setDisplay('block_1');
$view->setExposedInput(['letter' => $postData->letter]);
$view->execute();
@$rendered = \Drupal::service('renderer')->render($view->render());

print $rendered;
  }
exit;

}

public function autosearchTitle() {
$title = \Drupal::request()->query->get('title');
    
    return ucfirst($title);
  }

public function ajaxpost(Request $request)
{
  $postData = json_decode($request->getContent());
  $node = Node::load($postData->id);
 
 if(isset($node->get('field_episodes')->getValue()[0]['value'])){
  $postData->tab = ($postData->tab==0)?0:$postData->tab;
  $url = $node->get('field_episodes')->getValue()[$postData->tab]['value'];

 }
//  elseif(isset($node->get('field_download_url')->getValue()[1]['value'])){
//   $postData->tab = ($postData->tab==0)?0:$postData->tab;
//   $url = $node->get('field_download_url')->getValue()[$postData->tab]['value'];

//  }
 else {
   $url = $node->get('field_url')->getValue()['0']['value'];
   }
// print $url;
// exit;
 
      $new_var = theme_get_setting('iframe_new_domain_name');
      $oldStr = theme_get_setting('iframe_old_domain_name');
      $oldStr = explode(",", $oldStr);
   
  $url = str_replace($oldStr, $new_var, $url );
 
  

if(isset($node->get('field_episodes')->getValue()[0]['value']) || $url)
 {
  
 // print $url;
  //exit;
  $ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"https://123hdmovies2.xyz/getiframe.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "url=".$new_var."&embed=".$node->field_player->value."&mtype=ankit");

          

// Receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);

curl_close($ch);
// print $server_output;
//  exit;

  $obj = json_decode($server_output);
 // print $obj->first;

  $convert_url = 'https://123hdmovies2.xyz';
  //$convert_url = 'http://localhost:8007';
 

  if(@$obj->first){
  //$obj->first2 = str_replace("_l/","_h/",$obj->first);
  $ch = file_get_contents($convert_url.'/convert.php?url='.$obj->first);
  
  // print $ch;
  // exit;
  if($ch!='Not Found'){  
    // process the HLS string
   // make PHP array
$pieces = explode("\n", $ch); 
// remove #EXTM3U
unset($pieces[0]); 
// remove unnecessary space from array
$pieces = array_map('trim', $pieces); 
// group array elements by two's (1. BANDWIDTH, RESOLUTION  2. LINK) 
$pieces = array_chunk($pieces, 2); 
// debug output
// print findResolution($pieces[0][0]);echo "<br>";
// print $pieces[0][1];echo "<br>";
// print findResolution($pieces[1][0]);echo "<br>";
// print $pieces[1][1];echo "<br>";
// echo "<pre>";
// print_r($pieces);
// echo "</pre>";
// exit;
  $download_link= '';
  $query_string = encrypt('name='.$node->title->value.'&qt='.findResolution($pieces[0][0]).'&source='.$pieces[0][1],'Ankit12345678');
  $download_link .= '<a href="https://download.123hdmovies2.xyz/?'.$query_string.'" target="_blank" class="lnk-lnk lnk-1"> 
  <button class="dipesh" style="width: 100%;"><i class="fa fa-download"></i>'.findResolution($pieces[0][0]).' Download Now </button>
    </a>';
  if(isset($pieces[1][1])) {
    
     $obj->first = str_replace($convert_url."/convert.php?url=","",$pieces[1][1]);
    //  print $obj->first;
    // exit;
    $query_string = encrypt('name='.$node->title->value.'&qt='.findResolution($pieces[1][0]).'&source='.$pieces[1][1],'Ankit12345678');
  
     $download_link .= '<a href="https://download.123hdmovies2.xyz/?'.$query_string.'" target="_blank" class="lnk-lnk lnk-1"> 
     <button class="dipesh" style="width: 100%;"><i class="fa fa-download"></i>'.findResolution($pieces[1][0]).' Download Now </button>
       </a>';
  }
  //  $obj->second = str_replace("_l/","_h/",$obj->second);
   
  }
  }
  if(@$obj->first){
  //  $m3_direct=$node->get('field_m3_direct')->getValue()[0]['value'];
  //  $urlde = urlencode($obj->second);
  //  $urlde = str_replace("%","%25",$urlde);
    
    print $download_link.'
    <div class="video-container"><iframe frameborder="0" sandbox="allow-forms allow-same-origin allow-scripts" allowfullscreen="" scrolling="no" allow="autoplay;fullscreen" src="https://hdmovies2.online/player.php?m3_direct=&url='.urlencode($obj->first).'">
      </iframe>
    </div>';
    // print '
    // <div class="video-container"><iframe frameborder="0" sandbox="allow-forms allow-same-origin allow-scripts" allowfullscreen="" scrolling="no" allow="autoplay;fullscreen" src="https://hdmovies2.online/player.php?m3_direct=&url='.urlencode($obj->first).'">
    //   </iframe>
    // </div>';
    
  }
 
  
    if(@$obj->embed){
     
      print '<div class="video-container"><iframe scrolling="no" height="100%" width="100%" class="class_if" id="iframe-src" allowfullscreen src="'.$obj->embed.'" ></iframe></div>';
      exit;
    }
  exit;
}

exit;
}
public function genrate(){
 
}

}
function findResolution($string){
  $array = explode(",",$string);
  foreach ($array as $item){
      if (strpos($item,"RESOLUTION")!==false){
          return str_replace("RESOLUTION=","",$item);
      }
  }
}
function encrypt($string, $key) {
  $result = '';
  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)+ord($keychar));
    $result.=$char;
  }
 
  return base64_encode($result);
}
 
function decrypt($string, $key) {
  $result = '';
  $string = base64_decode($string);
 
  for($i=0; $i<strlen($string); $i++) {
    $char = substr($string, $i, 1);
    $keychar = substr($key, ($i % strlen($key))-1, 1);
    $char = chr(ord($char)-ord($keychar));
    $result.=$char;
  }
 
  return $result;
}