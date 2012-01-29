<?php
	if (!$isAdmin)
	    include('modules/admin/connection/view.php');
	else
	{
	    if(isset($_POST['submit']))
	    {
	        if(empty($data[0]))
	        {
	            ?>
	            <div class="message success">L'article a été créé avec succès<a href="/blog/admin/articles">Aller à la liste des articles</a></div>
	            <?php
	        }
	        else
	        {
	            ?>
	            <div class="message success">L'article a été édité avec succès<a href="/blog/admin/articles">Aller à la liste des articles</a></div>
	            <?php
	        }
	    }
	    else
	    {
	    	// TODO : The writing form should look just like the actual article
	        ?>
	        <form class="writeForm" method="post" action="/blog/admin/write<?php echo $articleId > 0 ? '-'.$articleId : ''; ?>">
	            <input type="text" name="title" value="<?php echo $title;?>" placeholder="Titre" /><br />
	            <div class="date"><?php echo date('d M. Y', $date); ?></div> -
	            <label for="categories">Catégories : </label><input type="text" name="categories" value="<?php echo $categories;?>" placeholder="Catégories" /><br />
	            <textarea name="content" placeholder="Contenu de l'article"><?php echo stripslashes($content); ?></textarea>
	            <input type="submit" name="submit" />
	        </form>
	        <?php
	    }
	}
?>