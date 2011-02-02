<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

   /**
	* 3Easy Last Editor Extension
	*
	* @package		3Easy Last Editor
	* @version		1.0.0
	* @author		3Easy <http://3easy.org>
	* @copyright	Copyright (c) 2011 3Easy <http://3easy.org>
	* @license		http://creativecommons.org/licenses/by-sa/3.0/
	* @see			https://github.com/3Easy/lasteditor
	*/
	
	class Last_editor_ext 
	{
	
		public $name			= '3Easy Last Editor';
		public $version			= '1.0.0';
		public $description		= 'Add {last_editor} tag to exp:channel:entries';
		public $settings_exist	= 'n';
		public $docs_url		= 'https://github.com/3Easy/lasteditor';
		
	   /**
		* Constructor
		*
		* @access	public
		* @return	void
		* 
		*/
		function __construct($settings = '') 
		{
		
		$this->EE =& get_instance();
		//		$this->settings = $settings;
		
		}
		
	   /**
		* Activate
		*
		* @access	public
		* @return	void
		* 
		**/
		public function activate_extension() 
		{
			
			$this->settings = array(
			
		);
		
		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'set_last_editor',
			'hook'		=> 'entry_submission_end',
			'settings'	=> serialize($this->settings),
			'priority'	=> 10,
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);
		
		$this->EE->db->insert('extensions', $data);
		
		$data = array(
			'class'		=> __CLASS__,
			'method'	=> 'get_last_editor',
			'hook'		=> 'channel_entries_tagdata_end',
			'settings'	=> serialize($this->settings),
			'priority'	=> 10,
			'version'	=> $this->version,
			'enabled'	=> 'y'
		);
		
		$this->EE->db->insert('extensions', $data);
		
		$this->EE->db->query("ALTER TABLE exp_channel_titles ADD last_editor_id int(10) AFTER author_id");
		
		}
		
	   /**
		* Disable
		*
		* @access	public
		* @return	void
		* 
		*/
		public function disable_extension() 
		{
			
			$this->EE->db->where('class', __CLASS__);
			$this->EE->db->delete('extensions');
			
			$this->EE->db->query("ALTER TABLE exp_channel_titles DROP last_editor_id");
			
		}
		
	   /**
		* Update
		*
		* @access	public
		* @return	void
		* 
		*/
		public function update_extension($current=FALSE) 
		{
			
			if ($current == '' OR $current == $this->version) 
			{
				return FALSE;
			}
			
			if ($current < '1.0')
			{
				// Update to version 1.0
			}
			
			$this->EE->db->where('class', __CLASS__);
			$this->EE->db->update(
				'extensions', 
				array('version' => $this->version)
			);
			
		}

		
	   /**
		* Set Last Editor
		* 
		* @access	public
		* @return	void
		* 
		*/
		public function set_last_editor($entry_id) 
		{
			
			$last_editor = $this->EE->session->userdata['member_id'];
			$this->EE->db->query("UPDATE exp_channel_titles SET last_editor_id =  '" . $last_editor . "' WHERE entry_id = '" . $entry_id ."'");
			
		}
		
	   /**
		* Get Last Editor
		* 
		* @access	public
		* @return	$tagdata
		* 
		*/
		public function get_last_editor($tagdata, $row) 
		{
		
		if(in_array('last_editor', $this->EE->TMPL->var_single))
		{
			
			$result = $this->EE->db->query("SELECT last_editor_id, screen_name FROM exp_channel_titles INNER JOIN exp_members ON member_id = IF(last_editor_id IS NULL, author_id, last_editor_id) WHERE entry_id = '" . $row['entry_id'] ."'");
			$last_editor_name = $result->row('screen_name');
			$tagdata = $this->EE->TMPL->swap_var_single('last_editor', $last_editor_name, $tagdata);
			
		}
		
		return $tagdata;
		
		}
		
	}

// END CLASS

/* End of file ext.last_editor.php */
/* Location: ./system/expressionengine/third_party/last_editor/ext.last_editor.php */