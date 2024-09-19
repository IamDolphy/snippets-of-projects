<?php


  $base_website = "https://www.foodnetwork.com/recipes/recipes-a-z/123";
  $folder = './';

  if(!$paging_index = file_get_contents($folder."/index-pagination")) {
    $food_network = file_get_contents($base_website);
    $index = null;
    preg_match_all('/(?<=<li class="o-IndexPagination__a-ListItem">).*(?=<\/li>)/im', $food_network, $index);
    $info = null;
    $search_urls = '';
    foreach($food_network[0] as $rollodeck)
    {
      preg_match_all('/(?<=href=").*?(?=")|(?<=>).*(?=<)/im', $rollodeck, $info);
      exec('mkdir -p '.$info[0][1]);// create the diretory if not exist
      $search_urls .= $info[0][0]. ','. $info[0][1]. "\n";
    }
    file_put_contents($folder.'/index-pagination', $search_urls);
    $paging_index = file_get_contents($folder."/index-pagination");
  }

  $item = [];
  foreach(explode("\n", $paging_index) as $line) {
    $x = explode(',', $line);
    $item[] = (object)[
      'link' => $x[0], 'folder' => $x[1]
    ];
  }

  foreach($item as $page) {
    $count = 1; $max_page = 0; $num = null;
    $file = file_get_contents($page->link);
    preg_match_all('/(?<=<section class="o-Pagination).*?(?=<\/section>)/s', $file, $num);
    preg_match_all('/(?<=>)\d?(?=<\/a>)/', $num[0][0], $num);
    $max_page = max($num[0]);
    file_put_contents($folder."/".$page->folder.'/index_'.$count, $file);
    while(($count += 1) <= $max_page) {
      $current_page = $page->link."/p/".$count;
      print $current_page ."\n";
      $indexing_page = file_get_contents($current_page);
      file_put_contents($folder."/".$page->folder.'/index_'.$count, $indexing_page);
      sleep(ceil(rand(5, 10)));
    }
  }


?>
