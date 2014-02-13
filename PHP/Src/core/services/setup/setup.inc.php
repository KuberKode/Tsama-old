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

	private function ShowForm($parentNode, $msg = ''){

		$brand = $parentNode->AddChild('div');
		$logo = $brand->AddChild('img');
		$logo->attr('src',Tsama::_conf('BASE').'media/visual/images/default.domain/logo/tsama.png');
		

		$h1 = $parentNode->AddChild('h1')->SetValue('Tsama Setup<span class="nl txtNormal">Welcome, please use the following form to configure your website.</span>');

		if(!empty($msg)){

			$error = $parentNode->AddChild('div');
			$error->attr('id','site-msg');
			$error->attr('class','hide');

			$error->attr('class','message error');
			$error->SetValue($msg);
		}


		//See if config directory is writable and show appropriate message
		if(!is_writable(Tsama::_conf('BASEDIR').DS.'conf')){

			$ms = '<strong>Configuration (<strong>/conf</strong>) directory is not writable. A chmod of 777 should fix this.<br />NB: Once setup is complete you should set chmod of /conf to 555 and any configuration files therein to 444</strong>';
			
			$dirErr = $parentNode->AddChild('div');
			$dirErr->attr('id','dir-msg');
			$dirErr->attr('class','hide');

			$dirErr->attr('class','message error');
			$dirErr->SetValue($ms);
		
		}
		

		$form = HTML5Parser::CreateForm($parentNode,'setup/save');

		$row = $form->AddChild('div');
		$row->attr('class','row');
		//$col->attr('class','column w50');
		//show site form
		$h3 = $row->AddChild('h3')->SetValue('Site Setup<span class="nl txtNormal">Please enter your site information.</span>');

		$col = $row->AddChild('div');
		$col->attr('class','column w50');	

		$site = '';
		if(isset($_POST['site'])){ $site = $_POST['site']; }
		$siteName = HTML5Parser::CreateTextField($col,'Site Name','site','your site name',$site,TRUE);
		

		$domain = Tsama::_conf('DOMAIN');
		if(isset($_POST['domain'])){ $domain = $_POST['domain']; }
		$ad = HTML5Parser::CreateTextField($col,'Main Domain','domain','your main domain',$domain,TRUE);

		$col = $row->AddChild('div');
		$col->attr('class','column w50');

		$adomain ='';
		if(isset($_POST['adomain1'])){ $adomain = $_POST['adomain1']; }
		$ad = HTML5Parser::CreateTextField($col,'Alternate Domains','adomain1','your alternate domain',$adomain,FALSE);

		$adomain ='';
		if(isset($_POST['adomain2'])){ $adomain = $_POST['adomain2']; }
		$ad = HTML5Parser::CreateTextField($col,'','adomain2','another alternate domain',$adomain,FALSE);

		$adomain ='';
		if(isset($_POST['adomain3'])){ $adomain = $_POST['adomain3']; }
		$ad = HTML5Parser::CreateTextField($col,'','adomain3','alternate domain',$adomain,FALSE);

		$row = $form->AddChild('div');
		$row->attr('class','row');
		//Site Administrator
		$h3 = $row->AddChild('h3')->SetValue('Administrator Setup<span class="nl txtNormal">Please enter your user information.</span>');

		$col = $row->AddChild('div');
		$col->attr('class','column w50');
		$admin = '';
		if(isset($_POST['admin'])){ $admin = $_POST['admin']; }
		$adminName = HTML5Parser::CreateTextField($col,'Username','admin','your username',$admin,TRUE);

		$email = '';
		if(isset($_POST['email'])){ $email = $_POST['email']; }
		$emailInfo = HTML5Parser::CreateEmailField($col,'Email','email','your email address',$email,TRUE);

		$col = $row->AddChild('div');
		$col->attr('class','column w50');

		$pwd1 = HTML5Parser::CreatePasswordField($col,'Password','pwd','your administrator password','',TRUE);

		$pwd2 = HTML5Parser::CreatePasswordField($col,'Confirm Password','cpwd','confirm password','',TRUE);

		$row = $form->AddChild('div');
		$row->attr('class','row');

		$h3 = $row->AddChild('h3')->SetValue('Database Setup<span class="nl txtNormal">Please enter your database credentials.</span>');

		$row = $form->AddChild('div');
		$row->attr('class','row');

		$col = $row->AddChild('div');
		$col->attr('class','column w50');

		//show db form
		$driver = 'mysql';
		if(isset($_POST['driver'])){ $driver = $_POST['driver']; }
		$driver = HTML5Parser::CreateTextField($col,'Driver','driver','your db driver',$driver,TRUE);

		$host = 'localhost';
		if(isset($_POST['host'])){ $host = $_POST['host']; }
		$host = HTML5Parser::CreateTextField($col,'Host','host','your db host',$host,TRUE);

		$nm = '';
		if(isset($_POST['nm'])){ $nm = $_POST['nm']; }
		$nm = HTML5Parser::CreateTextField($col,'Name','nm','your db name',$nm,TRUE);

		$col = $row->AddChild('div');
		$col->attr('class','column w50');
		$uid = '';
		if(isset($_POST['uid'])){ $uid = $_POST['uid']; }
		$uid = HTML5Parser::CreateTextField($col,'Username','uid','your db user',$uid,TRUE);

		$pwd = HTML5Parser::CreatePasswordField($col,'Password','dbpwd','your db user password','',TRUE);

		$pwd2 = HTML5Parser::CreatePasswordField($col,'Confirm Password','cdbpwd','confirm password','',TRUE);
		

		$save = HTML5Parser::CreateButton($form,'Save',TRUE);
	}

	private function WriteSiteConf(){
		//assuming files are writable, for now.

		//site
		$confSite = file_get_contents(Tsama::_conf('BASEDIR').DS.'conf'.DS.'site.conf.xmpl.php');

		$site = $_POST['site'];
		$domain = $_POST['domain'];
		$altdomains = $_POST['adomain1'];
		if(!@empty($_POST['adomain2'])){
			$altdomains .= ",".$_POST['adomain2'];
		}
		if(!@empty($_POST['adomain3'])){
			$altdomains .= ",".$_POST['adomain3'];
		}

		$conf = "<?php\n\$_TSAMA_CONFIG['NAME'] = '".$site."';\n\$_TSAMA_CONFIG['LOGO'] = 'tsama.png';\n\$_TSAMA_CONFIG['COMPRESS'] = TRUE;\n\$_TSAMA_CONFIG['HIDE_TSAMA'] = TRUE;\n\$_TSAMA_CONFIG['THEME'] = 'default';\n\$_TSAMA_CONFIG['LAYOUT'] = 'default';\n\$_TSAMA_CONFIG['PRIMARY_DOMAIN'] = '".$domain."';\n\$_TSAMA_CONFIG['ALTERNATE_DOMAINS'] = '".$altdomains."';\n\$_TSAMA_CONFIG['LANGUAGE'] = 'en';\n\$_TSAMA_CONFIG['DEBUG'] = TRUE;\n?>";
		

		$fn = Tsama::_conf('BASEDIR').DS.'conf'.DS.'site.conf.php';
		$fh = fopen($fn, 'w');
		fwrite($fh, $conf);
		fclose($fh);

		return TRUE;
	}

	private function WriteDbConf(){
		//continue with save
		$conf = "<?php\n\$_DB['Driver'] = '".$_POST['driver']."';\n\$_DB['Host'] = '".$_POST['host']."';\n\$_DB['Username'] = '".$_POST['uid']."';\n\$_DB['Password'] = '".$_POST['dbpwd']."';\n\$_DB['Name'] = '".$_POST['nm']."';\n?>";

		$fn = Tsama::_conf('BASEDIR').DS.'conf'.DS.'db.conf.php';
		$fh = fopen($fn, 'w');
		fwrite($fh, $conf);
		fclose($fh);

		return TRUE;
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
				if($_POST['cdbpwd'] != $_POST['dbpwd']){
					$this->ShowForm($setup,"Confirmed database password don't match. Please try again.");
					return;
				}
				if($_POST['cpwd'] != $_POST['pwd']){
					$this->ShowForm($setup,"Confirmed admin password don't match. Please try again.");
					return;
				}
				
				//continue with save
				try{
					$db = new PDO(strtolower($_POST['driver']).':host='.$_POST['host'].';dbname='.$_POST['nm'], $_POST['uid'],$_POST['dbpwd']);

					$this->WriteSiteConf();

					$this->WriteDbConf();

					$brand = $setup->AddChild('div');
					$logo = $brand->AddChild('img');
					$logo->attr('src',Tsama::_conf('BASE').'media/visual/images/default.domain/logo/tsama.png');

					$h1 = $setup->AddChild('h1')->SetValue('Tsama Setup<span class="nl txtNormal">Thank you, <a href="'.Tsama::_conf('BASE').'">you may now continue to your homepage</a>.</span>');
				}catch(PDOException $e) {
					$this->ShowForm($setup,'Invalid database credentials. Please try again.');
				}
			}
		}
	}
}
?>