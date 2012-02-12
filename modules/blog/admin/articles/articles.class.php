<?php
	class Articles
	{
		public function __construct($core, $settings, $data)
		{
			if(!$core->isAdmin())
			{
				header('Location: '.$core->getBaseURL().'admin/connexion');
				exit(0);
			}
		}
		
		public function execute($core)
		{
		    $this->baseURL = $core->getBaseURL();
		    
		    $query = $core->getDatabase()->prepare("SELECT id, title, date, categories FROM blog_articles ORDER BY id DESC");
		    $query->execute();
		    $rows = $query->fetchAll(PDO::FETCH_OBJ);
		    $query->closeCursor();
		    
		    foreach($rows as $row)
		        $this->articles[] = array('id' => $row->id, 'title' => $row->title, 'date' => $row->date, 'categories' => explode(', ', $row->categories));
			
		    $core->setPageTitle($core->getPageTitle().' - Articles');
			$core->setPageCanonicalLink($core->getBaseURL().'blog/admin/articles');
		    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
		    $core->addPagePathStep('Articles du blog', $core->getBaseURL().'blog/admin/articles');
		}
		
		public function display()
		{
		    ?>
		    <h1>Liste des articles</h1>
		    
		    <div class="actionBar">
		    	<a class="button" href="<?php echo $this->baseURL; ?>blog/admin/write">Écrire un article</a>
		    </div>
		    
		    <table class="articlesTable">
		        <tr>
		            <th class="title">Titre</th>
		            <th class="date">Date</th>
		            <th class="edit">Éditer</th>
		            <th class="delete">Supprimer</th>
		        </tr>
		
		        <?php
		        foreach ($this->articles as $article)
		        {
		            ?>
		            <tr>
		                <td class="title"><?php echo $article['title']; ?></td>
		                <td class="date"><?php echo date('d M. Y', $article['date']); ?></td>
		                <td class="edit"><a class="button" href="<?php echo $this->baseURL; ?>blog/admin/write-<?php echo $article['id']; ?>">Éditer</a></td>
		                <td class="delete"><a class="button" href="<?php echo $this->baseURL; ?>blog/admin/delete-<?php echo $article['id'] ?>">Supprimer</a></td>
		            </tr>
		            <?php
		        }
		        ?>
		    </table>
		    <?php
		}
	
		private $baseURL = '';
		private $articles = array();
	}
?>