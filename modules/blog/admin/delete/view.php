<?php
	if (!$isAdmin)
	    include('modules/admin/connection/view.php');
	else
	{
		if($articleId <= 0)
		{
	        ?>
	        <div class="message error">Aucun article n'a été sélectionné.<a href="/blog/admin/articles">Aller à la liste des articles</a></div>
	        <?php
		}
		else if(isset($_POST['submit']))
	    {
	        ?>
	        <div class="message success">L'article à été supprimé avec succès.<a href="/blog/admin/articles">Aller à la liste des articles</a></div>
	        <?php
	    }
	    else
	    {
	        ?>
	        <div class="center">
	            Êtes vous sûr de vouloir supprimer cet article ?
	            <form class="inline-block" method="post" action="/blog/admin/delete-<?php echo $articleId; ?>">
	                <input type="submit" name="submit" />
	            </form>
	        </div>
	        <?php
	    }
	}
?>