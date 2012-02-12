<?php
	class Delete
	{
		public function __construct($core, $settings, $data)
		{
			if(!$core->isAdmin())
			{
				header('Location: '.$core->getBaseURL().'admin/connexion');
				exit(0);
			}
			
			$this->formSent = isset($_POST['submit']);
			$this->articleId = empty($data[0]) ? 0 : intval($data[0]);
			$this->articleTitle = '';
		}

		public function execute($core)
		{
			$this->baseURL = $core->getBaseURL();
				
			if($this->articleId > 0)
			{
		        $query = $core->getDatabase()->prepare('SELECT title FROM blog_articles WHERE id=:articleId');
		        $query->execute(array('articleId' => intval($this->articleId)));
		        $rows = $query->fetchAll(PDO::FETCH_OBJ);
		        $query->closeCursor();

				if(count($rows) > 0)
		        	$articleTitle = $rows[0]->title;
				
			    if($this->formSent)
			    {
			        $query = $core->getDatabase()->prepare('DELETE FROM blog_articles WHERE id=:articleId');
			        $query->execute(array('articleId' => intval($this->articleId)));
			    }
			}
		
		    $core->setPageTitle($core->getPageTitle().' - Supprimer un article'.(!empty($this->articleTitle) ? ' : '.$this->articleTitle : ''));
			$core->setPageCanonicalLink($core->getBaseURL().'blog/admin/delete'.($this->articleId > 0 ? '-'.$this->articleId : ''));
		    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
		    $core->addPagePathStep('Blog', $core->getBaseURL().'blog/admin');
		    $core->addPagePathStep('Supprimer un article'.(!empty($this->articleTitle) ? ' : '.$this->articleTitle : ''), $core->getBaseURL().'blog/admin/delete'.($this->articleId > 0 ? '-'.$this->articleId : ''));
		}
		
		public function display()
		{
			if($this->articleId <= 0)
			{
		        ?>
		        <div class="message error">Aucun article n'a été sélectionné.<a href="<?php echo $this->baseURL ?>blog/admin/articles">Aller à la liste des articles</a></div>
		        <?php
			}
			else if(isset($_POST['submit']))
		    {
		        ?>
		        <div class="message success">L'article à été supprimé avec succès.<a href="<?php echo $this->baseURL ?>blog/admin/articles">Aller à la liste des articles</a></div>
		        <?php
		    }
		    else
		    {
		        ?>
		        <div class="center">
		            Êtes vous sûr de vouloir supprimer cet article ?
		            <form class="inline-block" method="post" action="<?php echo $this->baseURL ?>blog/admin/delete-<?php echo $this->articleId; ?>">
		                <input type="submit" name="submit" />
		            </form>
		        </div>
		        <?php
		    }
		}
	
		private $baseURL = '';
		private $formSent;
		private $articleId;
		private $articleTitle;
	}
?>