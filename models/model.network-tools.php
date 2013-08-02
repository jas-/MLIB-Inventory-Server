<?PHP

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

class networkTools
{
 function process($target,$ports,$worms,$type,$tools,$callback)
 {
  if (!empty($target)) {
   if ($this->valIPv4($target)!==-1) {
    $target = gethostbyaddr($target);
   }
   switch($type){
    case 'ping':
     return $this->results($callback,$this->ping($target,$tools['ping']),$type);
    case 'trace':
     return $this->results($callback,$this->trace($target,$tools['trace']),$type);
    case 'route':
     return $this->results($callback,$this->route($target,$worms,$tools['trace'],$tools['nmap']),$type);
    case 'pscan':
     return $this->results($callback,$this->pscan($target,$ports,$tools['nmap']),$type);
    case 'fingerprint':
     return $this->results($callback,$this->fingerprint($target,$ports,$tools['nmap']),$type);
    case 'whois':
     return $this->results($callback,$this->whois($target,$tools['whois']),$type);
    default:
     return $this->results($callback,$this->ping($target),'ping');
   }
  }
  return;
 }

 function results($callback,$array,$type)
 {
  $array['type']=$type;
  if (function_exists("json_encode")) {
   $jsonGetData = json_encode($array);
  } else {
   $jsonGetData = $handles['lib']->arr2json($array);
  }
  echo $callback . '(' . $jsonGetData . ');';
 }

 function processHTML($target,$ports,$worms,$type,$tools)
 {
  if (!empty($target)) {
   if ($this->valIPv4($target)!==-1) {
    $target = gethostbyaddr($target);
   }
   switch($type){
    case 'ping':
     return $this->resultsHTML($this->ping($target,$tools['ping']));
    case 'trace':
     return $this->resultsHTML($this->trace($target,$tools['trace']));
    case 'route':
     return $this->resultsHTML($this->route($target,$worms,$tools['trace'],$tools['nmap']));
    case 'pscan':
     return $this->resultsHTML($this->pscan($target,$ports,$tools['nmap']));
    case 'fingerprint':
     return $this->resultsHTML($this->fingerprint($target,$ports,$tools['nmap']));
    case 'whois':
     return $this->resultsHTML($this->whois($target,$tools['whois']));
    default:
     return $this->resultsHTML($this->ping($target));
   }
  }
  return;
 }

 function resultsHTML($array)
 {
  if(count($array)>0){
   foreach($array as $key => $value){
    if (is_array($value)){
     return $this->resultsHTML($value);
    } else {
     $data .= $value . "<br/>";
    }
   }
  }
  return $data;
 }

 function ping($target,$ping)
 {
  $cmd = $ping . ' -c 3 ' . $target;
  exec($cmd,$results[$target]);
  return $results;
 }

 function trace($target,$trace)
 {
  $cmd = $trace . ' ' . $target;
  exec($cmd,$results[$target]);
  return $results;
 }

 function pscan($target,$ports,$nmap)
 {
  $cmd = $nmap . ' -p ' . $ports . ' -P0 ' . $target;
  exec($cmd,$results[$target]);
  return $results;
 }

 function route($target,$ports,$trace,$nmap)
 {
  $cmd = $trace . '  ' . $target;
  exec($cmd,$results);
  if (count($results)>0) {
   foreach($results as $key => $value) {
    $t[$this->getIP($value)] = $this->getIP($value);
   }
   if (count($t)>0) {
    $ip = $this->routehelper($t,$ports,$trace,$nmap);
   }
  }
  return $ip;
 }

 function routehelper($target,$ports,$trace,$nmap)
 {
  global $handles;
  foreach($target as $key => $value) {
   if ($this->valIPv4($value)!==-1) {
    $data[$value] = $this->pscan($value,$ports,$nmap);
   }
  }
  return $data;
 }

 function getIP($string) {
  preg_match('/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/',$string,$a);
  return $a[0];
 }

 function fingerprint($target,$ports,$nmap)
 {
  $cmd = $nmap . ' -p ' . $ports . ' --allports --version-all -sV ' . $target;
  exec($cmd,$results[$target]);
  return $results;
 }

 function whois($target,$whois)
 {
  $cmd = $whois . ' ' . $target;
  exec($cmd,$results[$target]);
  return $results;
 }

 function valDomain($target)
 {
  if (!eregi("[:;<>?\/#$%^&*@!()]",$target)) {
   return 0;
  } else {
   return -1;
  }
 }

 function valIPv4($ip_v4)
 {
  $ip_v4 = rtrim($ip_v4);
  if (eregi("^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$",$ip_v4)) {
   $ip_v4 = 0;
   for($i=1;$i<=3;$i++) {
    if(!(substr($ip_v4,0,strpos($ip_v4,"."))>="0"&&substr($ip_v4,0,strpos($ip_v4,"."))<="255")) {
     $ip_v4 = -1;
    }
    $ip_v4 = substr($ip_v4,strpos($ip_v4,".")+1);
   }
   if(!($ip_v4>="0"&&$ip_v4<="255")) {
    $ip_v4 = -1;
   }
  } else {
   $ip_v4 = -1;
 }
  return $ip_v4;
 }

}
?>