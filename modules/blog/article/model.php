<?php
    $articleId = intval($data[0]);
    
    $query = $db->prepare('SELECT title, content, date, categories FROM blog_articles WHERE id=:articleId');
    $query->bindValue(':articleId', $articleId, PDO::PARAM_INT);
    $query->execute();
    $rows = $query->fetchAll(PDO::FETCH_OBJ);
    $query->closeCursor();
    $query = NULL;
    
    $articles = array();
    
    foreach($rows as $row)
    {
        $articleTitle = $row->title;
        $articleDate = $row->date;
        $articleContent = stripslashes($row->content);
        $articleCategories = explode(', ', $row->categories);
        $articles[] = array('id' => $articleId,
                            'title' => $articleTitle,
                            'date' => $articleDate,
                            'content' => $articleContent,
                            'categories' => $articleCategories);
    }
    
    if(count($articles) > 0)
    {
        $pageTitle .= ' - '.$articles[0]['title'];
		$pageCanonicalLink = '/blog/article-'.$articleId;
        $pagePath[] = array('Blog', '/blog');
        $pagePath[] = array($articles[0]['title'], '/blog/article-'.$articleId);
        // Append to categories to the html keyword meta tag
        $pageKeywords = array_merge($pageKeywords, $articles[0]['categories']);
        
        // Make a description for the html meta tags whith a max length of 200 chars
        $shortContent = substr(strip_tags($articles[0]['content']), 0, 200);
        $pageDescription = preg_replace('/\s\s+/', ' ', str_replace('\r\n', ' ', substr($shortContent, 0, strrpos($shortContent, ' ')))).'…';
    }
?>