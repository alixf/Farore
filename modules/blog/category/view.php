<?php
    if(count($articles) == 0)
    {
        ?>
        <div class="message error">Aucun article à afficher<a href="/blog">Retourner à la première page</a></div>
        <?php
    }
    else
    {
        ?>
        <table>
        <?php
        foreach($articles as $article)
        {
            ?>
            <tr>
                <td><?php echo date('d M Y', $article['date']); ?></td>
                <td><a href="/blog/article-<?php echo $article['id'] ?>"><?php echo $article['title']; ?></a></td>
            </tr>
            <?php
        }
        ?>
        </table>
        <?php
    }
?>