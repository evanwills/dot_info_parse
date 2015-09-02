<?php

require_once(dirname(__file__).'/parse_dot_info.class.php');


class extract_dot_info_test extends PHPUnit_Framework_TestCase
{
	protected $sample_content = 'split_sample = false
split_delim = "\n"
regex_delim : `
sample_len:300;
matched_len:200;
action:test;
regexes[0][find] = \'[abc]+;\'
regexes[0][replace] = "begining";
regexes[0][modifiers] = i;

regexes[1][find] = \"[wxy]+;
regexes[1][replace] = end;
regexes[1][modifiers] = i;

ws_trim:false;
ws_action:false;';
	protected $sample_file = 'parse_dot_info.sample.info';
	
	public function test__constructInfoContentIsValid() {
		$sample = array( $this->sample_content , $this->sample_file );

		for( $a = 0 ; $a < 2 ; $a += 1 ) {
			try {
				$tmp = new dot_info($sample[$a]);
				$this->assertEquals(get_class($tmp),'dot_info');
			} catch( exception $e ) {
				$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
			}
		}
	}

	public function test__constructInfoContentIsNotValid() {

		$sample = array( '' , 1 , 1.5 , false , true , array('sample' => 'blah',0 => 'blood') , 'extract-dot-info.sample.info' );

		for( $a = 0 ; $a < count($sample) ; $a += 1 ) {
			try {
				$tmp = new dot_info($sample[$a]);
				$this->fail('No exception raised for Invalid first parameter dot_info::__construct()');
			} catch( exception $e ) { }
		}
	}



	public function test__constructOptionIsValid() {
		
		$sample = array(
			dot_info::MATCH_CASE , 'match_case' , 'match case' , 'matchcase',
			dot_info::IGNORE_CASE , 'ignore_case' , 'ignore case' , 'ignorecase',
			dot_info::STORE_ERRORS , 'store_errors' , 'store errors' , 'storeerrors',
			dot_info::THROW_ERRORS , 'throw_errors' , 'throw errors' , 'throwerrors',
			dot_info::READ_ONLY , 'read_only' , 'read only' , 'readonly',
			dot_info::ADD_NEW_ONLY , 'add_new_only' , 'add new only' , 'addnewonly'
		);

		for( $a = 0 ; $a < 2 ; $a += 1 ) {
			try {
				$tmp = new dot_info( $this->sample_file , $sample[$a] );
				$this->assertEquals(get_class($tmp),'dot_info');
			} catch( exception $e ) {
				$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
			}
		}
	}

	public function test__constructOptionIsNotValid() {
		$sample = array( '' , 1 , 1.5 , array('sample' => 'blah',0 => 'blood') , 'extract-dot-info.sample.info' );

		for( $a = 0 ; $a < count($sample) ; $a += 1 ) {
			try {
				$tmp = new dot_info( $this->sample_file , $sample[$a] );
				$this->fail('No exception raised for Invalid second parameter dot_info::__construct()');
			} catch( exception $e ) { }
		}
	}



// ==================================================================



	public function testInfoExistsValidParameters() {
		$info = new dot_info($this->sample_file);
		$this->assertEquals(true,$info->info_exists('split_sample'));
		$this->assertEquals(true,$info->info_exists('split_delim'));
		$this->assertEquals(true,$info->info_exists('regex_delim'));
		$this->assertEquals(true,$info->info_exists('sample_len'));
		$this->assertEquals(true,$info->info_exists('test_float'));
		$this->assertEquals(true,$info->info_exists('regexes'));
		$this->assertEquals(true,$info->info_exists('regexes',1));
		$this->assertEquals(true,$info->info_exists('regexes',1,'find'));
		$this->assertEquals(true,$info->info_exists('regexes',0,'modifiers'));
		$this->assertEquals(true,$info->info_exists('regexes',1,'modifiers'));
		$this->assertEquals(true,$info->info_exists('regexes',1,'modifiers',2));

	}

	public function testInfoExistsInvalidParameter() {
		$sample = array( '' , 1 , 1.5 , false , true , array('sample' => 'blah',0 => 'blood') , 'extract-dot-info.sample.info' );
		$info = new dot_info($this->sample_file);
		for( $a = 0 ; $a < count($sample) ; $a += 1 )
		{
			$this->assertEquals(false,$info->info_exists($sample[$a]));
			try {
				$info->info_exists($sample[$a]);
//				$this->fail('No exception raised for Invalid first parameter dot_info::info_is_set()');
			} catch( exception $e ) {
				$this->fail('Exception appropriatly raised for dot_info::info_exists(). "'.$e->getMessage().'"');
			}
		}
	}

	public function testInfoExistsCaseSensitivityIssue() {
		$info = new dot_info($this->sample_file,dot_info::MATCH_CASE);

		$this->assertEquals(false,$info->info_exists('Regexes'));

		$this->assertEquals(true,$info->info_exists('regexes'));
	}

	public function testInfoExistsCaseInsensitivityIssue() {
		$info = new dot_info($this->sample_file);

		$this->assertEquals(true,$info->info_exists('Regexes'));

		$this->assertEquals(true,$info->info_exists('Regexes'));
	}




// ==================================================================




	public function testGetInfoValidParameters() {
		$info = new dot_info($this->sample_file);
		try {
			$tmp = $info->get_info('split_sample');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'boolean');

		try {
			$tmp = $info->get_info('split_delim');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'string');
		$this->assertEquals('\n',$tmp);

		try {
			$tmp = $info->get_info('sample_len');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'integer');
		$this->assertEquals(300,$tmp);

		try {
			$tmp = $info->get_info('test_float');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'double');
		$this->assertEquals(1.5,$tmp);

		try {
			$tmp = $info->get_info('zero_test');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'string');
		$this->assertEquals('0001',$tmp);

		try {
			$tmp = $info->get_info('regexes');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'array');

		try {
			$tmp = $info->get_info('regexes',1);
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'array');

		try {
			$tmp = $info->get_info('regexes',1,'find');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'string');
		$this->assertEquals('\"[wxy]+',$tmp);

		try {
			$tmp = $info->get_info('regexes',0,'modifiers');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'string');
		$this->assertEquals('i',$tmp);

		try {
			$tmp = $info->get_info('regexes',1,'modifiers');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'array');

		try {
			$tmp = $info->get_info('regexes',1,'modifiers',2);
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'string');
		$this->assertEquals('x',$tmp);
	}

	public function testGetInfoInvalidParameter() {
		$sample = array( '' , 1 , 1.5 , false , true , array('sample' => 'blah',0 => 'blood') , 'extract-dot-info.sample.info' );
		$info = new dot_info($this->sample_file);
		for( $a = 0 ; $a < count($sample) ; $a += 1 )
		{
			try {
				$info->get_info($sample[$a]);
				$this->fail('No exception raised for Invalid first parameter dot_info::get_info()');
			} catch( exception $e ) {

			}
		}
	}

	public function testGetInfoCaseSensitivityIssue() {
		$info = new dot_info($this->sample_file,dot_info::MATCH_CASE);
		try {
			$tmp = $info->get_info('Regexes');
			$this->fail('No exception raised for Invalid first parameter dot_info::get_info()');
		} catch( exception $e ) {
		}

		try {
			$tmp = $info->get_info('regexes');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'array');
	}

	public function testGetInfoCaseInsensitivityIssue() {
		$info = new dot_info($this->sample_file);
		try {
			$tmp = $info->get_info('Regexes');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'array');
		try {
			$tmp = $info->get_info('regexes');
		} catch( exception $e ) {
			$this->fail('Exception raised for valid input. "'.$e->getMessage().'"');
		}
		$this->assertEquals(gettype($tmp),'array');
	}

	public function testGetInfoDontThrowInvalidParameter1() {
		$sample = array(
			 'Cannot use empty string as a property name of dot_info.' => ''
			,'Cannot use boolean as a property name of dot_info.' => false
			,'Cannot use array as a property name of dot_info.' => array('sample' => 'blah',0 => 'blood')
		);
		$info = new dot_info($this->sample_file,dot_info::STORE_ERRORS);
		foreach( $sample as $msg => $value )
		{
			try {
				$info->get_info($value);
			} catch( exception $e ) {
				$this->fail('Exception raised for valid input. "'.$e->getMessage().'" - ('.$info->get_last_error().')');
			}
			$this->assertEquals($msg,$info->get_last_error());
		}


		$sample = array(
			 'Cannot use empty string as an array index in dot_info::$regexes in dot_info::get_info()' => ''
			,'Cannot use boolean as an array index in dot_info::$regexes in dot_info::get_info()' => false
			,'Cannot use array as an array index in dot_info::$regexes in dot_info::get_info()' => array('sample' => 'blah',0 => 'blood')
		);
		$info = new dot_info($this->sample_file,dot_info::STORE_ERRORS);
		foreach( $sample as $msg => $value )
		{
			try {
				$info->get_info('regexes',$value);
			} catch( exception $e ) {
				$this->fail('Exception raised for valid input. "'.$e->getMessage().'" - ('.$info->get_last_error().')');
			}
			$this->assertEquals($msg,$info->get_last_error());
		}
	}




// ==================================================================




	public function testGetInfoCount() {
		$info = new dot_info($this->sample_file);
		$this->assertEquals('integer',gettype($info->get_info_count()));
		$this->assertEquals(11,$info->get_info_count());
	}




// ==================================================================




	public function testAddInfoReadOnlyNewPorperty() {
		$info = new dot_info($this->sample_file);
		$this->assertEquals(false,$info->add_info('this one is added','add_new'));
		$this->assertEquals(false,$info->info_exists('add_new'));
	}

	public function testAddInfoReadOnlyExtraIncrementalKey() {
		$info = new dot_info($this->sample_file);
		$this->assertEquals(false,$info->add_info('^found it$','regexes','[]','find'));
		$this->assertEquals(false,$info->info_exists('regexes',2,'find'));
	}

	public function testAddInfoReadOnlyExtraPresetKey() {
		$info = new dot_info($this->sample_file);

		$this->assertEquals(false,$info->add_info('random stuff','regexes','1','what-tha'));
		$this->assertEquals(false,$info->info_exists('random stuff','regexes','1','what-tha'));
	}

	public function testAddInfoReadOnlyExtraDeepPresetKey1() {
		$info = new dot_info($this->sample_file);

		$this->assertEquals(false,$info->add_info('very deep','new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals(false,$info->info_exists('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
	}

	public function testAddInfoReadOnlyUpdateExistingProperty() {
		$info = new dot_info($this->sample_file);

		$this->assertEquals(false,$info->add_info('this one is NOT added','test_float'));
		$this->assertEquals(false,$info->info_exists('this one is NOT added','test_float'));
	}




// ==================================================================





	public function testAddInfoAddNewOnlyNewPorperty() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY);

		$this->assertEquals(true,$info->add_info('this one is added','add_new'));
		$this->assertEquals(true,$info->info_exists('add_new'));
		$this->assertEquals('this one is added',$info->get_info('add_new'));
	}
	
	public function testAddInfoAddNewOnlyExtraIncrementalKey() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY);

		$this->assertEquals(true,$info->add_info('^found it$','regexes','[]','find'));
		$this->assertEquals(true,$info->info_exists('regexes',2,'find'));
		$this->assertEquals('^found it$',$info->get_info('regexes',2,'find'));
	}

	public function testAddInfoAddNewOnlyExtraIncrementalKeyMatchCase() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY,dot_info::MATCH_CASE);
		$this->assertEquals(true,$info->add_info('^completely different branch$','Regexes','[]','find'));
		$this->assertEquals(true,$info->info_exists('Regexes',0,'find'));
		$this->assertEquals(true,( $info->get_info('Regexes',0,'find') !== $info->get_info('regexes',0,'find') ) );
		$this->assertEquals('^completely different branch$',$info->get_info('Regexes',0,'find'));
	}
	
	public function testAddInfoAddNewOnlyExtraPresetKey() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY);

		$this->assertEquals(true,$info->add_info('random stuff','regexes','1','what-tha'));
		$this->assertEquals(true,$info->info_exists('regexes','1','what-tha'));
		$this->assertEquals('random stuff',$info->get_info('regexes','1','what-tha'));
	}
	
	public function testAddInfoAddNewOnlyExtraDeepPresetKey1() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY);

		$this->assertEquals(true,$info->add_info('0015','new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_6'));
		$this->assertEquals(true,$info->info_exists('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_6'));
		$this->assertEquals('0015',$info->get_info('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_6'));
	}
	
	public function testAddInfoAddNewOnlyExtraDeepPresetKey2() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY);

		$this->assertEquals(true,$info->add_info('very deep','new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals(true,$info->info_exists('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals('very deep',$info->get_info('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
	}
	
	public function testAddInfoAddNewOnlyUpdateExtradDeepPresetKey2() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY);

		$this->assertEquals(true,$info->add_info('very deep','new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals(true,$info->info_exists('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals('very deep',$info->get_info('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));

		$this->assertEquals(false,$info->add_info(3.1456,'new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals(true,$info->info_exists('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals('very deep',$info->get_info('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
	}
	
	public function testAddInfoAddNewOnlyNewFloatProperty() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY);

		$this->assertEquals(true,$info->add_info('3.141592654','test_pie'));
		$this->assertEquals(true,$info->info_exists('test_pie'));
		$this->assertEquals(3.141592654,$info->get_info('test_pie'));
	}
	
	public function testAddInfoAddNewOnlyUpdateExistingProperty() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY);

		$this->assertEquals(false,$info->add_info('this one is NOT added','test_float'));
		$this->assertEquals(true,$info->info_exists('test_float'));
		$this->assertEquals(1.5,$info->get_info('test_float'));
	}

	
	public function testAddInfoAddNewOnlyUpdateExistingRegexIndex() {
		$info = new dot_info($this->sample_file,dot_info::ADD_NEW_ONLY);

		$this->assertEquals(false,$info->add_info('^regex$','regexes',0,'find'));
		$this->assertEquals(true,$info->info_exists('regexes',0,'find'));
		$this->assertEquals('[abc]+;',$info->get_info('regexes',0,'find'));
	}




// ==================================================================




	public function testAddInfoOverwriteNewPorperty() {
		$info = new dot_info($this->sample_file,dot_info::OVERWRITE);

		$this->assertEquals(true,$info->add_info('this one is added','add_new'));
		$this->assertEquals(true,$info->info_exists('add_new'));
		$this->assertEquals('this one is added',$info->get_info('add_new'));
	}

	public function testAddInfoOverwriteExtraIncrementalKey() {
		$info = new dot_info($this->sample_file,dot_info::OVERWRITE);

		$this->assertEquals(true,$info->add_info('^found it$','regexes','[]','find'));
		$this->assertEquals(true,$info->info_exists('regexes',2,'find'));
		$this->assertEquals('^found it$',$info->get_info('regexes',2,'find'));
	}

	public function testAddInfoOverwriteExtraPresetKey() {
		$info = new dot_info($this->sample_file,dot_info::OVERWRITE);

		$this->assertEquals(true,$info->add_info('random stuff','regexes','1','what-tha'));
		$this->assertEquals(true,$info->info_exists('regexes','1','what-tha'));
		$this->assertEquals('random stuff',$info->get_info('regexes','1','what-tha'));
	}

	public function testAddInfoOverwriteExtraDeepPresetKey1() {
		$info = new dot_info($this->sample_file,dot_info::OVERWRITE);


		$this->assertEquals(true,$info->add_info('very deep','new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals(true,$info->info_exists('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals('very deep',$info->get_info('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
	}

	public function testAddInfoOverwriteUpdateExtradDeepPresetKey1() {
		$info = new dot_info($this->sample_file,dot_info::OVERWRITE);

		$this->assertEquals(true,$info->add_info('very deep','new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals(true,$info->info_exists('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals('very deep',$info->get_info('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));

		$this->assertEquals(true,$info->add_info('0015','new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals(true,$info->info_exists('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
		$this->assertEquals('0015',$info->get_info('new_prop','new_prop_key_1','new_prop_key_2','new_prop_key_3','new_prop_key_4','new_prop_key_5'));
	}

	public function testAddInfoOverwriteNewFloatProperty() {
		$info = new dot_info($this->sample_file,dot_info::OVERWRITE);

		$this->assertEquals(true,$info->add_info('3.141592654','test_pie'));
		$this->assertEquals(true,$info->info_exists('test_pie'));
		$this->assertEquals(3.141592654,$info->get_info('test_pie'));
	}

	public function testAddInfoOverwriteUpdateExistingProperty() {
		$info = new dot_info($this->sample_file,dot_info::OVERWRITE);
		$tmp = $info->get_info('test_float');
		$this->assertEquals(true,$info->add_info('this one overwrote the old one ('.$tmp.')','test_float'));
		$this->assertEquals(true,$info->info_exists('test_float'));
		$this->assertEquals('this one overwrote the old one ('.$tmp.')',$info->get_info('test_float'));
	}

	
	public function testAddInfoOverwriteUpdateExistingRegexIndex() {
		$info = new dot_info($this->sample_file,dot_info::OVERWRITE);
		$this->assertEquals(true,$info->add_info('^regex$','regexes',0,'find'));
		$this->assertEquals(true,$info->info_exists('regexes',0,'find'));
		$this->assertEquals('^regex$',$info->get_info('regexes',0,'find'));
	}
}
