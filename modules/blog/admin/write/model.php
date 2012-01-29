<?php
	if (!$isAdmin)
	    include('modules/admin/connection/model.php');
	else
	{
		$articleId = empty($data[0]) ? 0 : intval($data[0]);
	    $articleTitle = '';
		
	    $formPosted = isset($_POST['submit']);
	    if($formPosted)
	    {
	        if($articleId > 0)
	        {
	            $query = 'UPDATE blog_articles SET content='.$db->quote($_POST['content']).', title='.$db->quote($_POST['title']).', categories='.$db->quote($_POST['categories']).' WHERE id='.$articleId;
	            $db->exec($query);
	        }
	        else
	        {
	            $query = 'INSERT INTO blog_articles VALUES (\'\', '.$db->quote($_POST['title']).', '.time().', '.$db->quote($_POST['categories']).', '.$db->quote($_POST['content']).')';
	            $db->exec($query);
	        }
	    }
	    else
	    {
	        $title = '';
			$date = time();
	        $content = '';
	        $categories = '';
	
	        if($articleId > 0)
	        {
	            $query = 'SELECT title, date, categories, content FROM blog_articles WHERE id='.$articleId.' ORDER BY id DESC';
	            $res = $db->query($query);
	            $rows = $res->fetchAll(PDO::FETCH_OBJ);
	            $res->closeCursor();
	            $res = NULL;
	            foreach($rows as $row)
	            {
	                $title = $row->title;
					$date = $row->date;
	                $content = $row->content;
	                $categories = $row->categories;
	            }
	        }
			
			$articleTitle = $title;
	    }
		
	    $pageTitle .= ' - '.($articleId > 0 ? 'Éditer' : 'Écrire').' un article'.(!empty($articleTitle) ? ' : '.$articleTitle : '');
		$pageCanonicalLink = '/blog/admin/write'.($articleId > 0 ? '-'.$articleId : '');
	    $pagePath[] = array('Administration', '/admin');
	    $pagePath[] = array('Blog', '/blog/admin');
	    $pagePath[] = array(($articleId > 0 ? 'Éditer' : 'Écrire').' un article'.(!empty($articleTitle) ? ' : '.$articleTitle : ''), '/blog/admin/write'.($articleId > 0 ? '-'.$articleId : ''));
	}
?>