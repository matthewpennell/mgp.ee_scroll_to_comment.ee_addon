<?php  if ( ! defined('EXT')) exit('No direct script access allowed');
/**
 * Scroll to the newly submitted comment
 *
 * An ExpressionEngine Extension that causes the page to scroll to a newly submitted
 * comment after submission
 *
 * @package		ExpressionEngine
 * @author		Matthew Pennell
 * @copyright	Copyright (c) 2008, Matthew Pennell
 * @license		http://creativecommons.org/licenses/by-sa/3.0/
 * @link		http://www.thewatchmakerproject.com/blog/new-expressionengine-extension-scroll-to-new-comment
 * @since		Version 1.0
 * @filesource
 * 
 * This work is licensed under the Creative Commons Attribution-Share Alike 3.0 Unported.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/
 * or send a letter to Creative Commons, 171 Second Street, Suite 300,
 * San Francisco, California, 94105, USA.
 * 
 */
class Scroll_to_comment {

	var $settings		= array();
	var $title			= 'Scroll To Comment';
	var $name			= 'Scroll to the newly submitted comment';
	var $version		= '1.0';
	var $description	= 'Causes the page to scroll to a newly submitted comment after submission.';
	var $settings_exist	= 'y';
	var $docs_url		= 'http://www.thewatchmakerproject.com/blog/new-expressionengine-extension-scroll-to-new-comment';

	/**
	 * Constructor
	 */
	function Scroll_to_comment($settings = '')
	{
		$this->settings = $settings;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Register hooks by adding them to the database
	 */
	function activate_extension()
	{
		global $DB;

		// default settings
		$settings =	array();
		$settings['prefix'] = 'comment';
		
		$hook = array(
						'extension_id'	=> '',
						'class'			=> __CLASS__,
						'method'		=> 'redirect_to_latest_comment',
						'hook'			=> 'insert_comment_end',
						'settings'		=> serialize($settings),
						'priority'		=> 1,
						'version'		=> $this->version,
						'enabled'		=> 'y'
					);
	
		$DB->query($DB->insert_string('exp_extensions',	$hook));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * No updates yet.
	 * Manual says this function is required.
	 * @param string $current currently installed version
	 */
	function update_extension($current = '')
	{
		global $DB, $EXT;

		if ($current < '1.0')
		{
			$query = $DB->query("SELECT settings FROM exp_extensions WHERE class = '".$DB->escape_str(__CLASS__)."'");
			
			$this->settings = unserialize($query->row['settings']);
			unset($this->settings['prefix']);
			
			$DB->query($DB->update_string('exp_extensions', array('settings' => serialize($this->settings), 'version' => $this->version), array('class' => __CLASS__)));
		}
		
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Uninstalls extension
	 */
	function disable_extension()
	{
		global $DB;
		$DB->query("DELETE FROM exp_extensions WHERE class = '".__CLASS__."'");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * EE extension settings
	 * @return array
	 */
	function settings()
	{
		$settings = array();
		
		$settings['prefix'] = "comment";
		
		return $settings;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Redirects the page to the permalink of the last submitted comment
	 * 
	 * @param array $data The comment data
	 * @param string $comment_moderate y/n flag
	 * @param integer $comment_id The id of the latest comment
	 */
	function redirect_to_latest_comment($data, $comment_moderate, $comment_id)
	{
		global $EXT, $LANG, $OUT, $FNS;
		
		if ($comment_moderate == 'y')
        {
			$data = array(	'title' 	=> $LANG->line('cmt_comment_accepted'),
							'heading'	=> $LANG->line('thank_you'),
							'content'	=> $LANG->line('cmt_will_be_reviewed'),
							'redirect'	=> $_POST['RET'] . '#' . $this->settings['prefix'] . $comment_id,							
							'link'		=> array($_POST['RET'] . '#' . $this->settings['prefix'] . $comment_id, $LANG->line('cmt_return_to_comments')),
							'rate'		=> 3
						 );
					
			$OUT->show_message($data);
		}
		else
		{
        	$FNS->redirect($_POST['RET'] . '#' . $this->settings['prefix'] . $comment_id);
    	}

		$EXT->end_script = TRUE;
	
	}
	
	// --------------------------------------------------------------------
	
}
// END CLASS Scroll_to_comment

/* End of file ext.scroll_to_comment.php */
/* Location: ./system/extensions/ext.scroll_to_comment.php */