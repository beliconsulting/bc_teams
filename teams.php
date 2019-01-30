<?php
/*
* Plugin Name:      BC-Teams
* Description:      A plugin to manage teams
* Author:           Bjoern Becker 
* Version:          0.1
* Author URI:       https://beli-consulting.com
* License:          GNU General Public License, version 3 (GPLv3)
* License URI:      http://www.gnu.org/licenses/gpl-3.0.txt
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_menu', 'bc_plugin_setup_menu');
 
function clean($string) {
  $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.
  return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
 
function bc_plugin_setup_menu(){
  add_menu_page( 'Teams Plugin Page', 'Teams', 'manage_options', 'bc-plugin', 'bc_init' );
}

add_action('wp_enqueue_scripts', 'callback_for_setting_up_scripts');
function callback_for_setting_up_scripts() {
    wp_register_style( 'namespace', '/wp-content/plugins/bc_teams/style.css' );
    wp_enqueue_style( 'namespace' );
}


function bc_load_options($params){
  $all_options = wp_load_alloptions();
  $my_options  = array();
  $shorts      = array();
  $cats        = get_option($team_prefix);
  $cats        = explode(',', $cats);

  if($params != ""){
    foreach($cats as $cat){
      if($params == "cat"){
        $show_cat = True;
      }
    }
  }
   
  foreach ( $all_options as $name => $value ) {
    if ( stristr( $name, 'bc_teams' ) ) {
      $my_option[$name] = $value;
      $short = explode("-", $name);
      //delete_option("$name");
      //echo "<pre>";
      //print_r($my_option);
      //echo "</pre>";
      if($params != ""){
        if($show_cat != True){
          if($short['1'] == $params){
            $shorts[$short['2']] = $value;
          }
        }else{
          echo "do cat";
        }
      }else{
        if($short['1'] != ""){
          if(!in_array($short['1'], $shorts)){
            $shorts[$short['1']][$short['2']] = $value;
          }
        }
      }
    }
  }
  return $shorts;
}

function bc_init(){
  echo "<h1>Teams!</h1>";

  $team_prefix = "bc_teams-cat";
  $shorts = bc_load_options("");
  $cats   = get_option($team_prefix);
  $cats   = explode(',', $cats);

  if(isset($_POST)){
    $s = $_POST['bc_short'];
    if($shorts[$s]['name'] != ""){
      echo "<h2>Shortcode</h2>";
      echo "<h3>[bc_teams_show_member name='".$s."']</h3>";
    }
    $bc_name = $shorts[$s]['name'];
    $bc_posi = $shorts[$s]['posi'];
    $bc_cate = $shorts[$s]['cate'];
    $bc_pict = $shorts[$s]['pict'];
    $bc_desc = $shorts[$s]['desc'];
    $bc_show = $shorts[$s]['show'];
    $bc_more = $shorts[$s]['more'];
    $bc_read = $shorts[$s]['read'];
    $bc_mail = $shorts[$s]['mail'];
    $bc_web  = $shorts[$s]['web'];
    $bc_fb   = $shorts[$s]['fb'];
    $bc_tw   = $shorts[$s]['tw'];
  }
   
  echo "<h4>Team Members</h4>";
  echo "<form name='bc_manage' method='POST' action=''>";
  echo "<table>";
  echo "<tr>";
  echo "<td>Shortcut:</td><td><select onChange='this.form.submit();' name='bc_short'>";
    echo "<option name='new'></option>";
    foreach($shorts as $k => $v){
      if($k != "cat"){
        echo "<option name='$k'>".$k."</option>";
      }
    }
  echo "</select></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Name:</td><td><input name='bc_name' value='".$bc_name."' required></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Position:</td><td><input name='bc_posi' value='".$bc_posi."'></td>";
  echo "</tr>";
  echo "<td>Category:</td><td><select name='bc_cate' required>";
    echo "<option name='cat'></option>";
    foreach($cats as $cat){
      if($cat != ""){
        if($bc_cate == $cat){
          echo "<option selected name='$cat'>".$cat."</option>";
        }else{
          echo "<option name='$cat'>".$cat."</option>";
        }
      }
    }
  echo "</select></td>";
  echo "<tr>";
  echo "<td>Picture:</td><td><input name='bc_pict' value='".$bc_pict."'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>E-Mail:</td><td><input name='bc_mail' value='".$bc_mail."'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Web:</td><td><input name='bc_web' value='".$bc_web."'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Facebook:</td><td><input name='bc_fb' value='".$bc_fb."'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Twitter:</td><td><input name='bc_tw' value='".$bc_tw."'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>Description:</td><td><textarea rows='20' cols='80' name='bc_desc'>".$bc_desc."</textarea></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>ShowCase:</td><td><textarea rows='20' cols='80' name='bc_show'>".$bc_show."</textarea></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>ReadMore Link:</td><td><input name='bc_more' value='".$bc_more."'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td>ReadMore Name:</td><td><input name='bc_read' value='".$bc_read."'></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td><input type='submit' name='bc_save' value='Save'></td>";
  echo "</tr>";
  echo "</table>";
  echo "</form>";

  echo "<h4>Add Category</h4>";
  echo "<form name='bc_manage_cat' method='POST' action=''>";
  echo "<table>";
  echo "<tr>";
  echo "<td>Name:</td><td><input name='bc_cat_name' required></td>";
  echo "</tr>";
  echo "<tr>";
  echo "<td><input type='submit' name='bc_save_cat' value='Add'></td>";
  echo "</tr>";
  echo "</table>";
  echo "</form>";

  if(isset($_POST['bc_save_cat'])){
    $cats = get_option($team_prefix);
    update_option($team_prefix, $cats.",".$_POST['bc_cat_name']);
  }
  if(isset($_POST['bc_save'])){
    if($_POST['bc_short'] != "new"){
      $shortcut = clean($_POST['bc_name']);
    }else{
      $shortcut = $_POST['bc_short'];
    }
    
    $team_prefix = "bc_teams-".$shortcut;
    update_option($team_prefix."-name", $_POST['bc_name']);
    update_option($team_prefix."-posi", $_POST['bc_posi']);
    update_option($team_prefix."-cate", $_POST['bc_cate']);
    update_option($team_prefix."-pict", $_POST['bc_pict']);
    update_option($team_prefix."-desc", $_POST['bc_desc']);
    update_option($team_prefix."-show", $_POST['bc_show']);
    update_option($team_prefix."-more", $_POST['bc_more']);
    update_option($team_prefix."-read", $_POST['bc_read']);
    update_option($team_prefix."-mail", $_POST['bc_mail']);
    update_option($team_prefix."-web", $_POST['bc_web']);
    update_option($team_prefix."-fb", $_POST['bc_fb']);
    update_option($team_prefix."-tw", $_POST['bc_tw']);
  }
}

function bc_teams_show_member($params)
{
  if($params['category'] != ""){
    $shorts = bc_load_options($params['category']);
  }

  if($params['name'] != ""){
    $shorts = bc_load_options($params['name']);
    if($shorts['name'] == ""){
      return "Doesn't exist";
    }

    $bc_name = $shorts['name'];
    $bc_posi = $shorts['posi'];
    $bc_cate = $shorts['cate'];
    $bc_pict = $shorts['pict'];
    $bc_desc = $shorts['desc'];
    $bc_show = $shorts['show'];
    $bc_more = $shorts['more'];
    $bc_read = $shorts['read'];
    $bc_mail = $shorts['mail'];
    $bc_web  = $shorts['web'];
    $bc_fb   = $shorts['fb'];
    $bc_tw   = $shorts['tw'];

    if($params['type'] == ""){
      $html = "<div class='bc_teams member'>";
      $html .= "<img id='bc_team_pict' src=$bc_pict alt='bc_teams picture'>";
      $html .= "<h3 id='bc_team_name'>$bc_name</h3>";
      $html .= "<strong id='bc_team_posi'>$bc_posi</strong>";
      $html .= "<article id='bc_team_desc'>$bc_desc</article>";
      $html .= "</div>";
      $content = $html;
    }elseif($params['type'] == "showcase"){
      $html  = "<div class='bc_teams showcase'>";

      $html  .= "<div class='bc_showcase_top'>";
        $html .= "<div class='bc_showcase_top_left'>";
          $html .= "<img id='bc_team_pict' src=$bc_pict alt='bc_teams picture'>";
        $html .= "</div>";
        $html .= "<div class='bc_showcase_top_right'>";
          $html .= "<article id='bc_team_show'>$bc_show</article>";
          if($bc_more != ""){
            if($bc_read != ""){
              $html .= "<strong id='bc_team_readmore'><a href='".$bc_more."'>".$bc_read."</a></strong>";
            }else{
              $html .= "<strong id='bc_team_readmore'><a href='".$bc_more."'>Read More</a></strong>";
            }
          }
        $html .= "</div>";
      $html .= "</div>";

      $html  .= "<div class='bc_showcase_bot'>";
        $html .= "<div class='bc_showcase_bot_left'>";
          $html .= "<h3 id='bc_team_name'>$bc_name</h3>";
          $html .= "<strong id='bc_team_posi'>$bc_posi</strong>";
        $html .= "</div>";
        $html .= "<div class='bc_showcase_bot_right'>";
          $html .= "<ul id='bc_showcase_social'>";
          if($bc_mail != ""){
            $html .= "<li><a href='mailto:".$bc_mail."'><span class='typcn typcn-mail'></span></a></li>"; 
          }
          if($bc_web != ""){
            $html .= "<li><a target='_BLANK' href='".$bc_web."'><span class='typcn typcn-home'></span></a></li>"; 
          }
          if($bc_fb != ""){
            $html .= "<li><a target='_BLANK' href='".$bc_fb."'><span class='typcn typcn-social-facebook'></span></a></li>"; 
          }
          if($bc_tw != ""){
            $html .= "<li><a target='_BLANK' href='".$bc_tw."'><span class='typcn typcn-social-twitter'></span></a></li>"; 
          }
          $html .= "</ul>";
        $html .= "</div>";
      $html .= "</div>";

      $html .= "</div>";
      $content = $html;
    }
  }

  return $content;
}
add_shortcode('bc_teams_show_member', 'bc_teams_show_member');

?>
