<?php
	$dataString = implode($dataSeparator, $data);
    $category = urldecode($dataString);
    
    $query = $db->prepare('SELECT id, title, date FROM blog_articles WHERE categories LIKE :categoryPattern ORDER BY id DESC');
    $query->bindValue(':categoryPattern', '%'.$category.'%');
    $query->execute();
    $rows = $query->fetchAll(PDO::FETCH_OBJ);
    $query->closeCursor();
    $query = NULL;
    
    $articles = array();
    
    foreach($rows as $row)
    {
        $id = $row->id;
        $title = $row->title;
        $date = $row->date;
        $articles[] = array('id' => $id, 'title' => $title, 'date' => $date);
    }
	
    $pageTitle .= ' - Catégorie : '.$category;
	$pageCanonicalLink = '/blog/category-'.urlencode($category);
    $pagePath[] = array('Blog', '/blog');
    $pagePath[] = array('Catégorie : '.$dataString, '/blog/category-'.urlencode($category));
?>