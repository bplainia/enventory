<? require "config.php";
require "./FPDF/fpdf.php";
class PDF extends FPDF
{
//variables
var $col=0;//Current column
var $y0;//Ordinate of column start
var $columns=1;//number of columns
var $colwid=80;//width of columns
var $colbegining=false;//is begining of page column


//Page header
function Header()
{
	//Logo
	//$this->Image('./FPDF/tutorial/logo_pb.png',10,8,30);
	//$this->Image('enventory.png',10,8,50);
	//Arial bold 15
	$this->SetFont('Arial','B',15);
	//Move to the right
	$this->Cell(80);
	//Title
	$this->setTextColor(0,128,0);
	$this->Cell(35,10,'eNVENTORY',1,0,'C');
	$this->setTextColor(255);
	//Line break
	$this->Ln(15);
}

//Page footer
function Footer()
{
	//Position at 1.5 cm from bottom
	$this->SetY(-15);
	//Arial italic 8
	$this->SetFont('Arial','I',8);
	//Page number
	$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

function SetCol($col)
{
    //Set position at a given column
    $this->col=$col;
    $x=10+$col*$this->colwid;
    $this->SetLeftMargin($x);
    $this->SetX($x);
}

function SetCols($number,$width)
{
  $this->columns=$number;
  $this->colwid=$width;
}

function AcceptPageBreak()
{
    //Method accepting or not automatic page break
    if($this->col<$this->columns)
    {
        //Go to next column
        $this->SetCol($this->col+1);
        //Set ordinate to top
        $this->SetY($this->y0);
        $this->colbegining=true;
        //Keep on page
        return false;
    }
    else
    {
        //Go back to first column
        $this->SetCol(0);
        $this->colbegining=true;
        //Page break
        return true;
    }
}


//Colored table
function table($header,$data)
{
	//Colors, line width and bold font
	$this->SetFillColor(0,255,0);
	$this->SetTextColor(255);
	$this->SetDrawColor(128,0,0);
	$this->SetLineWidth(.3);
	$this->SetFont('','B');
	//Header
	$w=array(40,35,40,45);
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
	$this->Ln();
	//Color and font restoration
	$this->SetFillColor(224,235,255);
	$this->SetTextColor(0);
	$this->SetFont('');
	//Data
	$fill=false;
	foreach($data as $row)
	{
		$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
		$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
		$this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
		$this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
		$this->Ln();
		$fill=!$fill;
	}
	$this->Cell(array_sum($w),0,'','T');
}
}

function color2Num($color,$multi){ //color is color, returns string
switch($color){
  case "gold":
    if($multi==1) return ".";
    if($multi==2) return array(null,.1);
    return;
  case "black":
    if($multi==1) return;
    if($multi==2) return array(null,1);
    return '0';
  case "brown":
    if($multi==1) return;
    if($multi==2) return array("0",10);
    return '1';
  case "red":
   if($multi==1) return ".";
   if($multi==2) return array(" K",100);
   return "2";
  case "orange":
    if($multi==1) return;
    if($multi==2) return array(" K",1000);
    return "3";
  case "yellow":
    if($multi==1) return;
    if($multi==2) return array("0 K",10000);
    return "4";
  case "green":
    if($multi==1) return ".";
    if($multi==2) return array(" M",100000);
    return "5";
  case "blue":
    if($multi==1) return;
    if($multi==2) return array(" M",1000000);
    return "6";
  case "purple":
    if($multi==1) return;
    if($multi==2) return array("0 M",10000000);
    return "7";
  case "violet":
    if($multi==1) return;
    if($multi==2) return array("0 M",100000000);
    return "7";
  case "grey":
    if($multi==1) return;
    if($multi==2) return array("00 M",1000000000);
    return "8";
  case "white":
    if($multi==1) return;
    if($multi==2) return array("Not a valid multiplier",10000000000);
    return "9";
  }
}//end color2Num
function getResType($type){ switch($type){ case "carbon": return "Carbon Film"; case "wire": return "Wire Wound"; default: return $type;}}

if(!isset($_POST['format'])){ //last form element
$title = "Reports"; 

// javascript is below here
$addscript = "function checkAll(go){ //everything
var form = document.form1;
if(go==1){
checkSemi(1); 
checkPass(1);
}
if(go==0){
checkSemi(0);
checkPass(0); 
return;
}
}

function checkSemi(go){ //Semiconductors
var form = document.form1;
if(go==1){
	form.semi.checked=true;
	form.IC.checked=true;
	form.trans.checked=true;
	form.diode.checked=true;
} 
if(go==0){
	form.semi.checked=false;
	form.IC.checked=false;
	form.trans.checked=false;
	form.diode.checked=false;
}
if(go==2) form.semi.checked=false;
return;
}

function checkPass(go){
  var form = document.form1;
  if(go==0){
    form.res.checked=false;
    form.cap.checked=false;
    form.ind.checked=false;
    form.pass.checked=false;
  }
  if(go==1){
    form.res.checked=true;
    form.cap.checked=true;
    form.ind.checked=true;
    form.pass.checked=true;
  }
  if(go==2) form.pass.checked=false;
}

///////////////////////////////////////////

function checkfrm(){
//var var0 = document.form1.type.value;
var var1 = document.form1.format.value;
//if(var0=='') { alert('no type selected'); return false; }
if(var1=='') { document.getElementById('formaterr').style.display='inline'; return false; }
else document.getElementById('formaterr').style.display='none';
//return true;
document.form1.submit();
}
";

require "header.php";

?>
<h1>Reports</h1>
<form action="" target="_BLANK" method="post" name="form1">
<p><a href="javascript:checkAll(1)">Select</a>/<a href="javascript:checkAll(0)">Unselect</a> All</p>
	<ul><li><input type="checkbox" name="semi" onclick="checkSemi(this.checked)"/>Semiconductors<ul>
		<li><input type="checkbox" name="IC"  value=1 onclick="if(!this.checked)checkSemi(2);"/>Integrated Circuts</li><font color="red">
		<li><input type="checkbox" name="trans"  value=1 onclick="if(!this.checked)checkSemi(2);"/>Transistors and Silicon Controlled Rectifiers</li>
		<li><input type="checkbox" name="diode" value=1 onclick="if(!this.checked)checkSemi(2);">Diodes</font></li></ul></li>
	<li><input type="checkbox" name="pass" onclick="checkPass(this.checked)"/>Passives<ul>
	  <li><input type="checkbox" name="res" value=1 onclick="if(!this.checked)checkPass(2);"/>Resistors</li>
	  <li><input type="checkbox" name="cap" value=1 onclick="if(!this.checked)checkPass(2);"/>Capacitors</li>
	  <li><input type="checkbox" name="ind" value=1 onclick="if(!this.checked)checkPass(2);"/>Inductors</li></ul>
	</li>
	</ul>
<p>Please select a format:<select name="format"><option value="">Select one</option><option value="pdf">PDF Document</option><option value="pdfd">PDF Download</option></select><div id="formaterr" class="hiderr">Please select a format.</div></p>
<p><input type="button" value="Create Report" onclick="return checkfrm()"></p></form>
<p><img src="./FPDF/tutorial/logo_pb.png" /></p>
</body></html>
<? } // end of input form
elseif($_POST['format']=='pdf' || $_POST['format']=='pdfd'){
startsql();
$pdf=new PDF('P','mm','Letter');
$pdf->AliasNbPages(); 
//$pdf->SetFont('Times','',12); //standard font


if(isset($_POST['IC'])){
  $pdf->AddPage(); 
  $one=true;
  $pdf->SetFont('Arial','B',15);
  $pdf->Cell(0,8,'Integrated Circuts',0,1);
  $pdf->SetFont('Times','',12);
  $sql="select number,quantity-used as qty,type,Package,Pins,Description from semiconductors_IC where user='".$user."' order by number;";
  $result = mysql_query($sql); $i=0;
  while($row=@mysql_fetch_array($result)){
    $data[$i]=$row;
    $i=$i+1;
  }
  //Colors, line width and bold font - BEGIN TABLE
	$pdf->SetFillColor(0,255,0);
	$pdf->SetTextColor(255);
	$pdf->SetDrawColor(128,0,0);
	$pdf->SetLineWidth(.3);
	$pdf->SetFont('','B');
	//Header\
	$header=array('ID','Qty','Type','Pack','Pins','Description');
	$w=array(40,10,20,15,15,null);
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	$pdf->Ln();
	//Color and font restoration
	$pdf->SetFillColor(224,235,255);
	$pdf->SetTextColor(0);

	$pdf->SetFont('');
	//Data
	$fill=false;
	foreach($data as $row)
	{
		$pdf->Cell($w[0],6,$row[0],1,0,'L',$fill);
		$pdf->Cell($w[1],6,number_format($row[1]),1,0,'L',$fill);
		$pdf->Cell($w[2],6,$row[2],1,0,'L',$fill);
		$pdf->Cell($w[3],6,$row[3],1,0,'L',$fill);
		$pdf->Cell($w[4],6,$row[4],1,0,'L',$fill);
		$pdf->Cell($w[5],6,$row[5],1,0,'L',$fill);
		$pdf->Ln();
		$fill=!$fill;
	}
	$pdf->Cell(array_sum($w),0,'','T'); // END TABLE
} 

if(isset($_POST['res'])){   ////////////////////////////////////////////Resistors///////////////////////
  $pdf->AddPage(); 
  $one=true;
  $pdf->SetFont('Arial','B',15);
  $pdf->Cell(0,8,'Resistors',0,1);
  $pdf->SetFont('Times','',12);
  $pdf->SetCols(2,80);
  $yhead=$pdf->GetY();
  $sql="select type,color1,color2,color3,multi,tollerance,wattage,quantity,used from passives_res where user='".$user."';";
  $result = mysql_query($sql); $i=0;
  while($row=@mysql_fetch_array($result)){
    $data[$i][0]=color2Num($row['color1'],0).color2Num($row['multi'],1).color2Num($row['color2'],0).$multi[0]." +/-".$row['tollerance']."%";//value+tollerance
    $data[$i][1]=$row['quantity']-$row['used'];//qty
    $data[$i][2]=getResType($row['type']);//type
    $data[$i][3]=$row['wattage'];//wattage
    $i=$i+1;
  }
  //Colors, line width and bold font - BEGIN TABLE
	$pdf->SetFillColor(0,255,0);
	$pdf->SetTextColor(255);
	$pdf->SetDrawColor(128,0,0);
	$pdf->SetLineWidth(.3);
	$pdf->SetFont('','B');
	//Header\
	$header=array('Value','Qty','Type','Watts');
	$w=array(25,10,25,15);
	for($i=0;$i<count($header);$i++)
		$pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	$pdf->Ln();
	//Color and font restoration
	$pdf->SetFillColor(224,235,255);
	$pdf->SetTextColor(0);

	$pdf->SetFont('');
	//Data
	$fill=false;
	
  $pdf->y0=$pdf->GetY();
	foreach($data as $row)
	{
		if($pdf->colbegining==true){
		  $pdf->SetY($yhead);
		  $pdf->setFillColor(0,255,0);
		  $pdf->setTextColor(255);
		  $pdf->SetFont('','B');
		  for($i=0;$i<count($header);$i++)
		    $pdf->Cell($w[$i],7,$header[$i],1,0,'C',true);
	    $pdf->Ln();
	    //Color and font restoration
	    $pdf->SetFillColor(224,235,255);
	    $pdf->SetTextColor(0);
	    $pdf->SetFont('');
	    $pdf->colbegining=false;
	    $pdf->SetY($pdf->y0+6);
	  }
		$pdf->Cell($w[0],6,$row[0],1,0,'L',$fill);
		$pdf->Cell($w[1],6,number_format($row[1]),1,0,'L',$fill);
		$pdf->Cell($w[2],6,$row[2],1,0,'L',$fill);
		$pdf->Cell($w[3],6,number_format($row[3],3),1,0,'L',$fill);
		$pdf->Ln();
		$fill=!$fill;
	}
	$pdf->Cell(array_sum($w),0,'','T'); // END TABLE
	$pdf->SetCols(1,80);
} 

//echo "this is data:";
//print_r($data[0]); /*
if(@$one==true){
if($_POST['format']=='pdfd')$pdf->Output("report.pdf",'D');
else $pdf->Output();  //*/
}
else die("<html><body>You did not select any category. <a href=\"javascript:window.close()\">Close Page</a></body></html>");
}//end of pdf output
else echo "error";
?>
