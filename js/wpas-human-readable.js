
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
    hr_option.style.display = "none";
    num_option.style.display = "block";
  }
}
