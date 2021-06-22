var $=jQuery.noConflict();
$(document).ready(function (){
   $("#sort").on('change',function(){
      this.form.submit();
   });
});
