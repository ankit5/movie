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

  
  return ucfirst($search).' for search';
}
public function search($search)
{

  $view = Views::getView('search'); 
$view->setDisplay('page_1');
$view->setExposedInput(['title' => $search]);
$view->execute();
@$rendered = \Drupal::service('renderer')->render($view->render());

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

public function autosearchTitle() {
$title = \Drupal::request()->query->get('title');
    
    return ucfirst($title);
  }

public function ajaxpost(Request $request)
{
  $postData = json_decode($request->getContent());
  $node = Node::load($postData->id);
  
  
   
   $class_if = '';
   $sandbox = '';
   $sandbox_if = '1';
 // $url = $node->get('field_url')->value;
 if(isset($node->get('field_episodes')->getValue()[0]['value'])){
  $postData->tab = ($postData->tab==0)?0:$postData->tab;
  $url = $node->get('field_episodes')->getValue()[$postData->tab]['value'];
  $sandbox_if = 'eps';
 }else {
   $url = $node->get('field_url')->getValue()['0']['value'];
   }
  //  elseif($node->get('field_embed')->getValue()[0]['value'] && $node->get('field_player')->getValue()[$postData->tab]['value']){
  //   $url = $node->get('field_player')->getValue()[$postData->tab]['value'];
  //   $sandbox_if = 'eps';
  //  }
 
      $new_var = theme_get_setting('iframe_new_domain_name');
      $oldStr = theme_get_setting('iframe_old_domain_name');
      $oldStr = explode(",", $oldStr);
   
  $url = str_replace($oldStr, $new_var, $url );
  // print $url;
  // exit;

  if($postData->link){
    print '<div class="dl-des">Download Icon-&gt; Right Click -&gt; Open link in new tab</div>
    <iframe frameborder="0" width="1200" scrolling="no" src="'.$url.'#list-dl" id="miframe" sandbox="allow-forms allow-same-origin allow-scripts"></iframe>
    '; 
    
    exit;
  }

  // print $url;
  // exit();
//allow-popups
// if($sandbox_if=='1'){
//   print '<iframe scrolling="no" sandbox="'.$sandbox.' allow-forms allow-same-origin allow-scripts" class="'.$class_if.'" id="iframe-src" allowfullscreen src="'.$url.'" ></iframe>';
// }elseif($sandbox_if=='eps'){
//   print '<iframe scrolling="no" height="100%" width="100%" class="'.$class_if.'" id="iframe-src" allowfullscreen src="'.$url.'" ></iframe>';
//  }else{
//   print '<div class=""><a href="'.$url.'" target="_blank"><img style="width: 100%; margin-top: -121px;" src="/sites/default/files/click-to-watch.png"/></a></div>';
// }
//$json = file_get_contents('https://techto.life/test.php?url='.$url);
//$obj = json_decode($json);
// print $url;
// print_r($json);
//   exit();
// $html = '<div id="direct-link">
// <h3>Direct link watch in Player</h3>
// <div class="direct-desktop">How to use in Desktop:</div>
// </div>';
if($node->field_m3_direct->value=="yes" || isset($node->get('field_episodes')->getValue()[0]['value']) || str_contains(@$node->get('field_player')->getValue()[$postData->tab]['value'], 'speedostream') || str_contains(@$node->get('field_player')->getValue()[$postData->tab]['value'], 'minoplres')){
 
  $ch = curl_init();

curl_setopt($ch, CURLOPT_URL,"http://13.200.103.33/hello.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,
            "url=".$url."&mtype=ankit");

// In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));

// Receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$server_output = curl_exec($ch);

curl_close($ch);


// Further processing ...
//var_dump($server_output);
// exit;
// $json = file_get_contents('http://13.200.103.33/hello.php?url='.$url);
  $obj = json_decode($server_output);
  if(@$obj->first){
    foreach($node->get('field_right')->getValue() as $value){
      if(str_contains($value['value'], 'Quality:')){
       $qua = str_replace("Quality:","",$value['value']);
      }
             
    }
    $m3_direct=$node->get('field_m3_direct')->getValue()[0]['value'];
    $urlde = urlencode($obj->second);
    $urlde = str_replace("%","%25",$urlde);
    print '<a href="'.$obj->first.'" style="display:none;">Link1</a>
    <a href="'.$obj->second.'" style="display:none;">Link2</a>
    <iframe frameborder="0" sandbox="allow-forms allow-same-origin allow-scripts" allowfullscreen="" scrolling="no" allow="autoplay;fullscreen" src="https://hdmovies2.online/player.php?m3_direct='.$m3_direct.'&url='.urlencode($obj->first).'"></iframe>
      <div id="list-dl" class="tab-pane active">
      <a data-toggle="tab" href="https://m3u8downloader.hdmovies2.online/?name='.$node->title->value.'&source=https://hdmovies2.online/convert.php?url='.$obj->second.'" aria-expanded="true" style="
"><span class="lnk lnk-dl" id="lnk-dl-button" target="_blank" style="
    width: 103px;
    display: flex;
"><i class="fa fa-download" aria-hidden="true"></i> <span class="dl_tit" style="
    display: block;
">Download</span></span></a>
      <div id="lnk list-downloads">
      <div class="btn-group btn-group-justified embed-selector" style="margin-bottom:1px;"> 
      <span style="" class="lnk lnk-title">Server</span>
       <span class="lnk lnk-title">Language</span>
        <span class="lnk lnk-title">Quality</span> 
        <span class="lnk lnk-title" role="" target="_blank">Links</span>
        </div>
      <div class="btn-group btn-group-justified embed-selector">
      <a href="https://m3u8downloader.hdmovies2.online/?name='.$node->title->value.'&source=https://hdmovies2.online/convert.php?url='.$obj->second.'" target="_blank" class="lnk-lnk lnk-1"> <span style="" class="lnk lnk-dl"><img style="" src="https://www.google.com/s2/favicons?domain=HD" alt="HD"> <span class="serv_tit">HD</span></span> <span class="lnk lnk-dl">
       <span class="lang_tit">
        '.$qua.'
                 </span></span> <span class="lnk lnk-dl">HD</span> <span class="lnk lnk-dl" id="lnk-dl-button" target="_blank"><i class="fa fa-download" aria-hidden="true"></i> <span class="dl_tit">Download</span></span> </a></div></div></div>
     
</div>
</div>
</div>
      ';
  }
 
    
    if(@$obj->embed){
      print '<iframe scrolling="no" height="100%" width="100%" class="class_if" id="iframe-src" allowfullscreen src="'.$obj->embed.'" ></iframe>';
    }
  exit;
}


if(@$node->get('field_player')->getValue()[$postData->tab]['value']){
  print '<iframe scrolling="no" height="100%" width="100%" class="class_if" id="iframe-src" allowfullscreen src="'.$node->get('field_player')->getValue()[$postData->tab]['value'].'" ></iframe>';
  exit;
}
$json = file_get_contents('http://13.200.103.33/hello.php?url='.$url);
$obj = json_decode($json);
if($obj->embed){
  print '<iframe scrolling="no" height="100%" width="100%" class="class_if" id="iframe-src" allowfullscreen src="'.$obj->embed.'" ></iframe>';
}
  //print_r( urlencode($obj->first));

 // print_r($node->get('field_url')->value);
//print $rendered;
exit;
}
public function genrate(){
 
}

}