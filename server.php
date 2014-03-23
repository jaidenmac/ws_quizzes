<?php

	import_request_variables("g","");
	extract($_REQUEST);
	
	if ($format != "xml" && $format != "chardelimited")	
	{
		$format = "json";
	}
	
	if ($format == "json")
	{
		header('Content-type: application/json');	
	}
	else if ($format == "xml")
	{
		header('Content-type: text/xml');
		
		$xml = new XMLWriter();
		$xml->openURI("php://output");
		$xml->startDocument();
		$xml->setIndent(true);			
	}
	

// Variables
	$Host = "localhost";
	$User = "jaidenma_android";
	$Password = "V1ntage";
	$DBName = "jaidenma_quotes";
	$day = date('d');
	$month = date('m');
	$year = date('Y');	
	$date = "$year-$month-$day";
	$debug = 0;
	
// connect to SQL DB server
	$Link = mysql_connect ($Host, $User, $Password) or die ("The database connection could not be established!");
	
	if ($debug)
	{
		echo "<p><font color='blue'><b>action = $action</b></font>";
		echo "<p>Date = $date";
		$format = "chardelimited";
	}
	
	if ($action == "RegisterUser") 
	{
		// ?action=RegisterUser&UUID=android4vn&FirstName=Rob&LastName=Chen&Email=jaidenmac@gmail.com
		
		$checkUserQuery = "SELECT * FROM Users WHERE DeviceID = '$UUID'";
		$checkUserResult = mysql_db_query($DBName, $checkUserQuery, $Link) or die("<p><font class=text>Could not complete checkUserQuery: <font color='red'>$checkUserQuery</font>");
		$checkUser = mysql_fetch_array($checkUserResult);
			
		if (!$checkUser)
		{
			$insertUserQuery = "INSERT INTO Users (DeviceID, FirstName, LastName, Email, DateCreated, DateLastActive) VALUES ('$UUID', '$FirstName', '$LastName', '$Email', '$date', '$date')";
			$insertUserResult = mysql_db_query($DBName, $insertUserQuery, $Link) or die("<p><font class=text>Could not complete insertUserQuery: <font color='red'>$insertUserQuery</font>");

			$addResult = "Success";
		}
		else 
		{
			$addResult = "User Already Exists";
		}

		if ($format == "json")
		{
			$returnResult  = array();
			$i = 0;
		
			$returnResult[$i]['Result'][] = $addResult;
			echo json_encode(array('RegisterUser'=>$returnResult));	
		}
		else if ($format == "xml")
		{
			$xml->startElement('AddQuote');
	
				$xml->startElement("Result");
				$xml->writeRaw($addResult);			
				$xml->endElement();
				
			$xml->endElement();
			$xml->flush();		
		}
		else
		{	
			echo "<p>$addResult";
		}			
	}	
	else if ($action == "AddCategory") 
	{
		// ?action=AddCategory&CategoryName=newcategory
		
		$checkCategoryQuery = "SELECT * FROM Categories WHERE CategoryName = '$CategoryName'";
		$checkCategoryResult = mysql_db_query($DBName, $checkCategoryQuery, $Link) or die("<p><font class=text>Could not complete checkCategoryQuery: <font color='red'>$checkCategoryQuery</font>");
		$checkCategory = mysql_fetch_array($checkCategoryResult);
			
		if (!$checkCategory)
		{
			$insertCategoryQuery = "INSERT INTO Categories (CategoryName) VALUES ('$CategoryName')";
			$insertUserResult = mysql_db_query($DBName, $insertCategoryQuery, $Link) or die("<p><font class=text>Could not complete insertCategoryQuery: <font color='red'>$insertCategoryQuery</font>");

			$addResult = "Success";
		}
		else 
		{
			$addResult = "Category Already Exists";
		}
		
		if ($format == "json")
		{	
			$returnResult  = array();
			$i = 0;
			
			$returnResult[$i]['Result'][] = $addResult;
	
			echo json_encode(array('AddCategory'=>$returnResult));	
		}
		else if ($format == "xml")
		{
			$xml->startElement('AddQuote');
	
				$xml->startElement("Result");
				$xml->writeRaw($addResult);			
				$xml->endElement();
				
			$xml->endElement();
			$xml->flush();		
		}
		else
		{	
			echo "<p>$addResult";
		}		
	}			
	else if ($action == "GetCategories") 
	{
		// ?action=GetCategories&UUID=android4vn

		if ($UUID)
		{
			$checkUserQuery = "SELECT * FROM Users WHERE DeviceID = '$UUID'";
			$checkUserResult = mysql_db_query($DBName, $checkUserQuery, $Link) or die("<p><font class=text>Could not complete checkUserQuery: <font color='red'>$checkUserQuery</font>");
			$checkUser = mysql_fetch_array($checkUserResult);
				
			if (!$checkUser)
			{
				$insertUserQuery = "INSERT INTO Users (DeviceID, DateCreated, DateLastActive) VALUES ('$UUID', '$date', '$date')";
				$insertUserResult = mysql_db_query($DBName, $insertUserQuery, $Link) or die("<p><font class=text>Could not complete insertUserQuery: <font color='red'>$insertUserQuery</font>");
			}		
		}
		
		$categoriesQuery = "SELECT * FROM Categories";
		$categoriesResult = mysql_db_query($DBName, $categoriesQuery, $Link) or die("<p><font class=text>Could not complete categoriesQuery: <font color='red'>$categoriesQuery</font>");

		if ($format == "json")
		{
			$returnCategories  = array();
			$i = 0;
			
			while ($categories = mysql_fetch_array($categoriesResult))
			{
				if ($debug)
				{			
					echo "<p>Category Name = $categories[CategoryName]";
				}
				
				$returnCategories[$i]['ID'][]       	= $categories['ID'];
	            $returnCategories[$i]['CategoryName'][] = $categories['CategoryName'];
				$i++;
			}
			
			echo json_encode(array('GetCategories'=>$returnCategories));	
			
		}
		else if ($format == "xml")
		{	
			$xml->startElement('Categories');
			
			while ($categories = mysql_fetch_array($categoriesResult))
			{
				if ($debug)
				{			
					echo "<p>Category Name = $categories[CategoryName]";
				}
				
				$xml->startElement("Category");
				//$xml->writeAttribute('ID', $categories['ID']);
					$xml->startElement("ID");
					$xml->writeRaw($categories['ID']);			
					$xml->endElement();
					
					$xml->startElement("Name");
					$xml->writeRaw($categories['CategoryName']);			
					$xml->endElement();
				$xml->endElement();
			}
			
			$xml->endElement();
			$xml->flush();		
		}
		else
		{
			$returnCategories = "";
			$tempCategories = "";
	
			while ($categories = mysql_fetch_array($categoriesResult))
			{
				if ($debug)
				{			
					echo "<p>Category Name = $categories[CategoryName]";
				}
				
				$returnCategories = $returnCategories . $categories[CategoryName] . "|";
			}
			
			echo "<p>$returnCategories";			
		}
	}
	else if ($action == "GetNextQuote") 
	{
		// ?action=GetNextQuote&UUID=SCH1001-229&categoryID=2&previousQuoteID=1&rating=1

		if ($debug) 
		{
			echo "<p>UniqueUserID = $UUID";
			echo "<p>Category ID = $categoryID";
			echo "<p>Previous Quote ID = $previousQuoteID";		
			echo "<p>Rating = $rating";		
		}	
		
		if ($UUID != "" && $categoryID != "")
		{	
			if ($previousQuoteID != "")
			{
				$getUserIDQuery = "SELECT * FROM Users WHERE DeviceID = '$UUID'";
				$getUserIDResult = mysql_db_query($DBName, $getUserIDQuery, $Link) or die("<p><font class=text>Could not complete getUserIDQuery: <font color='red'>$getUserIDQuery</font>");
				$getUserID = mysql_fetch_array($getUserIDResult);
				
				if ($debug) 
				{			
					echo "<p>User ID = $getUserID[ID]";
				}
				
				$insertRatingQuery = "INSERT INTO Ratings (QuoteID, UserID, Rating, DateRated) VALUES ('$previousQuoteID','$getUserID[ID]','$rating','$date')";
				$insertRatingResult = mysql_db_query($DBName, $insertRatingQuery, $Link) or die("<p><font class=text>Could not complete insertRatingQuery: <font color='red'>$insertRatingQuery</font>");
							
				$getRatedQuoteQuery = "SELECT * FROM Quotes WHERE ID = '$previousQuoteID'";
				$getRatedQuoteResult = mysql_db_query($DBName, $getRatedQuoteQuery, $Link) or die("<p><font class=text>Could not complete getRatedQuoteQuery: <font color='red'>$getRatedQuoteQuery</font>");
				$getRatedQuote = mysql_fetch_array($getRatedQuoteResult);
				
				if ($debug) 
				{					
					echo "<p><b>Updated Previous Quote:</b>";									
					echo "<p><table border='1' cellpadding='2' cellspacing='0'><tr><td>ID</td><td>CategoryID</td><td>QuoteText</td><td>QuoteAuthor</td><td>AddedbyUser</td><td>DateAdded</td><td>DateRatingUpdated</td><td>RatingsTotal</td><td>RatingsPositive</td><td>Score</td><td>Active</td></tr>";
					echo "<tr><td>$getRatedQuote[ID]</td><td>$getRatedQuote[CategoryID]</td><td>$getRatedQuote[QuoteText]</td><td>$getRatedQuote[QuoteAuthor]</td><td>$getRatedQuote[AddedbyUser]</td><td>$getRatedQuote[DateAdded]</td><td>$getRatedQuote[DateRatingUpdated]</td><td>$getRatedQuote[RatingsTotal]</td><td>$getRatedQuote[RatingsPositive]</td><td>$getRatedQuote[Score]</td><td>$getRatedQuote[Active]</td></tr>";
					echo "</table>";																		
				}
												
				$newRatingsTotal = (int)$getRatedQuote['RatingsTotal'] + 1;
				$newRatingsPositive = (int)$getRatedQuote[RatingsPositive];
				if ($rating == 1)
				{
					$newRatingsPositive = (int)$getRatedQuote[RatingsPositive] + 1;
				}
				$newRatingScore = (int)$newRatingsPositive / (int)$newRatingsTotal;
				
				if ($debug) 
				{
					echo "<p>New Rating Total = $newRatingsTotal";
					echo "<p>New Rating Positive = $newRatingsPositive";
					echo "<p>New Rating Score = $newRatingScore";
				}
													
				$updateQuoteRatingQuery = "UPDATE Quotes SET DateRatingUpdated = '$date', RatingsTotal = '$newRatingsTotal', RatingsPositive = '$newRatingsPositive', Score = '$newRatingScore' WHERE ID = '$previousQuoteID'";
				$updateQuoteRatingResult = mysql_db_query($DBName, $updateQuoteRatingQuery, $Link) or die("<p><font class=text>Could not complete updateQuoteRatingQuery: <font color='red'>$updateQuoteRatingQuery</font>");
				
			}
			else 
			{
				$previousQuoteID = 0;
			}
			
			$getNextQuoteQuery = "SELECT * FROM Quotes WHERE ID > '$previousQuoteID' AND CategoryID = '$categoryID' AND Active = '1' ORDER BY ID";
			$getNextQuoteResult = mysql_db_query($DBName, $getNextQuoteQuery, $Link) or die("<p><font class=text>Could not complete getNextQuoteQuery: <font color='red'>$getNextQuoteQuery</font>");
			$getNextQuote = mysql_fetch_array($getNextQuoteResult);
			
			if(!$getNextQuote) 
			{
				if ($debug)
				{
					echo "<p>No results were given for the following SQL statement<p><font color='red'>".$getNextQuoteQuery."</font></p>";
				}
				
				$getNextQuoteQuery = "SELECT * FROM Quotes WHERE CategoryID = '$categoryID' AND Active = '1' ORDER BY ID";
				$getNextQuoteResult = mysql_db_query($DBName, $getNextQuoteQuery, $Link) or die("<p><font class=text>Could not complete getNextQuoteQuery: <font color='red'>$getNextQuoteQuery</font>");
				$getNextQuote = mysql_fetch_array($getNextQuoteResult);	
				
			}
			
			// TODO: if ($getNextQuote == NULL) get first quote
			if ($debug) 
			{
				echo "<p><b>Next Quote:</b>";		
				echo "<p><table border='1' cellpadding='2' cellspacing='0'><tr><td>ID</td><td>CategoryID</td><td>QuoteText</td><td>QuoteAuthor</td><td>AddedbyUser</td><td>DateAdded</td><td>DateRatingUpdated</td><td>RatingsTotal</td><td>RatingsPositive</td><td>Score</td><td>Active</td></tr>";
				echo "<tr><td>$getNextQuote[ID]</td><td>$getNextQuote[CategoryID]</td><td>$getNextQuote[QuoteText]</td><td>$getNextQuote[QuoteAuthor]</td><td>$getNextQuote[AddedbyUser]</td><td>$getNextQuote[DateAdded]</td><td>$getNextQuote[DateRatingUpdated]</td><td>$getNextQuote[RatingsTotal]</td><td>$getNextQuote[RatingsPositive]</td><td>$getNextQuote[Score]</td><td>$getNextQuote[Active]</td></tr>";
				echo "</table>";
			}
			
			if ($format == "json")
			{	
				$returnQuote  = array();
				$i = 0;
	
				$returnQuote[$i]['ID'][]       		= $getNextQuote['ID'];
	            $returnQuote[$i]['QuoteText'][] 	= $getNextQuote['QuoteText'];
	            $returnQuote[$i]['QuoteAuthor'][] 	= $getNextQuote['QuoteAuthor'];
	
				echo json_encode(array('GetNextQuote'=>$returnQuote));				
			}
			else if ($format == "xml")
			{
				$xml->startElement('Quote');
	
					$xml->startElement("ID");
					$xml->writeRaw($getNextQuote['ID']);			
					$xml->endElement();
					
					$xml->startElement("Text");
					$xml->writeRaw($getNextQuote['QuoteText']);			
					$xml->endElement();
					
					$xml->startElement("Author");
					$xml->writeRaw($getNextQuote['QuoteAuthor']);			
					$xml->endElement();				
					
				$xml->endElement();
				$xml->flush();						
			}
			else
			{
				$returnQuote = $getNextQuote[ID] . "|" . $getNextQuote[QuoteText] . "|" . $getNextQuote[QuoteAuthor];
				echo "<p>$returnQuote";					
			}
		}
	}
	else if ($action == "AddQuote") 
	{
		// ?action=AddQuote&UUID=SCH1001-229&categoryID=5&quoteText=A person's a person, no matter how small.&quoteAuthor=Dr. Seuss&userEmail=test@test.com

		if ($debug) 
		{
			echo "<p>UniqueUserID = $UUID";
			echo "<p>Category ID = $categoryID";
			echo "<p>Quote Text = $quoteText";
			echo "<p>Quote Author = $quoteAuthor";
			echo "<p>User Email = $userEmail";
		}
				
		$addResult = "Fail";
		
		if ($UUID != "" && $categoryID != "" && $quoteText != "" && $quoteAuthor != "")
		{
			
			$getUserIDQuery = "SELECT * FROM Users WHERE DeviceID = '$UUID'";
			$getUserIDResult = mysql_db_query($DBName, $getUserIDQuery, $Link) or die("<p><font class=text>Could not complete getUserIDQuery: <font color='red'>$getUserIDQuery</font>");
			$getUserID = mysql_fetch_array($getUserIDResult);
			
			if (!$getUserID) 
			{
				$insertUserQuery = "INSERT INTO Users (DeviceID, FirstName, LastName, Email, DateCreated, DateLastActive) VALUES ('$UUID','','','','$date','$date')";
				$insertUserResult = mysql_db_query($DBName, $addQuoteQuery, $Link) or die("<p><font class=text>Could not complete insertUserQuery: $insertUserQuery");
				
				$getUserIDQuery = "SELECT * FROM Users WHERE DeviceID = '$UUID'";
				$getUserIDResult = mysql_db_query($DBName, $getUserIDQuery, $Link) or die("<p><font class=text>Could not complete getUserIDQuery: <font color='red'>$getUserIDQuery</font>");
				$getUserID = mysql_fetch_array($getUserIDResult);				
			}
						
			$addQuoteQuery = "INSERT INTO Quotes (CategoryID, QuoteText, QuoteAuthor, AddedbyUser, DateAdded, DateRatingUpdated) VALUES ('$categoryID','$quoteText','$quoteAuthor','$getUserID[ID]','$date','$date')";
			$addQuoteResult = mysql_db_query($DBName, $addQuoteQuery, $Link) or die("<p><font class=text>Could not complete addQuoteQuery: $addQuoteQuery");
			
			if ($userEmail != "")
			{
				$updateUserQuery = "UPDATE Users SET Email = '$userEmail' WHERE DeviceID = '$UUID'";
				$updateUserResult = mysql_db_query($DBName, $updateUserQuery, $Link) or die("<p><font class=text>Could not complete updateUserQuery: $updateUserQuery");
			
				if ($Signup == "True")
				{
					$updateUserQuery2 = "UPDATE Users SET Subscribed = '1' WHERE DeviceID = '$UUID'";
					$updateUserResult2 = mysql_db_query($DBName, $updateUserQuery2, $Link) or die("<p><font class=text>Could not complete updateUserQuery2: $updateUserQuery2");
				}	
			}
			
			$addResult = "Success";
		}	
		
		if ($format == "json")
		{	
			$returnResult  = array();
			$i = 0;
	
			$returnResult[$i]['Result'][] = $addResult;
	
			echo json_encode(array('AddQuote'=>$returnResult));				
		}
		else if ($format == "xml")
		{		
			$xml->startElement('AddQuote');
	
				$xml->startElement("Result");
				$xml->writeRaw($addResult);			
				$xml->endElement();
				
			$xml->endElement();
			$xml->flush();					
		}
		else
		{
			echo "<p>$addResult";
		}
	}

?>
