<?php
/**
 * 	CityGram simple template complier
 *
 *	Last Update Date : 2016/05/13
 *	Version : 1.3
 *	Auther : Jafar Rezaei 
 *
 *	Do Not Change this file
 */
 
namespace CTGram;
use Exception;

class TemplateEngine {

	function showVariable($name , $subname = NULL) {
		if (isset($this->data[$name])) {
			echo $this->data[$name];
		} else if($subname !== NULL) {
			$this->showNextVariable($name , $subname);
		} else {
			echo '{' . $name . '}';
		}
	}
    
	function getVariable($name , $subname = NULL) {
		if (isset($this->data[$name])) {
			return $this->data[$name];
		} else if(isset($this->data[$subname][$name])) {
			return $this->data[$subname][$name];
		} else {
			return $name;
		}
	}
    
    
	function showNextVariable($name , $subname) {
		if (isset($this->data[$name][0][$subname])) {
			echo $this->data[$name][0][$subname];
		} elseif( isset($this->data[$name][$subname])) {
			echo $this->data[$name][$subname];
		}else {
			echo '{' . $name . '->'.$subname.'}';
		}
	}
    
	function addTemplate($name , $data) {
		if( !file_exists(sprintf('./temp/%s/%s.ctg', $_SESSION['site']['template'] , $name)) ){
			echo '{TEMPALTE:' . $name . '}';
		}else{
			$page_to_include = sprintf('./temp/%s/%s.ctg', $_SESSION['site']['template'] , $name);
			
			//do page validation here with file_exists
			ob_start();
			include $page_to_include;
			$included_page = ob_get_clean(); //gets contents and cleans the buffer and closes it
			echo $this->process($included_page , $data);
		}
	}
    
	function addPagination($where , $name , $sperateor = "1"){
		$firstpage = $pervpage = $paged_others = $nextpage = $lastpage = $pageinfo = "" ;
		$page = $this->getVariable($name.'Page');
		$count = $this->getVariable($name.'Count');
		$perPage = $this->getVariable($name.'PP');
		
		if($sperateor == 1){
			$right= "<";
			$left= ">";
			$rightEnd = "<<";
			$leftEnd = ">>";
		}
		else{
			$left = '<i class="fa fa-angle-left"></i>';
			$right= '<i class="fa fa-angle-right"></i>';
			$leftEnd = '<i class="fa fa-angle-double-left"></i>';
			$rightEnd = '<i class="fa fa-angle-double-right"></i>';
		}
			
		
		if($count - $perPage   > 0 ){
			$preLink = "http://".$_SERVER['HTTP_HOST']."/".$where."/page/";
			$siffixLink = "";

			$paged_total = ceil($count / $perPage );
			$paged_last = $paged_total;    //صفحه آخر
			$paged_middle = $page + 3;    //صفحه میانی
			$paged_start = $paged_middle - 3;    //شروع صفحه بندی
			
			if($page > 1){
				$firstpage = '<a class="animate ftactive" data-toggle="tooltip"  data-placement="top"  href="'.$preLink.'1'.$siffixLink.'" title="صفحه نخست" >'.$rightEnd.' </a>';
			} else {
				$firstpage = '<a class="ftdeactive" data-toggle="tooltip"  data-placement="top"  title="صفحه نخست" >'.$rightEnd.' </a>';
			}
			
			if($page <= $paged_last - 1){
				$lastpage = '<a class="animate ftactive" data-toggle="tooltip"  data-placement="top"  href="'.$preLink.$paged_last.$siffixLink.'" title="صفحه آخر" >'.$leftEnd.' </a>';
			} else {
				$lastpage = '<a class="ftdeactive" data-toggle="tooltip"  data-placement="top"  title="صفحه آخر" >'.$leftEnd.' </a>';
			}
			if($page > 1){
				$paged_perv = $page - 1;
				$pervpage = '<a class="animate ftactive" data-toggle="tooltip"  data-placement="top"  href="'.$preLink.$paged_perv.$siffixLink.'" title="صفحه قبلی">'.$right.' </a>';
			}else{
				$pervpage = '<a class="ftdeactive" data-toggle="tooltip"  data-placement="top"  title="صفحه قبلی" >'.$right.' </a>';
			}
			if($page <= $paged_last-1){
				$paged_next =$page+1;
				$nextpage= '<a class="animate ftactive" data-toggle="tooltip"  data-placement="top"  style="margin-right:-4px;" href="'.$preLink.$paged_next.'" title="صفحه بعدی">'.$left.' </a>';
			}else{
				$nextpage= '<a class="ftdeactive" data-toggle="tooltip"  data-placement="top"  style="margin-right:-4px;" title="صفحه بعدی">'.$left.' </a>';
			}
			$paged_others = "";
			for ($i=$paged_start-2; $i<=$paged_middle; $i++){
				if ($i > 0 && $i <= $paged_last){
					if($i == $page){
						$paged_others .= '<a class="ftdeactive" style="background:#f8f8f8;" data-toggle="tooltip"  title="صفحه فعلی" data-placement="top" >صفحه'.$i.'</a>';
					}
					else{
						$paged_others .= '<a class="animate ftactive" data-toggle="tooltip"  data-placement="top"  href="'.$preLink.$i.$siffixLink.'" title="صفحه '.$i.'">صفحه'.$i.'</a>';
					}
				}
			}
			echo '
			<style>.paged-link-info{direction:rtl !important;float:right}
			.ftpagenav{direction:rtl !important;text-align:right !important;display:inline-block;float:left;margin-top:10px ;padding:5px;}
			.ftpagenav a{margin:0;margin-right:-1px;text-decoration:none;border:1px solid #ccc; padding:4px 8px;background:#f0f0f0;}
			.ftpagenav  > .ftactive:hover{background:#fff;}
			.ftpagenav a:last-child{border-left:1px solid #ccc;-webkit-border-top-left-radius: 5px;-webkit-border-bottom-left-radius: 5px;-moz-border-radius-topleft: 5px;-moz-border-radius-bottomleft: 5px;border-top-left-radius: 5px;border-bottom-left-radius: 5px;}
			.ftpagenav a:first-child{-webkit-border-top-right-radius: 5px;-webkit-border-bottom-right-radius: 5px;-moz-border-radius-topright: 5px;-moz-border-radius-bottomright: 5px;border-top-right-radius: 5px;border-bottom-right-radius: 5px;}
			.ftpagenav > .ftdeactive{background:#eee;cursor:default}</style>
			<div class="ftpagenav">'.$firstpage.' '.$pervpage.' '.$paged_others.'  '.$nextpage.' '.$lastpage.'</div>
			<div class="paged-link-info">&raquo; صفحه: '.$page.' از '.$paged_total.'</div>
			';              
		}
	}
    
	function startIf($element) {
		$this->stack[] = $this->data;
	}
    

	function wrap($element) {
		$this->stack[] = $this->data;
		$this->data['index'] = 0;
		foreach ($element as $k => $v) {
			$this->data[$k] = $v;
		}
	}
	
	
	function unwrap() {
		$this->data = array_pop($this->stack);
	}
	
	function run() {
		ob_start ();
		eval (func_get_arg(0));
		return ob_get_clean();
	}
    
    
	function process($template, $data) {
		$this->data = $data;
		$this->stack = array();
		$template = str_replace('<?', '<?php echo \'<\'; ?>', $template);
		$template = preg_replace('~\{TEMPLATE:(\w+)\}~', '<?php $this->addTemplate(\'$1\' , $this->data); ?>', $template);
		$template = preg_replace('~\{(\w+)\}~', '<?php $this->showVariable(\'$1\'); ?>', $template);
		$template = preg_replace('~\{(\w+)\->(\w+)\}~', '<?php $this->showNextVariable(\'$1\' , \'$2\'); ?>', $template);
		$template = preg_replace('~\{IF2:(\w+)\->(\w+)\==(\w+)\:\[([^]]+)\];ENDIF2}~', '<?php if($this->getVariable(\'$2\' , \'$1\') == $3){	?>$4<?} ?>', $template);
		$template = preg_replace('~\{IF:(\w+)\->(\w+)\==(\w+)\:\[([^]]+)\];ENDIF}~', '<?php if($this->getVariable(\'$2\' , \'$1\') == $3){	?>$4<?} ?>', $template);
		$template = preg_replace('~\{PAGING::(\w+)\->(\w+)\}~', '<?php $this->addPagination(\'$1\', \'$2\'); ?>', $template);
		$template = preg_replace('~\{LOOP:(\w+)\}~', '<?php if(count($this->data[\'$1\']) > 0)foreach ($this->data[\'$1\'] as $ELEMENT): $this->wrap($ELEMENT); ?>', $template);
		$template = preg_replace('~\{ENDLOOP:(\w+)\}~', '<?php $this->unwrap(); endforeach; ?>', $template);

		$template = '?>' . $template;
		return $this->run($template);
	}
}
