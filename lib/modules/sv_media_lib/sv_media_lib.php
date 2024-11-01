<?php
namespace sv_media_library;

class sv_media_lib extends modules {
	public function init(){
		// Section Info
		$this->set_section_title( __( 'Media Library', 'sv_media_library' ) )
			->set_section_desc( __( 'Settings for Media Library', 'sv_media_library' ) )
			->set_section_type( 'settings' )
			->load_settings()
			->get_root()
			->add_section( $this );

		// Action Hooks
		// deactivate WPThumb plugin as it's not compatible with media lib feature
		// plugin-uri: https://wordpress.org/plugins/wp-thumb/
		remove_filter( 'image_downsize', 'wpthumb_post_image', 99 );

		return $this;
	}

	public function load_settings() :sv_media_lib{
		$this->get_setting( 'path' )
			->set_title( __( 'Media Lib Path', 'sv_media_library' ) )
			->set_description( __( 'Absolute path to media library base directory, replacing default ' , 'sv_media_library' ) . ABSPATH . '/wp-content/uploads/' )
			->load_type( 'text' );

		$this->get_setting( 'url' )
			->set_title( __( 'Media Lib URL', 'sv_media_library' ) )
			->set_description( __( 'Absolute URL to media library directory, replacing default ' , 'sv_media_library' ) . get_home_url() . '/wp-content/uploads/' )
			->load_type('text');

		$this->set_media_paths();

		return $this;
	}

	public function set_media_paths() :sv_media_lib{
		if ($this->get_setting('url')->get_data() && $this->get_setting('path')->get_data() && file_exists($this->get_setting('path')->get_data()) ) {
			add_filter( 'upload_dir', function( $param ) {
				$param['baseurl']		= $this->get_setting('url')->get_data();
				$param['basedir']		= $this->get_setting('path')->get_data();

				$param['path']			= $param['basedir'].$param['subdir'];
				$param['url']			= $param['baseurl'].$param['subdir'];

				return $param;
			} );

			// set imagify backupdir within wp-content again to avoid bogus nor writable errors due to abspath check of imagify
			add_filter( 'imagify_backup_directory', function() { return trailingslashit( ABSPATH ) . 'wp-content/imagify-backups/'; } );
		}

		return $this;
	}
}