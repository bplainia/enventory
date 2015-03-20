// capacitor code calculator functions
// copyright Simon Carter 2001
// Please contact me via www.electronics2000.co.uk if you wish to use these
// and ensure this header block remains intact

//Array of tolerance values
var tolval = new Array()
tolval[0] = "+/- 0.25pF"
tolval[1] = "+/- 0.5pF"
tolval[2] = "+/- 1%"
tolval[3] = "+/- 2%"
tolval[4] = "+/- 5%"
tolval[5] = "+/- 10%"
tolval[6] = "+/- 20%"
tolval[7] = "- 20% + 80%"

//Array of tolerance codes
var tolcode = new Array()
tolcode[0] = "C"
tolcode[1] = "D"
tolcode[2] = "F"
tolcode[3] = "G"
tolcode[4] = "J"
tolcode[5] = "K"
tolcode[6] = "M"
tolcode[7] = "Z"

function codetovalue (obj) {

with (Math) {
	code=obj.capcode.value;
	if(code.length==3){
		value= eval("" + code.substring(0,2) + "e" + (code.substring(2,3) - 12));
		SetText("capacitance", format(value) + "F");}
	else{	SetText("capacitance", "?");}
	SetText("tolerance", tolval[obj.tolerancecode.selectedIndex]);
	}
}

function valuetocode (obj) {

with (Math) {
	value = calculatemult3((obj.capmult.selectedIndex+2),eval(obj.capval.value));
	value = parseInt(value * 1000000000000)
	value = value.toString();	

	if ((value.length == 0) || (isNaN(value)!=0)) SetText("coderesult", "?");
	else if((value.length<2)||(value.length>11))
			 SetText("coderesult", "? (Out of range)");		 
	else SetText ("coderesult", eval("" + value.substring(0,2) + ((value.length-2))));
	SetText("tolresult", tolcode[obj.tolval.selectedIndex]);
	}
}