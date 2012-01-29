<?php
	if (!$isAdmin)
	    include('modules/admin/connection/model.php');
	else
	{
		$articleId = empty($data[0]) ? 0 : intval($data[0]);
		$articleTitle = '';
		
		if($articleId > 0)
		{
	        $query = 'SELECT title FROM blog_articles WHERE id='.$articleId.' ORDER BY id DESC';
	        $res = $db->query($query);
	        $rows = $res->fetchAll(PDO::FETCH_OBJ);
	        $res->closeCursor();
	        $res = NULL;
            $articleTitle = $rows[0]->title;
		}

	    $pageTitle .= ' - Supprimer un article'.(!empty($articleTitle) ? ' : '.$articleTitle : '');
		$pageCanonicalLink = '/blog/admin/delete'.($articleId > 0 ? '-'.$articleId : '');
	    $pagePath[] = array('Administration', '/admin');
	    $pagePath[] = array('Blog', '/blog/admin');
	    $pagePath[] = array('Supprimer un article'.(!empty($articleTitle) ? ' : '.$articleTitle : ''), '/blog/admin/delete'.($articleId > 0 ? '-'.$articleId : ''));

	    if(isset($_POST['submit']) && $articleId > 0)
	    {
	        $query = "DELETE FROM blog_articles WHERE id=".$articleId;
	        $db->exec($query);
	    }
	}
?>