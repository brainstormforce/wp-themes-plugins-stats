
//display and hide human readable option.
function bsf_hrFunction() {
  var wpas_hr_option = document.getElementById("wpas_hr_option");
  var hr_option = document.getElementById("hr_option");
  var num_option = document.getElementById("num_option");
  if (wpas_hr_option.checked === true){
    hr_option.style.display = "block";
    num_option.style.display = "none";
   } 
   else {
   	num_option.style.display = "block";
    hr_option.style.display = "none";
  }
}
 // function disable_numbreText(){  
 //          if(document.getElementById("wpas_hr_option").checked == true){  
 //              document.getElementById("wpas_number_group").disabled = true;  
 //          }else{
 //            document.getElementById("wpas_number_group").disabled = false;
 //          }  
 //     }