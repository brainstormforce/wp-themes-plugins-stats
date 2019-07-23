//display and hide human readable option.
function bsf_hrFunction() {
  var wpas_hr_option = document.getElementById("wpas_hr_option");
  var hr_option = document.getElementById("hr_option");
  if (wpas_hr_option.checked === true){
    hr_option.style.display = "block";
   } 
   else {
    hr_option.style.display = "none";
  }
}