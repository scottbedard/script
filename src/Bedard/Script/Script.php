<?php namespace Bedard\Script;

use JsMin\Minify as Minify;

class Script {

	private $environment;
	private $files = [];
	private $minified = NULL;
	private $name;
	public $path;
	private $source;
	public $input_path;
	public $output_path;
	
	/**
	 * Defines the default paths for our input and output, and default environment
	 */
	public function __construct()
	{
		$this->input_path = (function_exists('base_path')) ? base_path() . '/' : '';
		$this->output_path = (function_exists('url')) ? url('assets/js') : '/assets/js';
		$this->path = (function_exists('public_path')) ? public_path() . '/assets/js/' : '/assets/js/';
		if (function_exists('app')) { $this->environment = app()->env; }
	}

	/**
	 * Getters
	 * 		getFiles			Returns all asset files
	 * 		getEnvironment		Returns the asset environemnt
	 * 		getMinified			Returns boolean for output minification
	 * 		getSource			Returns the output source
	 * 		output				Returns the fully qualified path to the output file
	 */
	public function getEnvironment() { return $this->environment; }
	public function getFiles() { return $this->files; }
	public function getMinified() {
		if ($this->minified === NULL) return ($this->environment == 'production') ? TRUE : FALSE;
		else return $this->minified;
	}
	public function getSource() { return $this->source; }
	public function output() { return $this->output_path . '/' . $this->name; }

	/**
	 * Setters
	 * 		environment			Determines how the asset file name will be generated
	 * 		minify				Enables or disables output minification
	 * 		path				Sets the file location for the output
	 * 		src					Sets the input_path for our assets
	 */
	public function environment($environment) { $this->environment = $environment; }
	public function minify($minified = TRUE) { $this->minified = $minified; }
	public function path($path) {
		$this->path = (substr($path, -1) == '/') ? $path : $path . '/';
		$this->output_path = (substr($path, -1) == '/') ? $path : $path;
	}
	public function src($path) { $this->input_path = (substr($path, -1) == '/') ? $path : $path . '/'; }

	/**
	 * Adds one or more files to the javascript pipeline
	 *
	 * @param	string	...
	 */
	public function add()
	{
		foreach (func_get_args() as $script) {
			$asset = $this->input_path . $script;
			array_push($this->files, $asset);
		}
	}

	/**
	 * Creates the output file name, and toggles $this->minify if it hasn't already been set
	 * 
	 * @return	string
	 */
	private function hashName()
	{
		// Figure out the name of our asset file
		$name = [];

		if ( $this->environment == 'production' ) {
			// For production, build our file name from the asset file names
			foreach ($this->files as $asset) {
				array_push($name, $asset);
			}

			// Set minified to TRUE if it hasn't been set yet
			if ($this->minified === NULL) $this->minified = TRUE;
		} else {
			// If we're working locally, build our file name from the asset file sources
			foreach ($this->files as $asset) {
				if (file_exists($asset)) {
					array_push($name, file_get_contents($asset));
				}
			}

			// Set minified to FALSE if it hasn't been set yet
			if ($this->minified === NULL) $this->minified = FALSE;
		}

		// Return the file name
		return md5(implode($name)).'.js';
	}

	/**
	 * Builds the final .js file
	 */
	public function build()
	{
		// Get our output filename
		$this->name = $this->hashName();
		
		// Save the source if it doesn't already exist
		$output = $this->path.$this->name;
		if ( ! file_exists($output) ) {

			// Load our asset sources
			foreach ($this->files as $asset) {
				if (file_exists($asset)) {
					$this->source .= file_get_contents($asset);
				}
			}

			// Minify the source if needed
			if ($this->minified) {
				$this->source = Minify::minify($this->source);
			}

			// Output the file
			file_put_contents($output, $this->source);
		}
	}

}