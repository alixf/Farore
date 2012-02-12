<?php
	class Write
	{
		public function __construct($core, $settings, $data)
		{
			if (!$core->isAdmin())
			{
				header('Location: '.$core->getBaseURL().'admin/connection');
				exit(0);
			}
			
		    $this->formSent = isset($_POST['submit']);
			$this->articleId = empty($data[0]) ? 0 : intval($data[0]);
		}
		
		public function execute($core)
		{
		    $this->baseURL = $core->getBaseURL();
		
		    if($this->formSent)
		    {
		        if($this->articleId > 0)
		        {
		            $query = $core->getDatabase()->prepare('UPDATE blog_articles SET title=:title, categories=:categories, content=:content WHERE id=:articleId');
		            $query->execute(array('title' => $_POST['title'], 'categories' => $_POST['categories'], 'content' => $_POST['content'], 'articleId' =>  intval($this->articleId)));
		        }
		        else
		        {
		            $query = $core->getDatabase()->prepare('INSERT INTO blog_articles VALUES (\'\', :title, :date, :categories, :content)');
					$query->execute(array('title' => $_POST['title'], 'date' => time(), 'categories' => $_POST['categories'], 'content' => $_POST['content']));
		        }
		    }
		    else
		    {
		        $this->title = '';
				$this->date = time();
		        $this->content = '';
		        $this->categories = '';
		
		        if($this->articleId > 0)
		        {
		            $query = $core->getDatabase()->prepare('SELECT title, date, categories, content FROM blog_articles WHERE id=:articleId');
					$query->execute(array('articleId' =>  intval($this->articleId)));
		    		$rows = $query->fetchAll(PDO::FETCH_OBJ);
		            $query->closeCursor();

		            foreach($rows as $row)
		            {
		                $this->title = $row->title;
						$this->date = $row->date;
		                $this->content = $row->content;
		                $this->categories = $row->categories;
		            }
		        }
		    }
			
		    $core->setPageTitle($core->getPageTitle().' - '.($this->articleId > 0 ? 'Éditer' : 'Écrire').' un article'.(!empty($this->title) ? ' : '.$this->title : ''));
			$core->setPageCanonicalLink($core->getBaseURL().'blog/admin/write'.($this->articleId > 0 ? '-'.$this->articleId : ''));
		    $core->addPagePathStep('Administration', $core->getBaseURL().'admin');
		    $core->addPagePathStep('Blog', $core->getBaseURL().'blog/admin');
		    $core->addPagePathStep(($this->articleId > 0 ? 'Éditer' : 'Écrire').' un article'.(!empty($this->title) ? ' : '.$this->title : ''), $core->getBaseURL().'blog/admin/write'.($this->articleId > 0 ? '-'.$this->articleId : ''));
		}
		
		public function display()
		{
		    if($this->formSent)
		    {
		        if($this->articleId == 0)
		        {
		            ?>
		            <div class="message success">L'article a été créé avec succès<a href="<?php echo $this->baseURL ?>blog/admin/articles">Aller à la liste des articles</a></div>
		            <?php
		        }
		        else
		        {
		            ?>
		            <div class="message success">L'article a été édité avec succès<a href="<?php echo $this->baseURL ?>blog/admin/articles">Aller à la liste des articles</a></div>
		            <?php
		        }
		    }
		    else
		    {
		        ?>
		        <form class="writeForm" method="post" action="<?php echo $this->baseURL ?>blog/admin/write<?php echo $this->articleId > 0 ? '-'.$this->articleId : ''; ?>">
		        	<article>
		        		<div class="header">
				            <input type="text" name="title" value="<?php echo $this->title;?>" placeholder="Titre" required /><br />
				            <div class="date"><?php echo date('d M. Y', $this->date); ?></div>
				            <label for="categories">Catégories : </label><input type="text" name="categories" value="<?php echo $this->categories;?>" placeholder="Catégories" /><br />
			            </div>
			            <textarea name="content" placeholder="Contenu de l'article" required><?php echo stripslashes($this->content); ?></textarea>
		            </article>
		            <input type="submit" name="submit" value="Valider" />
		        </form>
		        <?php
			}
		}

		private $baseURL = '';
		private $formSent;
		private $articleId;
		private $title;
		private $date;
		private $categories;
		private $content;
	}
?>