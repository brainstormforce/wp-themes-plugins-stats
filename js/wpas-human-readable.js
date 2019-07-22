function myFunction() {
// console.log('xdvfhjv');
document.getElementById('wpas_hr_option').checked ?
(
	
document.getElementById("thousand").disabled = false,
//document.getElementById("field1").disabled = false,
document.getElementById("million").disabled = false,
document.getElementById("billion").disabled = false,
document.getElementById("trillion").disabled = false
):(
document.getElementById("thousand").disabled = true,
document.getElementById("million").disabled = true,
document.getElementById("billion").disabled = true,
document.getElementById("trillion").disabled = true
)
;
}