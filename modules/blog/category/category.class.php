<?php
	class Category
	{
		public function __construct($core, $settings, $data)
		{
			$this->baseURL = $core->getBaseURL();
			$this->dataString = implode($core->getUrlDataSeparator(), $data);
		    $this->category = ($this->dataString);
		}
		
		public function execute($core)
		{
			$this->baseURL = $core->getBaseURL();
			
		    
		    $query = $core->getDatabase()->prepare('SELECT id, title, date FROM blog_articles WHERE categories LIKE :categoryPattern ORDER BY id DESC');
		    $query->execute(array('categoryPattern' => '%'.$this->category.'%'));
		    $rows = $query->fetchAll(PDO::FETCH_OBJ);
		    $query->closeCursor();
		    
		    foreach($rows as $row)
		        $this->articles[] = array('id' => $row->id, 'title' => $row->title, 'date' => $row->date);
			
		    $core->setPageTitle($core->getPageTitle().' - Catégorie : '.$this->category);
			$core->setPageCanonicalLink($core->getBaseURL().'blog/category-'.urlencode($this->category));
		    $core->addPagePathStep('Blog', $core->getBaseURL().'blog');
		    $core->addPagePathStep('Catégorie : '.$this->category, $core->getBaseURL().'blog/category-'.urlencode($this->category));
		}
		
		public function display()
		{
		    if(count($this->articles) == 0)
		    {
		        ?>
		        <div class="message error">Aucun article à afficher<a href="<?php echo $this->baseURL ?>blog">Retourner à la première page</a></div>
		        <?php
		    }
		    else
		    {
		        ?>
		        <table>
		        <?php
		        foreach($this->articles as $article)
		        {
		            ?>
		            <tr>
		                <td><?php echo date('d M Y', $article['date']); ?></td>
		                <td><a href="<?php echo $this->baseURL ?>blog/article-<?php echo $article['id'] ?>"><?php echo $article['title']; ?></a></td>
		            </tr>
		            <?php
		        }
		        ?>
		        </table>
		        <?php
		    }
		}
	
		private $baseURL;
		private $articles = array();
		private $dataString;
		private $category;
	}
?>