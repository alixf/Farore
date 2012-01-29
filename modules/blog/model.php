<?php
    $articlesPerPage = $moduleSettings['articlesPerPage'];
    $page = 1;
    
    $query = $db->prepare('SELECT COUNT(*) AS articleCount FROM articles');
    $query->execute();
    $rows = $query->fetchAll(PDO::FETCH_OBJ);
    $query->closeCursor();
    $query = NULL;
    
    $pageCount = isset($rows[0]) ? ceil($rows[0]->articleCount/$articlesPerPage) : 0;
	
    $query = $db->prepare('SELECT id, title, content, date, categories FROM articles ORDER BY id DESC LIMIT :page,:articlesPerPage');
    $query->bindValue(':page', ($page-1)*$articlesPerPage, PDO::PARAM_INT);
    $query->bindValue(':articlesPerPage', $articlesPerPage, PDO::PARAM_INT);
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
        $content = stripslashes($row->content);
        $categories = explode(', ', $row->categories);
        $articles[] = array('id' => $id, 'title' => $title, 'date' => $date, 'content' => $content, 'categories' => $categories);
    }

    $pageTitle .= ' - Blog';
	$pageCanonicalLink = '/blog';
    $pagePath[] = array('Blog', '/blog');
?>