<?php
/**
 * EEmailSintaxValidator class file.
 *
 * @author Rodolfo González González
 * @link http://www.yiiframework.com/
 * @copyright 2008-2011 Rodolfo González González
 * @version 1.1
 * @license The 3-Clause BSD License
 *
 * Copyright © 2008-2011 Rodolfo González González
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - Neither the name of MetaYii nor the names of its contributors may
 *   be used to endorse or promote products derived from this software without
 *   specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
 * GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT
 * OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * ----
 * Uses code from:
 * EmailAddressValidator Class by dave@addedbytes.com
 * @link http://code.google.com/p/php-email-address-validation/
 * Released under New BSD license
 * @link http://www.opensource.org/licenses/bsd-license.php
 * ----
 */

/**
 * EEmailSintaxValidator uses de BSD licensed class from
 * @link http://code.google.com/p/php-email-address-validation/
 * to validate if an email address is technically valid.
 *
 * EEmailSintaxValidator is a sort of wrapper for the class aforementioned.
 */
class EEmailSintaxValidator extends CValidator
{
   /**
    * Check email address validity
    * @param   strEmailAddress     Email address to be checked
    * @return  True if email is valid, false if not
    */
   protected function validateAttribute($object, $attribute)
   {
	   $valid = true;

      if (is_object($object) && isset($object->$attribute)) {
         $strEmailAddress = $object->$attribute;
      }

      if (isset($strEmailAddress) && strcmp($strEmailAddress, '')) {
         $valid = self::validateEmailAddress($strEmailAddress);
      }

		if (!$valid) {
		   $message = $this->message !== null ? $this->message : Yii::t('EEmailValidator', 'The e-mail address is invalid.');
			$this->addError($object, $attribute, $message);
		}
   }

   public static function validateEmailAddress($strEmailAddress)
   {
      if (preg_match('/[\x00-\x1F\x7F-\xFF]/', $strEmailAddress)) {
         return false;
      }
      $intAtSymbol = strrpos($strEmailAddress, '@');
      if ($intAtSymbol === false) {
         return false;
      }
      $arrEmailAddress[0] = substr($strEmailAddress, 0, $intAtSymbol);
      $arrEmailAddress[1] = substr($strEmailAddress, $intAtSymbol + 1);
      $arrTempAddress[0] = preg_replace('/"[^"]+"/', '', $arrEmailAddress[0]);
      $arrTempAddress[1] = $arrEmailAddress[1];
      $strTempAddress = $arrTempAddress[0] . $arrTempAddress[1];
      if (strrpos($strTempAddress, '@') !== false) {
         return false;
      }

      if (!self::check_local_portion($arrEmailAddress[0])) {
         return false;
      }

      if (!self::check_domain_portion($arrEmailAddress[1])) {
         return false;
      }

      return true;
   }

   /**
    * Checks email section before "@" symbol for validity
    * @param   strLocalPortion     Text to be checked
    * @return  True if local portion is valid, false if not
    */
   private static function check_local_portion($strLocalPortion)
   {
      if (!self::check_text_length($strLocalPortion, 1, 64)) {
         return false;
      }
      $arrLocalPortion = explode('.', $strLocalPortion);
      for ($i = 0, $max = sizeof($arrLocalPortion); $i < $max; $i++) {
         if (!preg_match('.^('
                          .    '([A-Za-z0-9!#$%&\'*+/=?^_`{|}~-]'
                          .    '[A-Za-z0-9!#$%&\'*+/=?^_`{|}~-]{0,63})'
                          .'|'
                          .    '("[^\\\"]{0,62}")'
                          .')$.'
                          ,$arrLocalPortion[$i])) {
              return false;
          }
      }
      return true;
   }

   /**
    * Checks email section after "@" symbol for validity
    * @param   strDomainPortion     Text to be checked
    * @return  True if domain portion is valid, false if not
    */
   private static function check_domain_portion($strDomainPortion)
   {
      if (!self::check_text_length($strDomainPortion, 1, 255)) {
          return false;
      }
      if (preg_match('/^(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])'
         .'(\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])){3}$/'
         ,$strDomainPortion) ||
          preg_match('/^\[(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])'
         .'(\.(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])){3}\]$/'
         ,$strDomainPortion)) {
          return true;
      }
      else {
         $arrDomainPortion = explode('.', $strDomainPortion);
         if (sizeof($arrDomainPortion) < 2) {
            return false;
         }
         for ($i = 0, $max = sizeof($arrDomainPortion); $i < $max; $i++) {
            if (!self::check_text_length($arrDomainPortion[$i], 1, 63)) {
               return false;
            }
            if (!preg_match('/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|'
                .'([A-Za-z0-9]+))$/', $arrDomainPortion[$i])) {
               return false;
            }
         }
      }
      return true;
   }

   /**
    * Check given text length is between defined bounds
    * @param   strText     Text to be checked
    * @param   intMinimum  Minimum acceptable length
    * @param   intMaximum  Maximum acceptable length
    * @return  True if string is within bounds (inclusive), false if not
    */
   private static function check_text_length($strText, $intMinimum, $intMaximum)
   {
      $intTextLength = strlen($strText);
      if (($intTextLength < $intMinimum) || ($intTextLength > $intMaximum)) {
         return false;
      }
      else {
         return true;
      }
   }
}