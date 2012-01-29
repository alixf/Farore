<?php
	if (!$isAdmin)
	    include('modules/admin/connection/view.php');
	else
	{
	    ?>
	    <h1>Liste des articles</h1>
	    
	    <div class="actionBar">
	    	<a class="button" href="/blog/admin/write">Écrire un article</a>
	    </div>
	    
	    <table class="articlesTable">
	        <tr>
	            <th class="title">Titre</th>
	            <th class="date">Date</th>
	            <th class="edit">Éditer</th>
	            <th class="delete">Supprimer</th>
	        </tr>
	
	        <?php
	        foreach ($articles as $article)
	        {
	            ?>
	            <tr>
	                <td class="title"><?php echo $article['title']; ?></td>
	                <td class="date"><?php echo date('d M. Y', $article['date']); ?></td>
	                <td class="edit"><a class="button" href="/blog/admin/write-<?php echo $article['id']; ?>">Éditer</a></td>
	                <td class="delete"><a class="button" href="/blog/admin/delete-<?php echo $article['id'] ?>">Supprimer</a></td>
	            </tr>
	            <?php
	        }
	        ?>
	    </table>
	    <?php
	}
?>