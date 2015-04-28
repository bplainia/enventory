<?php
class Capacitors implements component {
  
    // constructor: get mysql
    function __construct($command = null)
    {
        $this->db = startsql();
        if($command == null)
        {
            $this->makeTable();
        }
    }
    function title()
    {
        return "Capacitors";
    }
    function table()
    {
        return $this->table;
    }
  
  // calculate values from a value or a code
    function calcValues($value,$code) 
    {
        if($code==null || $code == 0) 
            {
            if(strlen($value) == 3 or strlen($value) == 2)
            {
                if (strlen($value) == 2)
                {
                    $power = -12;
                } else
                {
                    $power = (int) substr($value, 2, 1) - 12;
                }
                $raw = substr($value, 0, 2);
                if (!isset($_GET['micronly']))
                {
                    if ($power < -10)
                    {
                        $output = $raw * pow(10, $power + 12) . " pF";
                    } elseif ($power < -7)
                    {
                        $output = $raw * pow(10, $power + 9) . " nF";
                    } else
                    {
                        $output = $raw * pow(10, $power + 6) . "&mu;F";
                    }
                }
                else
                {
                    $output = number_format($raw * pow(10, $power + 6), 6) . " &mu;F";
                }
                return array($output, $raw * pow(10, $power));
            } 
            else
            {
                return array("error", null);
            }
              //SetText("tolerance", tolval[obj.tolerancecode.selectedIndex]);
        }
        else 
        { 
            $rawValue=$value*pow(10,-6);
            $value=(float)$value;
            if($value < 0.001)
            {
                $value *= 1000000;
                $unit = "pF";
            }
            elseif($value < 1.0)
            {
                $value = $value * 1000;
                $unit = "nF";
            }
            else 
            {
                $unit = "&mu;F";
            }
            return array($value." ".$unit,$rawValue);
        }
      }

    function modComponent($id,$data)
    {
        $action=substr($_POST['val'],0,1);
        if ($action == '+' || $action == '-')
        {
            $addval = $action . " " . substr($_POST['val'], 1);
        } 
        else
        {
            $addval = "+ $action";
        }
        $sql="update passives_caps set quantity = quantity ".$addval." where ID=".$_POST['id'].";";
        $db->query($sql);
    }
    function useComponent($id,$data)
    {
        $action=substr($_POST['val'],0,1);
        if ($action == '+' || $action == '-')
        {
            $addval = $action . " " . substr($_POST['val'], 1);
        } 
        else
        {
            $addval = "+ $action";
        }
        $sql="update passives_caps set used = used ".$addval." where ID=".$_POST['id'].";";
        $db->query($sql);
    }
    function delComponent($id)
    {
        // FIXME: what is $id for?
        $sql="delete from passives_caps where ID=".$_POST['id'].";";
        $db->query($sql);
    }
    function addComponent($data)
    {
        // FIXME: what is $data for?
        if ($_POST['valtype'] == 0)
        {
            $code = "true";
        } 
        else
        {
            $code = "false";
        }
        $stment = $db->prepare("insert into passives_caps (quantity,Used,value,voltage,type,Color,comment,code,Manufacturer,user) values ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
        $stment->execute(array($_POST['quantity'],$_POST['used'],$_POST['value'],$_POST['volt'],$_POST['type'],$_POST['color'],$_POST['comment'],$code,$_POST['manu'],$user));
    }

    // table
    function makeTable()
    {
        $table = '<div id="pager" class="pager">
        <form>
          <input type="button" value="&lt;&lt;" class="first" />
          <input type="button" value="&lt;" class="prev" />
          <input type="text" class="pagedisplay" readonly/>
          <input type="button" value="&gt;" class="next" />
          <input type="button" value="&gt;&gt;" class="last" />
          <select class="pagesize">
            <option selected="selected"  value="10">10</option>
            <option value="20">20</option>
            <option value="30">30</option>
            <option value="40">40</option>
          </select>
        </form>
        </div>
        <table border=1 class="tablesorter" id="myTable">
        <thead>
          <tr>
            <th>Qty</th>
            <th class="sorter-metric" data-metric-name="F|Farad">Value</th>
            <th>Rating</th>
            <th>Type</th>
            <th>Color</th>
            <th class="sorter-text">Manufacturer</th>
            <th class="sorter-false">Comment</th>
            <th class="sorter-false">AUR</th>
          </tr>
        </thead>
        <tbody>';
        $user = "benjamin";
        if (isset($cat[2]))
        {
            $sql = "select * from passives_caps where type='" . $cat[2] . "' and ((user='" . $user . "') or (user is null))  order by value;";
        } 
        else
        {
            $sql = "select * from passives_caps where (user='" . $user . "') or (user is null) order by value, voltage;";
        }
        $result = $this->db->query($sql);
        $quantity=0;
        $this->components = $result->fetchAll(PDO::FETCH_ASSOC);
        foreach($this->components as $row)
        {
            list($value,$valraw)=$this->calcValues($row['value'],$row['code']);
            $table .= "<tr>";
            $table .= "<td>".$row['quantity']."</td>";
            $quantity += $row['quantity'];
            $table .= "<td>".$value."</td>";
            if($row['voltage']!=0) $table .= "<td>".$row['voltage']."V</td>";
            else $table .= "<td>-</td>";
            $table .= "<td>".$row['type']."</td>";
            $table .= "<td>".$row['Color']."</td>";
            $table .= "<td>".$row['Manufacturer']."</td>";
            $table .= "<td>".$row['comment'];
            if($row['code']) $table .= " (".$row['value'].")";
            $table .= "</td>";
            $table .= "<td><form method=\"post\"><select name=\"com\" ><option value=\"add\">add<option value=\"use\">use<option value=\"rm\">remove</select><input type=\"text\" onKeyPress=\"return onlyNumbers()\" size=2 name=\"val\"><input type=\"image\" valign=\"middle\" src=\"go.gif\" width=30 height=16 border=0 value=\"\"><input type=\"hidden\" name=\"id\" value=\"".$row['ID']."\"></form></td>";
            $table .= "</tr>\n";
            $valraw=null;
        }
        $table .= "</tbody><tfoot><tr><td>$quantity</td></tr></tfooot></table>";
        $this->table = $table;
    }
    function content()
    { // TODO: change php feilds to variables for the selection.
        $noselect = " selected";
        $radial = $axial = $ceramic = "";
        if(isset($cat[2]))
        {
            if($cat[2]=='axial')
            {
                $axial = $noselect;
                $noselect = "";
            }
            if($cat[2]=='ceramic')
            {
                $ceramic = $noselect;
                $noselect = "";
            }
            if($cat[2]=='radial')
            {
                $radial = $noselect;
                $noselect = "";
            }
        }
    echo <<<EOD
  <h1>Capacitors</h1>
  <a href="passives.php">Back to passives home</a>
  <p><select onchange="goto('passives.php/capacitors'+this.value,false)"><option value="" $noselect>All<option value="/radial" $radial>radial<option value="/axial" $axial>axial<option value="/ceramic" $ceramic>ceramic</select></p>
EOD;
    }
    function jscript()
    {
        return ""; // no javascript
    }
    function form()
    {
        return <<<EOD
  <h2><a name="add">&nbsp;</a>Add Capacitor</h2>
  <script language="javascript">
  //<!--
  function checkType(type){
  return false;
  }
  //-->
  </script>
  <form name="addCap" method="post" action="/passives.php/capacitors#add"><table>
  <tr><td align="right">Quantity:</td><td><input tabindex=1 type="text" name="quantity" size=2 onkeypress="return onlyNumbers()" /></td></tr>
  <tr><td align="right">Used:</td><td><input tabindex=2 type="text" name="used" size=2 onkeypress="return onlyNumbers()" value="0" /></td></tr>
  <tr><td align="right">Type:</td><td><select tabindex=6 name="type"><option></option><option value="radial">Radial</option><option value="axial">Axial</option><option value="ceramic">Ceramic</option><option value="poly">Polymer</option><option value="mylar">Mylar</option></select></td></tr>
  <tr><td align="right">Value:</td><td><input tabindex=4 size=3 type="text" name="value" /><select name="valtype" tabindex=5><option value=1>&mu;F</option><option value=0>code</option></select></td></tr>
  <tr><td align="right">Voltage:</td><td><input tabindex=6 type="text" name="volt" size=2 onkeypress="return onlyNumbers()" /> Volts</td></tr>
  <tr><td align="right">Color:</td><td><input type="text" maxlength=15 tabindex=7 name="color"></select></td></tr>
  <tr><td align="right">Comment:</td><td><textarea tabindex=8 name="comment"></textarea></td></tr>
  <tr><td align="right">Manufacturer:</td><td><input tabindex=9 type="text" maxlength=15 name="manu"/></td></tr>
  <tr><td><input type="hidden" name="com" value="new" /></td><td><input tabindex=10 type="submit" value="Add Capacitor" /></td></tr>
  </table></form>
EOD;
    }
}
//The end
