<?php
	if (!$isAdmin)
	    include('modules/admin/connection/model.php');
	else
	{
	    $pageTitle .= ' - Articles';
		$pageCanonicalLink = '/blog/admin/articles';
	    $pagePath[] = array('Administration', '/admin');
	    $pagePath[] = array('Articles du blog', '/blog/admin/articles');
	    
	    $query = "SELECT id, title, date, categories FROM blog_articles ORDER BY id DESC";
	    $res = $db->query($query);
	    $rows = $res->fetchAll(PDO::FETCH_OBJ);
	    $res->closeCursor();
	    $res = NULL;
	    
	    $articles = array();
	    
	    foreach($rows as $row)
	    {
	        $id = $row->id;
	        $title = $row->title;
	        $date = $row->date;
	        $content = stripslashes($row->content);
	        $categories = explode(', ', $row->categories);
	        $articles[] = array('id' => $id, 'title' => $title, 'date' => $date, 'categories' => $categories);
	    }
	}
?>