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
   if(str_contains(@$node->get('field_player')->getValue()[$postData->tab]['value'], 'speedostream') || str_contains(@$node->get('field_player')->getValue()[$postData->tab]['value'], 'minoplres')) {
   $url = $node->get('field_url')->getValue()['0']['value'];
   }
  //  elseif($node->get('field_embed')->getValue()[0]['value'] && $node->get('field_player')->getValue()[$postData->tab]['value']){
  //   $url = $node->get('field_player')->getValue()[$postData->tab]['value'];
  //   $sandbox_if = 'eps';
  //  }
   elseif(isset($node->get('field_episodes')->getValue()[0]['value'])){
    $postData->tab = ($postData->tab==0)?0:$postData->tab-1;
    $url = $node->get('field_episodes')->getValue()[$postData->tab]['value'];
    $sandbox_if = 'eps';
   }elseif(isset($node->get('field_download_url')->getValue()[$postData->tab]['value'])){
    $url = $node->get('field_download_url')->getValue()[$postData->tab]['value'];
    $class_if = "class_if"; 
     $sandbox = 'allow-popups';
     $sandbox_if = '';
    }else{
      $class_if = "class_if";
      $postData->tab = ($postData->tab==0)?0:$postData->tab-1;
      $url = $node->get('field_player')->getValue()[$postData->tab]['value'];
      $sandbox_if = 'eps';
    }
      $new_var = theme_get_setting('iframe_new_domain_name');
      $oldStr = theme_get_setting('iframe_old_domain_name');
      $oldStr = explode(",", $oldStr);
   
  $url = str_replace($oldStr, $new_var, $url );

/*print $url;
exit();*/
//allow-popups
if($sandbox_if=='1'){
  print '<iframe scrolling="no" sandbox="'.$sandbox.' allow-forms allow-same-origin allow-scripts" class="'.$class_if.'" id="iframe-src" allowfullscreen src="'.$url.'" ></iframe>';
}elseif($sandbox_if=='eps'){
  print '<iframe scrolling="no" height="100%" width="100%" class="'.$class_if.'" id="iframe-src" allowfullscreen src="'.$url.'" ></iframe>';
 }else{
  print '<div class=""><a href="'.$url.'" target="_blank"><img style="width: 100%; margin-top: -121px;" src="/sites/default/files/click-to-watch.png"/></a></div>';
}
$json = file_get_contents('https://techto.life/test.php?url='.$url);
$obj = json_decode($json);
print_r($obj->first);
 // print_r($node->get('field_url')->value);
//print $rendered;
exit;
}

}