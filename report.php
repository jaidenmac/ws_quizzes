<style>
	BODY, P, TD { font-family: Calibri, Arial; }
</style>

<?php

// Variables
	$Host = "localhost";
	$User = "jaidenma_android";
	$Password = "V1ntage";
	$DBName = "jaidenma_quizzes";
	
// connect to SQL DB server
	$Link = mysql_connect ($Host, $User, $Password) or die ("The database connection could not be established!");
	

	echo "<h2>Quizzes App Database Tables</h2>";

	showTable("Companies");
	showTable("LanguageStrings");
	showTable("Logins");
	showTable("QuestionTypes");
	showTable("QuizAnswers");
	showTable("QuizQuestions");
	showTable("QuizResultsData");
	showTable("QuizTypes");
	showTable("Quizzes");
	showTable("UserQuizzes");
	showTable("Users");



function showTable($tableName)
{
	global $DBName, $Link;
	
	$showTableQuery = "SELECT * FROM $tableName";
	$showTableResult = mysql_db_query($DBName, $showTableQuery, $Link) or die("<p><font class=text>Could not complete showTableQuery: <font color='red'>$showTableQuery</font>");

	echo "<p><b>$tableName</b>";
	echo "<p><table border='1' cellpadding='2' cellspacing='0'><tr>";
	
	$getColumnsQuery = "SELECT COLUMN_NAME FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA` = '$DBName' AND `TABLE_NAME` = '$tableName'";
	$getColumnsResult = mysql_db_query($DBName, $getColumnsQuery, $Link) or die("<p><font class=text>Could not complete getColumnsQuery: <font color='red'>$getColumnsQuery</font>");
	
	while ($header = mysql_fetch_array($getColumnsResult))
	{	
		echo "<td>$header[COLUMN_NAME]</td>";
	}
	echo "</tr>";
	
	while ($rows = mysql_fetch_array($showTableResult))
	{
		echo "<tr>";
		$getColumnsResult2 = mysql_db_query($DBName, $getColumnsQuery, $Link) or die("<p><font class=text>Could not complete getColumnsQuery: <font color='red'>$getColumnsQuery</font>");
	
		while ($cols = mysql_fetch_array($getColumnsResult2))
		{	
			$colName = $cols[COLUMN_NAME];
			$colValue = $rows[$colName];
			if ($colValue == NULL)
			{
				$colValue = "<font color='#AAA'>NULL</font>";
			}
			echo "<td>$colValue</td>";
		}
		echo "</tr>";	
	}	
	echo "</table>";	
}


?>
