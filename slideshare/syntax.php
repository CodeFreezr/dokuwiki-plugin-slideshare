<?php
/**
 * Plugin Slideshare: Create Slideshare link and object from ID.
 *
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Detlef Burkhardt
 * @version    0.1.0
 * @update     2011-09-20
 *

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_slideshare extends DokuWiki_Syntax_Plugin {
  var $html;
  var $pattern;
  
  function syntax_plugin_slideshare(){
    $this->html    = @file_get_contents(DOKU_PLUGIN.'slideshare/div.htm');
    $this->pattern = '/\{\{(\s?)slide>(|link):([^} |]+)\|?(.*?)(\s?)\}\}/';
  }
  
  function getInfo() {
    return array(
    'author' => 'Detlef Burkhardt',
    'email'  => 'burkhardt@web.de',
    'date'   => '2008-04-05',
    'name'   => 'Slideshare Plugin',
    'desc'   => 'Slideshare link and object{{slide>[|link]:ID}}',
    'url'    => 'github commes here',
    );
  }
  
  function getType(){ return 'substition'; }
  function getSort(){ return 159; }
  function connectTo($mode) { $this->Lexer->addSpecialPattern('\{\{\s?slide>[^}]*\s?\}\}',$mode,'plugin_slideshare'); }
  
  function handle($match, $state, $pos, &$handler){
    $pm = preg_match_all($this->pattern,$match,$result);
    $left  = ($result[1][0]==" ");
    $right = ($result[5][0]==" ");
    $cmd   = $result[2][0];
    $id    = $result[3][0];
    $title = $result[4][0];
    if ($left==true && $right==true){
      $align = 'center';
    }else if($left==true){
      $align = 'right';
    }else if($right==true){
      $align = 'left';
    }
    return array($state, array($cmd,$id,$align,$title));
  } 

  function render($mode, &$renderer, $data){
    if($mode != 'xhtml'){return false;}
    list($state, $match) = $data;
    list($cmd,$id,$align,$title) = $match;
    $id    = urlencode($id);
    $title = urlencode($title);
    $title = str_replace("+"," ",$title);
    
    if ($cmd=='link') {
        $lnkFormat='<a href="http://www.slideshare.net/slideshow/embed_code/%s" title="Slideshare-Link: %s">';
        $href_start=sprintf($lnkFormat,$id,empty($title)?$id:$title.' ('.$id.')');
        $renderer->doc.=$href_start.'<div class="slide_icon">'.$title.'</div></a>';
        return true;
    } else {
        if ($align=='center'){$renderer->doc.="<center>";}        
        $renderer->doc.=sprintf($this->html,$id,425,350,$align,$id,425,350,$align,$title);
        if ($align=='center'){$renderer->doc.="</center>";}
        $renderer->doc.=NL;
        return true;
    }

/*    
    switch($cmd){
      case 'link':
        $lnkFormat='<a href="http://www.slideshare.net/slideshow/embed_code/%s" title="Slideshare-Link: %s">';
        $href_start=sprintf($lnkFormat,$id,empty($title)?$id:$title.' ('.$id.')');
        $renderer->doc.=$href_start.'<div class="slide_icon">'.$title.'</div></a>';
        return true;
        
      case "em":
        if ($align=='center'){$renderer->doc.="<center>";}
        
        $renderer->doc.=sprintf($this->html,$id,425,350,$align,$id,425,350,$align,$title);
        if ($align=='center'){$renderer->doc.="</center>";}
        $renderer->doc.=NL;
        return true;
        

      case 'small':
        if ($align=='center'){$renderer->doc.="<center>";}
        $renderer->doc.=sprintf($this->html,255,210,$id,$align,$title,$id);
        if ($align=='center'){$renderer->doc.="</center>";}
        return true;
       
    }
*/ 
    $renderer->doc.=NL;
  }
}
?>