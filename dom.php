<?php
require_once 'vendor/autoload.php';
use voku\helper\HtmlDomParser;
//echo voku\helper\HtmlDomParser::file_get_html('https://hdmovie2.tools/movies/demonic-2021-hindi-dubbed/')->plaintext;
$dom = HtmlDomParser::file_get_html('https://yomovies.work/gumraah-2023-Watch-online-full-movie/');
//print $dom;
// $iframes_arr = array();
           $iframes_arr = $dom->findOne('#tab1 iframe')->plaintext;
var_dump($iframes_arr);
exit;
$cover = array();

    $cover = $dom->findOne('#mv-info a.mvi-cover')->getAttribute('style');
   if(!$cover) $cover = $dom->findOne('#mv-info #content-cover')->getAttribute('style');
     preg_match('/\(([^)]+)\)/', $cover, $match);
    //$src[$i++] = $match[1];
     if ($cover) {
        print "<pre>";
    print_r($match[1]);
    print "</pre>";
     }
    
$des = array();

    $des = $dom->findOne('#mv-info .mvic-desc .desc .f-desc')->text();
    print "<pre>";
    print_r($des);
    print "</pre>";

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
  
print "<pre>";
    print_r($left);
    print "</pre>";

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

   
print "<pre>";
    print_r($right);
    print "</pre>";

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
  
print "<pre>";
    print_r($player2);
    print "</pre>";


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
  
print "<pre>";
    print_r($download_link);
    print "</pre>";


exit();
$data_attribute = [];

foreach ($dom->find('#content-embed #player2') as $meta) {
         $data_attribute[] = array(
           // $meta->getAttribute('data-nume'),
             $meta->findOne("iframe")->getAttribute('src'),
                        );
    
}

print "<pre>";
    print_r($data_attribute);
    print "</pre>";
 

    exit;
/*$imagesOrFalse = $dom->findMultiOrFalse('.cover');
if ($imagesOrFalse !== false) {
    foreach ($imagesOrFalse as $image) {
        echo $image->getAttribute('src') . "\n";
    }
}*/

$data_attribute1 = [];

foreach ($dom->find('meta') as $meta) {
    if ($meta->getAttribute('property')=='og:image') {
          $data_attribute1[] = array(
            $meta->getAttribute('content')

                        );
    }
}

print "<pre>";
    print_r($data_attribute1);
    print "</pre>";
$data_attribute = [];
$response ='';
foreach ($dom->find('#playeroptionsul li') as $meta) {
    if ($meta->hasAttribute('data-nume')) {

      //  $response = file_get_contents('https://hdmovie2.tools/wp-json/dooplayer/v2/73914/movie/'.$meta->getAttribute('data-nume'));


//echo $json->innertext;

//$response = json_decode($response);
          $data_attribute[] = array(
            $meta->getAttribute('data-nume'),
            $meta->findOne(".title")->text(),
            $response

                        );
    }
}

print "<pre>";
    print_r($data_attribute);
    print "</pre>";
 
//$articles=[];
/*print "<pre>";
    print_r($articles);
    print "</pre>";*/

    $articles = array();

    $items = $dom->find('#cast .persons');
    foreach($items as $post2) {
        foreach($post2 as $post) {
            if ($post->findOne(".person .img img")->hasAttribute('src')) {
        $articles[] = array(
            $post->findOne(".person .data .name")->text(),
            $post->findOne(".person .data .caracter")->text(),
            $post->findOne(".person .img img")->getAttribute('src')

                        );
    }
    }
    }
  
print "<pre>";
    print_r($articles);
    print "</pre>";

  /*  $ad= json_encode($articles);
$hdjh = json_decode($ad,true);
 foreach($hdjh as $item) {
        echo "<div class='item'>";
        echo $item[0];
        echo $item[1];
        echo "</div>";
    }*/
?>












