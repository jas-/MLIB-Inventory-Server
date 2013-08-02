<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle radius module functionality
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   authentication
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class radius
{
 protected static $instance;
 private $handle;
 private $response;
 private function __construct($configuration, $username, $password)
 {
  if (extension_loaded('radius')) {
   $this->main($configuration, $username, $password);
  } else {
   echo 'The radius extensions are not loaded.';
   unset($instance);
   exit;
  }
 }
 public static function instance($configuration, $username=NULL, $password=NULL)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration, $username, $password);
  }
  return self::$instance;
 }
 private function main($configuration, $username, $password)
 {
  if (extension_loaded('radius')) {
   $this->handle = $this->init();
   if (!$this->handle) return $this->error;
   $this->createrequest();
   $this->addserver($configuration);
   $this->sstring(RADIUS_NAS_IDENTIFIER, isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost');
   $this->sint(RADIUS_SERVICE_TYPE, RADIUS_FRAMED);
   $this->sint(RADIUS_FRAMED_PROTOCOL, RADIUS_PPP);
   $this->sstring(RADIUS_CALLING_STATION_ID, isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1');
   $this->sstring(RADIUS_USER_NAME, $username);
   $this->authenticate($configuration, $username, $password);
   $this->sint(RADIUS_SERVICE_TYPE, RADIUS_FRAMED);
   $this->sint(RADIUS_FRAMED_PROTOCOL, RADIUS_PPP);
   $this->response = $this->request();
   echo $this->results($this->response);
   $this->details($this->handle);
  } else {
   $this->handle = 'The radius extension is not installed';
  }
 }
 private function authenticate($configuration, $username, $password)
 {
  switch($configuration['cipher']) {
   case 'chap':
    mt_srand(time());
    $chall = mt_rand();
    $chapval = pack('H*', md5(pack('Ca*',1 , $password . $chall)));
    $pass = pack('C', 1) . $chapval;
    $this->challenge(RADIUS_CHAP_PASSWORD, $pass);
    return $this->challenge(RADIUS_CHAP_CHALLENGE, $chall);
   case 'mschapv1':
    $mschap = new mschap();
    $challenge = $mschap->GenerateChallenge();
    $this->challengevendor(RADIUS_VENDOR_MICROSOFT, RADIUS_MICROSOFT_MS_CHAP_CHALLENGE, $challenge);
    $ntresp = $mschap->ChallengeResponse($challenge, NtPasswordHash($password));
    $lmresp = str_repeat("\0", 24);
    $resp = pack('CCa48',1 , 1, $lmresp . $ntresp);
    return $this->challengevendor(RADIUS_VENDOR_MICROSOFT, RADIUS_MICROSOFT_MS_CHAP_RESPONSE, $resp);
   case 'mschapv2':
    $mschap = new mschap();
    $challenge = $mschap->GenerateChallenge(16);
    $peerChallenge = $mschap->GeneratePeerChallenge();
    $ntresp = $mschap->GenerateNTResponse($authChallenge, $peerChallenge, $username, $password);
    $reserved = str_repeat ("\0", 8);
    $resp = pack('CCa16a8a24',1 , 1, $peerChallenge, $reserved, $ntresp);
    return $this->challengevendor(RADIUS_VENDOR_MICROSOFT, RADIUS_MICROSOFT_MS_CHAP2_RESPONSE, $resp);
   default:
    return $this->sstring(RADIUS_USER_PASSWORD, "sepp");
  }
 }
 private function init()
 {
  return radius_auth_open();
 }
 private function addserver($configuration)
 {
  return radius_add_server($this->handle, $configuration['servers'], $configuration['port'], $configuration['key'], 5, 3);
 }
 private function createrequest()
 {
  return radius_create_request($this->handle, RADIUS_ACCESS_REQUEST);
 }
 private function sstring($cmd, $opt)
 {
  return radius_put_string($this->handle, $cmd, $opt);
 }
 private function sint($cmd, $opt)
 {
  return radius_put_int($this->handle, $cmd, $opt);
 }
 private function challenge()
 {
  return radius_put_attr($this->handle, $cmd, $opt);
 }
 private function challengevendor($cmd, $opt, $challenge)
 {
  return radius_put_vendor_attr($this->handle, $cmd, $top, $challenge);
 }
 private function request()
 {
  return radius_send_request($this->handle);
 }
 private function results($response)
 {
  switch($response) {
   case RADIUS_ACCESS_ACCEPT:
    return "Radius Request accepted<br>";
    break;
   case RADIUS_ACCESS_REJECT:
    return "Radius Request rejected<br>";
    break;
   default:
    return "Unexpected return value: ".$this->error."<br>";
  }
 }
 private function details($results)
 { echo '<pre>'; print_r(radius_get_attr($results)); echo '</pre>';
  while ($x = radius_get_attr($results)) {
   if (!is_array($x)) {
    printf ("Error getting attribute: %s\n",  $this->error($results));
    exit;
  }
  $attr = $x['attr'];
  $data = $x['data'];
  printf("Got Attr:%d %d Bytes %s\n", $attr, strlen($data), bin2hex($data));
  switch ($attr) {
   case RADIUS_FRAMED_IP_ADDRESS:
    $ip = radius_cvt_addr($data);
    echo "IP: $ip<br>\n";
    break;
   case RADIUS_FRAMED_IP_NETMASK:
    $mask = radius_cvt_addr($data);
    echo "MASK: $mask<br>\n";
    break;
   case RADIUS_FRAMED_MTU:
    $mtu = radius_cvt_int($data);
    echo "MTU: $mtu<br>\n";
    break;
   case RADIUS_FRAMED_COMPRESSION:
    $comp = radius_cvt_int($data);
    echo "Compression: $comp<br>\n";
    break;
   case RADIUS_SESSION_TIMEOUT:
    $time = radius_cvt_int($data);
    echo "Session timeout: $time<br>\n";
    break;
   case RADIUS_IDLE_TIMEOUT:
    $idletime = radius_cvt_int($data);
    echo "Idle timeout: $idletime<br>\n";
    break;
   case RADIUS_SERVICE_TYPE:
    $type = radius_cvt_int($data);
    echo "Service Type: $type<br>\n";
    break;
   case RADIUS_CLASS:
    $class = radius_cvt_int($data);
    echo "Class: $class<br>\n";
    break;
   case RADIUS_FRAMED_PROTOCOL:
    $proto = radius_cvt_int($data);
    echo "Protocol: $proto<br>\n";
    break;
   case RADIUS_FRAMED_ROUTING:
    $rout = radius_cvt_int($data);
    echo "Routing: $rout<br>\n";
    break;
   case RADIUS_FILTER_ID:
    $id = radius_cvt_string($data);
    echo "Filter ID: $id<br>\n";
    break;
   case RADIUS_VENDOR_SPECIFIC:
    printf ("Vendor specific (%d)<br>\n", $attr);
    $resv = radius_get_vendor_attr($data);
     if (is_array($resv)) {
      $vendor = $resv['vendor'];
      $attrv = $resv['attr'];
      $datav = $resv['data'];
      if ($vendor == RADIUS_VENDOR_MICROSOFT) {
       switch ($attrv) {
        case RADIUS_MICROSOFT_MS_CHAP2_SUCCESS:
         $mschap2resp = radius_cvt_string($datav);
         printf ("MS CHAPv2 success: %s<br>\n", $mschap2resp);                    
         break;
        case RADIUS_MICROSOFT_MS_CHAP_ERROR:
         $errormsg = radius_cvt_string(substr($datav,1));
         echo "MS CHAP Error: $errormsg<br>\n";
         break;
        case RADIUS_MICROSOFT_MS_CHAP_DOMAIN:
         $domain = radius_cvt_string($datav);
         echo "MS CHAP Domain: $domain<br>\n";
         break;
        case RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_POLICY:
         $policy = radius_cvt_int($datav);
         echo "MS MPPE Policy: $policy<br>\n";
         break;
        case RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_TYPES:
         $type = radius_cvt_int($datav);
         echo "MS MPPE Type: $type<br>\n";
         break;
        case RADIUS_MICROSOFT_MS_CHAP_MPPE_KEYS:
         $demangled = radius_demangle($results, $datav);
         $lmkey = substr($demangled, 0, 8);
         $ntkey = substr($demangled, 8, RADIUS_MPPE_KEY_LEN);
         printf ("MS MPPE Keys: LM-Key: %s NT-Key: %s<br>\n", bin2hex($lmkey), bin2hex($ntkey));
         break;
        case RADIUS_MICROSOFT_MS_MPPE_SEND_KEY:
         $demangled = radius_demangle_mppe_key($results, $datav);
         printf ("MS MPPE Send Key: %s<br>\n", bin2hex($demangled));
         break;
        case RADIUS_MICROSOFT_MS_MPPE_RECV_KEY:
         $demangled = radius_demangle_mppe_key($results, $datav);
         printf ("MS MPPE Send Key: %s<br>\n", bin2hex($demangled));
         break;
        case RADIUS_MICROSOFT_MS_PRIMARY_DNS_SERVER:
         $server = radius_cvt_string($datav);
         printf ("MS Primary DNS Server: %s<br>\n", $server);
         break;
        default:
         printf("Unexpected Microsoft attribute: %d<br>\n", $attrv);
       }
      }
     } else {
      printf ("Error getting Vendor attribute %s<br>\n", radius_strerror($results));
     }
     break;
    default:
     printf("Unexpected attribute: %d<br>\n", $attr);
   }
  }
 }
 private function error()
 {
  return radius_strerror($this->handle);
 }
 private function __destruct()
 {
  if (isset($this->handle)) {
   radius_close($this->handle);
  }
  return;
 }
}
/*
   The contents of this document are free for use by anyone for any purpose they choose.
   no warranty is implied, nor will one be honored.  The author assumes no liability for
   any consequences that may arise from the use of contents of this document.  Events not
   covered include but are not limited to: system crashes, system slowdown, system failure,
   fires, explosions, floods, earthquakes, boiling seas, intestinal inflammation, cold coffee,
   and total protonic inversion.
   Any questions, comments, or improvements are appreciated and can be directed to:
  
   Tim Thorpe
   blushift@netins.net

  This script implements the 56-bit DES encryption algorithm,
  created from scratch based on interpretation of the original
  specification document.

  des_encrypt_ecb("my key", "my text");
  and
  mcrypt_ecb (MCRYPT_DES, "my key", "my text", MCRYPT_ENCRYPT, str_pad("", 8, chr(0x00)));
  are functionally identical.

  Because PHP is lacking in bitwise operators, this set of routines
  makes use of data that has been expanded so that a byte becomes an 8-byte string.

  The script performs the transformations, and then condenses the data back to a usable form.
  Apologies for a lack of comments in the bulk of the functions.
*/
class mschap
{
 function NtPasswordHash($plain) 
 {
  return mhash (MHASH_MD4, $this->str2unicode($plain));
 }
 function str2unicode($str) 
 {
  for ($i=0;$i<strlen($str);$i++) {
   $a = ord($str{$i}) << 8;
   $uni .= sprintf("%X",$a);
  }
  return pack('H*', $uni);
 }
 function GenerateChallenge($size = 8) 
 {
  mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);
  for($i = 0; $i < $size; $i++) {
   $chall .= pack('C', 1 + mt_rand() % 255);
  }
  return $chall;
 }
 function ChallengeResponse($challenge, $nthash) 
 {
  while (strlen($nthash) < 21)
   $nthash .= "\0";
   $resp1 = $this->des_encrypt_ecb(substr($nthash, 0, 7), $challenge);
   $resp2 = $this->des_encrypt_ecb(substr($nthash, 7, 7), $challenge);
   $resp3 = $this->des_encrypt_ecb(substr($nthash, 14, 7), $challenge);
   return $resp1 . $resp2 . $resp3;
 }
 // MS-CHAPv2
 function GeneratePeerChallenge() 
 {
  return $this->GenerateChallenge(16);
 }
 function NtPasswordHashHash($hash) 
 {
  return mhash (MHASH_MD4, $hash);
 }
 function ChallengeHash($challenge, $peerChallenge, $username) 
 {
  return substr(mhash (MHASH_SHA1, $peerChallenge . $challenge . $username), 0, 8);
 }
 function GenerateNTResponse($challenge, $peerChallenge, $username, $password) 
 {
  $challengeHash = $this->ChallengeHash($challenge, $peerChallenge, $username);
  $pwhash = $this->NtPasswordHash($password);
  return $this->ChallengeResponse($challengeHash, $pwhash);
 }
 function des_encrypt_ecb($key, $clearText) {
  if (strlen($key) < 7)
   $key = $this->str_pad(substr($key, 0, 7), 7, chr(0x00));  //We need 7 bytes for a key; no more, no less
  if (strlen($key) == 7) $key = $this->des_add_parity($key);
 	$keys = $this->des_make_subkeys ($this->des_bits_to_bytes ($key));  //Chew the key into the subkeys needed for DES
	 $blockCount = (int) (strlen($clearText) / 8);  //Figure out how many blocks of 8 bytes we need to encode
	 if (strlen ($clearText) % 8) {  //Check to see if there are any leftovers
	  $blockCount++;  //Add another block for them
	  $clearText = str_pad($clearText, $blockCount * 8, chr(0x00));  //Pad it out with zeroes
	 }
	 for ($i = 0; $i < $blockCount; $i++) {  //Cycle through the blocks
	  $clearBlock = substr($clearText, $i * 8, 8);  //Grab a block from the input
	  $cypherBlock = $this->des_block_encode($this->des_bits_to_bytes ($clearBlock), $keys);  //Encrypt it
	  $cypherText .= $this->des_bytes_to_bits ($cypherBlock);  //Convert the result back to a useful form
	 }
	 return ($cypherText);  //Cough it up
 }
 function des_block_encode($clearText, $subKeys) {
	 $ip_table = array(58, 50, 42, 34, 26, 18, 10,  2,
			                 60, 52, 44, 36, 28, 20, 12,  4,
			                 62, 54, 46, 38, 30, 22, 14,  6,
			                 64, 56, 48, 40, 32, 24, 16,  8,
			                 57, 49, 41, 33, 25, 17,  9,  1,
			                 59, 51, 43, 35, 27, 19, 11,  3,
			                 61, 53, 45, 37, 29, 21, 13,  5,
			                 63, 55, 47, 39, 31, 23, 15,  7
                   );
	 $ip1_table = array(40,  8, 48, 16, 56, 24, 64, 32,
			                  39,  7, 47, 15, 55, 23, 63, 31,
			                  38,  6, 46, 14, 54, 22, 62, 30,
			                  37,  5, 45, 13, 53, 21, 61, 29,
			                  36,  4, 44, 12, 52, 20, 60, 28,
			                  35,  3, 43, 11, 51, 19, 59, 27,
			                  34,  2, 42, 10, 50, 18, 58, 26,
			                  33,  1, 41,  9, 49, 17, 57, 25
                    );
	 foreach ($ip_table as $bit) {
		 $ip .= $clearText[$bit - 1];
	 }
	 $l[0] = substr($ip, 0, 32);
	 $r[0] = substr($ip, 32);
	 for ($index = 1; $index <= 16; $index++) {
	 	$l[$index] = $r[$index - 1];
	 	$r[$index] = $this->des_xor($l[$index - 1], $this->des_transform($r[$index - 1], $subKeys[$index]));
	 }
	 foreach ($ip1_table as $bit) {
	 	$concat	= $r[16] . $l[16];
	 	$cypherBlock .= $concat[$bit - 1];
	 }
	 return ($cypherBlock);
 }
 function des_make_subkeys($key) {
	 $pc1 = array(57, 49, 41, 33, 25, 17,  9,
		             1, 58, 50, 42, 34, 26, 18,
		             10,  2, 59, 51, 43, 35, 27,
		             19, 11,  3, 60, 52, 44, 36,
		             63, 55, 47, 39, 31, 23, 15,
		             7, 62, 54, 46, 38, 30, 22,
		             14,  6, 61, 53, 45, 37, 29,
		             21, 13,  5, 28, 20, 12,  4
              );
	 $pc2 = array(14, 17, 11, 24,  1,  5,
		             3, 28, 15,  6, 21, 10,
		             23, 19, 12,  4, 26,  8,
		             16,  7, 27, 20, 13,  2,
		             41, 52, 31, 37, 47, 55,
		             30, 40, 51, 45, 33, 48,
		             44, 49, 39, 56, 34, 53,
		             46, 42, 50, 36, 29, 32
              );
	 $shifts = array(1, 1, 2, 2,
			               2, 2, 2, 2,
			               1, 2, 2, 2,
			               2, 2, 2, 1
                 );
	 foreach ($pc1 as $bit) {
	 	$k .= $key[$bit - 1];
	 }
	 $index = 0;
	 $c[$index] = substr($k, 0, 28);
	 $d[$index] = substr($k, 28);
	 unset ($k);
	 foreach ($shifts as $positions) {
	 	$index++;
	 	$c[$index] = $this->des_rotate_left ($c[$index - 1], $positions);
	 	$d[$index] = $this->des_rotate_left ($d[$index - 1], $positions);
	 }
	 for ($i = 1; $i <= 16; $i++) {
	 	foreach ($pc2 as $bit) {
	 		$concat = $c[$i] . $d[$i];
	 		$k[$i] .= $concat[$bit - 1];
	 	}
	 }
	 return ($k);
 }
 function des_transform($data, $key) {
	 $e_table = array(32,  1,  2,  3,  4,  5,
			                4,  5,  6,  7,  8,  9,
			                8,  9, 10, 11, 12, 13,
			                12, 13, 14, 15, 16, 17,
			                16, 17, 18, 19, 20, 21,
			                20, 21, 22, 23, 24, 25,
			                24, 25, 26, 27, 28, 29,
			                28, 29, 30, 31, 32,  1
                  );
	 $s = array(
             array(14, 4, 13, 1, 2, 15, 11, 8, 3, 10, 6, 12, 5, 9, 0, 7,
			                0, 15, 7, 4, 14, 2, 13, 1, 10, 6, 12, 11, 9, 5, 3, 8,
			                4, 1, 14, 8, 13, 6, 2, 11, 15, 12, 9, 7, 3, 10, 5, 0,
			                15, 12, 8, 2, 4, 9, 1, 7, 5, 11, 3, 14, 10, 0, 6, 13),
		           array(15, 1, 8, 14, 6, 11, 3, 4, 9, 7, 2, 13, 12, 0, 5, 10,
			                3, 13, 4, 7, 15, 2, 8, 14, 12, 0, 1, 10, 6, 9, 11, 5,
			                0, 14, 7, 11, 10, 4, 13, 1, 5, 8, 12, 6, 9, 3, 2, 15,
			                13, 8, 10, 1, 3, 15, 4, 2, 11, 6, 7, 12, 0, 5, 14, 9),
		           array(10, 0, 9, 14, 6, 3, 15, 5, 1, 13, 12, 7, 11, 4, 2, 8,
			                13, 7, 0, 9, 3, 4, 6, 10, 2, 8, 5, 14, 12, 11, 15, 1,
			                13, 6, 4, 9, 8, 15, 3, 0, 11, 1, 2, 12, 5, 10, 14, 7,
			                1, 10, 13, 0, 6, 9, 8, 7, 4, 15, 14, 3, 11, 5, 2, 12),
		           array(7, 13, 14, 3, 0, 6, 9, 10, 1, 2, 8, 5, 11, 12, 4, 15,
			                13, 8, 11, 5, 6, 15, 0, 3, 4, 7, 2, 12, 1, 10, 14, 9,
			                10, 6, 9, 0, 12, 11, 7, 13, 15, 1, 3, 14, 5, 2, 8, 4,
			                3, 15, 0, 6, 10, 1, 13, 8, 9, 4, 5, 11, 12, 7, 2, 14),
		           array(2, 12, 4, 1, 7, 10, 11, 6, 8, 5, 3, 15, 13, 0, 14, 9,
			                14, 11, 2, 12, 4, 7, 13, 1, 5, 0, 15, 10, 3, 9, 8, 6,
			                4, 2, 1, 11, 10, 13, 7, 8, 15, 9, 12, 5, 6, 3, 0, 14,
			                11, 8, 12, 7, 1, 14, 2, 13, 6, 15, 0, 9, 10, 4, 5, 3),
		           array(12, 1, 10, 15, 9, 2, 6, 8, 0, 13, 3, 4, 14, 7, 5, 11,
			                10, 15, 4, 2, 7, 12, 9, 5, 6, 1, 13, 14, 0, 11, 3, 8,
			                9, 14, 15, 5, 2, 8, 12, 3, 7, 0, 4, 10, 1, 13, 11, 6,
			                4, 3, 2, 12, 9, 5, 15, 10, 11, 14, 1, 7, 6, 0, 8, 13),
		           array(4, 11, 2, 14, 15, 0, 8, 13, 3, 12, 9, 7, 5, 10, 6, 1,
			                13, 0, 11, 7, 4, 9, 1, 10, 14, 3, 5, 12, 2, 15, 8, 6,
			                1, 4, 11, 13, 12, 3, 7, 14, 10, 15, 6, 8, 0, 5, 9, 2,
			                6, 11, 13, 8, 1, 4, 10, 7, 9, 5, 0, 15, 14, 2, 3, 12),
		           array(13, 2, 8, 4, 6, 15, 11, 1, 10, 9, 3, 14, 5, 0, 12, 7,
			                1, 15, 13, 8, 10, 3, 7, 4, 12, 5, 6, 11, 0, 14, 9, 2,
			                7, 11, 4, 1, 9, 12, 14, 2, 0, 6, 10, 13, 15, 3, 5, 8,
			                2, 1, 14, 7, 4, 10, 8, 13, 15, 12, 9, 0, 3, 5, 6, 11)
            );

	 $p_table = array(16, 7, 20, 21,
                   29, 12, 28, 17,
                   1, 15, 23, 26,
                   5, 18, 31, 10,
                   2, 8, 24, 14,
                   32, 27, 3, 9,
                   19, 13, 30,  6,
                   22, 11, 4, 25
                  );

 	$nybbles = array(chr(0x00).chr(0x00).chr(0x00).chr(0x00), chr(0x00).chr(0x00).chr(0x00).chr(0x01),
			                chr(0x00).chr(0x00).chr(0x01).chr(0x00), chr(0x00).chr(0x00).chr(0x01).chr(0x01),
			                chr(0x00).chr(0x01).chr(0x00).chr(0x00), chr(0x00).chr(0x01).chr(0x00).chr(0x01),
			                chr(0x00).chr(0x01).chr(0x01).chr(0x00), chr(0x00).chr(0x01).chr(0x01).chr(0x01),
			                chr(0x01).chr(0x00).chr(0x00).chr(0x00), chr(0x01).chr(0x00).chr(0x00).chr(0x01),
			                chr(0x01).chr(0x00).chr(0x01).chr(0x00), chr(0x01).chr(0x00).chr(0x01).chr(0x01),
			                chr(0x01).chr(0x01).chr(0x00).chr(0x00), chr(0x01).chr(0x01).chr(0x00).chr(0x01),
			                chr(0x01).chr(0x01).chr(0x01).chr(0x00), chr(0x01).chr(0x01).chr(0x01).chr(0x01)
                  );
	 foreach ($e_table as $bit) {
	 	$e .= $data[$bit - 1];
	 }
	 $ek = $this->des_xor($e, $key);
	 for ($i = 0; $i < 8; $i++) {
	 	$offset = $i * 6;
	 	$sAddress = ord($ek[$offset]) * 0x20 +
			            ord($ek[$offset + 1]) * 0x08 +
			            ord($ek[$offset + 2]) * 0x04 +
			            ord($ek[$offset + 3]) * 0x02 +
			            ord($ek[$offset + 4]) +
			            ord($ek[$offset + 5]) * 0x10;
		 $sResult .= $nybbles[$s[$i][$sAddress]];
	 }
	 foreach ($p_table as $bit) {
	 	$p .= $sResult[$bit - 1];
	 }
	 return ($p);
 }
 function des_bits_to_bytes($bitStream) {
	 for ($i = 0; $i < strlen($bitStream); $i++) {
	 	$val = ord($bitStream[$i]);
	 	if ($val & 0x80) {$byteStream .= chr(0x01);} else {$byteStream .= chr(0x00);}
	 	if ($val & 0x40) {$byteStream .= chr(0x01);} else {$byteStream .= chr(0x00);}
	 	if ($val & 0x20) {$byteStream .= chr(0x01);} else {$byteStream .= chr(0x00);}
	 	if ($val & 0x10) {$byteStream .= chr(0x01);} else {$byteStream .= chr(0x00);}
	 	if ($val & 0x08) {$byteStream .= chr(0x01);} else {$byteStream .= chr(0x00);}
	 	if ($val & 0x04) {$byteStream .= chr(0x01);} else {$byteStream .= chr(0x00);}
	 	if ($val & 0x02) {$byteStream .= chr(0x01);} else {$byteStream .= chr(0x00);}
	 	if ($val & 0x01) {$byteStream .= chr(0x01);} else {$byteStream .= chr(0x00);}
	 }
	 return ($byteStream);
 }
 function des_bytes_to_bits($byteStream) {
 	for ($i = 0; $i < (strlen($byteStream) / 8); $i++) {
 		$offset	= $i * 8;
 		$value = ord($byteStream[$offset]) * 0x80 +
 			        ord($byteStream[$offset + 1]) * 0x40 +
 			        ord($byteStream[$offset + 2]) * 0x20 +
			         ord($byteStream[$offset + 3]) * 0x10 +
			         ord($byteStream[$offset + 4]) * 0x08 +
			         ord($byteStream[$offset + 5]) * 0x04 +
			         ord($byteStream[$offset + 6]) * 0x02 +
			         ord($byteStream[$offset + 7]);
		 $bitStream .= chr($value);
	 }
	 return ($bitStream);
 }
 function des_rotate_left($input, $positions) {
	 return substr($input, $positions) . substr($input, 0, $positions);
 }
 function des_xor($a, $b) {
 	for ($i = 0; $i < strlen($a); $i++) {
 		$xor .= $a[$i] ^ $b[$i];
 	}
 	return $xor;
 }
 // DES helper function
 // input: 7-Bytes Key without parity
 // ouput: 8-Bytes Key with parity
 function des_add_parity($key) 
 {
  static $odd_parity = array(
                             1,  1,  2,  2,  4,  4,  7,  7,  8,  8, 11, 11, 13, 13, 14, 14,
                             16, 16, 19, 19, 21, 21, 22, 22, 25, 25, 26, 26, 28, 28, 31, 31,
                             32, 32, 35, 35, 37, 37, 38, 38, 41, 41, 42, 42, 44, 44, 47, 47,
                             49, 49, 50, 50, 52, 52, 55, 55, 56, 56, 59, 59, 61, 61, 62, 62,
                             64, 64, 67, 67, 69, 69, 70, 70, 73, 73, 74, 74, 76, 76, 79, 79,
                             81, 81, 82, 82, 84, 84, 87, 87, 88, 88, 91, 91, 93, 93, 94, 94,
                             97, 97, 98, 98,100,100,103,103,104,104,107,107,109,109,110,110,
                             112,112,115,115,117,117,118,118,121,121,122,122,124,124,127,127,
                             128,128,131,131,133,133,134,134,137,137,138,138,140,140,143,143,
                             145,145,146,146,148,148,151,151,152,152,155,155,157,157,158,158,
                             161,161,162,162,164,164,167,167,168,168,171,171,173,173,174,174,
                             176,176,179,179,181,181,182,182,185,185,186,186,188,188,191,191,
                             193,193,194,194,196,196,199,199,200,200,203,203,205,205,206,206,
                             208,208,211,211,213,213,214,214,217,217,218,218,220,220,223,223,
                             224,224,227,227,229,229,230,230,233,233,234,234,236,236,239,239,
                             241,241,242,242,244,244,247,247,248,248,251,251,253,253,254,254
                            );
  for ($i = 0; $i < strlen($key); $i++) {
   $bin .= sprintf('%08s', decbin(ord($key{$i})));
  }
  $str1 = explode('-', substr(chunk_split($bin, 7, '-'), 0, -1));
  foreach($str1 as $s) {
   $x .= sprintf('%02s', dechex($odd_parity[bindec($s . '0')]));
  }
  return pack('H*', $x);
 }
}
?>
