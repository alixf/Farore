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
            <h2><?php echo $article['title']; ?></h2>
            <div class="date"><?php echo date('d M. Y', $article['date']); ?></div> -
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
                <div class="g-plusone" data-size="medium" data-count="false" data-href="http://www.eolhing.me/blog/article-<?php echo $article['id'];?>"></div>
                <!--a href="http://twitter.com/share" class="twitter-share-button" data-text="eolhing.me - <?php echo $article['title'];?>" data-url="http://www.eolhing.me/blog/article-<?php echo $article['id'];?>" data-count="none" data-via="eolhing">Tweet</a-->
            </div>
            <div class="content"><?php echo nl2br($article['content']); ?></div>
        </article>
        <?php
    }
?>