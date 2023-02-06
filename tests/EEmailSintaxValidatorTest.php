<?php
/*
*Test for EEmailSintaxValidator.php 
*Tester:Beginner: Leonardo Allende
*Date: October 5, 2011
*/

/*
*<------------ PHPUnit Best Practices---------------------->
* Do not write test on Non-Public Attributes and Methods
*/

require_once("/var/www/yii/framework/test/CDbTestCase.php");
include_once("/var/www/testdrive/protected/extensions/emailsintaxvalidator/EEmailSintaxValidator.php");
include_once("/var/www/testdrive/protected/tests/unit/EmailValidator.php");

class EEmailSintaxValidatorTest extends CDbTestCase
{
	public $correo;
	public $checkemail;
	public $electronico = "leonardo.allende@hotmail.com";
	
	function setUp()
	{
		$this->correo = new EmailValidator();	
		$this->checkemail = new EEmailSintaxValidator();
		}
	
	
	public function testVAttribute()
	{
		//$this->checkemail->validateAttribute($this->correo, $this->electronico);
		$this->assertClassHasAttribute('electronico','EmailValidator');
		$this->assertTrue( true, $this->checkemail->validateAttribute($this->correo, $this->electronico));
		}	
		
	public function testCheckLocalPortion()
	{
		$cadena1 = "leonardo.allende";
		$this->assertFalse( false, $this->checkemail->check_local_portion($cadena1));
		}
		
	public function testCheckDomainPortion()
	{
		$cadena2 = "hotmail.com";
		$this->assertFalse( false, $this->checkemail->check_domain_portion($cadena2));
		}
	}

?>