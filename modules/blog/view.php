<?php
    if(count($articles) == 0)
    {
        ?>
        <div class="message error">Aucun article à afficher<a href="/blog">Retourner à la première page</a></div>
        <?php
    }
    foreach($articles as $article)
    {
        ?>
        <article>
            <h2><a href="/blog/article-<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a></h2>
            <div class="date"><?php echo date('d M Y', $article['date']); ?></div> -
            <div class="categories">
            <?php
                switch(count($article['categories']))
                {
                    case 0 : echo 'Aucune catégorie'; break;
                    case 1 : echo 'Catégorie : '; break;
                    default : echo 'Catégories : '; break;
                }
                $first = true;
                foreach($article['categories'] as $category)
                {
                    if($first)
                        $first = false;
                    else
                        echo ', ';
                    ?><a href="/blog/category-<?php echo urlencode($category); ?>"><?php echo $category; ?></a><?php
                }
            ?>
            </div>
            <div class="share"> - Partager :
                <div class="g-plusone" data-size="medium" data-count="false" data-href="http://www.eolhing.me/blog/article/<?php echo $article['id'];?>"></div>
                <!--a href="http://twitter.com/share" class="twitter-share-button" data-text="eolhing.me - <?php echo $article['title'];?>" data-url="http://www.eolhing.me/blog/article/<?php echo $article['id'];?>" data-count="none" data-via="eolhing">Tweet</a-->
            </div>
            <div class="content"><?php echo nl2br($article['content']); ?></div>
        </article>
        <?php
    }
?>

<div class="pageViewer">
	<a class="button<?php echo ($page > 1) ? '" href="/blog/page-1" ' : ' disabled" '; ?>>&#171;</a>
	<a class="button<?php echo ($page > 1) ? '" href="/blog/page-'.($page-1).'" ' : ' disabled" '; ?>>&#60;</a>
	<?php
        for($i = $page-2; $i <= $page+2; $i++)
        {
            if($i == $page and $page <= $pageCount)
            {
                ?>
                <a class="button"><?php echo $i; ?></a>
                <?php
            }
            else if($i > 0 and $i <= $pageCount)
            {
                ?>
                <a class="button" href="/blog/page-<?php echo $i ?>"><?php echo $i; ?></a>
                <?php
            }
        }
	?>
	<a class="button<?php echo ($page < $pageCount) ? '" href="/blog/page-'.($page+1).'" ' : ' disabled" '; ?>>&#62;</a>
	<a class="button<?php echo ($page < $pageCount) ? '" href="/blog/page-'.$pageCount.'" ' : ' disabled" '; ?>>&#187;</a>
</div>