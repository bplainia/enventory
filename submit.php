<!DOCTYPE html>
<html>
<head>
<title>Submit Parts</title>
<script src="submit.js"></script>
</head>
<body>
<h1>Submit a part</h1>
<form action="processor.php" method="POST">
<div id="group"><select name="group"><option>Semiconductor</option><option>Passive</option></select></div>
<div id="type"></div>
<div id="data"></div>
<div id="submitsection" style="display:none;"><input type="submit" name="submit" value="4"></div>
</form>
</body>
</html>