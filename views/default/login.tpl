<!-- authentication template state -->
<script type="text/javascript">
 var $j = jQuery.noConflict();
 $j(document).ready(function(){
  function _message(obj){
   var details = '';
   if (obj!=''){
    obj = (typeof obj=='object') ? JSON.parse(obj) : obj;
    $j.each(obj, function(k, v){
     if (k=='error'){
      $j('#message').html('<div class="error">'+v+'</div>').fadeIn(1000);
     }
     if (k=='warning'){
      $j('#message').html('<div class="warning">'+v+'</div>').fadeIn(1000);
     }
     if (k=='info'){
      $j('#message').html('<div class="info">'+v+'</div>').fadeIn(1000);
     }
     if (k=='success'){
      $j('#message').html('<div class="success">'+v+'</div>').fadeIn(1000);
     }
    });
   } else {
    $j('#message').html('<div class="warning">Empty response for request</div>').fadeIn(1000);
   }
  }
  function _load(){
   // load a spinner or something
  }
  $j('#auth').pidCrypt({
   appID:'{$token}',
   callback:function(){ _message(this); },
   preCallback:function(){ _load(); }
  });
 });
</script>
<div id="authenticate" class="rounder gradient">
 <h2>Authenticate</h2>
 <p>Please login to view active software licenses</p>
 <div id="message"></div>
 <form id="auth" name="authenticate" method="post" action="?nxs=proxy/authenticate">
  <label for="email">Email: </label>
   <input type="email" id="email" name="email" value="" placeholder="Enter email address" required="required" /><span class="required">*</span>
  <label for="password">Password: </label>
   <input type="password" id="password" name="password" value="" placeholder="Enter passphrase" required="required" /><span class="required">*</span>
  <input type="submit" value="Authenticate" id="submit-button" />
  <a href="">Register</a> | <a href="">Forgot username?</a>
 </form>
</div>
<!-- authentication template end -->