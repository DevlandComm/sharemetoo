<form action='./permitstatus_report_final.php' method='post' name='frm'>
<?php
foreach ($_POST as $a => $b) {
    if($a != "report_type")
        echo "<input type='hidden' name='".htmlentities($a)."' value='".htmlentities($b)."'>";
}
?>
<!--<input type="submit" value="submit">-->
</form>
<script language="JavaScript">
document.frm.submit();
</script>