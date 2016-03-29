# weather-Yii-
Getting weather from DB/API (using Yii framework)

 Question:
 Please download Yii framework (version 1, not version 2!).
Create a simple web application which makes a request to openweathermap API in order to retrieve current weather data for London.
Create a page which will allow the user to click a button and get the current weather data
a.            a REST call will be made in order to retrieve the weather data
(http://api.openweathermap.org/data/2.5/weather?q=London,uk)

Bonus:
1.            Use AJAX to create a request to your server side, the server side will call the weather API and will return the results in JSON format to the client.  
b.            Save the weather data into an sqlite database file (you don’t have to save all fields, choose whatever fields you wish).
c.             Display the data to the user (you can use a simple html table or use Yii gridView).
d.            If data exists for the same date already, update the record.
e.            If a request was made less than 5 minutes ago, do not initiate an API request, instead display the data from the database (sqlite).

You can find help using the API here: http://openweathermap.org/api

------------------------------------------------------------------------------

I implemented weather app ((site/weather)) in two ways: post via FORM and post via AJAX jquery: 

1. post via Form with simple captcha validation.
a. validation in Controller
b. getting data from DB(weather table) (updated before by API response)
c. building DataProvider
d. sending data to different Page into dataGrid widget

2. post with native Ajax jquery
a. bind event to some html element 
b. sending request to function in Controller
c. getting data from DB (updated before by API response)
d. sending as json to client side
e. building simple html table on the same page


Server Side:
1. sqlite database: protected\data\weather.db
2. external CURL extension plugin
3. CSqlDataProvider
4. Active Record
5. CVarDumper plugin for console printing

Software:
Chrome
SqliteBrowser
phpStorm
XAMPP

Possible mistakes:
1. There is no building parts of Yii_UI from server on  the same action client page .
2. There is no exporting dataGrid widget via ajax response in views


Future possible additions:
1. City input in the form
2. response id by City (800, 803 , ...) added to weather table
3. User session validation after/before login
