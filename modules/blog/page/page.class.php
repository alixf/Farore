<?php
	class Page
	{
		public function __construct($core, $settings, $data)
		{
			$this->page = isset($data[0]) ? intval($data[0]) : 1;
		    $this->articlesPerPage = $settings['articlesPerPage'];

			$this->baseURL = $core->getBaseURL();
		}
		
		public function execute($core, $setPageData = true)
		{
			
		    $page = (!empty($data[0])) ? intval($data[0]) : 1;
		    
		    $query = $core->getDatabase()->prepare('SELECT COUNT(*) AS articleCount FROM blog_articles');
		    $query->execute();
		    $rows = $query->fetchAll(PDO::FETCH_OBJ);
		    $query->closeCursor();
			
		    $this->pageCount = count($rows) > 0 ? ceil($rows[0]->articleCount/$this->articlesPerPage) : 0;
			
		    $query = $core->getDatabase()->prepare('SELECT * FROM blog_articles ORDER BY id DESC LIMIT '.intval(($this->page-1)*$this->articlesPerPage).','.intval($this->articlesPerPage));
		    $query->execute();
		    $rows = $query->fetchAll(PDO::FETCH_OBJ);
		    $query->closeCursor();
		    
		    foreach($rows as $row)
		        $this->articles[] = array('id' => $row->id, 'title' => $row->title, 'date' => $row->date, 'content' => stripslashes($row->content), 'categories' => explode(', ', $row->categories));
		
			if($setPageData)
			{
			    $core->setPageTitle($core->getPageTitle().' - Blog');
				$core->setPageCanonicalLink($core->getBaseURL().'blog/page-'.$page);
			    $core->addPagePathStep('Blog', $core->getBaseURL().'blog');
			    $core->addPagePathStep('Page '.$page, $core->getBaseURL().'blog/page-'.$page);
			}
		}
		
		public function display()
		{
			if(count($this->articles) <= 0)
		    {
		        ?>
		        <div class="message error">Aucun article à afficher<a href="<?php echo $this->baseURL; ?>blog">Retourner à la première page</a></div>
		        <?php
		    }
		    foreach($this->articles as $article)
		    {
		        ?>
		        <article>
		        	<div class="header">
			            <h1><a href="<?php echo $this->baseURL ?>blog/article-<?php echo $article['id']; ?>"><?php echo $article['title']; ?></a></h1>
			            <div class="date"><?php echo date('d M Y', $article['date']); ?></div>
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
			                    ?><a href="<?php echo $this->baseURL; ?>blog/category-<?php echo urlencode($category); ?>"><?php echo $category; ?></a><?php
			                }
			            ?>
			            </div>
			            <!-- div class="share">Partager :
			                <div class="g-plusone" data-size="medium" data-count="false" data-href="http://www.eolhing.me/blog/article/<?php echo $article['id'];?>"></div>
			                <a href="http://twitter.com/share" class="twitter-share-button" data-text="eolhing.me - <?php echo $article['title'];?>" data-url="http://www.eolhing.me/blog/article/<?php echo $article['id'];?>" data-count="none" data-via="eolhing">Tweet</a>
			            </div -->
						</div>
					<div class="content"><?php echo nl2br($article['content']); ?></div>
		        </article>
		        <?php
		    }
		?>
		
		<div class="pageViewer">
			<a class="button<?php echo ($this->page > 1) ? '" href="'.$this->baseURL.'blog/page-1"' : ' disabled"'; ?>>&#171;</a>
			<a class="button<?php echo ($this->page > 1) ? '" href="'.$this->baseURL.'blog/page-'.($this->page-1).'"' : ' disabled"'; ?>>&#60;</a>
			<?php
		        for($i = $this->page-2; $i <= $this->page+2; $i++)
		        {
		            if($i == $this->page && $this->page <= $this->pageCount)
		            {
		                ?>
		                <a class="button currentPage"><?php echo $i; ?></a>
		                <?php
		            }
		            else if($i > 0 && $i <= $this->pageCount)
		            {
		                ?>
		                <a class="button" href="<?php echo $this->baseURL ?>blog/page-<?php echo $i ?>"><?php echo $i; ?></a>
		                <?php
		            }
		        }
			?>
			<a class="button<?php echo ($this->page < $this->pageCount) ? '" href="'.$this->baseURL.'blog/page-'.($this->page+1).'"' : ' disabled"'; ?>>&#62;</a>
			<a class="button<?php echo ($this->page < $this->pageCount) ? '" href="'.$this->baseURL.'blog/page-'.$this->pageCount.'"' : ' disabled"'; ?>>&#187;</a>
		</div>
		<?php
		}
		
		private $baseURL;
		private $articles = array();
		private $page = 1;
		private $pageCount = 0;
		private $articlesPerPage = 5;
	}
?>