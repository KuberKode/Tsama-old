<?php
/*
** 12 February 2013
**
** The author disclaims copyright to this source code.  In place of
** a legal notice, here is a quote:
**
** If you’re willing to restrict the flexibility of your approach, 
** you can almost always do something better.
** - John Carmack
**
*************************************************************************/

if(!defined('TSAMA'))exit;

class TsamaSetup extends TsamaObject{
	private $m_node = NULL;

	public function __construct(&$parentNode){
		parent::__construct();
		$this->m_node = $parentNode;
	}

	public function Node(){
		return $this->m_node;
	}

	public function set($params){
		$this->get();
	}

	private function ShowForm($parentNode){
		

		$h1 = $parentNode->AddChild('h1')->SetValue('Tsama Setup<span class="nl txtNormal">Welcome, please use the following form to configure your website.</span>');

		//TODO: see if config directory is writable and show appropriate message

		$form = HTML5Parser::CreateForm($parentNode,'setup/save');
		//show site form
		$h3 = $form->AddChild('h3')->SetValue('Site Setup<span class="nl txtNormal">Please enter your site information.</span>');
		$siteName = HTML5Parser::CreateTextField($form,'Site Name','site','your site name','',TRUE);

		//TODO: Site Administrator

		$h3 = $form->AddChild('h3')->SetValue('Database Setup<span class="nl txtNormal">Please enter your database credentials.</span>');

		//show db form
		$driver = HTML5Parser::CreateTextField($form,'Driver','driver','your db driver','mysql',TRUE);
		$host = HTML5Parser::CreateTextField($form,'Host','host','your db host','localhost',TRUE);
		$uid = HTML5Parser::CreateTextField($form,'Username','uid','your db user','',TRUE);
		$pwd = HTML5Parser::CreateTextField($form,'Password','pwd','your db user password','',TRUE);
		$nm = HTML5Parser::CreateTextField($form,'Name','nm','your db name','',TRUE);

		$save = HTML5Parser::CreateButton($form,'Save',TRUE);
	}

	public function get($params){
		//only if configuration do not already exist
		if(!TsamaDatabase::IsConfigured()){

			$setup = $this->Node()->AddChild('div');
			$setup->attr('id','setup');
			
			if(!isset($_POST['site'])){
				$this->ShowForm($setup);
				return;
			}
			
			//extra check
			$route = Tsama::_conf('ROUTE');

			if((count($route) > 0) && ($route[0] == 'setup') && ($route[1] == 'save')){

				//continue with save
				//TODO: Validata Database connection

				//site
				$confSite = file_get_contents(Tsama::_conf('BASEDIR').DS.'conf'.DS.'site.conf.xmpl.php');

				//assuming files are writable, for now.

				$confSite = str_replace('My Site Name', $_POST['site'], $confSite);
				$confSite = str_replace('yourtheme', 'default', $confSite);
				$confSite = str_replace('yourlayout', 'default', $confSite);

				$fn = Tsama::_conf('BASEDIR').DS.'conf'.DS.'site.conf.php';
				$fh = fopen($fn, 'w');
				fwrite($fh, $confSite);
				fclose($fh);

				//continue with save
				$confDB = "<?php \$_DB['Driver'] = '".$_POST['driver']."'; \r\n \$_DB['Host'] = '".$_POST['host']."';\r\n \$_DB['Username'] = '".$_POST['uid']."'; \r\n \$_DB['Password'] = '".$_POST['pwd']."'; \r\n \$_DB['Name'] = '".$_POST['nm']."'; ?>";

				$fn = Tsama::_conf('BASEDIR').DS.'conf'.DS.'db.conf.php';
				$fh = fopen($fn, 'w');
				fwrite($fh, $confDB);
				fclose($fh);

				$h1 = $setup->AddChild('h1')->SetValue('Tsama Setup<span class="nl txtNormal">Thank you, <a href="'.Tsama::_conf('BASE').'">you may now continue to your homepage</a>.</span>');

			}
		}
	}
}
?>