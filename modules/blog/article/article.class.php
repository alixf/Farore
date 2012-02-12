<?php
	class Article
	{
		public function __construct($core, $settings, $data)
		{
			$this->baseURL = $core->getBaseURL();
		    $this->articleId = isset($data[0]) ? intval($data[0]) : 0;
		}
		
		public function execute($core)
		{
			
		    
		    $query = $core->getDatabase()->prepare('SELECT title, content, date, categories FROM blog_articles WHERE id=:articleId');
		    $query->execute(array('articleId' => intval($this->articleId)));
		    $rows = $query->fetchAll(PDO::FETCH_OBJ);
		    $query->closeCursor();

		    if(count($rows) > 0)
		    {
		        $this->articleTitle = $rows[0]->title;
		        $this->articleDate = $rows[0]->date;
		        $this->articleCategories = explode(', ', $rows[0]->categories);
		        $this->articleContent = stripslashes($rows[0]->content);

		        $core->setPageTitle($core->getPageTitle().' - '.$this->articleTitle);
				$core->setPageCanonicalLink($core->getBaseURL().'blog/article-'.$this->articleId);
		        $core->addPagePathStep('Blog', $core->getBaseURL().'blog');
		        $core->addPagePathStep($this->articleTitle, $core->getBaseURL().'blog/article-'.$this->articleId);
				
		        // Append to categories to the html keyword meta tag
		        $core->setPageKeywords(array_merge($core->getPageKeywords(), $this->articleCategories));
		        
		        // Make a description for the html meta tags whith a max length of 200 chars
		        $shortContent = substr(strip_tags($this->articleContent), 0, 200);
		        $core->setPageDescription(preg_replace('/\s\s+/', ' ', str_replace('\r\n', ' ', substr($shortContent, 0, strrpos($shortContent, ' ')))).'…');
		    }
		}
		
		public function display()
		{
		    if($this->articleId == 0)
		    {
		        ?>
		        <div class="message error">Aucun article à afficher<a href="<?php echo $this->baseURL;?>blog">Retourner à la première page</a></div>
		        <?php
		    }
			else
		    {
		        ?>
		        <article>
		        	<div class="header">
			            <h1><?php echo $this->articleTitle; ?></h1>
			            <div class="date"><?php echo date('d M. Y', $this->articleDate); ?></div> -
			            <div class="categories">
			            <?php
			                switch(count($this->articleCategories))
			                {
			                    case 0 : echo 'Aucune catégorie'; break;
			                    case 1 : echo 'Catégorie : '; break;
			                    default : echo 'Catégories : '; break;
			                }
			                $first = true;
			                foreach($this->articleCategories as $category)
			                {
			                    if($first)
			                        $first = false;
			                    else
			                        echo ', ';
			                    ?><a href="<?php echo $this->baseURL;?>blog/category-<?php echo urlencode($category); ?>"><?php echo $category; ?></a><?php
			                }
			            ?>
			            </div>
			            <!-- div class="share"> - Partager :
			                <div class="g-plusone" data-size="medium" data-count="false" data-href="http://www.eolhing.me/blog/article-<?php echo $article['id'];?>"></div>
			                <a href="http://twitter.com/share" class="twitter-share-button" data-text="eolhing.me - <?php echo $article['title'];?>" data-url="http://www.eolhing.me/blog/article-<?php echo $article['id'];?>" data-count="none" data-via="eolhing">Tweet</a>
			            </div -->
		        	</div>
		            <div class="content"><?php echo nl2br($this->articleContent); ?></div>
		        </article>
		        <?php
		    }
		}
	
		private $articleId;
        private $articleTitle;
        private $articleDate;
        private $articleContent;
        private $articleCategories;
	}
?>